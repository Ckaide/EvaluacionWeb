<?php
session_start();
include("config/conexion.php");

// LOGIN
if(!isset($_SESSION['user'])){

    header("Location: auth/login.php");
    exit();
}

// CARRITO
$carrito = $_SESSION['carrito'] ?? [];

if(empty($carrito)){

    header("Location: carrito.php");
    exit();
}

$total = 0;

// VALIDAR STOCK
foreach($carrito as $id => $p){

    $stmt = $conn->prepare("
    SELECT stock
    FROM celular
    WHERE id_celular=?
    ");

    $stmt->bind_param("i",$id);
    $stmt->execute();

    $res = $stmt->get_result()->fetch_assoc();

    // STOCK INSUFICIENTE
    if($p['cantidad'] > $res['stock']){

        die("
        <div style='
        font-family:Arial;
        padding:40px;
        background:#1f252d;
        color:white;
        height:100vh;
        '>

        <h1>❌ Stock insuficiente</h1>

        <p>
        {$p['marca']} {$p['modelo']}
        </p>

        <a href='carrito.php'
        style='
        background:#0d6efd;
        color:white;
        padding:10px 20px;
        text-decoration:none;
        border-radius:10px;
        '>
        Volver
        </a>

        </div>
        ");
    }

    $subtotal = $p['precio'] * $p['cantidad'];

    $total += $subtotal;
}

// CREAR VENTA
$fecha = date("Y-m-d H:i:s");

$stmt = $conn->prepare("
INSERT INTO venta(id_usuario,fecha,total)
VALUES(?,?,?)
");

$stmt->bind_param(
"isd",
$_SESSION['user']['id_usuario'],
$fecha,
$total
);

$stmt->execute();

// DESCONTAR STOCK
foreach($carrito as $id => $p){

    $stmt = $conn->prepare("
    UPDATE celular
    SET stock = stock - ?
    WHERE id_celular = ?
    ");

    $stmt->bind_param(
    "ii",
    $p['cantidad'],
    $id
    );

    $stmt->execute();
}

// LIMPIAR CARRITO
unset($_SESSION['carrito']);
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Compra Exitosa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#1f252d;
    color:white;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.card{
    border:none;
    border-radius:20px;
}

</style>

</head>

<body>

<div class="card p-5 shadow text-center">

<h1 class="text-success mb-4">
✅ Compra realizada
</h1>

<h4 class="mb-3">
Total pagado:
</h4>

<h2 class="text-primary">
$<?= number_format($total,2) ?>
</h2>

<a href="tienda.php" class="btn btn-success mt-4">
🛒 Volver a la tienda
</a>

</div>

</body>
</html>