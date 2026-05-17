<?php
require_once '../config.php';
secure_session_start();
session_unset();
session_destroy();
header("Location: ../client-login.php");
exit;
?>
