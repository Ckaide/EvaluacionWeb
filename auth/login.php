<?php
include("../config/conexion.php");
include("two_factor.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$info = $_SESSION['flash_2fa'] ?? "";
unset($_SESSION['flash_2fa']);

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

            if (isset($user['banned']) && $user['banned'] == 1) {
                $error = "❌ Usuario baneado. Contacta con el administrador.";
            } else {
                $secret = $user['two_factor_secret'] ?? '';
                if (empty($secret)) {
                    $error = "Este usuario no tiene configurado 2FA. Contacta al administrador.";
                } else {
                    $code = generateTotpCode($secret);
                    $subject = "Código 2FA Tienda Celulares";
                    $message = "Hola {$user['nombre']},\n\nTu código de seguridad 2FA es: {$code}\n\nNo compartas este código con nadie.\n\nGracias,\nTienda Celulares";
                    $headers = "From: no-reply@tiendacelulares.local\r\nReply-To: no-reply@tiendacelulares.local\r\nContent-Type: text/plain; charset=UTF-8\r\n";

                    if (!send2faMail($correo, $user['nombre'] ?? '', $subject, $message)) {
                        $error = "No se pudo enviar el código 2FA. Verifica la configuración de correo en config/config.php.";
                    } else {
                        $_SESSION['pending_2fa_user_id'] = $user['id'] ?? null;
                        $_SESSION['pending_2fa_email'] = $user['correo'];
                        $_SESSION['pending_2fa_role'] = $user['rol'];
                        $_SESSION['flash_2fa'] = "Te hemos enviado un código al correo registrado.";
                        header("Location: verify_2fa.php");
                        exit();
                    }
                }
            }

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

<?php if ($info != "") { ?>
<div class="alert alert-success">
<?= $info ?>
</div>
<?php } ?>

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