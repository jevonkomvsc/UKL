<?php
session_start();
require_once 'koneksi.php';

// Jika sudah login sebagai user, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

// Jika sudah login sebagai admin, redirect ke admin panel
if (isset($_SESSION['admin_login'])) {
    header("Location: admin/index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {
        $email_aman = mysqli_real_escape_string($koneksi, $email);

        // Cari user berdasarkan email ATAU username (untuk admin yang login pakai username)
        $query = "SELECT * FROM users WHERE email = '$email_aman' OR username = '$email_aman'";
        $hasil = mysqli_query($koneksi, $query);
        $user  = mysqli_fetch_assoc($hasil);

        if ($user && password_verify($password, $user['password'])) {
            // Cek role user
            $role = $user['role'] ?? 'user'; // Default ke 'user' kalau kolom role tidak ada

            if ($role === 'admin') {
                // Login sebagai admin
                $_SESSION['admin_login']    = true;
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_id']       = $user['id'];
                header("Location: admin/index.php");
                exit();
            } else {
                // Login sebagai user biasa
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'user/dashboard.php';
                header("Location: " . $redirect);
                exit();
            }
        } else {
            $error = "Email atau password salah!";
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
    <title>Login - Nusantara Journey</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body class="auth-page">

    <!-- Background foto pemandangan dengan inline fallback -->
    <div class="auth-bg" style="<?= $bg_image ? "background-image: url('$bg_image');" : '' ?>"></div>

    <!-- Wave curve putih di bawah -->
    <div class="auth-wave">
        <svg viewBox="0 0 1440 180" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path fill="#ffffff"
                d="M0,120 C240,180 480,60 720,100 C960,140 1200,40 1440,100 L1440,180 L0,180 Z" />
        </svg>
    </div>

    <!-- Judul Brand -->
    <div class="auth-brand-title">
        <h1>Nusantara Journey</h1>
    </div>

    <!-- Form Login Card -->
    <div class="auth-card">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="auth-form">
            <div class="form-group">
                <input type="text" id="email" name="email"
                    placeholder="Email atau Username" required
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password"
                    placeholder="Password" required>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="auth-switch">
            <p>Belum punya akun? <a href="register.php">Click ini</a></p>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>