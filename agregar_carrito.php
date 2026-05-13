<?php
session_start();
include("config/conexion.php");

$id = $_GET['id'] ?? 0;

// BUSCAR PRODUCTO
$stmt = $conn->prepare("SELECT * FROM celular WHERE id_celular=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows == 0){
    header("Location: tienda.php");
    exit();
}

$producto = $res->fetch_assoc();

// SI NO EXISTE EL CARRITO
if(!isset($_SESSION['carrito'])){
    $_SESSION['carrito'] = [];
}

// SI YA EXISTE EL PRODUCTO
if(isset($_SESSION['carrito'][$id])){

    $cantidadActual = $_SESSION['carrito'][$id]['cantidad'];

    // VALIDAR STOCK
    if($cantidadActual + 1 > $producto['stock']){

        $_SESSION['error_stock'] =
        "⚠ No hay suficiente stock para {$producto['marca']} {$producto['modelo']}";

        header("Location: tienda.php");
        exit();
    }

    $_SESSION['carrito'][$id]['cantidad']++;

}else{

    // VALIDAR STOCK MÍNIMO
    if($producto['stock'] <= 0){

        $_SESSION['error_stock'] =
        "❌ Producto agotado";

        header("Location: tienda.php");
        exit();
    }

    $_SESSION['carrito'][$id] = [

        'id' => $producto['id_celular'],
        'marca' => $producto['marca'],
        'modelo' => $producto['modelo'],
        'precio' => $producto['precio'],
        'imagenes' => $producto['imagenes'],
        'cantidad' => 1
    ];
}

header("Location: carrito.php");
exit();
?>