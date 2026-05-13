<?php
include("../config/conexion.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo=? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $res = $stmt->get_result();

    if ($res->num_rows > 0) {

        $user = $res->fetch_assoc();

        if (password_verify($password, $user['contraseña'])) {

            $_SESSION['user'] = $user;

            // 🔒 REDIRECCIÓN SEGÚN ROL
            if ($user['rol'] == 'admin') {

                header("Location: ../admin/dashboard.php");

            } else {

                header("Location: ../tienda.php");

            }

            exit();

        } else {

            $error = "❌ Contraseña incorrecta";

        }

    } else {

        $error = "❌ Usuario no encontrado";

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card{
    width:400px;
    border:none;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.4);
}

.form-control{
    border-radius:10px;
}

.btn{
    border-radius:10px;
}

</style>

</head>

<body>

<div class="card login-card p-4">

<h2 class="text-center mb-4">
🔐 Iniciar Sesión
</h2>

<?php if($error != "") { ?>

<div class="alert alert-danger">
<?= $error ?>
</div>

<?php } ?>

<form method="POST">

<input 
type="email" 
name="correo" 
class="form-control mb-3" 
placeholder="Correo"
required
>

<input 
type="password" 
name="password" 
class="form-control mb-3" 
placeholder="Contraseña"
required
>

<button class="btn btn-primary w-100">
Entrar
</button>

</form>

<div class="text-center mt-3">

<a href="registro.php">
Crear cuenta
</a>

</div>

</div>

</body>
</html>