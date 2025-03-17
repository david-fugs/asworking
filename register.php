<?php
	date_default_timezone_set("America/Bogota");
	session_start();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>ASWWORKING</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/popper.min.j"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <link href="fontawesome/css/all.css" rel="stylesheet"> <!--load all styles -->
		<style>
        	.responsive {
           		max-width: 100%;
            	height: auto;
        	}
    	</style>
	</head>
    <body>
  
		<center>
	    	<img src="img/logo_register.png" width="400" height="309" class="responsive">
		</center>

<?php
	require('conexion.php');
    // If form submitted, insert values into the database.
    if (isset($_REQUEST['usuario'])){
		$usuario = stripslashes($_REQUEST['usuario']); // removes backslashes
		$usuario = mysqli_real_escape_string($mysqli,$usuario); //escapes special characters in a string
		$password = stripslashes($_REQUEST['password']);
		$password = mysqli_real_escape_string($mysqli,$password);
		$nombre = stripslashes($_REQUEST['nombre']);
		$tipo_usuario = 3;
		
        $query = "INSERT INTO `usuarios` (usuario, password, tipo_usuario, nombre) VALUES ('$usuario', '".sha1($password)."', '$tipo_usuario', '$nombre')";
        $result = mysqli_query($mysqli,$query);
        if($result){
            echo "<center><p style='border-radius: 20px;box-shadow: 10px 10px 5px #c68615; font-size: 23px; font-weight: bold;' >REGISTRO CREADO SATISFACTORIAMENTE<br><br></p></center>
				<div class='form' align='center'><h3>Regresar para iniciar la sesión... <br/><br/><center><a href='index.php'>Regresar</a></center></h3></div>";
        }
    }else{
?>
		
		<div class="container">
			<h1><b><i class="fas fa-users"></i> REGISTRATION OF A NEW USER</b></h1>
			<p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
			<form action='' method="POST">
				
				<div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-4">
							<label for="nombre">* USER NAME:</label>
							<input type='text' name='nombre' class='form-control' id="nombre" required autofocus style="text-transform:uppercase;" />
						</div>
						<div class="col-12 col-sm-4">
							<label for="usuario">* USER:</label>
							<input type='text' name='usuario' id="usuario" class='form-control' required />
						</div>
						<div class="col-12 col-sm-4">
							<label for="password">* PASSWORD:</label>
							<input type='password' name='password' id="password" class='form-control' required style="text-transform:uppercase;" />
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-outline-warning">
					<span class="spinner-border spinner-border-sm"></span>
					ADD USER
				</button>
				<button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='img/atras.png' width=27 height=27> BACK
				</button>
			</form>
		</div>

	</body>
</html>


<?php } ?>
