<?php
require "conexion.php";
session_start();

if($_POST){
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    
    $sql = "SELECT id, password, nombre, tipo_usuario FROM usuarios WHERE usuario='$usuario'";
    $resultado = $mysqli->query($sql);
    $num = $resultado->num_rows;
    
    if($num>0) {
        $row = $resultado->fetch_assoc();
        $password_bd = $row['password'];
        $pass_c = sha1($password);
          if($password_bd == $pass_c){
            $_SESSION['id'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
            header("Location: access.php");
        } else {
            $error = "Password does not match";
        }
    } else {
        $error = "User does not exist";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASWWORKING | LOGIN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-dark: #4a2568;
            --primary: #632b8b;
            --primary-light: #5d337a;
            --secondary: #997cab;
            --secondary-light: #dac7e5;
            --text-dark: #2d2d2d;
            --text-light: #f8f9fa;
            --bg-light: #f5f3f7;
            --success: #28a745;
            --danger: #dc3545;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(rgba(99, 43, 139, 0.1), rgba(99, 43, 139, 0.1)), url('img/index.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .login-container {
            max-width: 1000px;
            margin: auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-image {
            background: url('img/logo_register.png') no-repeat center center;
            background-size: contain;
            height: 100%;
            min-height: 400px;
        }

        .login-form {
            padding: 3rem;
        }

        .logo-login {
            width: 180px;
            margin-bottom: 2rem;
            display: block;
        }

        .login-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            text-align: center;
        }

        .login-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--secondary-light));
            border-radius: 3px;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid var(--secondary-light);
            padding-left: 45px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
        }

        .btn-login {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(99, 43, 139, 0.3);
        }

        .register-link {
            color: var(--primary);
            text-align: center;
            display: block;
            margin-top: 1.5rem;
            transition: all 0.3s;
        }

        .register-link:hover {
            color: var(--primary-dark);
            text-decoration: none;
        }

        .error-message {
            color: var(--danger);
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 90%;
            }
            
            .login-image {
                min-height: 200px;
            }
            
            .login-form {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container login-container d-flex">        <div class="row w-100">
            <!-- Image column -->
            <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-5 login-image">
                <!-- Background image applied via CSS -->
            </div>
            
            <!-- Form column -->
            <div class="col-md-6 p-0">
                <div class="login-form">
                    <img src="img/logo.png" class="logo-login mx-auto" alt="ASWWORKING Logo">
                    <h2 class="login-title">Welcome</h2>
                    
                    <?php if(isset($error)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">                        <div class="form-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" class="form-control" name="usuario" placeholder="Username" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                          <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                        
                        <a href="register.php" class="register-link">
                            <i class="fas fa-user-plus"></i> Create Account
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if(isset($error)): ?>
    <script>        $(document).ready(function() {
            Swal.fire({
                icon: 'error',
                title: 'Access Error',
                text: '<?php echo $error; ?>',
                confirmButtonColor: 'var(--danger)'
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>