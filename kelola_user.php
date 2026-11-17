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
    $foto     = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload = simpanUploadFoto($_FILES['foto']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $foto = $upload['filename'];
        }
    }

    if (!$error) {
        $cek = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
        $cek->execute([$username]);
        if ($cek->fetch()) {
            $error = "Username sudah terdaftar!";
        } else {
            $stmt = $koneksi->prepare("INSERT INTO users (nama, username, password, role, foto) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $username, $password, $role, $foto]);
            $pesan = "Pengguna baru berhasil ditambahkan!";
        }
    }
}

// Handler ganti foto pengguna oleh admin
if (isset($_POST['ganti_foto'])) {
    $id_user = (int) $_POST['id_user'];
    $foto_baru = null;

    if (!empty($_POST['preset_avatar'])) {
        $foto_baru = trim($_POST['preset_avatar']);
    }

    if (isset($_FILES['foto_edit']) && $_FILES['foto_edit']['error'] === UPLOAD_ERR_OK) {
        $upload = simpanUploadFoto($_FILES['foto_edit']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $foto_baru = $upload['filename'];
        }
    }

    if (!$error && $foto_baru !== null) {
        $stmt = $koneksi->prepare("UPDATE users SET foto = ? WHERE id = ?");
        $stmt->execute([$foto_baru, $id_user]);
        $pesan = "Foto profil pengguna berhasil diperbarui!";
    } elseif (!$error && $foto_baru === null) {
        $error = "Pilih file gambar atau salah satu preset foto!";
    }
}

// Handler reset foto ke acak
if (isset($_POST['reset_foto'])) {
    $id_user = (int) $_POST['id_user'];
    $stmt = $koneksi->prepare("UPDATE users SET foto = NULL WHERE id = ?");
    $stmt->execute([$id_user]);
    $pesan = "Foto profil pengguna telah direset ke foto acak default!";
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

$stmt = $koneksi->query("SELECT id, nama, username, role, foto FROM users ORDER BY id ASC");
$daftar_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
$presets_list = getAvatarPresets();
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
                <form method="POST" enctype="multipart/form-data">
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
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">Role Pengguna</label>
                        <select name="role" class="form-select sp-input" required>
                            <option value="siswa">Siswa</option>
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-secondary fw-bold">Foto Profil (Opsional)</label>
                        <input type="file" name="foto" class="form-control sp-input" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text text-secondary" style="font-size: 0.72rem;">Unggah foto JPG/PNG (Maks 3MB). Jika kosong, foto acak akan dibuat otomatis.</div>
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
            foreach ($daftar_user as $index => $u):
                $userImg = getFotoUser($u['foto'] ?? '', $u['id'], $u['username']);
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
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-dark border border-secondary text-uppercase px-3 py-1">
                        <?= htmlspecialchars($u['role']) ?>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="bukaModalFoto(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nama'])) ?>', '<?= htmlspecialchars($userImg) ?>')">
                        <i class="bi bi-camera-fill me-1"></i> Ganti Foto
                    </button>
                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                        <a href="kelola_user.php?hapus=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hapus pengguna ini?')">
                            Hapus
                        </a>
                    <?php else: ?>
                        <span class="text-success small fw-bold px-2">Aktif</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Ganti Foto Pengguna -->
<div class="modal fade" id="modalGantiFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #18181f; border: 1px solid #333; color: #fff; border-radius: 14px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold">Ganti Foto Profil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_user" id="modalIdUser">
                <input type="hidden" name="preset_avatar" id="modalPresetAvatar" value="">
                
                <div class="modal-body text-center p-4">
                    <!-- Preview Foto Saat Ini -->
                    <div class="mb-3">
                        <img id="modalPreviewImg" src="" alt="Preview" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #1ed760;">
                        <div class="fw-bold mt-2 text-white" id="modalUserName"></div>
                    </div>

                    <!-- Opsi 1: Upload File Baru dari Komputer -->
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-secondary">Unggah File Foto Baru:</label>
                        <input type="file" name="foto_edit" id="inputFotoEdit" class="form-control sp-input" accept="image/jpeg,image/png,image/webp" onchange="previewFile(this)">
                        <div class="form-text text-secondary" style="font-size: 0.72rem;">Format: JPG, PNG, WEBP (Maks 3MB).</div>
                    </div>

                    <!-- Opsi 2: Pilih Preset Avatar Cepat -->
                    <div class="text-start mb-2">
                        <label class="form-label small fw-bold text-secondary">Atau Pilih dari Koleksi Avatar Cepat:</label>
                        <div class="d-flex flex-wrap gap-2 justify-content-center p-2 rounded-3" style="background-color: #121216;">
                            <?php foreach ($presets_list as $idx => $preset): ?>
                                <img src="<?= $preset ?>" alt="Avatar <?= $idx+1 ?>" class="preset-choice rounded-circle" style="width: 44px; height: 44px; object-fit: cover; cursor: pointer; border: 2px solid transparent; transition: all 0.2s;" onclick="pilihPreset(this, '<?= $preset ?>')" title="Pilih foto ini">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-secondary border-opacity-25 d-flex justify-content-between">
                    <button type="submit" name="reset_foto" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Kembalikan foto pengguna ini ke foto acak default?')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Foto Acak
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="ganti_foto" class="btn-sp-green btn-sm px-4">Simpan Foto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let modalFotoInstance = null;
function bukaModalFoto(id, nama, fotoUrl) {
    document.getElementById('modalIdUser').value = id;
    document.getElementById('modalUserName').innerText = nama;
    document.getElementById('modalPreviewImg').src = fotoUrl;
    document.getElementById('modalPresetAvatar').value = '';
    document.getElementById('inputFotoEdit').value = '';
    
    document.querySelectorAll('.preset-choice').forEach(el => {
        el.style.borderColor = 'transparent';
        el.style.transform = 'scale(1)';
    });

    if (!modalFotoInstance) {
        modalFotoInstance = new bootstrap.Modal(document.getElementById('modalGantiFoto'));
    }
    modalFotoInstance.show();
}

function pilihPreset(imgEl, url) {
    document.getElementById('modalPresetAvatar').value = url;
    document.getElementById('modalPreviewImg').src = url;
    document.getElementById('inputFotoEdit').value = '';
    
    document.querySelectorAll('.preset-choice').forEach(el => {
        el.style.borderColor = 'transparent';
        el.style.transform = 'scale(1)';
    });
    imgEl.style.borderColor = '#1ed760';
    imgEl.style.transform = 'scale(1.15)';
}

function previewFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('modalPreviewImg').src = e.target.result;
            document.getElementById('modalPresetAvatar').value = '';
            document.querySelectorAll('.preset-choice').forEach(el => {
                el.style.borderColor = 'transparent';
                el.style.transform = 'scale(1)';
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
    </div>
</div>

</body>
</html>