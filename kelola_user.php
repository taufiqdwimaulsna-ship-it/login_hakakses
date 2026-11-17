<?php
require 'auth.php';
require 'config.php';
cekRole('admin');

$pesan = "";
$error = "";

if (isset($_POST['tambah_user'])) {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $cek = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
    $cek->execute([$username]);
    if ($cek->fetch()) {
        $error = "Username sudah terdaftar!";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $username, $password, $role]);
        $pesan = "Pengguna baru berhasil ditambahkan!";
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    if ($id_hapus !== (int) $_SESSION['user_id']) {
        $stmt = $koneksi->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_hapus]);
        header("Location: kelola_user.php");
        exit;
    }
}

$stmt = $koneksi->query("SELECT id, nama, username, role FROM users ORDER BY id ASC");
$daftar_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pengguna — Admin Playlist View</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Figtree', sans-serif; background-color: #000; color: #fff; }
        .sp-shell { background: linear-gradient(180deg, #1e3264 0%, #121212 280px); border-radius: 10px; padding: 28px; min-height: 95vh; }
        .sp-form-card { background-color: #181818; border-radius: 8px; padding: 20px; }
        .sp-input { background-color: #242424; border: 1px solid #535353; color: #fff; border-radius: 6px; padding: 9px 12px; }
        .sp-input:focus { background-color: #2a2a2a; border-color: #fff; color: #fff; box-shadow: none; }
        .btn-sp-green { background-color: #1ed760; color: #000; font-weight: 700; border-radius: 500px; padding: 10px 20px; border: none; }
        .btn-sp-green:hover { background-color: #3be477; color: #000; }
        .sp-track-row {
            padding: 10px 16px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .sp-track-row:hover { background-color: rgba(255, 255, 255, 0.1); }
        .sp-mini-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #282828;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #1ed760;
            overflow: hidden;
            position: relative;
        }
        .sp-mini-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body class="p-2 p-md-3">

<div class="container-fluid sp-shell">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 rounded-3 bg-success text-black fs-2 fw-bold shadow"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="small fw-bold text-uppercase">Halaman Khusus Administrator</div>
                <h2 class="fw-extrabold mb-0" style="font-weight: 800;">Daftar & Manajemen Pengguna</h2>
            </div>
        </div>
        <a href="admin.php" class="btn btn-outline-light rounded-pill px-4 fw-bold btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard Admin
        </a>
    </div>

    <?php if ($pesan): ?>
        <div class="alert bg-success bg-opacity-25 border-0 text-white rounded-3"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert bg-danger bg-opacity-25 border-0 text-white rounded-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Kolom Kiri: Tambah Akun -->
        <div class="col-lg-4">
            <div class="sp-form-card">
                <h5 class="fw-bold mb-3">+ Buat Akun Pengguna</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control sp-input" placeholder="Nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">Username</label>
                        <input type="text" name="username" class="form-control sp-input" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">Password</label>
                        <input type="password" name="password" class="form-control sp-input" placeholder="Password" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-secondary fw-bold">Role Pengguna</label>
                        <select name="role" class="form-select sp-input" required>
                            <option value="siswa">Siswa</option>
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_user" class="btn-sp-green w-100">Simpan Pengguna</button>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Pengguna Gaya Tracklist Spotify -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between text-secondary small fw-bold px-3 pb-2 border-bottom border-secondary border-opacity-25 mb-2">
                <span># &nbsp; NAMA & USERNAME</span>
                <span>ROLE & AKSI</span>
            </div>

            <?php
            $avatar_images = [
                'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&h=150&q=80',
                'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=150&h=150&q=80'
            ];
            foreach ($daftar_user as $index => $u):
                $userImg = $avatar_images[(abs((int)$u['id'] - 1)) % count($avatar_images)];
            ?>
            <div class="sp-track-row d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary fw-bold" style="width: 20px;"><?= $index + 1 ?></span>
                    <div class="sp-mini-avatar">
                        <img src="<?= htmlspecialchars($userImg) ?>" alt="<?= htmlspecialchars($u['nama']) ?>" onerror="this.onerror=null; this.src='https://i.pravatar.cc/150?u=<?= urlencode($u['username']) ?>'; this.onerror=function(){ this.style.display='none'; this.nextElementSibling.style.display='inline'; };">
                        <span style="display:none;"><?= strtoupper(substr($u['nama'], 0, 1)) ?></span>
                    </div>
                    <div>
                        <div class="fw-bold text-white"><?= htmlspecialchars($u['nama']) ?></div>
                        <div class="text-secondary small">@<?= htmlspecialchars($u['username']) ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge rounded-pill bg-dark border border-secondary text-uppercase px-3 py-1">
                        <?= htmlspecialchars($u['role']) ?>
                    </span>
                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                        <a href="kelola_user.php?hapus=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hapus pengguna ini?')">
                            Hapus
                        </a>
                    <?php else: ?>
                        <span class="text-success small fw-bold">Aktif</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>