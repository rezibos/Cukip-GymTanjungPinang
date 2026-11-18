-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 20, 2026 at 10:38 AM
-- Server version: 8.4.7
-- PHP Version: 8.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gympinang`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Gym', 'gym', 'Tempat latihan beban dan kebugaran umum.', '2026-09-20 00:15:14', '2026-09-20 00:15:14'),
(2, 'Fitness Center', 'fitness-center', 'Pusat kebugaran dengan alat kardio, beban, dan kelas.', '2026-09-20 00:15:14', '2026-09-20 00:15:14'),
(3, 'CrossFit', 'crossfit', 'Latihan fungsional intensitas tinggi dengan pelatih.', '2026-09-20 00:15:14', '2026-09-20 00:15:14'),
(4, 'Women Only', 'women-only', 'Gym khusus perempuan.', '2026-09-20 00:15:14', '2026-09-20 00:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
CREATE TABLE IF NOT EXISTS `facilities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `gym_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facilities_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `gym_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Treadmill', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(2, 1, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(3, 1, 'Barbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(4, 1, 'Bench Press', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(5, 1, 'Locker', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(6, 1, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(7, 1, 'Parkir', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(8, 1, 'AC', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(9, 2, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(10, 2, 'Barbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(11, 2, 'Bench Press', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(12, 2, 'Squat Rack', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(13, 2, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(14, 2, 'Parkir', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(15, 3, 'Treadmill', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(16, 3, 'Leg Press', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(17, 3, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(18, 3, 'Locker', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(19, 3, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(20, 3, 'Parkir', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(21, 3, 'AC', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(22, 3, 'Personal Trainer', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(23, 4, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(24, 4, 'Barbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(25, 4, 'Bench Press', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(26, 4, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(27, 4, 'Parkir', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(28, 5, 'Treadmill', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(29, 5, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(30, 5, 'Kelas Senam', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(31, 5, 'Locker', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(32, 5, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(33, 5, 'Musala', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(34, 5, 'AC', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(35, 6, 'Treadmill', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(36, 6, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(37, 6, 'Barbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(38, 6, 'Locker', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(39, 6, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(40, 6, 'Parkir', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(41, 6, 'AC', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(42, 7, 'Dumbbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(43, 7, 'Barbell', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(44, 7, 'Squat Rack', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(45, 7, 'Bench Press', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(46, 7, 'Toilet', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(47, 7, 'Parkir', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(48, 8, 'Barbell', NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(49, 8, 'Kettlebell', NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(50, 8, 'Pull-up Rig', NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(51, 8, 'Rowing Machine', NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(52, 8, 'Toilet', NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(53, 8, 'Parkir', NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(54, 1, 'Sauna', NULL, '2026-09-20 09:18:35', '2026-09-20 09:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gyms`
--

DROP TABLE IF EXISTS `gyms`;
CREATE TABLE IF NOT EXISTS `gyms` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `opening_hours` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gyms_slug_unique` (`slug`),
  KEY `gyms_category_id_foreign` (`category_id`),
  KEY `gyms_region_index` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gyms`
--

INSERT INTO `gyms` (`id`, `category_id`, `name`, `slug`, `description`, `address`, `region`, `district`, `phone`, `whatsapp`, `latitude`, `longitude`, `opening_hours`, `image`, `created_at`, `updated_at`) VALUES
(1, 2, 'Mesin Otot Gym Center', 'fitzone-tanjungpinang', 'Fitness center di pusat kota dengan alat kardio dan beban yang lengkap, cocok untuk pemula maupun yang sudah rutin berlatih.', 'Cafe Bike Care, Komp Ruko Bintan Center Blok O No 10-11 Lt 2, Air Raja, Tanjungpinang Timur, Tanjung Pinang City, Riau Islands 29125', 'Tanjungpinang', 'Tanjungpinang Kota', '6285159392233', '6285159392233', 0.9197680, 104.5055680, '05:30 - 22:00', 'gyms/01M2Z5HNVQ7JX794XPK6KJEQSA.jpg', '2026-09-20 00:15:15', '2026-09-20 10:24:48'),
(2, 1, 'Power House Gym', 'power-house-gym', 'Gym fokus latihan beban dengan area free weight yang luas dan suasana latihan yang santai.', 'Jl. Contoh No. 25, Tanjungpinang', 'Tanjungpinang', 'Tanjungpinang Timur', '0771-000002', '6281200000002', 0.9110000, 104.4790000, '07:00 - 21:00', 'gyms/01M2Z62QG2XAFP6CPQJ33ZABKE.jpg', '2026-09-20 00:15:15', '2026-09-20 10:34:07'),
(3, 1, 'Titan Fitness', 'titan-fitness', 'Gym dengan peralatan mesin lengkap dan ruang latihan ber-AC. Tersedia personal trainer.', 'Jl. Contoh No. 7, Tanjungpinang', 'Tanjungpinang', 'Bukit Bestari', '0771-000003', '6281200000003', 0.9350000, 104.4560000, '06:00 - 22:00', 'gyms/01M2Z624FSTPNQYCTSNBD78ZH7.jpg', '2026-09-20 00:15:15', '2026-09-20 10:33:47'),
(4, 1, 'Iron Kepri Gym', 'iron-kepri-gym', 'Gym sederhana dengan harga bersahabat untuk pelajar dan mahasiswa.', 'Jl. Contoh No. 33, Tanjungpinang', 'Tanjungpinang', 'Tanjungpinang Barat', '0771-000004', '6281200000004', 0.9200000, 104.4480000, '08:00 - 21:00', 'gyms/01M2Z61GSYJXP5BCA99X144B5S.jpg', '2026-09-20 00:15:15', '2026-09-20 10:33:27'),
(5, 4, 'Srikandi Women Fitness', 'srikandi-women-fitness', 'Gym khusus perempuan dengan instruktur perempuan dan kelas senam rutin.', 'Jl. Contoh No. 18, Tanjungpinang', 'Tanjungpinang', 'Tanjungpinang Kota', '0771-000005', '6281200000005', 0.9165000, 104.4620000, '08:00 - 20:00', 'gyms/01M2Z60WQBS097R59C5VVYDRTP.jpg', '2026-09-20 00:15:15', '2026-09-20 10:33:07'),
(6, 2, 'Bintan Fitness Center', 'bintan-fitness-center', 'Pusat kebugaran di Kijang dengan area kardio, beban, dan lapangan parkir yang luas.', 'Jl. Contoh No. 5, Kijang, Bintan', 'Bintan', 'Bintan Timur', '0771-000006', '6281200000006', NULL, NULL, '06:00 - 21:00', 'gyms/01M2Z60DZTPH1D16P5SQ3EPHX7.jpg', '2026-09-20 00:15:15', '2026-09-20 10:32:51'),
(7, 1, 'Uban Muscle Club', 'uban-muscle-club', 'Gym di Tanjung Uban dengan peralatan beban lengkap untuk pekerja dan warga sekitar.', 'Jl. Contoh No. 12, Tanjung Uban, Bintan', 'Bintan', 'Bintan Utara', '0771-000007', '6281200000007', 1.0667000, 104.2167000, '07:00 - 22:00', 'gyms/01M2Z5ZWWTD01EKTQY79Z3BW9D.jpg', '2026-09-20 00:15:15', '2026-09-20 10:32:34'),
(8, 3, 'Bintan CrossFit Box', 'bintan-crossfit-box', 'Box CrossFit dengan kelas terjadwal dipandu pelatih. Tersedia sesi pengenalan untuk pemula.', 'Jl. Contoh No. 3, Bintan', 'Bintan', 'Teluk Sebong', '0771-000008', '6281200000008', 9.1010000, 104.4500000, '05:30 - 20:00', 'gyms/01M2Z5ZEM0A3G8S44EGP3NMYSS.jpg', '2026-09-20 00:15:15', '2026-09-20 10:32:19'),
(9, 1, 'cukipGym', 'cukipgym', 'wassap', 'Jalan Sidomulyo', 'Bintan', 'Bintan Timur', '083142969101', '6283142969101', 0.9028030, 104.4873970, '06:00 - 22:00', 'gyms/01M2YR34DDTZS9VJNESZMKMYHW.jpg', '2026-09-20 06:29:40', '2026-09-20 06:29:40');

-- --------------------------------------------------------

--
-- Table structure for table `gym_images`
--

DROP TABLE IF EXISTS `gym_images`;
CREATE TABLE IF NOT EXISTS `gym_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `gym_id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_images_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_images`
--

INSERT INTO `gym_images` (`id`, `gym_id`, `image`, `caption`, `created_at`, `updated_at`) VALUES
(1, 1, 'gyms/gallery/01M2Z1KHJ6RGKY90ZC7CDR08AT.jpg', 'treadmil', '2026-09-20 09:15:55', '2026-09-20 09:15:55'),
(2, 1, 'gyms/gallery/01M2Z1N4J2NWR4TK1TFN9R40S3.jpg', 'bench press', '2026-09-20 09:16:47', '2026-09-20 09:16:47'),
(3, 1, 'gyms/gallery/01M2Z1P6GNE66G7HSG6FDTVA43.jpg', 'cable fly', '2026-09-20 09:17:22', '2026-09-20 09:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `gym_packages`
--

DROP TABLE IF EXISTS `gym_packages`;
CREATE TABLE IF NOT EXISTS `gym_packages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `gym_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_packages_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_packages`
--

INSERT INTO `gym_packages` (`id`, `gym_id`, `name`, `duration`, `price`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Paket Harian', '1 hari', 25000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(2, 1, 'Paket Mingguan', '1 minggu', 100000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(3, 1, 'Paket Bulanan', '1 bulan', 250000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(4, 2, 'Paket Harian', '1 hari', 20000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(5, 2, 'Paket Mingguan', '1 minggu', 80000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(6, 2, 'Paket Bulanan', '1 bulan', 200000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(7, 3, 'Paket Harian', '1 hari', 30000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(8, 3, 'Paket Mingguan', '1 minggu', 120000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(9, 3, 'Paket Bulanan', '1 bulan', 300000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(10, 4, 'Paket Harian', '1 hari', 15000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(11, 4, 'Paket Mingguan', '1 minggu', 70000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(12, 4, 'Paket Bulanan', '1 bulan', 150000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(13, 5, 'Paket Harian', '1 hari', 25000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(14, 5, 'Paket Mingguan', '1 minggu', 100000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(15, 5, 'Paket Bulanan', '1 bulan', 275000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(16, 6, 'Paket Harian', '1 hari', 20000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(17, 6, 'Paket Mingguan', '1 minggu', 90000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(18, 6, 'Paket Bulanan', '1 bulan', 225000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(19, 7, 'Paket Harian', '1 hari', 20000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(20, 7, 'Paket Mingguan', '1 minggu', 85000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(21, 7, 'Paket Bulanan', '1 bulan', 200000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(22, 8, 'Paket Harian', '1 hari', 50000, NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(23, 8, 'Paket Mingguan', '1 minggu', 200000, NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(24, 8, 'Paket Bulanan', '1 bulan', 450000, NULL, '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(25, 1, 'Paket Tahunan', '1 Tahun', 2500000, NULL, '2026-09-20 09:18:16', '2026-09-20 09:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_20_065103_create_categories_table', 1),
(5, '2026_09_20_065104_create_gyms_table', 1),
(6, '2026_09_20_065105_create_facilities_table', 1),
(7, '2026_09_20_065106_create_gym_packages_table', 1),
(8, '2026_09_20_065107_create_gym_images_table', 1),
(9, '2026_09_20_065108_create_reviews_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `gym_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_user_id_gym_id_unique` (`user_id`,`gym_id`),
  KEY `reviews_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `gym_id`, `rating`, `review`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 4, 'Suasananya nyaman, tidak terlalu ramai di pagi hari.', 'approved', '2026-09-20 00:15:16', '2026-09-20 09:21:00'),
(2, 5, 1, 3, 'Harga terjangkau, cocok untuk pemula.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(3, 7, 1, 3, 'AC dingin dan tempatnya terawat.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(4, 3, 2, 3, 'Lokasinya mudah dijangkau, jam bukanya pas.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(5, 5, 2, 3, 'Staf ramah dan membantu menjelaskan cara pakai alat.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(6, 6, 2, 4, 'Harga terjangkau, cocok untuk pemula.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(7, 2, 3, 4, 'Lokasinya mudah dijangkau, jam bukanya pas.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(8, 5, 3, 5, 'AC dingin dan tempatnya terawat.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(9, 6, 3, 3, 'Alat cukup lengkap, tapi parkir agak sempit.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(10, 2, 4, 3, 'Suasananya nyaman, tidak terlalu ramai di pagi hari.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(11, 4, 4, 4, 'AC dingin dan tempatnya terawat.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(12, 8, 4, 5, 'Suasananya nyaman, tidak terlalu ramai di pagi hari.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(13, 3, 5, 3, 'Sedikit ramai di sore hari, tapi overall bagus.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(14, 6, 5, 3, 'Suasananya nyaman, tidak terlalu ramai di pagi hari.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(15, 8, 5, 5, 'Tempatnya bersih dan alatnya cukup lengkap.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(16, 4, 6, 4, 'Suasananya nyaman, tidak terlalu ramai di pagi hari.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(17, 7, 6, 4, 'Tempatnya bersih dan alatnya cukup lengkap.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(18, 8, 6, 4, 'Staf ramah dan membantu menjelaskan cara pakai alat.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(19, 4, 7, 3, 'Sedikit ramai di sore hari, tapi overall bagus.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(20, 5, 7, 3, 'Suasananya nyaman, tidak terlalu ramai di pagi hari.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(21, 6, 7, 3, 'Harga terjangkau, cocok untuk pemula.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(22, 3, 8, 3, 'AC dingin dan tempatnya terawat.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(23, 4, 8, 3, 'Alat cukup lengkap, tapi parkir agak sempit.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(24, 5, 8, 3, 'Harga terjangkau, cocok untuk pemula.', 'approved', '2026-09-20 00:15:16', '2026-09-20 00:15:16'),
(25, 9, 6, 5, 'cihuyyy', 'approved', '2026-09-20 05:55:27', '2026-09-20 05:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('TIaszuabJ1Apy9nIB3rz6WOGTajDuq1ysStWJwcV', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJESWNCUVAxaDBDZzg3S24zdXU4dEpJQmNSSUhXa1hWVGd0R0xWbFo5IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiMjA3MWY1M2FmYTJlYjc2MDI5ZGRkYWZmYjk5OWMwNjIxNzJhZjNkZTMyODY0MzhkY2YxZWUwZWZmMjlkZjMzMiIsInRhYmxlcyI6eyJlZDNiMzhhOTI0YTA1MWJmOWZlOTk5Y2I0ZjE3MjhiOV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWUiLCJsYWJlbCI6Ik5hbWEiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2F0ZWdvcnkubmFtZSIsImxhYmVsIjoiS2F0ZWdvcmkiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicmVnaW9uIiwibGFiZWwiOiJXaWxheWFoIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IkRpYnVhdCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XSwiYzk3MmQzZjEwM2Y1OGY3ZmUyZTcwZjY2MzllYjI2ZWJfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ1c2VyLm5hbWUiLCJsYWJlbCI6IlVzZXIiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZ3ltLm5hbWUiLCJsYWJlbCI6Ikd5bSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyYXRpbmciLCJsYWJlbCI6IlJhdGluZyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyZXZpZXciLCJsYWJlbCI6IktvbWVudGFyIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IlRhbmdnYWwiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sImU2NDQ4MzNmNGU0ZTA4NzEyMzE1ZGE3MWIzM2ZhY2QyX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtZSIsImxhYmVsIjoiTmFtYSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJlbWFpbCIsImxhYmVsIjoiRW1haWwiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicm9sZSIsImxhYmVsIjoiUm9sZSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyZXZpZXdzX2NvdW50IiwibGFiZWwiOiJSZXZpZXciLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY3JlYXRlZF9hdCIsImxhYmVsIjoiVGVyZGFmdGFyIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCI1NzYzNTllMTcyZjMxZjA3ODNhNDBmNTEyYTVlZGY1NV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImltYWdlIiwibGFiZWwiOiJGb3RvIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWUiLCJsYWJlbCI6Ik5hbWEiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2F0ZWdvcnkubmFtZSIsImxhYmVsIjoiS2F0ZWdvcmkiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicmVnaW9uIiwibGFiZWwiOiJXaWxheWFoIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImFwcHJvdmVkX3Jldmlld3NfYXZnX3JhdGluZyIsImxhYmVsIjoiUmF0aW5nIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IkRpYnVhdCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9XSwiNzg2ODgwOTI2ODNhMzJkMzM2MzA1ZTdlM2IyODZkMzFfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJuYW1lIiwibGFiZWwiOiJOYW1hIHBha2V0IiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImR1cmF0aW9uIiwibGFiZWwiOiJEdXJhc2kiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicHJpY2UiLCJsYWJlbCI6IkhhcmdhIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJjYWIwZmMwYTE2OGI1YzU3ZGVjNDFiZmIyYzA5ODljZF9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Imd5bS5uYW1lIiwibGFiZWwiOiJHeW0iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtZSIsImxhYmVsIjoiUGFrZXQiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZHVyYXRpb24iLCJsYWJlbCI6IkR1cmFzaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJwcmljZSIsImxhYmVsIjoiSGFyZ2EiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sImM4MjE0M2QzOGVlNDU4ZDliYTIwN2FkMWU2ODVjOTMyX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiaW1hZ2UiLCJsYWJlbCI6IkZvdG8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2FwdGlvbiIsImxhYmVsIjoiS2V0ZXJhbmdhbiIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XX0sImZpbGFtZW50IjpbXX0=', 1789900670);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin GymPinang', 'admin@gympinang.test', NULL, '$2y$12$aZ5EURSMWSzSsPdZnkl7GeqTwf8JHZV4EWrposkJ3OjUlv59led2q', 'admin', NULL, '2026-09-20 00:15:14', '2026-09-20 00:15:14'),
(2, 'User Demo', 'user@gympinang.test', NULL, '$2y$12$sc8MhSJ5a1zTQQzOXj8BHeRKu45fybNKRaX/6Nz.8zclAdkvCHM7i', 'user', NULL, '2026-09-20 00:15:14', '2026-09-20 00:15:14'),
(3, 'Andi Contoh', 'member1@example.com', NULL, '$2y$12$UbSUcKydabKqhR9.6QieMev3eownWMI2mu6ninhrXRAjD0Fnj0lKi', 'user', NULL, '2026-09-20 00:15:14', '2026-09-20 00:15:14'),
(4, 'Sari Contoh', 'member2@example.com', NULL, '$2y$12$8rEttaIx7m2n/Uae9TxjeOJEDbxE6HrY8ltSzHhw8urfhi3Qr2BnO', 'user', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(5, 'Budi Contoh', 'member3@example.com', NULL, '$2y$12$Gn7T.0j/mBwMJ1524j67Wubt3lCIm6pmHOc4PuXS62SrGzC7XlH8W', 'user', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(6, 'Dewi Contoh', 'member4@example.com', NULL, '$2y$12$NpTna4FvRkoadwr/5BJIjOVEjOJ30EofwsUWEsyGhIWkKmR7xGvB.', 'user', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(7, 'Rizky', 'member5@example.com', NULL, '$2y$12$mItQm36K1x9nAu4BY.OEIurMotl54LvxjQU5NPJ5kDTr20dd/soMS', 'user', NULL, '2026-09-20 00:15:15', '2026-09-20 09:23:09'),
(8, 'Maya Contoh', 'member6@example.com', NULL, '$2y$12$4Ndk38eV.AtPi2SXQAcMMOtqT8s4jshdrJrOeaFXz35K1mshDWfZq', 'user', NULL, '2026-09-20 00:15:15', '2026-09-20 00:15:15'),
(9, 'cukip', 'cukip404@gmail.com', NULL, '$2y$12$XYOcL0iB2MI8YFm3adnWoOZUAFN7.xgOso8Vm1IzQsQBn15pPfj26', 'user', NULL, '2026-09-20 04:50:20', '2026-09-20 04:50:20');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gyms`
--
ALTER TABLE `gyms`
  ADD CONSTRAINT `gyms_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `gym_images`
--
ALTER TABLE `gym_images`
  ADD CONSTRAINT `gym_images_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gym_packages`
--
ALTER TABLE `gym_packages`
  ADD CONSTRAINT `gym_packages_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
