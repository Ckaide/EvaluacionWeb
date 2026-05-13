<?php include("navbar.php"); ?>
<?php
include("../includes/proteger.php");
include("../config/conexion.php");

$res = $conn->query("SELECT * FROM celular");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<body class="bg-dark text-white">

<div class="container mt-4">

<!-- BOTÓN VOLVER -->
<button onclick="history.back()" class="btn btn-secondary mb-3">⬅ Volver</button>

<h2>📱 Celulares</h2>

<a href="crear.php" class="btn btn-success mb-3">+ Nuevo</a>

<div class="row g-4">

<?php while($r = $res->fetch_assoc()): 
$imgs = json_decode($r['imagenes'], true) ?: [];
?>

<div class="col-lg-4 col-md-6">

<div class="card text-dark mb-3 shadow">

<!-- IMAGEN + ELIMINAR -->
<div class="position-relative">

<img src="../uploads/<?= $imgs[0] ?? 'noimg.png' ?>" class="card-img-top" height="200" style="object-fit:cover;">

<a href="eliminar.php?id=<?= $r['id_celular'] ?>"
onclick="return confirm('⚠ ¿Seguro eliminar este celular?');"
class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">✖</a>

</div>

<div class="card-body">

<h5><?= $r['marca'] ?> <?= $r['modelo'] ?></h5>

<p class="text-success fw-bold">$<?= $r['precio'] ?></p>

<div class="d-flex gap-2">

<!-- VER -->
<button class="btn btn-outline-primary btn-sm"
data-bs-toggle="modal"
data-bs-target="#m<?= $r['id_celular'] ?>">
👁 Ver
</button>

<!-- EDITAR -->
<a href="editar.php?id=<?= $r['id_celular'] ?>" class="btn btn-warning btn-sm">
✏
</a>

</div>

</div>

</div>

</div>

<!-- MODAL PRO -->
<div class="modal fade" id="m<?= $r['id_celular'] ?>" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content border-0 shadow-lg">

<div class="modal-header bg-dark text-white">
<h5 class="modal-title">
📱 <?= $r['marca'] ?> <?= $r['modelo'] ?>
</h5>
<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="row">

<!-- IMÁGENES -->
<div class="col-md-6">

<div id="carousel<?= $r['id_celular'] ?>" class="carousel slide">

<div class="carousel-inner">

<?php foreach($imgs as $i => $img): ?>
<div class="carousel-item <?= $i==0 ? 'active' : '' ?>">
<img src="../uploads/<?= $img ?>" class="d-block w-100 rounded" style="height:300px; object-fit:cover;">
</div>
<?php endforeach; ?>

</div>

<button class="carousel-control-prev" type="button"
data-bs-target="#carousel<?= $r['id_celular'] ?>" data-bs-slide="prev">
<span class="carousel-control-prev-icon"></span>
</button>

<button class="carousel-control-next" type="button"
data-bs-target="#carousel<?= $r['id_celular'] ?>" data-bs-slide="next">
<span class="carousel-control-next-icon"></span>
</button>

</div>

</div>

<!-- INFO -->
<div class="col-md-6">

<h4><?= $r['marca'] ?> <?= $r['modelo'] ?></h4>

<p class="text-muted">ID: <?= $r['id_celular'] ?></p>

<h3 class="text-success">$<?= $r['precio'] ?></h3>

<p><b>Stock:</b> <?= $r['stock'] ?></p>

<hr>

<p class="text-dark">
📱 Equipo ideal para uso diario, redes sociales, multimedia y apps modernas.
Buen rendimiento y excelente relación calidad-precio.
</p>

</div>

</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>

</div>
</div>
</div>

<?php endwhile; ?>

</div>

</div>

</body>