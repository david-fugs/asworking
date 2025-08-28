<?php
session_start();
include("../../conexion.php");

// Verificar sesión
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}

// Verificar que se recibieron datos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: editLocationFolder.php");
    exit();
}

// Verificar que se seleccionaron reportes
if (!isset($_POST['selected_reports']) || empty($_POST['selected_reports'])) {
    $_SESSION['error_message'] = "No se seleccionaron reportes para actualizar.";
    header("Location: editLocationFolder.php");
    exit();
}

$selected_reports = $_POST['selected_reports'];
$new_folders = $_POST['new_folder'] ?? [];

$new_locations = $_POST['new_location'] ?? [];
// New edited quantities from form: edited_quantity[<id>] => value
$edited_quantities = $_POST['edited_quantity'] ?? [];

$success_count = 0;
$error_count = 0;
$errors = [];

// Comenzar transacción
$mysqli->autocommit(FALSE);

try {
    foreach ($selected_reports as $report_id) {
        $report_id = intval($report_id);
        // Obtener los nuevos valores (solo folder y location)
        $new_folder = isset($new_folders[$report_id]) ? trim($new_folders[$report_id]) : '';
        $new_location = isset($new_locations[$report_id]) ? trim($new_locations[$report_id]) : '';

        // DEBUG: Log de valores recibidos (only if debug is enabled)
        if (isset($_GET['debug']) || isset($_POST['debug'])) {
            error_log("DEBUG - Report ID: $report_id");
            error_log("DEBUG - new_folder: '$new_folder'");
            error_log("DEBUG - new_location: '$new_location'");
        }
        $estado_item = 1;
        // 1. Actualizar daily_report (consulta plana)
        $new_folder_esc = $mysqli->real_escape_string($new_folder);
        $new_location_esc = $mysqli->real_escape_string($new_location);
        $update_sql = "UPDATE daily_report 
                      SET folder_report = '$new_folder_esc', 
                          loc_report = '$new_location_esc',
                          estado_reporte = -1,
                          fecha_modificacion = NOW()
                      WHERE id_report = $report_id 
                      AND estado_reporte = 0";
        if ($mysqli->query($update_sql) && $mysqli->affected_rows > 0) {
            // 2. Obtener el upc_final_report de este reporte
                    // Also obtain sku_report so we can target the correct items row
                    $get_upc_sql = "SELECT upc_final_report, sku_report FROM daily_report WHERE id_report = ?";
            $get_upc_stmt = $mysqli->prepare($get_upc_sql);
            if ($get_upc_stmt) {
                $get_upc_stmt->bind_param("i", $report_id);
                if ($get_upc_stmt->execute()) {
                    $upc_result = $get_upc_stmt->get_result();
                    if ($upc_row = $upc_result->fetch_assoc()) {
                        $upc_final = $upc_row['upc_final_report'];
                        $sku_report = isset($upc_row['sku_report']) ? $upc_row['sku_report'] : '';
                        // 3. Actualizar solo el campo inventory_item en la tabla items (consulta plana)
                        $new_location_esc = $mysqli->real_escape_string($new_location);
                        $new_folder_esc = $mysqli->real_escape_string($new_folder);
                        $upc_final_esc = $mysqli->real_escape_string($upc_final);
                        // If sku_report is provided, include it in the WHERE clause to avoid updating other SKUs with same UPC
                        if (!empty($sku_report)) {
                            $sku_report_esc = $mysqli->real_escape_string($sku_report);
                            $update_items_sql = "UPDATE items SET inventory_item = '$new_location_esc', estado_item = $estado_item, folder_item= '$new_folder_esc' WHERE upc_item = '$upc_final_esc' AND sku_item = '$sku_report_esc'";
                        } else {
                            // Fallback: no sku available, update by UPC only (legacy behavior)
                            $update_items_sql = "UPDATE items SET inventory_item = '$new_location_esc', estado_item = $estado_item, folder_item= '$new_folder_esc' WHERE upc_item = '$upc_final_esc'";
                        }
                        
                        if ($mysqli->query($update_items_sql)) {
                            if ($mysqli->affected_rows > 0) {
                                $success_count++;
                                if (isset($_GET['debug']) || isset($_POST['debug'])) {
                                    error_log("SUCCESS - Updated inventory_item in items table for UPC: $upc_final");
                                }
                            } else {
                                $errors[] = "Reporte ID $report_id: Actualizado en daily_report pero no se encontró en tabla items (UPC: $upc_final).";
                                $error_count++;
                            }
                        } else {
                            $errors[] = "Reporte ID $report_id: Error actualizando inventory_item en tabla items - " . $mysqli->error;
                            $error_count++;
                        }
                        
                                        // 4. If the user provided an edited quantity and it's different from the daily_report quantity, update inventory table
                                        $edited_qty = isset($edited_quantities[$report_id]) ? trim($edited_quantities[$report_id]) : '';
                                        if ($edited_qty !== '') {
                                            // Need to obtain sku for the UPC/report to correctly identify inventory row
                                            $get_sku_sql = "SELECT sku_report, quantity_report FROM daily_report WHERE id_report = ?";
                                            $get_sku_stmt = $mysqli->prepare($get_sku_sql);
                                            if ($get_sku_stmt) {
                                                $get_sku_stmt->bind_param('i', $report_id);
                                                if ($get_sku_stmt->execute()) {
                                                    $res = $get_sku_stmt->get_result();
                                                    if ($row = $res->fetch_assoc()) {
                                                        $sku_report = $row['sku_report'];
                                                        $orig_qty = isset($row['quantity_report']) ? $row['quantity_report'] : '';
                                                        if ((string)$edited_qty !== (string)$orig_qty) {
                                                            $upc_inv_esc = $mysqli->real_escape_string($upc_final);
                                                            // Determine SKU to use for inventory update: prefer sku_report, else try to lookup sku_item in items table
                                                            $sku_to_use = trim((string)$sku_report);
                                                            if ($sku_to_use === '') {
                                                                $lookup_stmt = $mysqli->prepare("SELECT sku_item FROM items WHERE upc_item = ? LIMIT 1");
                                                                if ($lookup_stmt) {
                                                                    $lookup_stmt->bind_param('s', $upc_final);
                                                                    if ($lookup_stmt->execute()) {
                                                                        $lookup_res = $lookup_stmt->get_result();
                                                                        if ($lookup_row = $lookup_res->fetch_assoc()) {
                                                                            $sku_to_use = isset($lookup_row['sku_item']) ? $lookup_row['sku_item'] : '';
                                                                        }
                                                                    }
                                                                    $lookup_stmt->close();
                                                                }
                                                            }
                                                            $sku_inv_esc = $mysqli->real_escape_string($sku_to_use);
                                                            $edited_qty_esc = $mysqli->real_escape_string($edited_qty);
                                                            $update_inventory_sql = "UPDATE inventory SET quantity_inventory = '$edited_qty_esc' WHERE upc_inventory = '$upc_inv_esc' AND sku_inventory = '$sku_inv_esc'";
                                                            if ($mysqli->query($update_inventory_sql)) {
                                                                if ($mysqli->affected_rows > 0) {
                                                                    if (isset($_GET['debug']) || isset($_POST['debug'])) {
                                                                        error_log("SUCCESS - Updated inventory quantity for UPC: $upc_final, SKU: $sku_report to $edited_qty");
                                                                    }
                                                                } else {
                                                                    // inventory row not found - note as warning but don't fail whole transaction
                                                                    $errors[] = "Reporte ID $report_id: Inventory row not found for UPC: $upc_final, SKU: $sku_report.";
                                                                    $error_count++;
                                                                }
                                                            } else {
                                                                $errors[] = "Reporte ID $report_id: Error updating inventory - " . $mysqli->error;
                                                                $error_count++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $get_sku_stmt->close();
                                            }
                                        }
                    } else {
                        $errors[] = "Reporte ID $report_id: No se pudo obtener UPC final del reporte.";
                        $error_count++;
                    }
                } else {
                    $errors[] = "Reporte ID $report_id: Error ejecutando consulta para obtener UPC final - " . $get_upc_stmt->error;
                    $error_count++;
                }
                $get_upc_stmt->close();
            } else {
                $errors[] = "Reporte ID $report_id: Error preparando consulta para obtener UPC final - " . $mysqli->error;
                $error_count++;
            }
        } else {
            $errors[] = "Reporte ID $report_id: No se pudo actualizar (posiblemente no existe o no está procesado).";
            $error_count++;
        }
    // no statement to close here (using flat query)
    }
    
    // Si hubo errores pero también éxitos, solo mostrar errores como advertencia
    if ($error_count > 0 && $success_count == 0) {
        $mysqli->rollback();
        $_SESSION['error_message'] = "No se pudo actualizar ningún reporte. Errores: " . implode("; ", $errors);
    } else {
        // Confirmar transacción
        $mysqli->commit();
        
        // Preparar mensaje de éxito
        $message_parts = [];
        if ($success_count > 0) {
            $message_parts[] = "$success_count reporte(s) actualizado(s) exitosamente.";
        }
        if ($error_count > 0) {
            $message_parts[] = "$error_count reporte(s) con errores: " . implode("; ", $errors);
        }
        
        if ($success_count > 0) {
            $_SESSION['success_message'] = implode(" ", $message_parts);
        } else {
            $_SESSION['error_message'] = implode(" ", $message_parts);
        }
    }
    
} catch (Exception $e) {
    // Rollback en caso de excepción
    $mysqli->rollback();
    $_SESSION['error_message'] = "Error durante la actualización: " . $e->getMessage();
} finally {
    // Restaurar autocommit
    $mysqli->autocommit(TRUE);
}

                
// Redireccionar de vuelta al formulario
header("Location: editLocationFolder.php");
exit();
?>
