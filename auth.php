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

function getAvatarPresets() {
    return [
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
}

function getFotoUser($foto, $id = 0, $username = '') {
    if (!empty($foto)) {
        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
            return $foto;
        }
        if (file_exists(__DIR__ . '/uploads/' . $foto)) {
            return 'uploads/' . $foto;
        }
    }
    $presets = getAvatarPresets();
    $idx = abs((int)$id - 1) % count($presets);
    return $presets[$idx];
}

function simpanUploadFoto($file) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_exts)) {
        return ['error' => 'Format file harus JPG, PNG, atau WEBP!'];
    }
    
    if ($file['size'] > 3 * 1024 * 1024) {
        return ['error' => 'Ukuran foto maksimal 3MB!'];
    }
    
    $target_dir = __DIR__ . '/uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $file_name = 'avatar_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
    if (move_uploaded_file($file['tmp_name'], $target_dir . $file_name)) {
        return ['filename' => $file_name];
    }
    return ['error' => 'Gagal mengunggah foto ke server.'];
}
?>