-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jul 2024 pada 12.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banten_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `banten`
--

CREATE TABLE `banten` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `banten`
--

INSERT INTO `banten` (`id`, `name`, `price`, `description`, `quantity`, `image`) VALUES
(1, 'Canang kotak', 10000.00, 'Canang Ceper/Kotak dipakai untuk mebanten sehari2 maupun untuk Persembahyangan ke Pura', 2000, 'no-brand_no-brand_full01.webp'),
(2, 'Pejati', 500000.00, 'banten pejati lengkap terdiri dari tandingan peras, soda, daksina, pesucian, tipat kelanan, segeh,  sampean peras, penyeneng dan canang 5 tanding', 7, 'pejati12-1559327608.webp');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `banten`
--
ALTER TABLE `banten`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `banten`
--
ALTER TABLE `banten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
