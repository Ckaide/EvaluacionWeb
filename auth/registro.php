<?php
include("../config/conexion.php");
include("two_factor.php");

$error = "";

if ($_POST) {
    $nombre = $_POST['nombre'] ?? "";
    $correo = $_POST['correo'] ?? "";
    $p1 = $_POST['password'] ?? "";
    $p2 = $_POST['password2'] ?? "";

    if ($p1 !== $p2) {
        $error = "❌ Las contraseñas no coinciden";
    } elseif (strlen($p1) < 8 || !preg_match('/[A-Z]/', $p1) || !preg_match('/[0-9]/', $p1) || !preg_match('/[!@#$%^&*()_+\-=[\]{};:\"\\|,.<>\/?]/', $p1)) {
        $error = "❌ La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
    } else {
        $hash = password_hash($p1, PASSWORD_DEFAULT);
        $secret = generateBase32Secret(16);

        $stmt = $conn->prepare("INSERT INTO usuario(nombre,correo,contraseña,rol,two_factor_secret) VALUES(?,?,?, 'cliente', ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $hash, $secret);

        try {
            $stmt->execute();
            header("Location: login.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $error = "❌ Este correo ya está registrado. Usa otro correo o inicia sesión.";
            } else {
                $error = "Error al registrar. Intenta de nuevo más tarde.";
            }
        }
    }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<body class="bg-dark d-flex justify-content-center align-items-center vh-100">
<div class="card p-4" style="width:400px">
<h3 class="text-center">📝 Registro</h3>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
<input name="nombre" class="form-control my-2" placeholder="Nombre">
<input name="correo" class="form-control my-2" placeholder="Correo">
<input type="password" name="password" class="form-control my-2" placeholder="Contraseña">
<input type="password" name="password2" class="form-control my-2" placeholder="Repetir contraseña">
<button class="btn btn-primary w-100">Registrar</button>
</form>
</div>
</body>