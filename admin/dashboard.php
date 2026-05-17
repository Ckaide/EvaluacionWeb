<?php include("navbar.php"); ?>
<?php
include("../includes/proteger.php");
include("../config/conexion.php");

// 🔒 SOLO ADMIN
if ($_SESSION['user']['rol'] != 'admin') {
    header("Location: ../tienda.php");
    exit();
}

// ACCIONES ADMIN
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action === 'delete_product' && $id > 0) {
        $stmt = $conn->prepare("SELECT imagenes FROM celular WHERE id_celular=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows) {
            $imgs = json_decode($res->fetch_assoc()['imagenes'], true) ?: [];
            foreach ($imgs as $img) {
                $path = __DIR__ . "/../uploads/" . $img;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $stmt = $conn->prepare("DELETE FROM celular WHERE id_celular=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: dashboard.php#products");
        exit();
    }

    if ($action === 'toggle_ban_user' && $id > 0) {
        $stmt = $conn->prepare("UPDATE usuario SET banned = CASE WHEN banned = 1 THEN 0 ELSE 1 END WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: dashboard.php#users");
        exit();
    }
}

// 📊 ESTADÍSTICAS
$totalCelulares = $conn->query("SELECT COUNT(*) t FROM celular")->fetch_assoc()['t'];
$totalUsuarios = $conn->query("SELECT COUNT(*) t FROM usuario WHERE rol='cliente'")->fetch_assoc()['t'];
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

$productos = $conn->query("SELECT * FROM celular ORDER BY id_celular DESC");
$clientes = $conn->query("SELECT id_usuario, nombre, correo, banned FROM usuario WHERE rol='cliente' ORDER BY nombre ASC");
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

<a href="dashboard.php#products" class="btn btn-light mt-2">
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

<a href="dashboard.php#products" class="btn btn-primary btn-lg">
📱 Ver Productos
</a>

<a href="ventas.php" class="btn btn-warning btn-lg">
🧾 Historial Ventas
</a>

<a href="../tienda.php" class="btn btn-info btn-lg">
🛒 Abrir Tienda
</a>

</div>

</div>

<!-- LISTA DE PRODUCTOS -->
<div class="mt-5" id="products">

<h4>📦 Productos</h4>

<div class="table-responsive">
<table class="table table-dark table-hover align-middle mt-3">
<thead>
<tr>
<th>Marca</th>
<th>Modelo</th>
<th>Precio</th>
<th>Stock</th>
<th class="text-end">Acciones</th>
</tr>
</thead>
<tbody>
<?php while($p = $productos->fetch_assoc()) {
    $imagenes = json_decode($p['imagenes'], true) ?: [];
?>
<tr>
<td><?= htmlspecialchars($p['marca']) ?></td>
<td><?= htmlspecialchars($p['modelo']) ?></td>
<td class="text-success">$<?= number_format($p['precio'],2) ?></td>
<td><?= intval($p['stock']) ?></td>
<td class="text-end">
<button
    class="btn btn-info btn-sm"
    data-bs-toggle="modal"
    data-bs-target="#productModal"
    data-brand="<?= htmlspecialchars($p['marca'], ENT_QUOTES) ?>"
    data-model="<?= htmlspecialchars($p['modelo'], ENT_QUOTES) ?>"
    data-price="<?= number_format($p['precio'],2) ?>"
    data-stock="<?= intval($p['stock']) ?>"
    data-images="<?= htmlspecialchars(json_encode($imagenes), ENT_QUOTES, 'UTF-8') ?>"
    >Ver</button>

<a href="editar.php?id=<?= intval($p['id_celular']) ?>" class="btn btn-warning btn-sm">Modificar</a>

<a href="dashboard.php?action=delete_product&id=<?= intval($p['id_celular']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este celular?');">Eliminar</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

<!-- LISTA DE CLIENTES -->
<div class="mt-5" id="users">

<h4>👥 Clientes registrados</h4>

<div class="table-responsive">
<table class="table table-dark table-hover align-middle mt-3">
<thead>
<tr>
<th>Nombre</th>
<th>Email</th>
<th>Estado</th>
<th class="text-end">Acciones</th>
</tr>
</thead>
<tbody>
<?php while($u = $clientes->fetch_assoc()) { ?>
<tr>
<td><?= htmlspecialchars($u['nombre']) ?></td>
<td><?= htmlspecialchars($u['correo']) ?></td>
<td><?= $u['banned'] ? '<span class="badge bg-danger">Baneado</span>' : '<span class="badge bg-success">Activo</span>' ?></td>
<td class="text-end">
<a href="dashboard.php?action=toggle_ban_user&id=<?= intval($u['id_usuario']) ?>" class="btn <?= $u['banned'] ? 'btn-success' : 'btn-danger' ?> btn-sm" onclick="return confirm('¿Seguro que deseas <?= $u['banned'] ? 'desbanear' : 'banear' ?> este usuario?');">
<?= $u['banned'] ? 'Desbanear' : 'Banear' ?>
</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
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

<!-- PRODUCTO DETALLES -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-white border-0">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title" id="productModalLabel">Detalles del producto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <ul class="list-group list-group-flush text-white-75">
              <li class="list-group-item bg-transparent border-secondary"><strong>Marca:</strong> <span id="modalBrand"></span></li>
              <li class="list-group-item bg-transparent border-secondary"><strong>Modelo:</strong> <span id="modalModel"></span></li>
              <li class="list-group-item bg-transparent border-secondary"><strong>Precio:</strong> $<span id="modalPrice"></span></li>
              <li class="list-group-item bg-transparent border-secondary"><strong>Stock:</strong> <span id="modalStock"></span></li>
            </ul>
          </div>
          <div class="col-md-6" id="modalImagesContainer">
            <div class="row g-2" id="modalImages"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top border-secondary">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var productModal = document.getElementById('productModal');
productModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var brand = button.getAttribute('data-brand');
  var model = button.getAttribute('data-model');
  var price = button.getAttribute('data-price');
  var stock = button.getAttribute('data-stock');
  var images = [];

  try {
    images = JSON.parse(button.getAttribute('data-images') || '[]');
  } catch (e) {
    images = [];
  }

  document.getElementById('modalBrand').textContent = brand;
  document.getElementById('modalModel').textContent = model;
  document.getElementById('modalPrice').textContent = price;
  document.getElementById('modalStock').textContent = stock;

  var imagesContainer = document.getElementById('modalImages');
  imagesContainer.innerHTML = '';

  if (images.length === 0) {
    imagesContainer.innerHTML = '<div class="col-12"><div class="p-3 bg-secondary rounded text-center">No hay imágenes disponibles.</div></div>';
  } else {
    images.forEach(function(src) {
      var col = document.createElement('div');
      col.className = 'col-6';
      col.innerHTML = '<img src="../uploads/' + encodeURIComponent(src) + '" class="img-fluid rounded">';
      imagesContainer.appendChild(col);
    });
  }
});
</script>

<!-- FOOTER -->
<div class="text-center text-secondary mt-5 mb-3">

Sistema Tienda de Celulares © 2026

</div>

</div>

</body>
</html>