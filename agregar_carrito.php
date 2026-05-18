<?php
session_start();
include("config/conexion.php");

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$userId = $_SESSION['user']['id_usuario'];

$stmt = $conn->prepare("SELECT * FROM celular WHERE id_celular=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();

if (!$producto) {
    header("Location: tienda.php");
    exit();
}

if ($producto['stock'] <= 0) {
    $_SESSION['error_stock'] = "❌ Producto agotado";
    header("Location: tienda.php");
    exit();
}

$stmt = $conn->prepare("SELECT cantidad FROM carrito WHERE id_usuario=? AND id_celular=?");
$stmt->bind_param("ii", $userId, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cantidadActual = intval($row['cantidad']);

    if ($cantidadActual + 1 > $producto['stock']) {
        $_SESSION['error_stock'] = "⚠ No hay suficiente stock para {$producto['marca']} {$producto['modelo']}";
    } else {
        $stmt = $conn->prepare("UPDATE carrito SET cantidad = cantidad + 1 WHERE id_usuario=? AND id_celular=?");
        $stmt->bind_param("ii", $userId, $id);
        $stmt->execute();
    }
} else {
    $stmt = $conn->prepare("INSERT INTO carrito(id_usuario, id_celular, cantidad) VALUES(?,?,1)");
    $stmt->bind_param("ii", $userId, $id);
    $stmt->execute();
}

header("Location: carrito.php");
exit();
?>