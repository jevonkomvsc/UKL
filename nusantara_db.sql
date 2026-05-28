-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Bulan Mei 2026 pada 00.24
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nusantara_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `destinasi_id` int(11) NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `jumlah_tiket` int(11) NOT NULL DEFAULT 1,
  `total_harga` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `email_pemesan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `status` enum('pending','paid','cancelled','used','expired') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `destinasi_id`, `tanggal_kunjungan`, `jumlah_tiket`, `total_harga`, `nama_pemesan`, `email_pemesan`, `no_hp`, `status`, `created_at`, `updated_at`, `catatan`) VALUES
(5, 5, 14, '2026-05-30', 1, 50000000, 'Jevon Agnibrata S.', 'jevonpersonalkom@gmail.com', '8183781823', 'paid', '2026-05-21 16:01:47', '2026-05-21 16:01:51', 'vip'),
(6, 5, 14, '2026-05-28', 1, 50000000, 'adwaw', 'jevonpersonalkom@gmail.com', 'adad', 'pending', '2026-05-21 17:52:45', '2026-05-21 17:52:45', 'adad'),
(7, 5, 14, '2026-05-31', 9, 450000000, 'jevon', 'jevonpersonalkom@gmail.com', '081211111', 'paid', '2026-05-21 21:00:26', '2026-05-21 21:00:33', 'vip'),
(8, 5, 14, '2026-05-25', 1, 50000000, 'Jevon Agnibrata S.', 'jevonpersonalkom@gmail.com', '119238912', 'paid', '2026-05-25 07:33:56', '2026-05-25 07:34:00', '12312');

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_logs`
--

CREATE TABLE `booking_logs` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `status_lama` varchar(20) DEFAULT NULL,
  `status_baru` varchar(20) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi`
--

CREATE TABLE `destinasi` (
  `id` int(11) NOT NULL,
  `provinsi_id` int(11) DEFAULT NULL,
  `nama_destinasi` varchar(150) NOT NULL,
  `provinsi` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stok_tiket` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi`
--

INSERT INTO `destinasi` (`id`, `provinsi_id`, `nama_destinasi`, `provinsi`, `deskripsi`, `harga`, `gambar`, `created_at`, `stok_tiket`) VALUES
(14, 37, 'Bromo', '', 'Gunung Bromo adalah wisata alam di Jawa Timur yang terkenal dengan sunrise, lautan pasir, dan kawahnya yang indah.', 50000000, '1779379268_bromot.jpg', '2026-05-21 16:00:15', 100);

-- --------------------------------------------------------

--
-- Struktur dari tabel `e_tickets`
--

CREATE TABLE `e_tickets` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `kode_tiket` varchar(50) NOT NULL,
  `qr_code` text DEFAULT NULL,
  `status` enum('active','used','expired','cancelled') DEFAULT 'active',
  `digunakan_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `e_tickets`
--

INSERT INTO `e_tickets` (`id`, `booking_id`, `kode_tiket`, `qr_code`, `status`, `digunakan_at`, `created_at`) VALUES
(5, 5, 'TIX-C6F91459', NULL, 'active', NULL, '2026-05-21 16:01:51'),
(6, 7, 'TIX-2715CC4C', NULL, 'active', NULL, '2026-05-21 21:00:33'),
(7, 8, 'TIX-B68DE9E3', NULL, 'active', NULL, '2026-05-25 07:34:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `metode` enum('gopay','qris','bca_va','bni_va','bri_va','mandiri_va','permata_va','credit_card') NOT NULL,
  `amount` int(11) NOT NULL,
  `status` enum('pending','success','failed','expired','refunded') DEFAULT 'pending',
  `external_id` varchar(100) DEFAULT NULL,
  `payment_url` text DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `provinsi`
--

CREATE TABLE `provinsi` (
  `id` int(11) NOT NULL,
  `nama_provinsi` varchar(100) NOT NULL,
  `pulau` varchar(100) DEFAULT NULL,
  `ibu_kota` varchar(100) DEFAULT NULL,
  `populasi` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `highlight` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `provinsi`
--

INSERT INTO `provinsi` (`id`, `nama_provinsi`, `pulau`, `ibu_kota`, `populasi`, `deskripsi`, `gambar`, `highlight`, `created_at`) VALUES
(23, 'Nanggroe Aceh Darussalam', NULL, NULL, NULL, 'Provinsi paling barat Indonesia, dikenal dengan budaya Islami, sejarah kuat, dan wisata alam seperti pantai dan pegunungan.', '1779343160_provinsi_Aceh.jpg', NULL, '2026-05-21 05:59:20'),
(24, 'Sumatera Utara', NULL, NULL, NULL, 'Provinsi dengan Kota Medan sebagai pusat ekonomi, terkenal dengan Danau Toba dan budaya Batak.', '1779343628_provinsi_sumut.jpg', NULL, '2026-05-21 06:07:08'),
(25, 'Sumatera Barat', NULL, NULL, NULL, 'Dikenal dengan budaya Minangkabau, rumah gadang, dan kuliner rendang yang mendunia.', '1779343641_provinsi_sumbar.jpg', NULL, '2026-05-21 06:07:21'),
(26, 'Riau', NULL, NULL, NULL, 'Provinsi kaya sumber daya alam, terutama perkebunan dan minyak, dengan budaya Melayu yang kuat.', '1779343661_provinsi_riau.jpg', NULL, '2026-05-21 06:07:41'),
(27, 'Kepulauan Riau', NULL, NULL, NULL, 'Provinsi kepulauan yang strategis, dekat dengan Singapura dan Malaysia, terkenal dengan wisata bahari.', '1779343678_provinsi_kepuriau.jpg', NULL, '2026-05-21 06:07:58'),
(28, 'Jambi', NULL, NULL, NULL, 'Provinsi di Sumatera yang memiliki hutan tropis, sungai besar, dan peninggalan sejarah.', '1779343743_provinsi_jambi.jpg', NULL, '2026-05-21 06:09:03'),
(29, 'Bengkulu', NULL, NULL, NULL, 'Dikenal dengan pantai indah, bunga rafflesia, dan sejarah pengasingan Bung Karno.', '1779343759_provinsi_bengkulu.jpg', NULL, '2026-05-21 06:09:19'),
(30, 'Sumatera Selatan', NULL, NULL, NULL, 'Provinsi dengan Palembang sebagai pusatnya, terkenal dengan Sungai Musi dan Jembatan Ampera.', '1779343776_provinsi_sumsel.jpg', NULL, '2026-05-21 06:09:36'),
(31, 'Bangka Belitung', NULL, NULL, NULL, 'Terkenal dengan pantai batu granit, laut biru, dan hasil tambang timah.', '1779343795_provinsi_bangka.jpg', NULL, '2026-05-21 06:09:55'),
(32, 'Lampung', NULL, NULL, NULL, 'Provinsi paling selatan di Sumatera, dikenal sebagai gerbang Sumatera dan Taman Nasional Way Kambas.', '1779343813_provinsi_lampung.jpg', NULL, '2026-05-21 06:10:13'),
(33, 'Banten', NULL, NULL, NULL, 'Provinsi di ujung barat Pulau Jawa, memiliki sejarah kuat dan wilayah industri yang berkembang pesat.', '1779343831_provinsi_banten.jpg', NULL, '2026-05-21 06:10:31'),
(34, 'DKI Jakarta', NULL, NULL, NULL, 'Ibu kota negara dan pusat pemerintahan, ekonomi, serta bisnis Indonesia. Jawa Barat: Provinsi dengan penduduk besar, budaya Sunda yang khas, dan banyak destinasi wisata alam.', '1779343859_provinsi_jabar.jpg', NULL, '2026-05-21 06:10:59'),
(35, 'Jawa Tengah', NULL, NULL, NULL, 'Dikenal sebagai pusat budaya Jawa, dengan kota-kota sejarah dan warisan tradisional yang kuat.', '1779343923_provinsi_jateng.jpg', NULL, '2026-05-21 06:12:03'),
(36, 'Daerah Istimewa Yogyakarta', NULL, NULL, NULL, 'Provinsi budaya dan pendidikan, terkenal dengan keraton, batik, dan suasana khas pelajar.', '1779343935_provinsi_diy.jpg', NULL, '2026-05-21 06:12:15'),
(37, 'Jawa Timur', NULL, NULL, NULL, 'Provinsi besar dengan pusat industri dan perdagangan, serta wisata alam seperti Bromo dan Ijen.', '1779343965_provinsi_bromo.jpg', NULL, '2026-05-21 06:12:45'),
(38, 'Bali', NULL, NULL, NULL, 'Provinsi wisata internasional yang terkenal dengan pantai, seni budaya, dan tradisi Hindu.', '1779344113_provinsi_bali.jpg', NULL, '2026-05-21 06:15:13'),
(39, 'Nusa Tenggara Barat', NULL, NULL, NULL, 'Dikenal dengan Pulau Lombok, Gunung Rinjani, dan wisata bahari yang indah.', '1779350321_provinsi_ntb.jpg', NULL, '2026-05-21 07:58:41'),
(40, 'Nusa Tenggara Timur', NULL, NULL, NULL, 'Provinsi kepulauan dengan budaya beragam, alam eksotis, dan destinasi seperti Labuan Bajo.', '1779350419_provinsi_ntt.jpg', NULL, '2026-05-21 08:00:19'),
(41, 'Kalimantan Barat', NULL, NULL, NULL, 'Provinsi dengan wilayah luas, budaya Dayak dan Melayu, serta perbatasan langsung dengan Malaysia.', '1779350531_provinsi_kalbar.jpg', NULL, '2026-05-21 08:02:11'),
(42, 'Kalimantan Timur', NULL, NULL, NULL, 'Kaya sumber daya alam, menjadi lokasi Ibu Kota Nusantara, dan memiliki hutan tropis luas.', '1779353288_provinsi_kaltim.jpg', NULL, '2026-05-21 08:48:08'),
(43, 'Kalimantan Selatan', NULL, NULL, NULL, 'Terkenal dengan sungai-sungai besar, pasar terapung, dan budaya Banjar.', '1779353674_provinsi_kalsel.jpg', NULL, '2026-05-21 08:54:34'),
(44, 'Kalimantan Tengah', NULL, NULL, NULL, 'Memiliki hutan lebat dan budaya Dayak yang masih sangat kuat.', '1779353974_provinsi_kalteng.jpg', NULL, '2026-05-21 08:59:34'),
(45, 'Kalimantan Utara', NULL, NULL, NULL, 'Provinsi termuda di Kalimantan, berbatasan dengan Malaysia dan memiliki potensi alam besar.', '1779354806_provinsi_kalmut.jpg', NULL, '2026-05-21 09:13:26'),
(46, 'Sulawesi Utara', NULL, NULL, NULL, 'Dikenal dengan wisata laut Bunaken, alam indah, dan budaya Minahasa.', '1779355847_provinsi_sulut.jpg', NULL, '2026-05-21 09:30:47'),
(47, 'Sulawesi Barat', NULL, NULL, NULL, 'Provinsi dengan pesona alam pegunungan dan laut, serta budaya Mandar.', '1779355920_provinsi_sulbar.jpg', NULL, '2026-05-21 09:32:00'),
(48, 'Sulawesi Selatan', NULL, NULL, NULL, 'Provinsi maju dengan Makassar sebagai pusatnya, terkenal dengan budaya Bugis-Makassar.', '1779356061_provinsi_sulsel.jpg', NULL, '2026-05-21 09:34:21'),
(49, 'Sulawesi Tengah', NULL, NULL, NULL, 'Memiliki kekayaan alam, budaya beragam, dan kawasan wisata seperti Danau Poso.', '1779356177_provinsi_sulteng.jpg', NULL, '2026-05-21 09:36:17'),
(50, 'Sulawesi Tenggara', NULL, NULL, NULL, 'Dikenal dengan wisata bahari, budaya Buton, dan potensi kelautan yang besar.', '1779356282_provinsi_sultengg.jpg', NULL, '2026-05-21 09:38:02'),
(51, 'Gorontalo', NULL, NULL, NULL, 'Provinsi yang tenang dan religius, dikenal dengan budaya lokal dan wisata alamnya.', '1779356557_provinsi_gorontalo.jpg', NULL, '2026-05-21 09:42:37'),
(52, 'Maluku Utara', NULL, NULL, NULL, 'Provinsi kepulauan dengan sejarah rempah-rempah dan keindahan laut yang menakjubkan.', '1779356849_provinsi_malkut.jpg', NULL, '2026-05-21 09:47:29'),
(53, 'Maluku', NULL, NULL, NULL, 'Dikenal sebagai Kepulauan Rempah, dengan sejarah perdagangan dan budaya bahari yang kuat.', '1779357048_provinsi_maluku.jpg', NULL, '2026-05-21 09:50:48'),
(54, 'Papua Barat', NULL, NULL, NULL, 'Memiliki alam yang masih asri, termasuk kawasan Raja Ampat yang terkenal di dunia.', '1779357110_provinsi_pabar.jpg', NULL, '2026-05-21 09:51:50'),
(55, 'Papua', NULL, NULL, NULL, 'Provinsi dengan alam pegunungan dan hutan tropis luas, serta kekayaan budaya suku-suku asli.', '1779357945_provinsi_papua.jpg', NULL, '2026-05-21 10:05:45'),
(56, 'Papua Tengah', NULL, NULL, NULL, 'Provinsi baru di wilayah Papua yang memiliki potensi alam dan budaya yang besar.', '1779358036_provinsi_pateng.jpg', NULL, '2026-05-21 10:07:16'),
(57, 'Papua Selatan', NULL, NULL, NULL, 'Provinsi baru di bagian selatan Papua, memiliki wilayah luas dan potensi pertanian.', '1779358098_provinsi_pasel.jpg', NULL, '2026-05-21 10:08:18'),
(58, 'Papua Barat Daya', NULL, NULL, NULL, 'Provinsi baru di barat Papua, dikenal dengan wilayah kepulauan dan potensi wisata bahari.', '1779360821_provinsi_pabaday.jpg', NULL, '2026-05-21 10:53:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `role`) VALUES
(1, 'demo@nusantara.com', 'TravelerDemo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
(5, 'jevonpersonalkom@gmail.com', 'jevon', '$2y$10$6SRMib7h/ymY.gUj0xDo3u0vNgZIe4E2KKleOpmX1Lw8WMOu3c/Z.', 'user'),
(6, 'jevonvon@gmail.com', 'jevon', '$2y$10$S0q3ZFfaT6HlIn5B1vhfEeO9jQYnXLHNuhLhHVMNeAxJxQJS12RVa', 'user'),
(7, 'admin@nusantara.id', 'jevon', '$2y$10$8Fe2AoL.3.mEXGD0Wy2l2etmWI9wH4KvckSS/xMrCku/xF/8EOR2K', 'admin'),
(8, 'joshmaximilianwijaya@gmail.com', 'joshua', '$2y$10$tEaQx7k/aG9KP8xZylCLtuC9ktGyq/JEZfhddBy3xRQHDswt7PqRa', 'user'),
(9, 'azfarhatiyanto73@gmail.com', 'azfar', '$2y$10$.c6/.885UGsS3LpdMs4ZSu3EKfydmenwhCKDdGJUPyXXruH/C5S6S', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `destinasi_id` (`destinasi_id`);

--
-- Indeks untuk tabel `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_destinasi_provinsi` (`provinsi_id`);

--
-- Indeks untuk tabel `e_tickets`
--
ALTER TABLE `e_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_tiket` (`kode_tiket`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `provinsi`
--
ALTER TABLE `provinsi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_provinsi` (`nama_provinsi`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `booking_logs`
--
ALTER TABLE `booking_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `e_tickets`
--
ALTER TABLE `e_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `provinsi`
--
ALTER TABLE `provinsi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD CONSTRAINT `booking_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  ADD CONSTRAINT `fk_destinasi_provinsi` FOREIGN KEY (`provinsi_id`) REFERENCES `provinsi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `e_tickets`
--
ALTER TABLE `e_tickets`
  ADD CONSTRAINT `e_tickets_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
