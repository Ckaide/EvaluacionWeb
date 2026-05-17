<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // en entrega

// Configuración de correo para PHPMailer
define('EMAIL_FROM_ADDRESS', 'no-reply@tiendacelulares.local');
define('EMAIL_FROM_NAME', 'Tienda Celulares');

// Si usas SMTP, cambia estos datos. Para Gmail usa smtp.gmail.com, usuario y contraseña o app password.
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'wascarITjim93@gmail.com');
define('SMTP_PASS', 'yvzn rrdz jpgw iilo');
define('SMTP_SECURE', 'tls');
?>