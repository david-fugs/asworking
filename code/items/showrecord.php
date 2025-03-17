<?php
    session_start();
    
    if(!isset($_SESSION['id'])){
        header("Location: ../../index.php");
    }
    
    $usuario      = $_SESSION['usuario'];
    $nombre       = $_SESSION['nombre'];
    $tipo_usuario = $_SESSION['tipo_usuario'];
   
?>

<!DOCTYPE html>
<html lang="es">
    <head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ASWWORKING</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">

		<style>
        	.responsive {
           		max-width: 100%;
            	height: auto;
        	}

        	.selector-for-some-widget {
  				box-sizing: content-box;
			}

			.principal {
                padding: 20px; /* Añadido para mejorar el espaciado */
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th, td {
                padding: 15px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            @media screen and (max-width: 600px) {
                th, td {
                    padding: 8px;
                }
            }
    	</style>
    </head>
    <body>

    	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>

		<center>
	    	<img src='../../img/logo.png' width=300 height=174 class="responsive">
		</center>

		<section class="principal">

			<div style="border-radius: 9px 9px 9px 9px; -moz-border-radius: 9px 9px 9px 9px; -webkit-border-radius: 9px 9px 9px 9px; border: 4px solid #FFFFFF;" align="center">

				<div align="center">
					<h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em"><b><i class="fa-solid fa-shop"></i> SHOW RECORD</b></h1>
				</div>

    			<div style="border-radius: 9px 9px 9px 9px; -moz-border-radius: 9px 9px 9px 9px; -webkit-border-radius: 9px 9px 9px 9px; border: 1px solid #efd47d; width: 500px; height: 30px; background:#FAFAFA; display:table-cell; vertical-align:middle;">

					<label for="buscar">Search</label>

	    			<form action="showrecord.php" method="get">
	    				<input name="upc_sku_item" type="text"  placeholder="UPC/SKU" size=30>
	    				<input name="item_item" type="text"  placeholder="ITEM" size=20>
	    				<input name="brand_item" type="text"  placeholder="BRAND" size=20>
	    				<input name="ref_item" type="text"  placeholder="REF" size=20>
	    				<input name="color_item" type="text"  placeholder="COLOR" size=20>
	    				<input name="size_item" type="text"  placeholder="SIZE" size=20>
						<input value="Search Item" type="submit">
					</form>
					
	    		</div>

<?php

	date_default_timezone_set("America/Bogota");
	include("../../conexion.php");
	require_once("../../zebra.php");

	@$upc_sku_item 	= ($_GET['upc_sku_item']);
	@$item_item 	= ($_GET['item_item']);
	@$brand_item 	= ($_GET['brand_item']);
	@$ref_item 		= ($_GET['ref_item']);
	@$color_item 	= ($_GET['color_item']);
	@$size_item 	= ($_GET['size_item']);

	$query = "SELECT * FROM items 
          INNER JOIN record ON items.upc_sku_item = record.upc_sku_item 
          WHERE (items.upc_sku_item LIKE '%$upc_sku_item%') 
          AND (items.item_item LIKE '%$item_item%') 
          AND (items.brand_item LIKE '%$brand_item%') 
          AND (items.ref_item LIKE '%$ref_item%') 
          AND (items.color_item LIKE '%$color_item%') 
          AND (items.size_item LIKE '%$size_item%') 
          ORDER BY record.date_rec ASC";
	$res = $mysqli->query($query);
	$num_registros = mysqli_num_rows($res);
	$resul_x_pagina = 1000;

	echo "<section class='content'>
			<div class='card-body'>
        		<div class='table-responsive'>
		        	<table>
		            	<thead>
		                	<tr>
								<th>No.</th>
								<th>UPC</th>
								<th>SKU</th>
								<th>DATE</th>
								<th>BRAND</th>
								<th>ITEM</th>
								<th>REF</th>
								<th>COLOR</th>
								<th>SIZE</th>
				        		<th>CATEGORY</th>
				        		<th>COST</th>
				        		<th>WEIGHT</th>
				        		<th>INVENTORY</th>
				        		<th>EDIT</th>
				    		</tr>
				  		</thead>
            			<tbody>";

	$paginacion = new Zebra_Pagination();
	$paginacion->records($num_registros);
	$paginacion->records_per_page($resul_x_pagina);

	$consulta = "SELECT * FROM items 
          INNER JOIN record ON items.upc_sku_item = record.upc_sku_item 
          WHERE (items.upc_sku_item LIKE '%$upc_sku_item%') 
          AND (items.item_item LIKE '%$item_item%') 
          AND (items.brand_item LIKE '%$brand_item%') 
          AND (items.ref_item LIKE '%$ref_item%') 
          AND (items.color_item LIKE '%$color_item%') 
          AND (items.size_item LIKE '%$size_item%') 
          ORDER BY record.date_rec ASC LIMIT " .(($paginacion->get_page() - 1) * $resul_x_pagina). "," .$resul_x_pagina;
	$result = $mysqli->query($consulta);

	$i = 1;
	while($row = mysqli_fetch_array($result))
	{

		echo '
				<tr>
					<td data-label="No." style="' . $color . '">'.($i + (($paginacion->get_page() - 1) * $resul_x_pagina)).'</td>
					<td data-label="UPC">'.$row['upc_sku_item'].'</td>
					<td data-label="SKU">'.$row['id_rec'].'</td>
					<td data-label="DATE">'.$row['date_rec'].'</td>
					<td data-label="BRAND">'.$row['brand_item'].'</td>
					<td data-label="ITEM">'.$row['item_item'].'</td>
					<td data-label="REF">'.$row['ref_item'].'</td>
					<td data-label="COLOR">'.$row['color_item'].'</td>
					<td data-label="SIZE">'.$row['size_item'].'</td>
					<td data-label="CATEGORY">'.$row['category_item'].'</td>
					<td data-label="COST">'.$row['cost_rec'].'</td>
					<td data-label="WEIGHT">'.$row['weight_item'].'</td>
					<td data-label="INVENTORY">'.$row['inventory_rec'].'</td>
					<td data-label="EDITAR"><a href="edititems.php?upc_sku_item='.$row['upc_sku_item'].'"><img src="../../img/editar.png" width=28 height=28></a></td>

				</tr>';
		$i++;
	}
 
	echo '</table>
		</div>

		';

	$paginacion->render();

?>
			<div class="share-container">
	            <!-- Go to www.addthis.com/dashboard to customize your tools -->
	            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
	            <!-- Go to www.addthis.com/dashboard to customize your tools -->
	            <div class="addthis_sharing_toolbox"></div>
	        </div>
			<center>
			<br/><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
			</center>

			</div>
		</div>
		</section>
		<script src="js/app.js"></script>
		<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>

	</body>
</html>