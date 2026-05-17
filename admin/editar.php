<?php include("navbar.php"); ?>
<?php
include("../includes/proteger.php");
include("../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

$res = $conn->query("SELECT * FROM celular WHERE id_celular=$id");
$row = $res->fetch_assoc();
$imagenes = json_decode($row['imagenes'], true) ?: [];

// eliminar imagen individual
if (isset($_GET['del'])) {
    $i = intval($_GET['del']);
    if (isset($imagenes[$i])) {
        $ruta = "../uploads/".$imagenes[$i];
        if (file_exists($ruta)) unlink($ruta);

        unset($imagenes[$i]);
        $imagenes = array_values($imagenes);

        $json = json_encode($imagenes);
        $conn->query("UPDATE celular SET imagenes='$json' WHERE id_celular=$id");

        header("Location: editar.php?id=$id");
        exit();
    }
}

if ($_POST) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // nuevas imágenes
    if (!empty($_FILES['imagenes']['name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $i=>$tmp) {
            if ($_FILES['imagenes']['error'][$i]==0) {
                $name=time()."_".$_FILES['imagenes']['name'][$i];
                move_uploaded_file($tmp,"../uploads/".$name);
                $imagenes[]=$name;
            }
        }
    }

    $json=json_encode($imagenes);

    $stmt=$conn->prepare("UPDATE celular SET marca=?,modelo=?,precio=?,stock=?,imagenes=? WHERE id_celular=?");
    $stmt->bind_param("ssdisi",$marca,$modelo,$precio,$stock,$json,$id);
    $stmt->execute();

    header("Location: dashboard.php#products");
    exit();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<body class="bg-dark text-white d-flex justify-content-center align-items-center vh-100">
<div class="card p-4" style="width:450px">

<h3>Editar</h3>

<form method="POST" enctype="multipart/form-data">
<input name="marca" value="<?= $row['marca'] ?>" class="form-control my-2">
<input name="modelo" value="<?= $row['modelo'] ?>" class="form-control my-2">
<input name="precio" value="<?= $row['precio'] ?>" class="form-control my-2">
<input name="stock" value="<?= $row['stock'] ?>" class="form-control my-2">

<p>Imágenes:</p>
<?php foreach($imagenes as $i=>$img): ?>
<div style="position:relative;display:inline-block;">
<img src="../uploads/<?= $img ?>" width="70">
<a href="editar.php?id=<?= $id ?>&del=<?= $i ?>" style="position:absolute;top:0;right:0;background:red;color:white">x</a>
</div>
<?php endforeach; ?>

<input type="file" name="imagenes[]" multiple class="form-control my-2">

<button class="btn btn-warning w-100">Actualizar</button>
</form>

</div>
</body>