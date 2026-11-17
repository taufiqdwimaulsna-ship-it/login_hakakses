<?php
require 'config.php';

$data = [
    ['Administrator', 'admin', 'admin123', 'admin'],
    ['Petugas Sekolah', 'petugas', 'petugas123', 'petugas'],
    ['Siswa SMK', 'siswa', 'siswa123', 'siswa']
];

$sql = "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);

foreach ($data as $u) {
    $passwordHash = password_hash($u[2], PASSWORD_DEFAULT);
    $stmt->execute([$u[0], $u[1], $passwordHash, $u[3]]);
}

echo "Data pengguna berhasil dibuat! Segera hapus file buat_user.php ini.";
?>