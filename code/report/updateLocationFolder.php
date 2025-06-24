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

        // Sin validación - permitir campos vacíos
        // Solo actualizar folder_report y loc_report en daily_report
        $update_sql = "UPDATE daily_report 
                      SET folder_report = ?, 
                          loc_report = ?,
                          estado_reporte = -1,
                          fecha_modificacion = NOW()
                      WHERE id_report = ? 
                      AND estado_reporte = 0";
        
        $stmt = $mysqli->prepare($update_sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $mysqli->error);
        }
        
        $stmt->bind_param("ssi", $new_folder, $new_location, $report_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $success_count++;
                // Log de éxito si debug está habilitado
                if (isset($_GET['debug']) || isset($_POST['debug'])) {
                    error_log("SUCCESS - Updated daily_report for ID: $report_id, folder: '$new_folder', location: '$new_location'");
                }
            } else {
                $errors[] = "Reporte ID $report_id: No se pudo actualizar (posiblemente no existe o no está procesado).";
                $error_count++;
            }
        } else {
            $errors[] = "Reporte ID $report_id: Error en la actualización - " . $stmt->error;
            $error_count++;
        }
        
        $stmt->close();
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
                
                if ($upc_row = $upc_result->fetch_assoc()) {
                    $upc_final = $upc_row['upc_final_report'];
                    
                    // Actualizar la tabla items con los nuevos valores
                    $update_items_sql = "UPDATE items 
                                        SET item_item = ?,
                                            brand_item = ?,
                                            ref_item = ?,
                                            color_item = ?,
                                            size_item = ?,
                                            inventory_item = ?
                                        WHERE upc_item = ?";
                    
                    $items_stmt = $mysqli->prepare($update_items_sql);
                    if ($items_stmt) {
                        $items_stmt->bind_param("sssssss", $new_item, $new_brand, $new_vendor, $new_color, $new_size, $new_location, $upc_final);
                          if ($items_stmt->execute()) {
                            if ($items_stmt->affected_rows > 0) {
                                // Ambas tablas actualizadas exitosamente
                                $success_count++;
                                // Log de éxito si debug está habilitado
                                if (isset($_GET['debug']) || isset($_POST['debug'])) {
                                    error_log("SUCCESS - Updated both daily_report and items table for UPC: $upc_final");
                                }
                            } else {
                                // Item no encontrado en tabla items, pero reporte se actualizó
                                $errors[] = "Reporte ID $report_id: Actualizado en daily_report pero no se encontró en tabla items (UPC: $upc_final).";
                                $error_count++;
                            }
                        } else {
                            $errors[] = "Reporte ID $report_id: Error actualizando tabla items - " . $items_stmt->error;
                            $error_count++;
                        }
                        $items_stmt->close();
                    } else {
                        $errors[] = "Reporte ID $report_id: Error preparando consulta para tabla items - " . $mysqli->error;
                        $error_count++;
                    }
                } else {
                    $errors[] = "Reporte ID $report_id: No se pudo obtener UPC final del reporte.";
                    $error_count++;
                }
                $get_upc_stmt->close();
                
            } else {
                $errors[] = "Reporte ID $report_id: No se pudo actualizar (posiblemente no existe o no está procesado).";
                $error_count++;
            }
        } else {
            $errors[] = "Reporte ID $report_id: Error en la actualización - " . $stmt->error;
            $error_count++;
        }
        
        $stmt->close();
    }
    
    // Si hubo errores, hacer rollback
    if ($error_count > 0 && $success_count == 0) {
        $mysqli->rollback();
        $_SESSION['error_message'] = "No se pudo actualizar ningún reporte. Errores: " . implode("; ", $errors);
    } else {
        // Confirmar transacción
        $mysqli->commit();        // Preparar mensaje de éxito
        $message_parts = [];
        if ($success_count > 0) {
            $message_parts[] = "$success_count reporte(s) actualizado(s) exitosamente en daily_report y tabla items, removido(s) de la lista de edición.";
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
