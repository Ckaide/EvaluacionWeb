<?php
include("../includes/proteger.php");
include("../config/conexion.php");

if ($_SESSION['user']['rol'] != 'admin') {
    header("Location: ../tienda.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $imagenes = [];

    if (!file_exists("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    foreach($_FILES['imagenes']['tmp_name'] as $key => $tmp) {

        if($_FILES['imagenes']['error'][$key] == 0){

            $nombre = time() . "_" . $_FILES['imagenes']['name'][$key];

            move_uploaded_file($tmp, "../uploads/" . $nombre);

            $imagenes[] = $nombre;
        }
    }

    $json = json_encode($imagenes);

    $stmt = $conn->prepare("INSERT INTO celular(marca,modelo,precio,stock,imagenes) VALUES(?,?,?,?,?)");

    $stmt->bind_param("ssdis",
        $marca,
        $modelo,
        $precio,
        $stock,
        $json
    );

    $stmt->execute();

    header("Location: dashboard.php#products");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Nuevo Celular</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#1f252d;
    color:white;
}

.form-card{
    max-width:600px;
    margin:auto;
    margin-top:50px;
    background:white;
    color:black;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,.4);
}

.preview{
    width:100px;
    height:100px;
    object-fit:cover;
    border-radius:10px;
}

.img-box{
    position:relative;
}

.remove-btn{
    position:absolute;
    top:-10px;
    right:-10px;
    border:none;
    border-radius:50%;
    width:30px;
    height:30px;
}

</style>

</head>

<body>

<?php include("navbar.php"); ?>

<div class="container py-5">

<div class="form-card">

<h1 class="text-center mb-4">
📱 Nuevo Celular
</h1>

<form method="POST" enctype="multipart/form-data">

<input 
type="text" 
name="marca" 
placeholder="Marca"
class="form-control mb-3"
required
>

<input 
type="text" 
name="modelo" 
placeholder="Modelo"
class="form-control mb-3"
required
>

<input 
type="number" 
name="precio" 
placeholder="Precio"
class="form-control mb-3"
required
>

<input 
type="number" 
name="stock" 
placeholder="Stock"
class="form-control mb-3"
required
>

<input 
type="file" 
name="imagenes[]" 
multiple
class="form-control mb-3"
id="imagenes"
>

<!-- PREVIEW -->
<div id="preview" class="d-flex flex-wrap gap-3 mb-3"></div>

<div class="d-grid">

<button class="btn btn-success btn-lg">
Guardar
</button>

</div>

</form>

</div>

</div>

<script>

const input = document.getElementById("imagenes");
const preview = document.getElementById("preview");

input.addEventListener("change", function(){

    preview.innerHTML = "";

    [...this.files].forEach((file,index)=>{

        const reader = new FileReader();

        reader.onload = function(e){

            const div = document.createElement("div");

            div.classList.add("img-box");

            div.innerHTML = `
            
            <img src="${e.target.result}" class="preview">

            <button type="button"
            class="btn btn-danger remove-btn">
            ✖
            </button>

            `;

            div.querySelector("button").onclick = ()=>{

                div.remove();

            };

            preview.appendChild(div);
        }

        reader.readAsDataURL(file);

    });

});

</script>

</body>
</html>