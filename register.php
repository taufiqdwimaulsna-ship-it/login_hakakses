<?php
session_start();
require 'config.php';
require 'auth.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama            = trim($_POST['nama'] ?? '');
    $username        = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';
    $konfirmasi_pass = $_POST['konfirmasi_password'] ?? '';
    $role            = $_POST['role'] ?? 'siswa';

    if ($nama === '' || $username === '' || $password === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif ($password !== $konfirmasi_pass) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (!in_array($role, ['admin', 'petugas', 'siswa'])) {
        $error = "Role tidak valid!";
    } else {
        $cek = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
        $cek->execute([$username]);

        if ($cek->fetch()) {
            $error = "Username sudah digunakan, pilih username lain!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $foto = null;

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $upload = simpanUploadFoto($_FILES['foto']);
                if (isset($upload['error'])) {
                    $error = $upload['error'];
                } else {
                    $foto = $upload['filename'];
                }
            }

            if (!$error) {
                $sql = "INSERT INTO users (nama, username, password, role, foto) VALUES (?, ?, ?, ?, ?)";
                $stmt = $koneksi->prepare($sql);
                $stmt->execute([$nama, $username, $passwordHash, $role, $foto]);

                header("Location: index.php?registered=1");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Hak Akses — Daftar</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #121212;
            min-height: 100vh;
            color: #ffffff;
        }
        .sp-reg-box {
            max-width: 440px;
            width: 100%;
            padding: 32px 24px;
        }
        .sp-input {
            background-color: #121212;
            border: 1px solid #727272;
            border-radius: 6px;
            color: #fff;
            padding: 11px 14px;
        }
        .sp-input:focus {
            background-color: #121212;
            border-color: #fff;
            box-shadow: 0 0 0 2px #fff;
            color: #fff;
        }
        .btn-sp-green {
            background-color: #1ed760;
            color: #000000;
            font-weight: 700;
            border-radius: 500px;
            padding: 13px;
            border: none;
            transition: transform 0.15s ease;
        }
        .btn-sp-green:hover {
            background-color: #3be477;
            color: #000;
            transform: scale(1.02);
        }
        .app-logo-badge {
            transition: transform 0.25s ease, filter 0.25s ease;
            filter: drop-shadow(0 4px 14px rgba(30, 215, 96, 0.35));
        }
        .app-logo-badge:hover {
            transform: scale(1.08);
            filter: drop-shadow(0 6px 20px rgba(30, 215, 96, 0.6));
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">

<div class="sp-reg-box">
    <div class="text-center mb-4">
        <div class="d-inline-flex justify-content-center align-items-center mb-3">
            <svg class="app-logo-badge" width="58" height="58" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="shieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#1ed760"/>
                        <stop offset="100%" stop-color="#00d2ff"/>
                    </linearGradient>
                    <linearGradient id="shieldBg" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#1a1a22"/>
                        <stop offset="100%" stop-color="#0e0e13"/>
                    </linearGradient>
                    <filter id="shieldGlow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#1ed760" flood-opacity="0.5"/>
                    </filter>
                </defs>
                <path d="M24 4L7 11V22C7 32.8 14.3 42.6 24 45C33.7 42.6 41 32.8 41 22V11L24 4Z" fill="url(#shieldBg)" stroke="url(#shieldGrad)" stroke-width="2.5" stroke-linejoin="round" filter="url(#shieldGlow)"/>
                <path d="M19 21V17C19 14.2 21.2 12 24 12C26.8 12 29 14.2 29 17V21" stroke="url(#shieldGrad)" stroke-width="2.5" stroke-linecap="round"/>
                <rect x="16" y="21" width="16" height="12" rx="3" fill="url(#shieldGrad)"/>
                <circle cx="24" cy="26" r="1.5" fill="#0e0e13"/>
                <path d="M24 27.5V30" stroke="#0e0e13" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </div>
        <h2 class="fw-extrabold mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Daftar Akun Hak Akses</h2>
        <p class="text-secondary small mb-0">Buat akun baru untuk mengakses sistem</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger bg-danger bg-opacity-25 border-0 text-white small py-2">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label small fw-bold">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control sp-input" placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold">Username</label>
            <input type="text" name="username" class="form-control sp-input" placeholder="Buat username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control sp-input" placeholder="Password" required>
            </div>
            <div class="col-6">
                <label class="form-label small fw-bold">Konfirmasi</label>
                <input type="password" name="konfirmasi_password" class="form-control sp-input" placeholder="Ulangi" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold">Role Pengguna</label>
            <select name="role" class="form-select sp-input" required>
                <option value="siswa" selected>Siswa (Akses Akademik Pribadi)</option>
                <option value="petugas">Petugas (Akses Data Operasional)</option>
                <option value="admin">Admin (Pengelola Penuh)</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold">Foto Profil (Opsional)</label>
            <input type="file" name="foto" class="form-control sp-input" accept="image/jpeg,image/png,image/webp">
            <div class="form-text text-secondary" style="font-size: 0.72rem;">Unggah JPG/PNG (Maks 3MB). Jika kosong, foto profil acak akan dibuat otomatis.</div>
        </div>
        <button type="submit" class="btn btn-sp-green w-100 mb-3">Daftar Sekarang</button>
    </form>

    <hr class="border-secondary opacity-25 my-4">
    <div class="text-center">
        <span class="text-secondary small">Sudah punya akun?</span>
        <a href="index.php" class="text-white fw-bold small ms-1 text-decoration-underline">Masuk di sini</a>
    </div>
</div>

</body>
</html>