<?php
    
    session_start();
    
    if(!isset($_SESSION['id'])){
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
		<style>
        	.responsive {
           		max-width: 100%;
            	height: auto;
        	}
    	</style>
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
			<h1><b><i class="fa-solid fa-barcode"></i> ITEMS RECORD</b></h1>
			<p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
	        
	        <div class="row">
	        	<div class="col-md-12">
                    <form id="form_contacto" action='addrecord1.php' method="POST">
	                	<div class="form-group">
                			<div class="row">
                    			<div class="col-12 col-sm-2">
	                   	        	<label for="date_rec">* DATE:</label>
									<input type='date' name='date_rec' class='form-control' id="date_rec" required autofocus />
	                        	</div>
	                        	<div class="col-12 col-sm-5">
		                            <label for="upc_sku_item">* UPC/SKU:</label>
									<input type='text' name='upc_sku_item' id="upc_sku_item" class='form-control' style="text-transform:uppercase;" required />
	                        	</div>
	                        	<!--<div class="col-12 col-sm-5">
	                        		<label for="upc_sku_item">* UPC/SKU:</label>
			                        <select id='upc_sku_item' name='upc_sku_item' class='form-control' required />
							         	<option value=''></option>
							            	<?php
							              		//header('Content-Type: text/html;charset=utf-8');
							              		//$consulta='SELECT * FROM items';
							              		//$res = mysqli_query($mysqli,$consulta);
						              			//$num_reg = mysqli_num_rows($res);
							              		//while($row = $res->fetch_array())
							              		{
						            			?>
							            			<option value='<?php //echo $row['upc_sku_item']; ?>'>
							            			<?php //echo $row['upc_sku_item'].' '.$row['item_item']; ?>
							            			</option>
							            		<?php
							              		}
						            		?>   
			    					</select>
		                        </div>-->
	                        	<div class="col-12 col-sm-2">
		                            <label for="cost_rec">* COST:</label>
									<input type='number' name='cost_rec' class='form-control' id="cost_rec" step="0.01" required />
	                        	</div>
	                        	<div class="col-12 col-sm-3">
		                            <label for="inventory_rec">* INVENTORY:</label>
									<input type='text' name='inventory_rec' class='form-control' style="text-transform:uppercase;" id="inventory_rec" required />
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
   
	</body>
</html>