<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}

function cekRole($role) {
    cekLogin();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit("403 - Anda tidak memiliki hak akses!");
    }
}
?>