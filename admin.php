<?php
require 'auth.php';
require 'config.php';
cekRole('admin');

// Ambil daftar pengguna dari database untuk ditampilkan di bagian "Pengguna Terdaftar (Gaya Artis Populer)"
$stmt = $koneksi->query("SELECT id, nama, username, role FROM users ORDER BY id ASC");
$daftar_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_user    = count($daftar_user);
$total_admin   = $koneksi->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$total_petugas = $koneksi->query("SELECT COUNT(*) FROM users WHERE role='petugas'")->fetchColumn();
$total_siswa   = $koneksi->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — Aplikasi Hak Akses</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #000000;
            color: #ffffff;
            margin: 0;
            overflow-x: hidden;
        }
        /* Topbar */
        .sp-topbar {
            height: 64px;
            background-color: #000000;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .app-logo-badge {
            transition: transform 0.25s ease, filter 0.25s ease;
            filter: drop-shadow(0 2px 10px rgba(30, 215, 96, 0.35));
        }
        .app-logo-badge:hover {
            transform: scale(1.08);
            filter: drop-shadow(0 4px 16px rgba(30, 215, 96, 0.6));
        }
        .sp-home-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #1f1f1f;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            text-decoration: none;
            transition: transform 0.15s;
        }
        .sp-home-btn:hover { transform: scale(1.05); color: #fff; background-color: #2a2a2a; }
        .sp-search-pill {
            background-color: #1f1f1f;
            border-radius: 500px;
            height: 48px;
            padding: 0 18px;
            width: 100%;
            max-width: 440px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .sp-search-pill:focus-within {
            background-color: #242424;
            border-color: #ffffff;
        }
        .sp-search-pill input {
            background: transparent;
            border: none;
            color: #fff;
            width: 100%;
            outline: none;
            font-size: 0.95rem;
        }
        .btn-pill-white {
            background-color: #ffffff;
            color: #000000;
            font-weight: 700;
            border-radius: 500px;
            padding: 8px 22px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: transform 0.15s;
        }
        .btn-pill-white:hover { transform: scale(1.04); color: #000; }

        /* Layout Kiri & Kanan */
        .sp-wrapper {
            display: grid;
            grid-template-columns: 310px 1fr;
            gap: 8px;
            padding: 0 8px 8px 8px;
            min-height: calc(100vh - 72px);
        }
        @media (max-width: 991px) {
            .sp-wrapper { grid-template-columns: 1fr; }
        }
        .sp-sidebar {
            background-color: #121212;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sp-lib-card {
            background-color: #242424;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
        }
        .sp-main {
            background: linear-gradient(180deg, #1f1f1f 0%, #121212 260px);
            border-radius: 8px;
            padding: 24px;
        }

        /* Kartu Album / Modul (Kotak) */
        .sp-card {
            background-color: #181818;
            border-radius: 8px;
            padding: 14px;
            transition: background-color 0.25s ease;
            position: relative;
            height: 100%;
            text-decoration: none;
            color: #fff;
            display: block;
        }
        .sp-card:hover { background-color: #282828; color: #fff; }
        .sp-cover {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }
        .sp-play-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background-color: #1ed760;
            color: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            opacity: 0;
            transform: translateY(8px);
            transition: all 0.25s ease;
            box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        }
        .sp-card:hover .sp-play-btn {
            opacity: 1;
            transform: translateY(0);
        }

        /* Kartu Artis / Pengguna (Lingkaran) */
        .sp-artist-avatar {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
            background-color: #282828;
        }
        .sp-artist-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }
        .sp-card:hover .sp-artist-avatar img {
            transform: scale(1.08);
        }

        /* Banner Bawah ala Cookie Banner Spotify */
        .sp-bottom-banner {
            background: linear-gradient(90deg, #af2896, #509bf5);
            border-radius: 8px;
            padding: 12px 20px;
            margin-top: 28px;
        }
    </style>
</head>
<body>

<!-- TOPBAR APLIKASI HAK AKSES -->
<header class="sp-topbar d-flex align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="admin.php" class="d-flex align-items-center gap-2 text-decoration-none" title="Aplikasi Hak Akses">
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
        <a href="admin.php" class="sp-home-btn" title="Beranda"><i class="bi bi-house-door-fill"></i></a>
        <div class="sp-search-pill d-flex align-items-center gap-2">
            <i class="bi bi-search text-secondary fs-5"></i>
            <input type="text" id="globalSearch" onkeyup="cariKonten()" placeholder="Apa yang ingin kamu kelola hari ini?">
            <i class="bi bi-folder2-open text-secondary fs-5 ps-2 border-start border-secondary"></i>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <span class="text-secondary small fw-bold d-none d-md-inline">Role: ADMIN</span>
        <a href="profil.php" class="text-secondary text-decoration-none fw-bold small d-none d-sm-inline hover-white">
            <?= htmlspecialchars($_SESSION['nama']) ?>
        </a>
        <a href="logout.php" class="btn-pill-white">Logout</a>
    </div>
</header>

<!-- WRAPPER KIRI & KANAN -->
<div class="sp-wrapper">

    <!-- SIDEBAR KIRI: "KOLEKSI KAMU" -->
    <aside class="sp-sidebar">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                <div class="fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-collection-fill fs-5"></i> Koleksi Sistem
                </div>
                <a href="kelola_user.php" class="btn btn-sm rounded-pill bg-dark text-white fw-bold px-3 border border-secondary border-opacity-25">
                    + Buat Akun
                </a>
            </div>

            <!-- Box 1: Buat Pengguna Baru (Mirip "Buat playlist pertamamu") -->
            <div class="sp-lib-card">
                <h6 class="fw-bold mb-1">Tambah pengguna baru</h6>
                <p class="text-secondary small mb-3">Kelola hak akses Admin, Petugas, dan Siswa dengan mudah.</p>
                <a href="kelola_user.php" class="btn-pill-white d-inline-block py-1 px-3 small">Kelola Pengguna</a>
            </div>

            <!-- Box 2: Keamanan & Sandi (Mirip "Ayo cari beberapa podcast") -->
            <div class="sp-lib-card">
                <h6 class="fw-bold mb-1">Keamanan & Kata Sandi</h6>
                <p class="text-secondary small mb-3">Akun diamankan dengan fungsi <code>password_hash()</code> & PDO.</p>
                <a href="profil.php" class="btn-pill-white d-inline-block py-1 px-3 small">Pengaturan Profil</a>
            </div>
        </div>

        <!-- Footer Sidebar -->
        <div class="pt-3 border-top border-secondary border-opacity-25">
            <div class="d-flex flex-wrap gap-2 text-secondary mb-3" style="font-size: 0.72rem;">
                <span>PHP Native</span> &bull; <span>MySQL PDO</span> &bull; <span>Session Auth</span> &bull; <span>HTTP 403 Guard</span>
            </div>
            <span class="badge rounded-pill border border-secondary text-white px-3 py-2">
                <i class="bi bi-globe me-1"></i> Bahasa Indonesia
            </span>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN -->
    <main class="sp-main">

        <!-- BARIS 1: MODUL & STATISTIK (GAYA "LAGU TRENDING") -->
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h4 class="fw-extrabold mb-0" style="font-weight: 800;">Modul & Statistik Utama</h4>
                <span class="text-secondary small">Selamat datang kembali, <b><?= htmlspecialchars($_SESSION['nama']) ?></b></span>
            </div>
            <a href="kelola_user.php" class="text-secondary small fw-bold text-decoration-none">Tampilkan semua</a>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-3 mb-5" id="modulGrid">
            <!-- Card 1 -->
            <div class="col item-searchable">
                <a href="kelola_user.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #1ed760, #0d5c2a);">
                        <span class="display-5 fw-extrabold text-black"><?= $total_user ?></span>
                        <span class="badge bg-black text-white mt-1">TOTAL AKUN</span>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold text-truncate">Manajemen User</div>
                    <div class="text-secondary small text-truncate">CRUD Seluruh Akun</div>
                </a>
            </div>

            <!-- Card 2 -->
            <div class="col item-searchable">
                <a href="kelola_user.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #e91429, #6b0913);">
                        <span class="display-5 fw-extrabold text-white"><?= $total_admin ?></span>
                        <span class="badge bg-black text-white mt-1">ROLE ADMIN</span>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold text-truncate">Administrator</div>
                    <div class="text-secondary small text-truncate">Akses Penuh Sistem</div>
                </a>
            </div>

            <!-- Card 3 -->
            <div class="col item-searchable">
                <a href="kelola_user.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #27856a, #0f382c);">
                        <span class="display-5 fw-extrabold text-white"><?= $total_petugas ?></span>
                        <span class="badge bg-black text-white mt-1">ROLE PETUGAS</span>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold text-truncate">Staf Petugas</div>
                    <div class="text-secondary small text-truncate">Data Operasional</div>
                </a>
            </div>

            <!-- Card 4 -->
            <div class="col item-searchable">
                <a href="kelola_user.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #f59b23, #7a4605);">
                        <span class="display-5 fw-extrabold text-black"><?= $total_siswa ?></span>
                        <span class="badge bg-black text-white mt-1">ROLE SISWA</span>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold text-truncate">Akun Siswa</div>
                    <div class="text-secondary small text-truncate">Data Akademik Pribadi</div>
                </a>
            </div>

            <!-- Card 5 -->
            <div class="col item-searchable">
                <a href="profil.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #8d67ab, #3b234f);">
                        <i class="bi bi-person-badge-fill display-4 text-white"></i>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold text-truncate">Profil & Sandi</div>
                    <div class="text-secondary small text-truncate">@<?= htmlspecialchars($_SESSION['username']) ?></div>
                </a>
            </div>

            <!-- Card 6 -->
            <div class="col item-searchable">
                <a href="petugas.php" class="sp-card">
                    <div class="sp-cover" style="background: linear-gradient(135deg, #509bf5, #1b4375);">
                        <i class="bi bi-shield-slash-fill display-4 text-white"></i>
                        <div class="sp-play-btn"><i class="bi bi-play-fill"></i></div>
                    </div>
                    <div class="fw-bold text-truncate">Uji Proteksi 403</div>
                    <div class="text-secondary small text-truncate">Simulasi Cek Role</div>
                </a>
            </div>
        </div>

        <!-- BARIS 2: PENGGUNA TERDAFTAR (GAYA "ARTIS POPULER" LINGKARAN) -->
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h4 class="fw-extrabold mb-0" style="font-weight: 800;">Pengguna terdaftar di database</h4>
            <a href="kelola_user.php" class="text-secondary small fw-bold text-decoration-none">Kelola semua</a>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-3">
            <?php
            $avatar_images = [
                'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=300&h=300&q=80',
                'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=300&h=300&q=80'
            ];
            $gradients = [
                'admin'   => 'linear-gradient(135deg, #e91429, #8c1932)',
                'petugas' => 'linear-gradient(135deg, #1ed760, #117a37)',
                'siswa'   => 'linear-gradient(135deg, #f59b23, #b85d19)'
            ];
            foreach ($daftar_user as $u):
                $inisial = strtoupper(substr($u['nama'], 0, 2));
                $bgGrad = $gradients[$u['role']] ?? 'linear-gradient(135deg, #509bf5, #2b568a)';
                $userImg = $avatar_images[(abs((int)$u['id'] - 1)) % count($avatar_images)];
            ?>
            <div class="col item-searchable">
                <a href="kelola_user.php" class="sp-card">
                    <div class="sp-artist-avatar text-white">
                        <img src="<?= htmlspecialchars($userImg) ?>" alt="<?= htmlspecialchars($u['nama']) ?>" onerror="this.onerror=null; this.src='https://i.pravatar.cc/300?u=<?= urlencode($u['username']) ?>'; this.onerror=function(){ this.style.display='none'; this.nextElementSibling.style.display='flex'; };">
                        <div class="sp-avatar-fallback text-white fw-bold" style="background: <?= $bgGrad ?>; display: none; width: 100%; height: 100%; align-items: center; justify-content: center; position: absolute; top:0; left:0;">
                            <?= htmlspecialchars($inisial) ?>
                        </div>
                        <div class="sp-play-btn"><i class="bi bi-gear-fill fs-5"></i></div>
                    </div>
                    <div class="fw-bold text-truncate"><?= htmlspecialchars($u['nama']) ?></div>
                    <div class="text-secondary small text-capitalize">Role: <?= htmlspecialchars($u['role']) ?></div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Banner Status Keamanan di Bawah -->
        <div class="sp-bottom-banner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <div class="fw-bold small text-uppercase">Status Keamanan Aplikasi Aktif</div>
                <div class="small opacity-90">Menggunakan Session PHP, Prepared Statement PDO, Password Hashing, dan validasi <code>cekRole('admin')</code> di sisi server.</div>
            </div>
            <a href="kelola_user.php" class="btn-pill-white text-nowrap">Buka Tabel User</a>
        </div>

    </main>
</div>

<script>
function cariKonten() {
    const q = document.getElementById('globalSearch').value.toLowerCase();
    document.querySelectorAll('.item-searchable').forEach(el => {
        el.style.display = el.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>