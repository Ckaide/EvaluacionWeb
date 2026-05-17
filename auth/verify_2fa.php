<?php
include("../config/conexion.php");
include("two_factor.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$info = $_SESSION['flash_2fa'] ?? "";
unset($_SESSION['flash_2fa']);

$pendingEmail = $_SESSION['pending_2fa_email'] ?? null;
$pendingUserId = $_SESSION['pending_2fa_user_id'] ?? null;

if (!$pendingEmail && !$pendingUserId) {
    header("Location: login.php");
    exit();
}

function fetchPendingUser($conn, $email, $userId) {
    if ($userId) {
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
    } else {
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $email);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0 ? $res->fetch_assoc() : null;
}

$user = fetchPendingUser($conn, $pendingEmail, $pendingUserId);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_GET['resend'])) {
    $code = generateTotpCode($user['two_factor_secret']);
    $subject = "Código 2FA Tienda Celulares";
    $message = "Hola {$user['nombre']},\n\nTu código de seguridad 2FA es: {$code}\n\nNo compartas este código con nadie.\n\nGracias,\nTienda Celulares";

    if (send2faMail($user['correo'], $user['nombre'], $subject, $message)) {
        $info = "Se ha reenviado un nuevo código al correo registrado.";
    } else {
        $error = "No se pudo reenviar el código. Verifica la configuración de correo en config/config.php.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    if ($code === '') {
        $error = "Ingresa el código 2FA enviado a tu correo.";
    } elseif (!verifyTotpCode($user['two_factor_secret'], $code)) {
        $error = "Código incorrecto o vencido. Intenta de nuevo.";
    } else {
        $_SESSION['user'] = $user;
        unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_email']);

        if ($user['rol'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../tienda.php");
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Verificar 2FA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
}
.card {
    width: 420px;
    border: none;
    border-radius: 24px;
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.35);
    background: rgba(11, 22, 36, 0.94);
}
.form-control {
    border-radius: 12px;
}
.btn {
    border-radius: 12px;
}
.small-note {
    color: #cbd5e1;
}
</style>
</head>
<body>
<div class="card p-4">
    <h3 class="text-center mb-3">Verificación 2FA</h3>
    <p class="small-note text-center mb-4">Se ha enviado un código al correo <strong><?= htmlspecialchars($user['correo']) ?></strong>.</p>

    <?php if ($info): ?>
        <div class="alert alert-success"><?= htmlspecialchars($info) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="code" class="form-control mb-3" placeholder="Código 2FA" maxlength="6" required>
        <button class="btn btn-primary w-100">Validar código</button>
    </form>

    <div class="text-center mt-3">
        <a href="verify_2fa.php?resend=1" class="text-info">Reenviar código</a>
    </div>
</div>
</body>
</html>
