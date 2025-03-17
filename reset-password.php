<?php
    session_start();
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
    }
    
    $nombre = $_SESSION['nombre'];
    $tipo_usuario = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>BD SISBEN</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/popper.min.j"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
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
	    	<img src='img/alcaldia.png' width="231" height="185" class="responsive">
	    	<img src="img/sisben.png" width="300" height="185" class="responsive">
		</center>
		<br />
		<div class="container">
			<h1><b><i class="fas fa-key"></i> ACTUALIZACIÓN DE LA CONTRASEÑA</b></h1>
			<p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
			<form action='reset-password1.php' method="POST">

				<div class="form-group">
                    <div class="row">
						<div class="col-12">
		                	<input type='number' name='id' class='form-control' id="id" value='<?php echo $row['id']; ?>' hidden />
		                </div>
		            </div>
		        </div>
				
				<div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
							<label for="nuevopassword">* NUEVA CONTRASEÑA:</label>
							<input type='password' name='nuevopassword' class='form-control' id="nuevopassword" required autofocus />
						</div>
						<div class="col-12 col-sm-6">
							<label for="confirmapassword">* CONFIRMAR CONTRASEÑA:</label>
							<input type='password' name='confirmapassword' id="confirmapassword" class='form-control' required />
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-outline-warning">
					<span class="spinner-border spinner-border-sm"></span>
					ACTUALIZAR CONTRASEÑA
				</button>
				<button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='img/atras.png' width=27 height=27> REGRESAR
				</button>
			</form>
		</div>

	</body>
</html>