<?php
require 'auth.php';
cekLogin();

switch ($_SESSION['role']) {
    case 'admin':
        header("Location: admin.php");
        break;
    case 'petugas':
        header("Location: petugas.php");
        break;
    case 'siswa':
        header("Location: siswa.php");
        break;
    default:
        session_unset();
        session_destroy();
        header("Location: index.php");
}
exit;
?>