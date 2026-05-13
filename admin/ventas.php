<?php include("navbar.php"); ?>
<?php
include("../includes/proteger.php");
include("../config/conexion.php");

$res = $conn->query("
SELECT v.*, u.nombre 
FROM venta v
JOIN usuario u ON v.id_usuario = u.id_usuario
ORDER BY v.fecha DESC
");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="bg-dark text-white">

<div class="container mt-4">

<h2>🧾 Historial de Ventas</h2>

<table class="table table-dark">

<tr>
<th>ID</th>
<th>Usuario</th>
<th>Fecha</th>
<th>Total</th>
</tr>

<?php while($r = $res->fetch_assoc()) { ?>

<tr>
<td><?= $r['id_venta'] ?></td>
<td><?= $r['nombre'] ?></td>
<td><?= $r['fecha'] ?></td>
<td>$<?= $r['total'] ?></td>
</tr>

<?php } ?>

</table>


</div>

</body>