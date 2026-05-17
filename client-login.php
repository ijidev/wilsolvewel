<?php
$redirect = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/wilsovewel.com/client/login.php';
header('HTTP/1.1 301 Moved Permanently');
header("Location: $redirect");
exit;
