<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("config/conexion.php");

$id = $_GET['id'] ?? 0;
$accion = $_GET['accion'] ?? '';

// VALIDAR EXISTE EN CARRITO
if(!isset($_SESSION['carrito'][$id])){

    header("Location: carrito.php");
    exit();
}

// =========================
// 🗑 ELIMINAR DIRECTO
// =========================
if($accion == "eliminar"){

    unset($_SESSION['carrito'][$id]);

    header("Location: carrito.php");
    exit();
}

// =========================
// OBTENER STOCK
// =========================
$stmt = $conn->prepare("
SELECT stock
FROM celular
WHERE id_celular=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows == 0){

    header("Location: carrito.php");
    exit();
}

$producto = $res->fetch_assoc();

$stock = $producto['stock'];

// =========================
// ➕ SUMAR
// =========================
if($accion == "sumar"){

    if($_SESSION['carrito'][$id]['cantidad'] < $stock){

        $_SESSION['carrito'][$id]['cantidad']++;

    }else{

        $_SESSION['error_stock'] =
        "⚠ No hay más stock disponible";
    }
}

// =========================
// ➖ RESTAR
// =========================
elseif($accion == "restar"){

    if($_SESSION['carrito'][$id]['cantidad'] > 1){

        $_SESSION['carrito'][$id]['cantidad']--;

    }else{

        unset($_SESSION['carrito'][$id]);
    }
}

header("Location: carrito.php");
exit();
?>