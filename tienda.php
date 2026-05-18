<?php
include("config/conexion.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$res = $conn->query("SELECT c.*, cat.nombre AS categoria FROM celular c LEFT JOIN categoria cat ON c.id_categoria = cat.id_categoria");
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Tienda</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#1f252d;
    color:white;
}

/* HEADER */
.header-user{
    background: rgba(255,255,255,0.1);
    padding:6px 12px;
    border-radius:10px;
    font-weight:bold;
}

/* CARDS */
.card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card img{
    object-fit:cover;
}

/* PRECIO */
.precio{
    font-size:22px;
    font-weight:bold;
    color:#28a745;
}

/* STOCK */
.stock-bajo{
    color:red;
    font-weight:bold;
}

.stock-normal{
    color:green;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container py-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

<h1>🛒 Tienda</h1>

<div class="d-flex align-items-center gap-3">

<a href="carrito.php" class="btn btn-warning">
🛒 Ver Carrito
</a>

<?php if(isset($_SESSION['user'])) { ?>

<span class="header-user">
👤 <?= is_array($_SESSION['user']) ? $_SESSION['user']['nombre'] : $_SESSION['user'] ?>
</span>

<a href="auth/logout.php" class="btn btn-danger">
🚪 Cerrar Sesión
</a>

<?php } else { ?>

<a href="auth/login.php" class="btn btn-primary">
🔐 Iniciar Sesión
</a>

<?php } ?>

</div>

</div>

<!-- ERROR STOCK -->
<?php if(isset($_SESSION['error_stock'])) { ?>

<div class="alert alert-danger">
<?= $_SESSION['error_stock'] ?>
</div>

<?php unset($_SESSION['error_stock']); } ?>

<!-- PRODUCTOS -->
<div class="row g-4">

<?php while($r = $res->fetch_assoc()):

$imgs = json_decode($r['imagenes'], true) ?: [];

?>

<div class="col-lg-4 col-md-6">

<div class="card bg-white text-dark shadow h-100">

<!-- IMAGEN -->
<img 
src="uploads/<?= $imgs[0] ?? 'noimg.png' ?>" 
height="250"
class="w-100"
>

<div class="card-body d-flex flex-column">

<h4>
<?= $r['marca'] ?> <?= $r['modelo'] ?>
</h4>

<p class="precio">
$<?= number_format($r['precio'],2) ?>
</p>

<p class="text-muted mb-2">
Categoria: <?= htmlspecialchars($r['categoria'] ?: 'Sin categoría') ?>
</p>

<!-- STOCK -->
<?php if($r['stock'] <= 0) { ?>

<p class="stock-bajo">
❌ Agotado
</p>

<?php } elseif($r['stock'] <= 3) { ?>

<p class="stock-bajo">
⚠ Últimas unidades (<?= $r['stock'] ?>)
</p>

<?php } else { ?>

<p class="stock-normal">
✅ Stock disponible (<?= $r['stock'] ?>)
</p>

<?php } ?>

<!-- BOTONES -->
<div class="mt-auto d-grid gap-2">

<?php if($r['stock'] > 0) { ?>

<a 
href="agregar_carrito.php?id=<?= $r['id_celular'] ?>"
class="btn btn-success"
>
🛒 Agregar al carrito
</a>

<?php } else { ?>

<button class="btn btn-secondary" disabled>
Sin stock
</button>

<?php } ?>

</div>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

</div>

</body>
</html>