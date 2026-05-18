<?php
include("config/conexion.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

$userId = $_SESSION['user']['id_usuario'];
$stmt = $conn->prepare("DELETE FROM carrito WHERE id_usuario = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

$_SESSION['mensaje'] = "🗑 Carrito vaciado correctamente";

header("Location: carrito.php");
exit();
?>