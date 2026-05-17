<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function base32Decode(string $secret): string {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = strtoupper(preg_replace('/[^A-Z2-7]/', '', $secret));
    $binary = '';

    foreach (str_split($secret) as $char) {
        $val = strpos($alphabet, $char);
        if ($val === false) {
            continue;
        }
        $binary .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
    }

    $bytes = '';
    foreach (str_split($binary, 8) as $chunk) {
        if (strlen($chunk) === 8) {
            $bytes .= chr(bindec($chunk));
        }
    }

    return $bytes;
}

function generateBase32Secret(int $length = 16): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $chars[random_int(0, 31)];
    }
    return $secret;
}

function generateTotpCode(string $secret, int $digits = 6, int $period = 30, ?int $time = null): string {
    $time = $time ?? time();
    $counter = floor($time / $period);
    $key = base32Decode($secret);

    $binaryCounter = pack('N*', 0) . pack('N*', $counter);
    $hash = hash_hmac('sha1', $binaryCounter, $key, true);
    $offset = ord($hash[19]) & 0x0f;
    $code = ((ord($hash[$offset]) & 0x7f) << 24)
          | ((ord($hash[$offset + 1]) & 0xff) << 16)
          | ((ord($hash[$offset + 2]) & 0xff) << 8)
          | (ord($hash[$offset + 3]) & 0xff);

    $code = $code % pow(10, $digits);
    return str_pad((string)$code, $digits, '0', STR_PAD_LEFT);
}

function verifyTotpCode(string $secret, string $code, int $window = 1): bool {
    $code = trim($code);
    if ($code === '') {
        return false;
    }

    for ($i = -$window; $i <= $window; $i++) {
        $generated = generateTotpCode($secret, 6, 30, time() + ($i * 30));
        if (hash_equals($generated, $code)) {
            return true;
        }
    }

    return false;
}

function send2faMail(string $to, string $toName, string $subject, string $body): bool {
    $mail = new PHPMailer(true);

    try {
        if (defined('SMTP_ENABLED') && SMTP_ENABLED) {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->SMTPAutoTLS = true;
        } else {
            $mail->isMail();
        }

        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(false);

        return $mail->send();
    } catch (Exception $e) {
        error_log('PHPMailer 2FA error: ' . $mail->ErrorInfo);
        return false;
    }
}
