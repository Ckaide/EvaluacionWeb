<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VACIAR CARRITO
unset($_SESSION['carrito']);

// MENSAJE
$_SESSION['mensaje'] = "🗑 Carrito vaciado correctamente";

header("Location: carrito.php");
exit();
?>