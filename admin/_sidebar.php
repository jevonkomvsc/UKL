<?php
// ============================================================
// ADMIN/_SIDEBAR.PHP - Komponen Sidebar
// Set $halaman_aktif sebelum include ini
// ============================================================

if (!isset($halaman_aktif)) $halaman_aktif = 'dashboard';
?>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <!-- Logo -->
        <div class="sidebar-brand">
            <div>
                <div class="sidebar-title">Nusantara</div>
                <div class="sidebar-sub">Admin Panel</div>
            </div>
        </div>

        <!-- Info Admin -->
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <?= strtoupper(substr($_SESSION['admin_username'], 0, 1)) ?>
            </div>
            <div>
                <div class="sidebar-username"><?= htmlspecialchars($_SESSION['admin_username']) ?></div>
                <div class="sidebar-role">Administrator</div>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item <?= $halaman_aktif === 'dashboard' ? 'aktif' : '' ?>">
                <span>Dashboard</span>
            </a>
            <a href="provinsi.php" class="nav-item <?= $halaman_aktif === 'provinsi' ? 'aktif' : '' ?>">
                <span>Kelola Provinsi</span>
            </a>
            <a href="destinasi.php" class="nav-item <?= $halaman_aktif === 'destinasi' ? 'aktif' : '' ?>">
                <span>Kelola Destinasi</span>
            </a>
            <a href="user.php" class="nav-item <?= $halaman_aktif === 'user' ? 'aktif' : '' ?>">
                <span>Kelola User</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="sidebar-footer">
            <a href="logout.php" class="nav-item nav-logout"
               onclick="return confirm('Yakin mau logout?')">
                <span>Logout</span>
            </a>
        </div>

    </aside>

    <!-- AREA KONTEN UTAMA -->
    <main class="admin-main">
