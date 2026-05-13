<?php include("navbar.php"); ?>
<?php
include("../includes/proteger.php");
include("../config/conexion.php");

// 🔒 SOLO ADMIN
if ($_SESSION['user']['rol'] != 'admin') {
    header("Location: ../tienda.php");
    exit();
}

// 📊 ESTADÍSTICAS
$totalCelulares = $conn->query("SELECT COUNT(*) t FROM celular")->fetch_assoc()['t'];
$totalUsuarios = $conn->query("SELECT COUNT(*) t FROM usuario")->fetch_assoc()['t'];
$totalVentas = $conn->query("SELECT COUNT(*) t FROM venta")->fetch_assoc()['t'];
$ingresos = $conn->query("SELECT SUM(total) t FROM venta")->fetch_assoc()['t'] ?? 0;

// 📋 ÚLTIMAS VENTAS
$ventas = $conn->query("
SELECT v.*, u.nombre 
FROM venta v
JOIN usuario u ON v.id_usuario = u.id_usuario
ORDER BY v.fecha DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#121212;
    color:white;
}

.card{
    border:none;
    border-radius:15px;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.navbar{
    box-shadow:0 2px 10px rgba(0,0,0,.5);
}

.table{
    border-radius:10px;
    overflow:hidden;
}

.btn{
    border-radius:10px;
}

</style>

</head>

<body>



<!-- BIENVENIDA -->
<div class="mb-4">
<h2>
👋 Bienvenido, <?= $_SESSION['user']['nombre'] ?>
</h2>

<p class="text-light">
Panel de administración del sistema.
</p>
</div>

<!-- CARDS -->
<div class="row g-4">

<!-- CELULARES -->
<div class="col-md-3">

<div class="card bg-primary text-white shadow p-3">

<h5>📱 Celulares</h5>

<h1><?= $totalCelulares ?></h1>

<a href="celulares.php" class="btn btn-light mt-2">
Gestionar
</a>

</div>

</div>

<!-- USUARIOS -->
<div class="col-md-3">

<div class="card bg-success text-white shadow p-3">

<h5>👤 Usuarios</h5>

<h1><?= $totalUsuarios ?></h1>

</div>

</div>

<!-- VENTAS -->
<div class="col-md-3">

<div class="card bg-warning text-dark shadow p-3">

<h5>🛒 Ventas</h5>

<h1><?= $totalVentas ?></h1>

<a href="ventas.php" class="btn btn-dark mt-2">
Ver Ventas
</a>

</div>

</div>

<!-- INGRESOS -->
<div class="col-md-3">

<div class="card bg-danger text-white shadow p-3">

<h5>💰 Ingresos</h5>

<h1>$<?= number_format($ingresos,2) ?></h1>

</div>

</div>

</div>

<!-- ACCIONES RÁPIDAS -->
<div class="mt-5">

<h4>⚡ Acciones rápidas</h4>

<div class="d-flex flex-wrap gap-3 mt-3">

<a href="crear.php" class="btn btn-success btn-lg">
➕ Nuevo Celular
</a>

<a href="celulares.php" class="btn btn-primary btn-lg">
📱 Ver Celulares
</a>

<a href="ventas.php" class="btn btn-warning btn-lg">
🧾 Historial Ventas
</a>

<a href="../tienda.php" class="btn btn-info btn-lg">
🛒 Abrir Tienda
</a>

</div>

</div>

<!-- ÚLTIMAS VENTAS -->
<div class="mt-5">

<h4>📋 Últimas Ventas</h4>

<div class="table-responsive">

<table class="table table-dark table-hover align-middle mt-3">

<tr>
<th>ID</th>
<th>Usuario</th>
<th>Fecha</th>
<th>Total</th>
</tr>

<?php while($v = $ventas->fetch_assoc()) { ?>

<tr>

<td>
#<?= $v['id_venta'] ?>
</td>

<td>
<?= $v['nombre'] ?>
</td>

<td>
<?= $v['fecha'] ?>
</td>

<td class="text-success fw-bold">
$<?= number_format($v['total'],2) ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<!-- FOOTER -->
<div class="text-center text-secondary mt-5 mb-3">

Sistema Tienda de Celulares © 2026

</div>

</div>

</body>
</html>