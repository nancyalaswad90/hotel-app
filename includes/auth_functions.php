<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit();
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validatePassword($password) {
    return strlen($password) >= 8;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
