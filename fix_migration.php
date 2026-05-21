<?php
// ============================================================
// fix_migration.php - Jalankan sekali untuk memperbaiki data
// ============================================================
require_once 'koneksi.php';

echo "<h2>Migrasi Data Nusantara Journey</h2>";

// 1. Update destinasi yang belum punya provinsi
echo "<h3>1. Update provinsi_id destinasi...</h3>";

// Update berdasarkan nama yang mirip
mysqli_query($koneksi, "UPDATE destinasi d JOIN provinsi p ON d.nama_destinasi LIKE CONCAT('%', p.nama_provinsi, '%') OR p.nama_provinsi LIKE CONCAT('%', d.nama_destinasi, '%') SET d.provinsi_id = p.id WHERE d.provinsi_id IS NULL OR d.provinsi_id = 0");

// Update manual untuk destinasi spesifik
$updates = [
    [1, '%Bali%'],
    [2, '%Jogja%'],
    [2, '%Borobudur%'],
    [3, '%Raja Ampat%'],
    [3, '%Komodo%'],
    [4, '%Bromo%'],
    [5, '%Lombok%'],
];

foreach ($updates as $upd) {
    $prov_id = $upd[0];
    $pattern = $upd[1];
    mysqli_query($koneksi, "UPDATE destinasi SET provinsi_id = $prov_id WHERE (provinsi_id IS NULL OR provinsi_id = 0) AND nama_destinasi LIKE '$pattern'");
}

// 2. Update data provinsi
$provinsi_data = [
    ['Bali', 'Pulau Bali', 'Denpasar', '4.3 Juta'],
    ['DI Yogyakarta', 'Pulau Jawa', 'Yogyakarta', '3.7 Juta'],
    ['Papua Barat', 'Pulau Papua', 'Jayapura', '5.4 Juta'],
    ['Jawa Timur', 'Pulau Jawa', 'Surabaya', '40 Juta'],
    ['NTB', 'Pulau Lombok', 'Mataram', '3.4 Juta'],
];

echo "<h3>2. Update detail provinsi...</h3>";
foreach ($provinsi_data as $p) {
    $nama = mysqli_real_escape_string($koneksi, $p[0]);
    $pulau = mysqli_real_escape_string($koneksi, $p[1]);
    $ibu_kota = mysqli_real_escape_string($koneksi, $p[2]);
    $populasi = mysqli_real_escape_string($koneksi, $p[3]);
    mysqli_query($koneksi, "UPDATE provinsi SET pulau='$pulau', ibu_kota='$ibu_kota', populasi='$populasi' WHERE nama_provinsi='$nama'");
}

// 3. Tampilkan hasil
echo "<h3>3. Hasil:</h3>";
$q = mysqli_query($koneksi, "SELECT d.id, d.nama_destinasi, d.provinsi_id, p.nama_provinsi FROM destinasi d LEFT JOIN provinsi p ON d.provinsi_id = p.id");
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Destinasi</th><th>Provinsi ID</th><th>Provinsi</th></tr>";
while ($row = mysqli_fetch_assoc($q)) {
    echo "<tr><td>{$row['id']}</td><td>{$row['nama_destinasi']}</td><td>{$row['provinsi_id']}</td><td>" . ($row['nama_provinsi'] ?? 'TANPA PROVINSI') . "</td></tr>";
}
echo "</table>";

echo "<p><strong>Selesai!</strong> <a href='admin/destinasi.php'>Kembali ke Admin</a></p>";