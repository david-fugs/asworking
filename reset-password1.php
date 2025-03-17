<?php
	session_start();
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
    }
    
    $id =   $_SESSION['id'];
    $nombre = $_SESSION['nombre'];
    $tipo_usuario = $_SESSION['tipo_usuario'];

	include("conexion.php");

	if($_SERVER['REQUEST_METHOD']== 'POST')
	{
		if($_POST['nuevopassword'] === $_POST['confirmapassword'])
		{
			$password = mysqli_real_escape_string($mysqli,$_POST['nuevopassword']);
			$password_encrypt = sha1($password);

			$sql = "UPDATE usuarios SET password='$password_encrypt' WHERE id='$id'";
			$result = $mysqli->query($sql);

			if($result > 0)
			{
				echo "<script>
						alert('LA CONTRASEÑA FUE ACTUALIZADA DE FORMA CORRECTA');
						window.location = 'access.php';
					</script>";
				exit();
			}
		}else{
			echo "<script>
						alert('LAS CONTRASEÑAS NO COINCIDEN, POR FAVOR VERIFIQUE');
						window.location = 'access.php';
					</script>";
			exit();
	
		}
	}
?>