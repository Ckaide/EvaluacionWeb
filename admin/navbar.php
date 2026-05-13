<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.navbar{
    box-shadow:0 2px 10px rgba(0,0,0,.4);
}

.nav-btn{
    border-radius:10px;
}

</style>

<nav class="navbar navbar-dark bg-primary px-4">

<a class="navbar-brand fw-bold" href="dashboard.php">
📱 Admin Panel
</a>

<div class="d-flex gap-2 align-items-center">

<!-- USUARIO -->
<span class="btn btn-info disabled nav-btn">
👤 <?= $_SESSION['user']['nombre'] ?>
</span>

<!-- CERRAR SESIÓN -->
<a href="../auth/logout.php" class="btn btn-danger nav-btn">
🚪 Cerrar Sesión
</a>

</div>

</nav>