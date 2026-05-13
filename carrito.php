<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$carrito = $_SESSION['carrito'] ?? [];

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Carrito</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#1f252d;
    color:white;
}

.card{
    border:none;
    border-radius:20px;
    overflow:hidden;
}

img{
    object-fit:cover;
}

.total-box{
    background:#2a313b;
    border-radius:20px;
    padding:30px;
}

.btn{
    border-radius:10px;
}

</style>

</head>

<body>

<div class="container py-5">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

<h1>
🛒 Carrito
</h1>

<div class="d-flex gap-2 flex-wrap">

<!-- VOLVER -->
<a href="tienda.php" class="btn btn-secondary">
⬅ Seguir comprando
</a>

<!-- VACIAR -->
<?php if(!empty($carrito)) { ?>

<a 
href="vaciar_carrito.php"
class="btn btn-danger"
onclick="return confirm('¿Vaciar todo el carrito?')"
>
🗑 Vaciar carrito
</a>

<?php } ?>

</div>

</div>

<!-- MENSAJES -->
<?php if(isset($_SESSION['mensaje'])) { ?>

<div class="alert alert-success">
<?= $_SESSION['mensaje'] ?>
</div>

<?php unset($_SESSION['mensaje']); } ?>

<?php if(isset($_SESSION['error_stock'])) { ?>

<div class="alert alert-danger">
<?= $_SESSION['error_stock'] ?>
</div>

<?php unset($_SESSION['error_stock']); } ?>

<!-- CARRITO VACÍO -->
<?php if(empty($carrito)) { ?>

<div class="alert alert-warning text-center p-5">

<h3>
🛒 Tu carrito está vacío
</h3>

</div>

<?php } else { ?>

<div class="row g-4">

<?php foreach($carrito as $id => $p):

$imgs = json_decode($p['imagenes'], true) ?: [];

$subtotal = $p['precio'] * $p['cantidad'];

$total += $subtotal;

?>

<div class="col-lg-6">

<div class="card bg-dark text-white shadow h-100">

<div class="row g-0">

<!-- IMAGEN -->
<div class="col-4">

<img 
src="uploads/<?= $imgs[0] ?? 'noimg.png' ?>"
class="img-fluid h-100 w-100"
>

</div>

<!-- INFO -->
<div class="col-8">

<div class="card-body d-flex flex-column h-100">

<h4>
<?= $p['marca'] ?> <?= $p['modelo'] ?>
</h4>

<p class="mb-1">
💲 Precio:
<strong>
<?= number_format($p['precio'],2) ?>
</strong>
</p>

<p class="mb-1">
📦 Cantidad:
<strong>
<?= $p['cantidad'] ?>
</strong>
</p>

<p class="mb-3">
💰 Subtotal:
<strong class="text-success">
$<?= number_format($subtotal,2) ?>
</strong>
</p>

<!-- BOTONES -->
<div class="mt-auto d-flex gap-2 flex-wrap">

<!-- SUMAR -->
<a 
href="cantidad.php?id=<?= $id ?>&accion=sumar"
class="btn btn-success"
>
➕
</a>

<!-- RESTAR -->
<a 
href="cantidad.php?id=<?= $id ?>&accion=restar"
class="btn btn-warning"
>
➖
</a>

<!-- ELIMINAR -->
<a 
href="cantidad.php?id=<?= $id ?>&accion=eliminar"
class="btn btn-danger"
onclick="return confirm('¿Eliminar producto del carrito?')"
>
🗑
</a>

</div>

</div>

</div>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

<!-- TOTAL -->
<div class="total-box mt-5 text-center shadow">

<h2 class="mb-3">
💰 Total:
</h2>

<h1 class="text-success">
$<?= number_format($total,2) ?>
</h1>

<div class="d-flex justify-content-center gap-3 flex-wrap mt-4">

<a href="comprar.php" class="btn btn-primary btn-lg">
✅ Finalizar Compra
</a>

<a 
href="vaciar_carrito.php"
class="btn btn-danger btn-lg"
onclick="return confirm('¿Seguro que deseas vaciar todo el carrito?')"
>
🗑 Vaciar Todo
</a>

</div>

</div>

<?php } ?>

</div>

</body>
</html>