<?php
session_start();
include("config/conexion.php");

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

$userId = $_SESSION['user']['id_usuario'];

$stmt = $conn->prepare("SELECT car.id_celular, car.cantidad, c.marca, c.modelo, c.precio, c.stock, c.imagenes\nFROM carrito car\nJOIN celular c ON car.id_celular = c.id_celular\nWHERE car.id_usuario = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$carrito = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['precio'] * $row['cantidad'];
    $carrito[$row['id_celular']] = $row;
    $total += $row['subtotal'];
}

if (empty($carrito)) {
    header("Location: carrito.php");
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $pais = trim($_POST['pais'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $metodo_pago = trim($_POST['metodo_pago'] ?? '');

    if ($direccion === '') {
        $errors[] = 'La dirección es obligatoria.';
    }
    if ($ciudad === '') {
        $errors[] = 'La ciudad es obligatoria.';
    }
    if ($codigo_postal === '') {
        $errors[] = 'El código postal es obligatorio.';
    }
    if ($pais === '') {
        $errors[] = 'El país es obligatorio.';
    }
    if ($telefono === '') {
        $errors[] = 'El teléfono es obligatorio.';
    }
    if ($metodo_pago === '') {
        $errors[] = 'El método de pago es obligatorio.';
    }

    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            foreach ($carrito as $item) {
                $stmt = $conn->prepare("SELECT stock FROM celular WHERE id_celular = ? FOR UPDATE");
                $stmt->bind_param("i", $item['id_celular']);
                $stmt->execute();
                $stockRow = $stmt->get_result()->fetch_assoc();

                if (!$stockRow || $item['cantidad'] > $stockRow['stock']) {
                    throw new Exception("Stock insuficiente para {$item['marca']} {$item['modelo']}.");
                }
            }

            $fecha = date("Y-m-d H:i:s");
            $estadoVenta = 'pendiente';

            $stmt = $conn->prepare("INSERT INTO venta(id_usuario, fecha, total, estado) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isds", $userId, $fecha, $total, $estadoVenta);
            $stmt->execute();
            $idVenta = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO direccion(id_usuario, direccion, ciudad, referencia) VALUES (?, ?, ?, ?)");
            $referencia = "CP: $codigo_postal | PAIS: $pais | TEL: $telefono";
            $stmt->bind_param("isss", $userId, $direccion, $ciudad, $referencia);
            $stmt->execute();

            $pagoEstado = 'procesado';
            $stmt = $conn->prepare("INSERT INTO pago(id_venta, metodo, estado, fecha) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $idVenta, $metodo_pago, $pagoEstado, $fecha);
            $stmt->execute();

            $stmtDetalle = $conn->prepare("INSERT INTO detalle_venta(id_venta, id_celular, cantidad, precio) VALUES (?, ?, ?, ?)");

            foreach ($carrito as $item) {
                $stmtDetalle->bind_param("iiid", $idVenta, $item['id_celular'], $item['cantidad'], $item['precio']);
                $stmtDetalle->execute();

                $stmtUpdate = $conn->prepare("UPDATE celular SET stock = stock - ? WHERE id_celular = ?");
                $stmtUpdate->bind_param("ii", $item['cantidad'], $item['id_celular']);
                $stmtUpdate->execute();
            }

            $stmt = $conn->prepare("DELETE FROM carrito WHERE id_usuario = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            $conn->commit();
            $success = true;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Finalizar Compra</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #1f252d; color: white; }
.card { border-radius: 20px; border: none; }
.form-label, .form-control, .form-select {
    color: #ffffff;
}
.form-control, .form-select {
    background: #2a313b;
    border-color: #444d59;
}
.form-control::placeholder {
    color: #d1d5db;
}
</style>
</head>
<body>
<div class="container py-5">
<?php if ($success): ?>
    <div class="card p-5 shadow text-center bg-dark">
        <h1 class="text-success mb-4">✅ Compra realizada</h1>
        <h4 class="mb-3">Total pagado:</h4>
        <h2 class="text-primary">$<?= number_format($total, 2) ?></h2>
        <a href="tienda.php" class="btn btn-success mt-4">🛒 Volver a la tienda</a>
    </div>
<?php else: ?>
    <div class="card p-5 shadow bg-dark">
        <h1 class="mb-4">Finalizar Compra</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-6">
                <h4 class="mb-3">Resumen del pedido</h4>
                <ul class="list-group mb-3">
                    <?php foreach ($carrito as $item): ?>
                        <li class="list-group-item bg-secondary text-white d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($item['marca'] . ' ' . $item['modelo']) ?> x<?= $item['cantidad'] ?></span>
                            <strong>$<?= number_format($item['subtotal'], 2) ?></strong>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
                        <span>Total</span>
                        <strong>$<?= number_format($total, 2) ?></strong>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <form method="post" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Código postal</label>
                        <input type="text" name="codigo_postal" class="form-control" value="<?= htmlspecialchars($_POST['codigo_postal'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">País</label>
                        <input type="text" name="pais" class="form-control" value="<?= htmlspecialchars($_POST['pais'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Método de pago</label>
                        <select name="metodo_pago" class="form-select">
                            <option value="" <?= empty($_POST['metodo_pago']) ? 'selected' : '' ?>>Selecciona un método</option>
                            <option value="tarjeta" <?= ($_POST['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta de crédito</option>
                            <option value="paypal" <?= ($_POST['metodo_pago'] ?? '') === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                            <option value="transferencia" <?= ($_POST['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia bancaria</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <a href="carrito.php" class="btn btn-secondary">Volver al carrito</a>
                        <button type="submit" class="btn btn-primary">Procesar pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>
</body>
</html>
