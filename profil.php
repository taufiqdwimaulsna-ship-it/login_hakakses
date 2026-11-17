<?php
require 'auth.php';
require 'config.php';
cekLogin();

$pesan = "";
$error = "";

if (isset($_POST['ubah_nama'])) {
    $nama_baru = trim($_POST['nama'] ?? '');
    if ($nama_baru !== '') {
        $stmt = $koneksi->prepare("UPDATE users SET nama = ? WHERE id = ?");
        $stmt->execute([$nama_baru, $_SESSION['user_id']]);
        $_SESSION['nama'] = $nama_baru;
        $pesan = "Nama profil berhasil diperbarui!";
    } else {
        $error = "Nama tidak boleh kosong!";
    }
}

if (isset($_POST['ubah_password'])) {
    $pass_lama = $_POST['password_lama'] ?? '';
    $pass_baru = $_POST['password_baru'] ?? '';

    $stmt = $koneksi->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass_lama, $user['password'])) {
        $hash_baru = password_hash($pass_baru, PASSWORD_DEFAULT);
        $update = $koneksi->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hash_baru, $_SESSION['user_id']]);
        $pesan = "Password berhasil diperbarui!";
    } else {
        $error = "Password lama tidak sesuai!";
    }
}

$inisial = strtoupper(substr($_SESSION['nama'], 0, 2));

$stmt = $koneksi->prepare("SELECT foto FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userDB = $stmt->fetch(PDO::FETCH_ASSOC);
$currentFoto = $userDB['foto'] ?? null;

if (isset($_POST['ubah_foto'])) {
    $foto_baru = null;
    if (!empty($_POST['preset_avatar'])) {
        $foto_baru = trim($_POST['preset_avatar']);
    }
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload = simpanUploadFoto($_FILES['foto']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $foto_baru = $upload['filename'];
        }
    }
    if (!$error && $foto_baru !== null) {
        $stmt = $koneksi->prepare("UPDATE users SET foto = ? WHERE id = ?");
        $stmt->execute([$foto_baru, $_SESSION['user_id']]);
        $pesan = "Foto profil Anda berhasil diperbarui!";
        $currentFoto = $foto_baru;
    } elseif (!$error && $foto_baru === null) {
        $error = "Pilih file gambar atau salah satu foto preset!";
    }
}

if (isset($_POST['reset_foto_profil'])) {
    $stmt = $koneksi->prepare("UPDATE users SET foto = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $pesan = "Foto profil telah dikembalikan ke foto acak default!";
    $currentFoto = null;
}

$userImg = getFotoUser($currentFoto, $_SESSION['user_id'], $_SESSION['username']);
$presets_list = getAvatarPresets();
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pengguna — Aplikasi Hak Akses</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Figtree', sans-serif; background-color: #000; color: #fff; }
        .sp-profile-header {
            background: linear-gradient(180deg, #535353 0%, #121212 100%);
            border-radius: 10px 10px 0 0;
            padding: 36px 28px;
        }
        .sp-avatar-circle {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background-color: #282828;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            box-shadow: 0 12px 32px rgba(0,0,0,0.6);
            overflow: hidden;
            position: relative;
        }
        .sp-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .sp-body {
            background-color: #121212;
            border-radius: 0 0 10px 10px;
            padding: 28px;
        }
        .sp-panel {
            background-color: #181818;
            border-radius: 8px;
            padding: 24px;
            height: 100%;
        }
        .sp-input {
            background-color: #242424;
            border: 1px solid #535353;
            color: #fff;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .sp-input:focus { background-color: #2a2a2a; border-color: #fff; color: #fff; box-shadow: none; }
        .btn-sp-green {
            background-color: #1ed760;
            color: #000;
            font-weight: 700;
            border-radius: 500px;
            padding: 10px 24px;
            border: none;
        }
        .btn-sp-green:hover { background-color: #3be477; color: #000; }
    </style>
</head>
<body class="p-2 p-md-3">

<div class="container" style="max-width: 960px;">
    <div class="sp-profile-header d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3">
        <div class="d-flex flex-column flex-sm-row align-items-sm-end gap-4">
            <div class="sp-avatar-circle">
                <img src="<?= htmlspecialchars($userImg) ?>" alt="<?= htmlspecialchars($_SESSION['nama']) ?>" onerror="this.onerror=null; this.src='https://i.pravatar.cc/300?u=<?= urlencode($_SESSION['username']) ?>'; this.onerror=function(){ this.style.display='none'; this.nextElementSibling.style.display='flex'; };">
                <div class="text-success fw-bold fs-2" style="display: none; width:100%; height:100%; align-items:center; justify-content:center;"><?= htmlspecialchars($inisial) ?></div>
            </div>
            <div>
                <div class="small fw-bold text-uppercase"><i class="bi bi-patch-check-fill text-info me-1"></i> Profil Terautentikasi</div>
                <h1 class="display-5 fw-extrabold mb-1" style="font-weight: 800;"><?= htmlspecialchars($_SESSION['nama']) ?></h1>
                <div class="text-secondary small">Username: <b>@<?= htmlspecialchars($_SESSION['username']) ?></b> &bull; Role: <span class="text-white text-uppercase fw-bold"><?= htmlspecialchars($_SESSION['role']) ?></span></div>
            </div>
        </div>
        <a href="dashboard.php" class="btn btn-outline-light rounded-pill px-4 fw-bold btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="sp-body">
        <?php if ($pesan): ?>
            <div class="alert bg-success bg-opacity-25 border-0 text-white rounded-3"><?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert bg-danger bg-opacity-25 border-0 text-white rounded-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Panel 1: Ubah Foto Profil -->
            <div class="col-12">
                <div class="sp-panel">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="bi bi-camera-fill text-success me-2"></i>Ubah Foto Profil</h5>
                            <p class="text-secondary small mb-0">Unggah foto baru dari komputer Anda atau pilih salah satu foto avatar cepat di bawah.</p>
                        </div>
                        <?php if (!empty($currentFoto)): ?>
                            <form method="POST" class="m-0">
                                <button type="submit" name="reset_foto_profil" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Kembalikan foto profil Anda ke foto acak default?')">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Foto Acak
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="preset_avatar" id="profilPresetAvatar" value="">
                        
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <img id="profilPreviewImg" src="<?= htmlspecialchars($userImg) ?>" alt="Preview" class="rounded-circle shadow" style="width: 72px; height: 72px; object-fit: cover; border: 3px solid #1ed760;">
                            </div>
                            <div class="col">
                                <label class="form-label small text-secondary fw-bold">Pilih File Foto Baru dari Komputer:</label>
                                <input type="file" name="foto" id="inputFotoProfil" class="form-control sp-input" accept="image/jpeg,image/png,image/webp" onchange="previewProfilFile(this)">
                                <div class="form-text text-secondary" style="font-size: 0.72rem;">Format: JPG, PNG, WEBP (Maks 3MB).</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-bold">Atau Pilih dari Koleksi Foto Avatar Cepat:</label>
                            <div class="d-flex flex-wrap gap-2 p-2 rounded-3" style="background-color: #121216;">
                                <?php foreach ($presets_list as $idx => $preset): ?>
                                    <img src="<?= $preset ?>" alt="Avatar <?= $idx+1 ?>" class="profil-preset-choice rounded-circle" style="width: 44px; height: 44px; object-fit: cover; cursor: pointer; border: 2px solid transparent; transition: all 0.2s;" onclick="pilihPresetProfil(this, '<?= $preset ?>')" title="Pilih foto ini">
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" name="ubah_foto" class="btn-sp-green">Simpan Foto Profil</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sp-panel">
                    <h5 class="fw-bold mb-3">Ubah Nama Pengguna</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control sp-input" value="<?= htmlspecialchars($_SESSION['nama']) ?>" required>
                        </div>
                        <button type="submit" name="ubah_nama" class="btn-sp-green">Simpan Nama</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sp-panel">
                    <h5 class="fw-bold mb-3">Ubah Password</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-bold">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control sp-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-bold">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control sp-input" required>
                        </div>
                        <button type="submit" name="ubah_password" class="btn-sp-green">Perbarui Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function pilihPresetProfil(imgEl, url) {
    document.getElementById('profilPresetAvatar').value = url;
    document.getElementById('profilPreviewImg').src = url;
    document.getElementById('inputFotoProfil').value = '';
    
    document.querySelectorAll('.profil-preset-choice').forEach(el => {
        el.style.borderColor = 'transparent';
        el.style.transform = 'scale(1)';
    });
    imgEl.style.borderColor = '#1ed760';
    imgEl.style.transform = 'scale(1.15)';
}

function previewProfilFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilPreviewImg').src = e.target.result;
            document.getElementById('profilPresetAvatar').value = '';
            document.querySelectorAll('.profil-preset-choice').forEach(el => {
                el.style.borderColor = 'transparent';
                el.style.transform = 'scale(1)';
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>