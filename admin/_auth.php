<?php
// ============================================================
// ADMIN/_AUTH.PHP - Penjaga Halaman Admin
// Sertakan file ini di SEMUA halaman admin dengan:
//   require_once '_auth.php';
// Fungsi: Jika belum login sebagai admin → redirect ke login root
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    // Redirect ke login.php di ROOT folder (bukan admin/login.php)
    header("Location: ../login.php");
    exit();
}
