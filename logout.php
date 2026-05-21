<?php
// ============================================================
// ADMIN/LOGOUT.PHP - Logout Admin
// Redirect ke login.php di root (bukan admin/login.php)
// ============================================================

session_start();

// Hapus semua session (admin + user)
$_SESSION = array();

// Hapus cookie session jika ada
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Hancurkan session
session_destroy();

// Redirect ke login.php di ROOT folder
header("Location: login.php");
exit();