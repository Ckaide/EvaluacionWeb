<?php include("navbar.php"); ?>
<?php
include("../includes/proteger.php");
include("../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

$res = $conn->query("SELECT imagenes FROM celular WHERE id_celular=$id");
if ($res && $res->num_rows) {
    $imgs = json_decode($res->fetch_assoc()['imagenes'], true);
    if ($imgs) {
        foreach ($imgs as $img) {
            $ruta = "../uploads/".$img;
            if (file_exists($ruta)) unlink($ruta);
        }
    }
}

$conn->query("DELETE FROM celular WHERE id_celular=$id");

header("Location: celulares.php");
exit();