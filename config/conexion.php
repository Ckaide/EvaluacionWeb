<?php
include(__DIR__ . "/config.php");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "tienda";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // no mostrar detalle al usuario
    die("Error de conexión");
}

$conn->set_charset("utf8mb4");
?>