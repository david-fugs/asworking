<?php

session_start();

if (!isset($_SESSION['id'])) {
	header("Location: index.php");
}

$usuario      = $_SESSION['usuario'];
$nombre       = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];
header("Content-Type: text/html;charset=utf-8");

?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ASWWORKING</title>
	<script type="text/javascript" src="../../js/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link href="../../fontawesome/css/all.css" rel="stylesheet">
	<script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<style>
		.responsive {
			max-width: 100%;
			height: auto;
		}

		.mensaje-error {
			padding: 10px;
			border-radius: 5px;
			margin-bottom: 10px;
			display: none;
		}
	</style>
	<!--SCRIPT PARA VALIDAR SI EL REGISTRO YA ESTÁ EN LA BD-->
	<script type="text/javascript">
		$(document).ready(function() {
			$('#upc_sku_item').on('blur', function() {
				$('#result-upc_sku_item').html('<img src="../../img/loader.gif" />').fadeOut(1000);
				var upc_sku_item = $(this).val();
				var dataString = 'upc_sku_item=' + upc_sku_item;

				$.ajax({
					type: "POST",
					url: "chkitems.php",
					data: dataString,
					success: function(data) {
						$('#result-upc_sku_item').fadeIn(1000).html(data);
					}
				});
			});
		});
	</script>
</head>

<body>

	<center>
		<img src='../../img/logo.png' width=300 height=174 class="responsive">
	</center>

	<?php

	date_default_timezone_set("America/Bogota");
	include("../../conexion.php");
	require_once("../../zebra.php");

	?>

	<div class="container pt-2">
		<h1><b><i class="fa-solid fa-dolly"></i> ITEMS</b></h1>
		<p><i><b>
					<font size=3 color=#c68615>*Datos obligatorios</i></b></font>
		</p>

		<div class="row">
			<div class="col-md-12">
				<form id="form_contacto" action='additems1.php' method="POST">
					<div class="row">
						<div class="col">
							<div id="result-upc_sku_item"></div>
						</div>
					</div>
					<div id="mensaje-upc" class="mensaje-error mb-2"></div>
					<div class="form-group">
						<div class="row">
							<div class="col-12 col-sm-3">
								<label for="date_item">* DATE:</label>
								<input type='date' name='date_item' class='form-control' id="date_item" required autofocus />
							</div>
							<div class="col-12 col-sm-3">
								<label for="upc_item">* UPC:</label>
								<input type='text' name='upc_item' id="upc_item" class='form-control' style="text-transform:uppercase;" required />
							</div>
							<div class="col-12 col-sm-3">
								<label for="sku_item">* SKU:</label>
								<input type='text' name='sku_item' id="sku_item" class='form-control' style="text-transform:uppercase;" />
							</div>

							<div class="col-12 col-sm-3">
								<label for="brand_item">* BRAND:</label>
								<input type='text' name='brand_item' class='form-control' style="text-transform:uppercase;" id="brand_item" required />
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-12 col-sm-4">
								<label for="item_item">* ITEM:</label>
								<input type='text' name='item_item' class='form-control' style="text-transform:uppercase;" id="item_item" required />
							</div>

							<div class="col-12 col-sm-4">
								<label for="ref_item">* REF:</label>
								<input type='text' name='ref_item' class='form-control' style="text-transform:uppercase;" id="ref_item" required />
							</div>
							<div class="col-12 col-sm-4">
								<label for="color_item">* COLOR:</label>
								<input type='text' name='color_item' class='form-control' style="text-transform:uppercase;" id="color_item" required />
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-12 col-sm-3">
								<label for="size_item">* SIZE:</label>
								<input type='text' name='size_item' class='form-control' style="text-transform:uppercase;" id="size_item" required />
							</div>
							<div class="col-12 col-sm-3">
								<label for="category_item">* CATEGORY:</label>
								<input type='text' name='category_item' class='form-control' style="text-transform:uppercase;" id="category_item" required />
							</div>
							<div class="col-12 col-sm-3">
								<label for="cost_item">* COST:</label>
								<input type='number' name='cost_item' class='form-control' id="cost_item" step="0.01" required />
							</div>

							<div class="col-12 col-sm-3">
								<label for="weight_item">* WEIGHT:</label>
								<input type='text' name='weight_item' class='form-control' style="text-transform:uppercase;" id="weight_item" required />
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-12 col-sm-3">
								<label for="inventory_item">* INVENTORY:</label>
								<input type='text' name='inventory_item' class='form-control' style="text-transform:uppercase;" id="inventory_item" required />
							</div>
							<div class="col-12 col-sm-3">
								<label for="quantity_inventory">* QUANTITY:</label>
								<input type='text' name='quantity_inventory' class='form-control' style="text-transform:uppercase;" id="quantity_inventory" required />
							</div>
						</div>
					</div>


					<button type="submit" class="btn btn-primary">
						<span class="spinner-border spinner-border-sm"></span>
						ADD ITEM
					</button>
					<button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> BACK
					</button>
				</form>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

	<script>
		$(document).ready(function() {
			$('#upc_item').on('blur', function() {
				var upc = $(this).val().toUpperCase();

				if (upc.trim() !== '') {
					$.ajax({
						url: 'verificar_upc.php',
						type: 'POST',
						data: {
							upc_item: upc
						},
						success: function(respuesta) {
							var data = JSON.parse(respuesta);

							// Si se encontró el UPC
							if (data.status === 'existe') {
								var itemsMessage = '';

								// Si hay múltiples coincidencias, mostramos todas
								data.items.forEach(function(item) {
									itemsMessage += 'Brand: ' + item.brand_item + ', Item: ' + item.item_item + '\n';

									// Llenar los campos con los primeros valores encontrados
									$('#brand_item').val(item.brand_item);
									$('#item_item').val(item.item_item);
								});

								// Usamos SweetAlert para mostrar las coincidencias
								Swal.fire({
									title: 'UPC already exists!',
									text: itemsMessage,
									icon: 'warning',
									confirmButtonText: 'Ok'
								});

								// Cambiar el atributo de mensaje-error de display none a display
								$('#mensaje-upc').show();
								$('#mensaje-upc').text('This UPC already exists in the database.').css('color', 'red');
								$('#mensaje-upc').addClass('alert alert-danger');
							} else {
								// Cambiar el color de mensaje-upc a verde
								$('#mensaje-upc').removeClass('alert alert-danger');
								$('#mensaje-upc').addClass('alert alert-success');
								$('#mensaje-upc').text('This UPC is available.').css('color', 'green');
							}
						}
					});
				}
			});
		});
	</script>
</body>

</html>