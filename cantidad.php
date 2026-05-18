<?php
include("config/conexion.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';
$userId = $_SESSION['user']['id_usuario'];

$stmt = $conn->prepare("SELECT c.stock, car.cantidad FROM carrito car JOIN celular c ON car.id_celular = c.id_celular WHERE car.id_usuario = ? AND car.id_celular = ?");
$stmt->bind_param("ii", $userId, $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    header("Location: carrito.php");
    exit();
}

$row = $res->fetch_assoc();
$stock = intval($row['stock']);
$cantidad = intval($row['cantidad']);

if ($accion == "eliminar") {
    $stmt = $conn->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_celular = ?");
    $stmt->bind_param("ii", $userId, $id);
    $stmt->execute();
    header("Location: carrito.php");
    exit();
}

if ($accion == "sumar") {
    if ($cantidad < $stock) {
        $stmt = $conn->prepare("UPDATE carrito SET cantidad = cantidad + 1 WHERE id_usuario = ? AND id_celular = ?");
        $stmt->bind_param("ii", $userId, $id);
        $stmt->execute();
    } else {
        $_SESSION['error_stock'] = "⚠ No hay más stock disponible";
    }
}

if ($accion == "restar") {
    if ($cantidad > 1) {
        $stmt = $conn->prepare("UPDATE carrito SET cantidad = cantidad - 1 WHERE id_usuario = ? AND id_celular = ?");
        $stmt->bind_param("ii", $userId, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_celular = ?");
        $stmt->bind_param("ii", $userId, $id);
        $stmt->execute();
    }
}

header("Location: carrito.php");
exit();
?>