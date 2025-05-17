<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

session_destroy();
header("Location: " . BASE_URL . "/auth/login.php");
exit();
?>
