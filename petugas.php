<?php
require 'auth.php';
cekRole('petugas');
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Petugas — Aplikasi Hak Akses</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Figtree', sans-serif; background-color: #000; color: #fff; margin: 0; }
        .sp-topbar { height: 64px; background-color: #000; padding: 0 16px; }
        .app-logo-badge {
            transition: transform 0.25s ease, filter 0.25s ease;
            filter: drop-shadow(0 2px 10px rgba(30, 215, 96, 0.35));
        }
        .app-logo-badge:hover {
            transform: scale(1.08);
            filter: drop-shadow(0 4px 16px rgba(30, 215, 96, 0.6));
        }
        .sp-home-btn { width: 48px; height: 48px; border-radius: 50%; background: #1f1f1f; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; text-decoration: none; }
        .sp-search-pill { background: #1f1f1f; border-radius: 500px; height: 48px; padding: 0 18px; width: 100%; max-width: 420px; }
        .btn-pill-white { background: #fff; color: #000; font-weight: 700; border-radius: 500px; padding: 8px 22px; text-decoration: none; font-size: 0.9rem; }
        .sp-wrapper { display: grid; grid-template-columns: 300px 1fr; gap: 8px; padding: 0 8px 8px 8px; min-height: calc(100vh - 72px); }
        @media (max-width: 991px) { .sp-wrapper { grid-template-columns: 1fr; } }
        .sp-sidebar { background: #121212; border-radius: 8px; padding: 16px; }
        .sp-lib-card { background: #242424; border-radius: 8px; padding: 16px; margin-bottom: 12px; }
        .sp-main { background: linear-gradient(180deg, #113c28 0%, #121212 260px); border-radius: 8px; padding: 24px; }
        .sp-card { background: #181818; border-radius: 8px; padding: 14px; transition: 0.25s; display: block; text-decoration: none; color: #fff; height: 100%; }
        .sp-card:hover { background: #282828; color: #fff; }
        .sp-cover { width: 100%; aspect-ratio: 1/1; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; position: relative; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .sp-play-btn { position: absolute; bottom: 10px; right: 10px; width: 44px; height: 44px; border-radius: 50%; background: #1ed760; color: #000; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; opacity: 0; transition: 0.25s; }
        .sp-card:hover .sp-play-btn { opacity: 1; }
    </style>
</head>
<body>

<header class="sp-topbar d-flex align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="petugas.php" class="d-flex align-items-center gap-2 text-decoration-none" title="Aplikasi Hak Akses">
            <svg class="app-logo-badge" width="38" height="38" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
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
            <span class="fw-bold text-white d-none d-sm-inline" style="font-size: 1.15rem; letter-spacing: -0.3px;">Aplikasi <span style="color: #1ed760;">Hak Akses</span></span>
        </a>
    </div>
    <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-center">
        <a href="petugas.php" class="sp-home-btn"><i class="bi bi-house-door-fill"></i></a>
        <div class="sp-search-pill d-flex align-items-center gap-2 text-secondary">
            <i class="bi bi-search"></i>
            <span class="small">Area Kerja Petugas Operasional</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="badge rounded-pill bg-success text-black fw-bold px-3 py-2">PETUGAS</span>
        <a href="profil.php" class="text-white fw-bold small text-decoration-none"><?= htmlspecialchars($_SESSION['nama']) ?></a>
        <a href="logout.php" class="btn-pill-white">Logout</a>
    </div>
</header>

<div class="sp-wrapper">
    <aside class="sp-sidebar">
        <div class="fw-bold text-secondary mb-3"><i class="bi bi-collection-fill me-2"></i>Menu Petugas</div>
        <div class="sp-lib-card">
            <h6 class="fw-bold mb-1">Profil & Kata Sandi</h6>
            <p class="text-secondary small mb-3">Perbarui nama lengkap atau ubah password akun petugas Anda.</p>
            <a href="profil.php" class="btn-pill-white d-inline-block py-1 px-3 small">Buka Profil</a>
        </div>
        <div class="sp-lib-card">
            <h6 class="fw-bold mb-1">Batas Kewenangan</h6>
            <p class="text-secondary small mb-0">Petugas dapat mengelola operasional, namun ditolak (HTTP 403) jika membuka halaman Admin.</p>
        </div>
    </aside>

    <main class="sp-main">
        <h3 class="fw-extrabold mb-1" style="font-weight: 800;">Halo Petugas, <?= htmlspecialchars($_SESSION['nama']) ?>!</h3>
        <p class="text-secondary small mb-4">Pilih modul operasional atau uji pembatasan hak akses di bawah ini:</p>

        <div class="row row-cols-2 row-cols-md-4 g-3">
            <div class="col">
                <a href="petugas.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #1ed760, #0b5e28);">
                        <i class="bi bi-clipboard2-pulse-fill display-3 text-black"></i>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold">Data Operasional</div>
                    <div class="text-success small">Akses Diizinkan</div>
                </a>
            </div>
            <div class="col">
                <a href="profil.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #509bf5, #1d4273);">
                        <i class="bi bi-person-gear display-3 text-white"></i>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold">Pengaturan Akun</div>
                    <div class="text-secondary small">Ubah Nama & Sandi</div>
                </a>
            </div>
            <div class="col">
                <a href="admin.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #e91429, #5e0811);">
                        <i class="bi bi-shield-lock-fill display-3 text-white"></i>
                        <div class="sp-play-btn"><i class="bi bi-lock-fill"></i></div>
                    </div>
                    <div class="fw-bold">Uji Buka Admin</div>
                    <div class="text-danger small">Ditolak (HTTP 403)</div>
                </a>
            </div>
            <div class="col">
                <a href="siswa.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #f59b23, #6e4107);">
                        <i class="bi bi-mortarboard-fill display-3 text-white"></i>
                        <div class="sp-play-btn"><i class="bi bi-lock-fill"></i></div>
                    </div>
                    <div class="fw-bold">Uji Buka Siswa</div>
                    <div class="text-danger small">Ditolak (HTTP 403)</div>
                </a>
            </div>
        </div>
    </main>
</div>

</body>
</html>