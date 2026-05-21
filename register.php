<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$error  = "";
$sukses = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($username) || empty($password)) {
        $error = "Semua kolom wajib diisi!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $email_aman = mysqli_real_escape_string($koneksi, $email);
        $cek_hasil  = mysqli_query($koneksi, "SELECT id FROM users WHERE email = '$email_aman'");

        if (mysqli_num_rows($cek_hasil) > 0) {
            $error = "Email sudah terdaftar! Gunakan email lain.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $username_aman = mysqli_real_escape_string($koneksi, $username);

            $sql = "INSERT INTO users (email, username, password)
                    VALUES ('$email_aman', '$username_aman', '$password_hash')";

            if (mysqli_query($koneksi, $sql)) {
                $sukses = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan, coba lagi.";
            }
        }
    }
}

// Cari gambar background yang tersedia
$bg_images = [
    'uploads/background.jpg',
    'uploads/1778555909_bali.jpg',
    'uploads/1778551955_ntt.jpg',
    'uploads/1778551964_jogja.jpg'
];
$bg_image = '';
foreach ($bg_images as $img) {
    if (file_exists($img)) {
        $bg_image = $img;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Nusantara Journey</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="auth-page">

    <!-- Background foto pemandangan dengan inline fallback -->
    <div class="auth-bg" style="<?= $bg_image ? "background-image: url('$bg_image');" : '' ?>"></div>

    <!-- Wave curve putih di bawah -->
    <div class="auth-wave">
        <svg viewBox="0 0 1440 180" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path fill="#ffffff"
                d="M0,120 C240,180 480,60 720,100 C960,140 1200,40 1440,100 L1440,180 L0,180 Z"/>
        </svg>
    </div>

    <!-- Judul Brand -->
    <div class="auth-brand-title">
        <h1>Nusantara Journey</h1>
    </div>

    <!-- Form Register Card -->
    <div class="auth-card">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($sukses): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sukses) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="auth-form">
            <div class="form-group">
                <input type="email" id="email" name="email"
                       placeholder="Email" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <input type="text" id="username" name="username"
                       placeholder="Username" required
                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password"
                       placeholder="Password" required>
            </div>

            <button type="submit" class="btn-submit">Create</button>
        </form>

        <div class="auth-switch">
            <p>Sudah punya akun? <a href="login.php">Click ini</a></p>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>