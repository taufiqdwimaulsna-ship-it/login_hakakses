<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Hak Akses — Masuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(180deg, #222222 0%, #000000 100%);
            min-height: 100vh;
            color: #ffffff;
        }
        .sp-login-box {
            background-color: #121212;
            border-radius: 12px;
            max-width: 460px;
            width: 100%;
            padding: 40px 36px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }
        .app-logo-badge {
            transition: transform 0.25s ease, filter 0.25s ease;
            filter: drop-shadow(0 4px 14px rgba(30, 215, 96, 0.35));
        }
        .app-logo-badge:hover {
            transform: scale(1.08);
            filter: drop-shadow(0 6px 20px rgba(30, 215, 96, 0.6));
        }
        .sp-logo {
            font-size: 2.5rem;
            color: #ffffff;
        }
        .sp-input {
            background-color: #121212;
            border: 1px solid #727272;
            border-radius: 6px;
            color: #fff;
            padding: 12px 14px;
            font-size: 0.95rem;
        }
        .sp-input:hover { border-color: #ffffff; }
        .sp-input:focus {
            background-color: #121212;
            border-color: #ffffff;
            box-shadow: 0 0 0 2px #ffffff;
            color: #fff;
        }
        .btn-sp-green {
            background-color: #1ed760;
            color: #000000;
            font-weight: 700;
            border-radius: 500px;
            padding: 13px;
            border: none;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }
        .btn-sp-green:hover {
            background-color: #3be477;
            color: #000000;
            transform: scale(1.03);
        }
        .sp-chip {
            cursor: pointer;
            background-color: #242424;
            color: #ffffff;
            border-radius: 500px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .sp-chip:hover {
            background-color: #2a2a2a;
            border-color: #1ed760;
            color: #1ed760;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">

<div class="sp-login-box">
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
        <h3 class="fw-extrabold mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Aplikasi Hak Akses</h3>
        <p class="text-secondary small mb-0">Implementasi Login & Hak Akses (PHP + MySQL)</p>
    </div>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success bg-success bg-opacity-25 border-0 text-white small rounded-3 py-2">
            <i class="bi bi-check-circle-fill text-success me-1"></i> Akun berhasil dibuat! Silakan masuk.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger bg-danger bg-opacity-25 border-0 text-white small rounded-3 py-2">
            <i class="bi bi-exclamation-circle-fill text-danger me-1"></i> Username atau password salah!
        </div>
    <?php endif; ?>

    <!-- Quick Fill Akun Uji Modul -->
    <div class="mb-3">
        <div class="text-secondary mb-2" style="font-size: 0.75rem; font-weight: 700;">PILIH CEPAT AKUN UJI:</div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="sp-chip" onclick="isiAkun('admin','admin123')"><i class="bi bi-shield-lock-fill text-success me-1"></i>Admin</span>
            <span class="sp-chip" onclick="isiAkun('petugas','petugas123')"><i class="bi bi-person-workspace text-info me-1"></i>Petugas</span>
            <span class="sp-chip" onclick="isiAkun('siswa','siswa123')"><i class="bi bi-mortarboard-fill text-warning me-1"></i>Siswa</span>
        </div>
    </div>

    <form action="proses_login.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold mb-1">Username</label>
            <input type="text" name="username" id="userInp" class="form-control sp-input" placeholder="Username" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold mb-1">Password</label>
            <input type="password" name="password" id="passInp" class="form-control sp-input" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-sp-green w-100 mb-4">Masuk</button>
    </form>

    <hr class="border-secondary opacity-25 my-3">
    <div class="text-center mt-3">
        <span class="text-secondary small">Belum punya akun?</span>
        <a href="register.php" class="text-white fw-bold small ms-1 text-decoration-underline">Daftar di sini</a>
    </div>
</div>

<script>
function isiAkun(u, p) {
    document.getElementById('userInp').value = u;
    document.getElementById('passInp').value = p;
}
</script>
</body>
</html>