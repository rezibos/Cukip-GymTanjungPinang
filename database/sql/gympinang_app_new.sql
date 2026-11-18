/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.20-13.0.2-MariaDB, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: gympinang_app
-- ------------------------------------------------------
-- Server version	8.4.13

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('gympinang-cache-5c785c036466adea360111aa28563bfd556b5fba','i:3;',1794753989),
('gympinang-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1794753989;',1794753989);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `class_bookings`
--

DROP TABLE IF EXISTS `class_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `class_session_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'booked',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_bookings_user_id_foreign` (`user_id`),
  KEY `class_bookings_class_session_id_foreign` (`class_session_id`),
  CONSTRAINT `class_bookings_class_session_id_foreign` FOREIGN KEY (`class_session_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_bookings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `class_bookings` WRITE;
/*!40000 ALTER TABLE `class_bookings` DISABLE KEYS */;
INSERT INTO `class_bookings` VALUES
(1,15,4,'cancelled','2026-11-14 23:25:54','2026-11-14 23:26:26');
/*!40000 ALTER TABLE `class_bookings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `class_sessions`
--

DROP TABLE IF EXISTS `class_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gym_class_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `capacity` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_sessions_gym_class_id_date_unique` (`gym_class_id`,`date`),
  CONSTRAINT `class_sessions_gym_class_id_foreign` FOREIGN KEY (`gym_class_id`) REFERENCES `gym_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `class_sessions` WRITE;
/*!40000 ALTER TABLE `class_sessions` DISABLE KEYS */;
INSERT INTO `class_sessions` VALUES
(1,3,'2026-11-11',20,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(2,4,'2026-11-12',12,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(3,5,'2026-11-13',15,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(4,6,'2026-11-14',10,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(5,1,'2026-11-16',15,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(6,2,'2026-11-17',12,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(7,3,'2026-11-18',20,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(8,4,'2026-11-19',12,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(9,5,'2026-11-20',15,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(10,6,'2026-11-21',10,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(11,1,'2026-11-23',15,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(12,2,'2026-11-24',12,'2026-11-11 05:13:14','2026-11-11 05:13:14');
/*!40000 ALTER TABLE `class_sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `facilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kenyamanan',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `facilities` WRITE;
/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
INSERT INTO `facilities` VALUES
(1,'Treadmill','kardio','aaaa','facilities/01M7EN9V6AHZQXK68TKHG3HV40.png','2026-11-11 05:13:14','2026-11-15 10:51:32'),
(2,'Dumbbell','kekuatan','Peralatan beban bebas yang fleksibel untuk berbagai kelompok otot.','facilities/01M7ENB0781VMMMRMCSM17PG3X.png','2026-11-11 05:13:14','2026-11-15 10:52:10'),
(3,'Barbell','kekuatan','Beban bebas untuk menyusun variasi gerakan dan latihan kekuatan.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(4,'Bench Press','kekuatan','Bangku latihan untuk mendukung gerakan dorong dan latihan beban.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(5,'Locker','kenyamanan','Tempat menyimpan barang agar kamu bisa lebih fokus selama latihan.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(6,'Toilet','kenyamanan','Fasilitas sanitasi yang melengkapi kebutuhan selama berada di gym.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(7,'Parkir','kenyamanan','Area untuk memarkir kendaraan sebelum memulai sesi latihanmu.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(8,'AC','kenyamanan','Area berpendingin untuk menjaga kenyamanan selama kamu berlatih.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(9,'Ruang Ganti','kenyamanan','Ruang untuk berganti pakaian sebelum dan setelah beraktivitas.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(10,'Air Minum','kenyamanan','Lengkapi kebutuhan minummu di sela aktivitas dan setelah latihan.',NULL,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(11,'Squat Rack','kekuatan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(12,'Leg Press','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(13,'Personal Trainer','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(14,'Kelas Senam','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(15,'Musala','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(16,'Kettlebell','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(17,'Pull-up Rig','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(18,'Rowing Machine','kenyamanan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(19,'Sauna','pemulihan','Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.',NULL,'2026-09-20 09:18:35','2026-09-20 09:18:35');
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `gym_classes`
--

DROP TABLE IF EXISTS `gym_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `day_of_week` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `capacity` int unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_classes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `gym_classes` WRITE;
/*!40000 ALTER TABLE `gym_classes` DISABLE KEYS */;
INSERT INTO `gym_classes` VALUES
(1,'Yoga Pagi',NULL,'Kak Rina','senin','07:00:00','08:00:00',15,1,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(2,'HIIT',NULL,'Kak Doni','selasa','17:00:00','18:00:00',12,1,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(3,'Zumba',NULL,'Kak Sinta','rabu','18:00:00','19:00:00',20,1,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(4,'Body Combat',NULL,'Kak Doni','kamis','17:00:00','18:00:00',12,1,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(5,'Yoga Sore',NULL,'Kak Rina','jumat','16:00:00','17:00:00',15,1,'2026-11-11 05:13:14','2026-11-11 05:13:14'),
(6,'Functional Training',NULL,'Kak Bayu','sabtu','09:00:00','10:00:00',10,1,'2026-11-11 05:13:14','2026-11-11 05:13:14');
/*!40000 ALTER TABLE `gym_classes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `gym_locations`
--

DROP TABLE IF EXISTS `gym_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `opening_hours` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_position` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'center',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_locations_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_locations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `gym_locations` WRITE;
/*!40000 ALTER TABLE `gym_locations` DISABLE KEYS */;
INSERT INTO `gym_locations` VALUES
(1,'CukipGym · Pusat Kota','Area Tanjungpinang Kota, Tanjungpinang',0.9274000,104.4446000,'06:00 – 22:00 · Setiap hari',NULL,'images/gym-equipment-editorial.webp','left center',1,1,'2026-11-15 17:14:55','2026-11-15 17:14:55'),
(2,'CukipGym · Batu 9','Area Batu IX, Tanjungpinang Timur',0.9270000,104.5158000,'06:00 – 22:00 · Setiap hari',NULL,'images/gym-cable-row-editorial.webp','35% center',2,1,'2026-11-15 17:14:55','2026-11-15 17:14:55'),
(3,'CukipGym · Bukit Bestari','Area Bukit Bestari, Tanjungpinang',0.8988000,104.4622000,'07:00 – 22:00 · Setiap hari',NULL,'images/gym-kettlebell-editorial.webp','center',3,1,'2026-11-15 17:14:55','2026-11-15 17:14:55'),
(4,'CukipGym · Tanjung Unggat','Area Tanjung Unggat, Bukit Bestari',0.9108000,104.4528000,'06:00 – 21:00 · Setiap hari',NULL,'images/gym-equipment-editorial.webp','right center',4,1,'2026-11-15 17:14:55','2026-11-15 17:14:55'),
(5,'CukipGym · Melayu Kota Piring','Area Melayu Kota Piring, Tanjungpinang Timur',0.9295000,104.4892000,'06:00 – 22:00 · Setiap hari',NULL,'images/gym-cable-row-editorial.webp','right center',5,1,'2026-11-15 17:14:55','2026-11-15 17:14:55'),
(6,'CukipGym · Senggarang','Area Senggarang, Tanjungpinang Kota',0.9625000,104.4300000,'07:00 – 21:00 · Setiap hari',NULL,'images/gym-kettlebell-editorial.webp','65% center',6,1,'2026-11-15 17:14:55','2026-11-15 17:14:55');
/*!40000 ALTER TABLE `gym_locations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `gym_profiles`
--

DROP TABLE IF EXISTS `gym_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `opening_hours` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hero_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_eyebrow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_title_primary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_title_secondary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_description` text COLLATE utf8mb4_unicode_ci,
  `hero_cta_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_about_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_eyebrow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_location_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_title_line_one` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_title_line_two` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_title_line_three` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_description_primary` text COLLATE utf8mb4_unicode_ci,
  `about_description_secondary` text COLLATE utf8mb4_unicode_ci,
  `about_gallery_image_one` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_gallery_image_two` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_gallery_image_three` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_gallery_image_four` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_gallery_image_five` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_gallery_eyebrow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_gallery_title` text COLLATE utf8mb4_unicode_ci,
  `footer_description` text COLLATE utf8mb4_unicode_ci,
  `footer_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_sponsors` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_profiles`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `gym_profiles` WRITE;
/*!40000 ALTER TABLE `gym_profiles` DISABLE KEYS */;
INSERT INTO `gym_profiles` VALUES
(1,'cukipGym','aaaa','Jalan Sidomulyo',0.9028030,104.4873970,'06:00 - 22:00','083142969101','6283142969101',NULL,'2026-11-11 05:13:14','2026-11-15 21:40:13',NULL,'Gym lokal Tanjungpinang','Bukan cuma.','Latihan.','Ruang untuk membangun tubuh, disiplin, dan versi terbaik dirimu. Mulai hari ini satu repetisi, satu progres.','Mulai sekarang','Jelajahi GymPinang','Tentang CukipGym','Tanjungpinang, Indonesia','Tempatmu','menjadi','lebih kuat.','Dari latihan pertamamu sampai rutinitas yang kamu banggakan. CukipGym adalah ruang untuk bergerak, membangun kekuatan, dan terus berkembang.','Temukan peralatan yang kamu butuhkan dan pilihan membership yang sesuai. Mulai dari kemampuanmu hari ini, lalu jalani progresmu satu sesi demi satu sesi.',NULL,NULL,NULL,NULL,NULL,'Ruang untuk progresmu','Latihanmu.\nRitmemu.','Tempat latihan, kelas kebugaran, dan komunitas untuk membantu kamu bergerak lebih konsisten.','Gym lokal Tanjungpinang','[{\"url\": null, \"logo\": null, \"name\": \"FORMA.\", \"tagline\": null, \"is_active\": true}, {\"url\": null, \"logo\": null, \"name\": \"PINANG\", \"tagline\": \"ACTIVE CLUB\", \"is_active\": true}, {\"url\": null, \"logo\": null, \"name\": \"replab\", \"tagline\": null, \"is_active\": true}, {\"url\": null, \"logo\": null, \"name\": \"NUSA\", \"tagline\": \"HYDRATE\", \"is_active\": true}]');
/*!40000 ALTER TABLE `gym_profiles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_cache`
--

DROP TABLE IF EXISTS `legacy_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_cache` WRITE;
/*!40000 ALTER TABLE `legacy_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_cache_locks`
--

DROP TABLE IF EXISTS `legacy_cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_cache_locks` WRITE;
/*!40000 ALTER TABLE `legacy_cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_categories`
--

DROP TABLE IF EXISTS `legacy_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_categories` WRITE;
/*!40000 ALTER TABLE `legacy_categories` DISABLE KEYS */;
INSERT INTO `legacy_categories` VALUES
(1,'Gym','gym','Tempat latihan beban dan kebugaran umum.','2026-09-20 00:15:14','2026-09-20 00:15:14'),
(2,'Fitness Center','fitness-center','Pusat kebugaran dengan alat kardio, beban, dan kelas.','2026-09-20 00:15:14','2026-09-20 00:15:14'),
(3,'CrossFit','crossfit','Latihan fungsional intensitas tinggi dengan pelatih.','2026-09-20 00:15:14','2026-09-20 00:15:14'),
(4,'Women Only','women-only','Gym khusus perempuan.','2026-09-20 00:15:14','2026-09-20 00:15:14');
/*!40000 ALTER TABLE `legacy_categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_facilities`
--

DROP TABLE IF EXISTS `legacy_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_facilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gym_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facilities_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_facilities`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_facilities` WRITE;
/*!40000 ALTER TABLE `legacy_facilities` DISABLE KEYS */;
INSERT INTO `legacy_facilities` VALUES
(1,1,'Treadmill',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(2,1,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(3,1,'Barbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(4,1,'Bench Press',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(5,1,'Locker',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(6,1,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(7,1,'Parkir',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(8,1,'AC',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(9,2,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(10,2,'Barbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(11,2,'Bench Press',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(12,2,'Squat Rack',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(13,2,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(14,2,'Parkir',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(15,3,'Treadmill',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(16,3,'Leg Press',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(17,3,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(18,3,'Locker',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(19,3,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(20,3,'Parkir',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(21,3,'AC',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(22,3,'Personal Trainer',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(23,4,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(24,4,'Barbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(25,4,'Bench Press',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(26,4,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(27,4,'Parkir',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(28,5,'Treadmill',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(29,5,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(30,5,'Kelas Senam',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(31,5,'Locker',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(32,5,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(33,5,'Musala',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(34,5,'AC',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(35,6,'Treadmill',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(36,6,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(37,6,'Barbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(38,6,'Locker',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(39,6,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(40,6,'Parkir',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(41,6,'AC',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(42,7,'Dumbbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(43,7,'Barbell',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(44,7,'Squat Rack',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(45,7,'Bench Press',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(46,7,'Toilet',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(47,7,'Parkir',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(48,8,'Barbell',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(49,8,'Kettlebell',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(50,8,'Pull-up Rig',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(51,8,'Rowing Machine',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(52,8,'Toilet',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(53,8,'Parkir',NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(54,1,'Sauna',NULL,'2026-09-20 09:18:35','2026-09-20 09:18:35');
/*!40000 ALTER TABLE `legacy_facilities` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_failed_jobs`
--

DROP TABLE IF EXISTS `legacy_failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_failed_jobs` WRITE;
/*!40000 ALTER TABLE `legacy_failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_gym_images`
--

DROP TABLE IF EXISTS `legacy_gym_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_gym_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gym_id` bigint unsigned NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_images_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_gym_images`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_gym_images` WRITE;
/*!40000 ALTER TABLE `legacy_gym_images` DISABLE KEYS */;
INSERT INTO `legacy_gym_images` VALUES
(1,1,'gyms/gallery/01M2Z1KHJ6RGKY90ZC7CDR08AT.jpg','treadmil','2026-09-20 09:15:55','2026-09-20 09:15:55'),
(2,1,'gyms/gallery/01M2Z1N4J2NWR4TK1TFN9R40S3.jpg','bench press','2026-09-20 09:16:47','2026-09-20 09:16:47'),
(3,1,'gyms/gallery/01M2Z1P6GNE66G7HSG6FDTVA43.jpg','cable fly','2026-09-20 09:17:22','2026-09-20 09:17:22');
/*!40000 ALTER TABLE `legacy_gym_images` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_gym_packages`
--

DROP TABLE IF EXISTS `legacy_gym_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_gym_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gym_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int unsigned NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_packages_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_gym_packages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_gym_packages` WRITE;
/*!40000 ALTER TABLE `legacy_gym_packages` DISABLE KEYS */;
INSERT INTO `legacy_gym_packages` VALUES
(1,1,'Paket Harian','1 hari',25000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(2,1,'Paket Mingguan','1 minggu',100000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(3,1,'Paket Bulanan','1 bulan',250000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(4,2,'Paket Harian','1 hari',20000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(5,2,'Paket Mingguan','1 minggu',80000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(6,2,'Paket Bulanan','1 bulan',200000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(7,3,'Paket Harian','1 hari',30000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(8,3,'Paket Mingguan','1 minggu',120000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(9,3,'Paket Bulanan','1 bulan',300000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(10,4,'Paket Harian','1 hari',15000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(11,4,'Paket Mingguan','1 minggu',70000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(12,4,'Paket Bulanan','1 bulan',150000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(13,5,'Paket Harian','1 hari',25000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(14,5,'Paket Mingguan','1 minggu',100000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(15,5,'Paket Bulanan','1 bulan',275000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(16,6,'Paket Harian','1 hari',20000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(17,6,'Paket Mingguan','1 minggu',90000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(18,6,'Paket Bulanan','1 bulan',225000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(19,7,'Paket Harian','1 hari',20000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(20,7,'Paket Mingguan','1 minggu',85000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(21,7,'Paket Bulanan','1 bulan',200000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(22,8,'Paket Harian','1 hari',50000,NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(23,8,'Paket Mingguan','1 minggu',200000,NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(24,8,'Paket Bulanan','1 bulan',450000,NULL,'2026-09-20 00:15:16','2026-09-20 00:15:16'),
(25,1,'Paket Tahunan','1 Tahun',2500000,NULL,'2026-09-20 09:18:16','2026-09-20 09:18:16');
/*!40000 ALTER TABLE `legacy_gym_packages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_gyms`
--

DROP TABLE IF EXISTS `legacy_gyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_gyms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `opening_hours` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gyms_slug_unique` (`slug`),
  KEY `gyms_category_id_foreign` (`category_id`),
  KEY `gyms_region_index` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_gyms`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_gyms` WRITE;
/*!40000 ALTER TABLE `legacy_gyms` DISABLE KEYS */;
INSERT INTO `legacy_gyms` VALUES
(1,2,'Mesin Otot Gym Center','fitzone-tanjungpinang','Fitness center di pusat kota dengan alat kardio dan beban yang lengkap, cocok untuk pemula maupun yang sudah rutin berlatih.','Cafe Bike Care, Komp Ruko Bintan Center Blok O No 10-11 Lt 2, Air Raja, Tanjungpinang Timur, Tanjung Pinang City, Riau Islands 29125','Tanjungpinang','Tanjungpinang Kota','6285159392233','6285159392233',0.9197680,104.5055680,'05:30 - 22:00','gyms/01M2Z5HNVQ7JX794XPK6KJEQSA.jpg','2026-09-20 00:15:15','2026-09-20 10:24:48'),
(2,1,'Power House Gym','power-house-gym','Gym fokus latihan beban dengan area free weight yang luas dan suasana latihan yang santai.','Jl. Contoh No. 25, Tanjungpinang','Tanjungpinang','Tanjungpinang Timur','0771-000002','6281200000002',0.9110000,104.4790000,'07:00 - 21:00','gyms/01M2Z62QG2XAFP6CPQJ33ZABKE.jpg','2026-09-20 00:15:15','2026-09-20 10:34:07'),
(3,1,'Titan Fitness','titan-fitness','Gym dengan peralatan mesin lengkap dan ruang latihan ber-AC. Tersedia personal trainer.','Jl. Contoh No. 7, Tanjungpinang','Tanjungpinang','Bukit Bestari','0771-000003','6281200000003',0.9350000,104.4560000,'06:00 - 22:00','gyms/01M2Z624FSTPNQYCTSNBD78ZH7.jpg','2026-09-20 00:15:15','2026-09-20 10:33:47'),
(4,1,'Iron Kepri Gym','iron-kepri-gym','Gym sederhana dengan harga bersahabat untuk pelajar dan mahasiswa.','Jl. Contoh No. 33, Tanjungpinang','Tanjungpinang','Tanjungpinang Barat','0771-000004','6281200000004',0.9200000,104.4480000,'08:00 - 21:00','gyms/01M2Z61GSYJXP5BCA99X144B5S.jpg','2026-09-20 00:15:15','2026-09-20 10:33:27'),
(5,4,'Srikandi Women Fitness','srikandi-women-fitness','Gym khusus perempuan dengan instruktur perempuan dan kelas senam rutin.','Jl. Contoh No. 18, Tanjungpinang','Tanjungpinang','Tanjungpinang Kota','0771-000005','6281200000005',0.9165000,104.4620000,'08:00 - 20:00','gyms/01M2Z60WQBS097R59C5VVYDRTP.jpg','2026-09-20 00:15:15','2026-09-20 10:33:07'),
(6,2,'Bintan Fitness Center','bintan-fitness-center','Pusat kebugaran di Kijang dengan area kardio, beban, dan lapangan parkir yang luas.','Jl. Contoh No. 5, Kijang, Bintan','Bintan','Bintan Timur','0771-000006','6281200000006',NULL,NULL,'06:00 - 21:00','gyms/01M2Z60DZTPH1D16P5SQ3EPHX7.jpg','2026-09-20 00:15:15','2026-09-20 10:32:51'),
(7,1,'Uban Muscle Club','uban-muscle-club','Gym di Tanjung Uban dengan peralatan beban lengkap untuk pekerja dan warga sekitar.','Jl. Contoh No. 12, Tanjung Uban, Bintan','Bintan','Bintan Utara','0771-000007','6281200000007',1.0667000,104.2167000,'07:00 - 22:00','gyms/01M2Z5ZWWTD01EKTQY79Z3BW9D.jpg','2026-09-20 00:15:15','2026-09-20 10:32:34'),
(8,3,'Bintan CrossFit Box','bintan-crossfit-box','Box CrossFit dengan kelas terjadwal dipandu pelatih. Tersedia sesi pengenalan untuk pemula.','Jl. Contoh No. 3, Bintan','Bintan','Teluk Sebong','0771-000008','6281200000008',9.1010000,104.4500000,'05:30 - 20:00','gyms/01M2Z5ZEM0A3G8S44EGP3NMYSS.jpg','2026-09-20 00:15:15','2026-09-20 10:32:19'),
(9,1,'cukipGym','cukipgym','wassap','Jalan Sidomulyo','Bintan','Bintan Timur','083142969101','6283142969101',0.9028030,104.4873970,'06:00 - 22:00','gyms/01M2YR34DDTZS9VJNESZMKMYHW.jpg','2026-09-20 06:29:40','2026-09-20 06:29:40');
/*!40000 ALTER TABLE `legacy_gyms` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_job_batches`
--

DROP TABLE IF EXISTS `legacy_job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_job_batches` WRITE;
/*!40000 ALTER TABLE `legacy_job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_jobs`
--

DROP TABLE IF EXISTS `legacy_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_jobs` WRITE;
/*!40000 ALTER TABLE `legacy_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_migrations`
--

DROP TABLE IF EXISTS `legacy_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_migrations` WRITE;
/*!40000 ALTER TABLE `legacy_migrations` DISABLE KEYS */;
INSERT INTO `legacy_migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_09_20_065103_create_categories_table',1),
(5,'2026_09_20_065104_create_gyms_table',1),
(6,'2026_09_20_065105_create_facilities_table',1),
(7,'2026_09_20_065106_create_gym_packages_table',1),
(8,'2026_09_20_065107_create_gym_images_table',1),
(9,'2026_09_20_065108_create_reviews_table',1);
/*!40000 ALTER TABLE `legacy_migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_password_reset_tokens`
--

DROP TABLE IF EXISTS `legacy_password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `legacy_password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_reviews`
--

DROP TABLE IF EXISTS `legacy_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `gym_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_user_id_gym_id_unique` (`user_id`,`gym_id`),
  KEY `reviews_gym_id_foreign` (`gym_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_reviews`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_reviews` WRITE;
/*!40000 ALTER TABLE `legacy_reviews` DISABLE KEYS */;
INSERT INTO `legacy_reviews` VALUES
(1,4,1,4,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 09:21:00'),
(2,5,1,3,'Harga terjangkau, cocok untuk pemula.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(3,7,1,3,'AC dingin dan tempatnya terawat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(4,3,2,3,'Lokasinya mudah dijangkau, jam bukanya pas.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(5,5,2,3,'Staf ramah dan membantu menjelaskan cara pakai alat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(6,6,2,4,'Harga terjangkau, cocok untuk pemula.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(7,2,3,4,'Lokasinya mudah dijangkau, jam bukanya pas.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(8,5,3,5,'AC dingin dan tempatnya terawat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(9,6,3,3,'Alat cukup lengkap, tapi parkir agak sempit.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(10,2,4,3,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(11,4,4,4,'AC dingin dan tempatnya terawat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(12,8,4,5,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(13,3,5,3,'Sedikit ramai di sore hari, tapi overall bagus.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(14,6,5,3,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(15,8,5,5,'Tempatnya bersih dan alatnya cukup lengkap.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(16,4,6,4,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(17,7,6,4,'Tempatnya bersih dan alatnya cukup lengkap.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(18,8,6,4,'Staf ramah dan membantu menjelaskan cara pakai alat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(19,4,7,3,'Sedikit ramai di sore hari, tapi overall bagus.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(20,5,7,3,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(21,6,7,3,'Harga terjangkau, cocok untuk pemula.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(22,3,8,3,'AC dingin dan tempatnya terawat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(23,4,8,3,'Alat cukup lengkap, tapi parkir agak sempit.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(24,5,8,3,'Harga terjangkau, cocok untuk pemula.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16'),
(25,9,6,5,'cihuyyy','approved','2026-09-20 05:55:27','2026-09-20 05:55:56');
/*!40000 ALTER TABLE `legacy_reviews` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_sessions`
--

DROP TABLE IF EXISTS `legacy_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_sessions` WRITE;
/*!40000 ALTER TABLE `legacy_sessions` DISABLE KEYS */;
INSERT INTO `legacy_sessions` VALUES
('Ne1JMY0uqRkpYyFvr0Knp8SallFxYFhZt6av6jZZ',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJBMzVFRHZDeUI4MWRiczJ4cWhxTGNDMnR1aEI1Tk1zUDZ6SG9NbTROIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1794348555),
('TIaszuabJ1Apy9nIB3rz6WOGTajDuq1ysStWJwcV',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJESWNCUVAxaDBDZzg3S24zdXU4dEpJQmNSSUhXa1hWVGd0R0xWbFo5IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiMjA3MWY1M2FmYTJlYjc2MDI5ZGRkYWZmYjk5OWMwNjIxNzJhZjNkZTMyODY0MzhkY2YxZWUwZWZmMjlkZjMzMiIsInRhYmxlcyI6eyJlZDNiMzhhOTI0YTA1MWJmOWZlOTk5Y2I0ZjE3MjhiOV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWUiLCJsYWJlbCI6Ik5hbWEiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2F0ZWdvcnkubmFtZSIsImxhYmVsIjoiS2F0ZWdvcmkiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicmVnaW9uIiwibGFiZWwiOiJXaWxheWFoIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IkRpYnVhdCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XSwiYzk3MmQzZjEwM2Y1OGY3ZmUyZTcwZjY2MzllYjI2ZWJfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ1c2VyLm5hbWUiLCJsYWJlbCI6IlVzZXIiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZ3ltLm5hbWUiLCJsYWJlbCI6Ikd5bSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyYXRpbmciLCJsYWJlbCI6IlJhdGluZyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyZXZpZXciLCJsYWJlbCI6IktvbWVudGFyIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IlRhbmdnYWwiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sImU2NDQ4MzNmNGU0ZTA4NzEyMzE1ZGE3MWIzM2ZhY2QyX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtZSIsImxhYmVsIjoiTmFtYSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJlbWFpbCIsImxhYmVsIjoiRW1haWwiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicm9sZSIsImxhYmVsIjoiUm9sZSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyZXZpZXdzX2NvdW50IiwibGFiZWwiOiJSZXZpZXciLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY3JlYXRlZF9hdCIsImxhYmVsIjoiVGVyZGFmdGFyIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCI1NzYzNTllMTcyZjMxZjA3ODNhNDBmNTEyYTVlZGY1NV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImltYWdlIiwibGFiZWwiOiJGb3RvIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWUiLCJsYWJlbCI6Ik5hbWEiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2F0ZWdvcnkubmFtZSIsImxhYmVsIjoiS2F0ZWdvcmkiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicmVnaW9uIiwibGFiZWwiOiJXaWxheWFoIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImFwcHJvdmVkX3Jldmlld3NfYXZnX3JhdGluZyIsImxhYmVsIjoiUmF0aW5nIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IkRpYnVhdCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9XSwiNzg2ODgwOTI2ODNhMzJkMzM2MzA1ZTdlM2IyODZkMzFfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJuYW1lIiwibGFiZWwiOiJOYW1hIHBha2V0IiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImR1cmF0aW9uIiwibGFiZWwiOiJEdXJhc2kiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicHJpY2UiLCJsYWJlbCI6IkhhcmdhIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJjYWIwZmMwYTE2OGI1YzU3ZGVjNDFiZmIyYzA5ODljZF9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Imd5bS5uYW1lIiwibGFiZWwiOiJHeW0iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtZSIsImxhYmVsIjoiUGFrZXQiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZHVyYXRpb24iLCJsYWJlbCI6IkR1cmFzaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJwcmljZSIsImxhYmVsIjoiSGFyZ2EiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sImM4MjE0M2QzOGVlNDU4ZDliYTIwN2FkMWU2ODVjOTMyX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiaW1hZ2UiLCJsYWJlbCI6IkZvdG8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2FwdGlvbiIsImxhYmVsIjoiS2V0ZXJhbmdhbiIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XX0sImZpbGFtZW50IjpbXX0=',1789900670);
/*!40000 ALTER TABLE `legacy_sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legacy_users`
--

DROP TABLE IF EXISTS `legacy_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legacy_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legacy_users` WRITE;
/*!40000 ALTER TABLE `legacy_users` DISABLE KEYS */;
INSERT INTO `legacy_users` VALUES
(1,'Admin GymPinang','admin@gympinang.test',NULL,'$2y$12$aZ5EURSMWSzSsPdZnkl7GeqTwf8JHZV4EWrposkJ3OjUlv59led2q','admin',NULL,'2026-09-20 00:15:14','2026-09-20 00:15:14'),
(2,'User Demo','user@gympinang.test',NULL,'$2y$12$sc8MhSJ5a1zTQQzOXj8BHeRKu45fybNKRaX/6Nz.8zclAdkvCHM7i','user',NULL,'2026-09-20 00:15:14','2026-09-20 00:15:14'),
(3,'Andi Contoh','member1@example.com',NULL,'$2y$12$UbSUcKydabKqhR9.6QieMev3eownWMI2mu6ninhrXRAjD0Fnj0lKi','user',NULL,'2026-09-20 00:15:14','2026-09-20 00:15:14'),
(4,'Sari Contoh','member2@example.com',NULL,'$2y$12$8rEttaIx7m2n/Uae9TxjeOJEDbxE6HrY8ltSzHhw8urfhi3Qr2BnO','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(5,'Budi Contoh','member3@example.com',NULL,'$2y$12$Gn7T.0j/mBwMJ1524j67Wubt3lCIm6pmHOc4PuXS62SrGzC7XlH8W','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(6,'Dewi Contoh','member4@example.com',NULL,'$2y$12$NpTna4FvRkoadwr/5BJIjOVEjOJ30EofwsUWEsyGhIWkKmR7xGvB.','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(7,'Rizky','member5@example.com',NULL,'$2y$12$mItQm36K1x9nAu4BY.OEIurMotl54LvxjQU5NPJ5kDTr20dd/soMS','user',NULL,'2026-09-20 00:15:15','2026-09-20 09:23:09'),
(8,'Maya Contoh','member6@example.com',NULL,'$2y$12$4Ndk38eV.AtPi2SXQAcMMOtqT8s4jshdrJrOeaFXz35K1mshDWfZq','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(9,'cukip','cukip404@gmail.com',NULL,'$2y$12$XYOcL0iB2MI8YFm3adnWoOZUAFN7.xgOso8Vm1IzQsQBn15pPfj26','user',NULL,'2026-09-20 04:50:20','2026-09-20 04:50:20');
/*!40000 ALTER TABLE `legacy_users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `membership_plans`
--

DROP TABLE IF EXISTS `membership_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `membership_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_days` int unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `original_price` int unsigned DEFAULT NULL,
  `discount_label` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `benefits` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membership_plans`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `membership_plans` WRITE;
/*!40000 ALTER TABLE `membership_plans` DISABLE KEYS */;
INSERT INTO `membership_plans` VALUES
(6,'1 Day Pass',1,50000,NULL,NULL,'Trial','Coba pengalaman latihan di CukipGym untuk satu kali kunjungan tanpa komitmen panjang.','[]',1,'2026-11-14 15:04:03','2026-11-14 15:04:03'),
(7,'1 Month',30,500000,NULL,NULL,NULL,'Nikmati akses membership selama satu bulan untuk mulai membangun rutinitas latihanmu.','[]',1,'2026-11-14 15:04:03','2026-11-14 15:04:03'),
(8,'3 Month',90,1350000,1500000,'10% OFF',NULL,'Mulai membangun kebiasaan latihan dengan masa aktif tiga bulan dan bonus pembelian.','[\"1 sesi personal trainer\", \"Gratis gantungan kunci CukipGym\"]',1,'2026-11-14 15:04:03','2026-11-14 15:04:03'),
(9,'6 Month',180,2400000,3000000,'20% OFF',NULL,'Bangun progres lebih konsisten dengan masa aktif enam bulan dan bonus pendamping.','[\"2 sesi personal trainer\", \"Gratis kaos CukipGym\", \"Gratis gantungan kunci CukipGym\"]',1,'2026-11-14 15:04:03','2026-11-14 15:04:03'),
(10,'12 Month',365,4200000,6000000,'30% OFF',NULL,'Jadikan latihan sebagai gaya hidup dengan membership satu tahun dan bonus eksklusif.','[\"3 sesi personal trainer\", \"Gratis shaker CukipGym\", \"Gratis kaos CukipGym\", \"Gratis gantungan kunci CukipGym\"]',1,'2026-11-14 15:04:03','2026-11-14 15:04:03'),
(11,'18 Month',540,5400000,9000000,'40% OFF','Best Value','Pilihan terlengkap untuk perjalanan latihan jangka panjang dengan nilai terbaik.','[\"5 sesi personal trainer\", \"Gratis tas gym CukipGym\", \"Gratis handuk CukipGym\", \"Gratis shaker CukipGym\", \"Gratis kaos CukipGym\", \"Gratis gantungan kunci CukipGym\"]',1,'2026-11-14 15:04:03','2026-11-14 15:04:03');
/*!40000 ALTER TABLE `membership_plans` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `memberships`
--

DROP TABLE IF EXISTS `memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `memberships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `membership_plan_id` bigint unsigned NOT NULL,
  `quantity` smallint unsigned NOT NULL DEFAULT '1',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memberships_user_id_foreign` (`user_id`),
  KEY `memberships_membership_plan_id_foreign` (`membership_plan_id`),
  CONSTRAINT `memberships_membership_plan_id_foreign` FOREIGN KEY (`membership_plan_id`) REFERENCES `membership_plans` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `memberships_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memberships`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `memberships` WRITE;
/*!40000 ALTER TABLE `memberships` DISABLE KEYS */;
INSERT INTO `memberships` VALUES
(12,15,9,1,'active','2026-11-14','2027-05-13','2026-11-14 23:12:05','2026-11-14 23:12:05'),
(13,16,7,1,'active','2026-11-15','2026-12-15','2026-11-15 11:15:18','2026-11-15 11:15:18');
/*!40000 ALTER TABLE `memberships` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_09_22_222354_drop_old_gym_directiry_tables',1),
(5,'2026_09_22_222449_create_gym_profiles_table',1),
(6,'2026_09_22_222450_create_membership_plans_table',1),
(7,'2026_09_22_222451_create_memberships_table',1),
(8,'2026_09_22_222452_create_transactions_table',1),
(9,'2026_09_22_222544_create_gym_classes_table',1),
(10,'2026_09_22_222545_create_class_sessions_table',1),
(11,'2026_09_22_222546_create_class_bookings_table',1),
(12,'2026_09_22_223253_add_phone_to_users_table',1),
(13,'2026_09_23_053342_create_facilities_table',1),
(14,'2026_09_23_053343_create_reviews_table',1),
(15,'2026_11_07_012940_add_name_to_reviews_table',1),
(16,'2026_11_08_220857_add_cart_checkout_fields_to_memberships_and_transactions',1),
(17,'2026_11_09_125344_create_payments_table',1),
(18,'2026_11_09_125345_add_payment_id_to_transactions_table',1),
(19,'2026_11_10_223640_add_account_delivery_fields_to_payments_table',1),
(20,'2026_11_10_224054_add_guest_checkout_details_to_payments_table',1),
(21,'2026_11_11_170237_add_payment_confirmation_sent_at_to_payments_table',2),
(22,'2026_11_14_145016_add_promotional_fields_to_membership_plans_table',3),
(23,'2026_11_14_222212_create_personal_trainers_table',4),
(24,'2026_11_14_222213_create_trainer_slots_table',4),
(25,'2026_11_14_222214_create_trainer_bookings_table',4),
(26,'2026_11_14_234222_add_certification_documents_to_personal_trainers_table',5),
(27,'2026_11_15_101140_add_category_and_image_path_to_facilities_table',6),
(28,'2026_11_15_103347_backfill_facility_descriptions',7),
(29,'2026_11_15_160228_add_homepage_content_to_gym_profiles_table',8),
(30,'2026_11_15_170332_create_gym_locations_table',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checkout_items` json DEFAULT NULL,
  `checkout_batch` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_amount` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `creates_account` tinyint(1) NOT NULL DEFAULT '0',
  `account_password` text COLLATE utf8mb4_unicode_ci,
  `credentials_sent_at` timestamp NULL DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midtrans_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fraud_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` text COLLATE utf8mb4_unicode_ci,
  `redirect_url` text COLLATE utf8mb4_unicode_ci,
  `last_notification` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_confirmation_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_checkout_batch_unique` (`checkout_batch`),
  UNIQUE KEY `payments_order_id_unique` (`order_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_midtrans_transaction_id_index` (`midtrans_transaction_id`),
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,NULL,'Fachrezi Bachri','fachreeezibachri@gmail.com','0831682183920','[{\"name\": \"Day Pass\", \"price\": 50000, \"quantity\": 1, \"subtotal\": 50000, \"membership_plan_id\": 1}]','c0a70fab-3d76-4879-b208-ed1e91f08f82','GYMPINANG-c0a70fab-3d76-4879-b208-ed1e91f08f82',50000,'pending',1,NULL,NULL,'90387b58-5301-4ea7-bbe7-08c66c8e78fe','pending','qris','accept','399da4e1-a709-4fbf-b3f6-7711a20c0349','https://app.sandbox.midtrans.com/snap/v4/redirection/399da4e1-a709-4fbf-b3f6-7711a20c0349','{\"currency\": \"IDR\", \"order_id\": \"GYMPINANG-c0a70fab-3d76-4879-b208-ed1e91f08f82\", \"expiry_time\": \"2026-11-11 11:06:24\", \"merchant_id\": \"M332158236\", \"status_code\": \"201\", \"fraud_status\": \"accept\", \"gross_amount\": \"50000.00\", \"payment_type\": \"qris\", \"signature_key\": \"e89a76dabe0b38c7ec7f6984f5f1674c16331cca552f064319c96e9e9f09997e047186e918c32f984c34bb8488dcda1b43ea347ba2ec02d507836731126b5f6d\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"90387b58-5301-4ea7-bbe7-08c66c8e78fe\", \"transaction_time\": \"2026-11-11 10:51:24\", \"transaction_status\": \"pending\"}',NULL,NULL,'2026-11-11 10:50:38','2026-11-11 10:51:34'),
(2,NULL,'Fachrezi Bachri','fachreeezibachri@gmail.com','0831682183920','[{\"name\": \"Day Pass\", \"price\": 50000, \"quantity\": 1, \"subtotal\": 50000, \"membership_plan_id\": 1}]','91d8ce66-5ed2-41cd-a33f-01da9dbdb3c6','GYMPINANG-91d8ce66-5ed2-41cd-a33f-01da9dbdb3c6',50000,'pending',1,NULL,NULL,NULL,NULL,NULL,NULL,'7909b2f5-656f-41c2-986d-559239ba1da6','https://app.sandbox.midtrans.com/snap/v4/redirection/7909b2f5-656f-41c2-986d-559239ba1da6',NULL,NULL,NULL,'2026-11-11 18:19:59','2026-11-11 18:27:32'),
(4,NULL,'Fachrezi Bachri','fachreeeeezibachri@gmail.com','0821833899','[{\"name\": \"Day Pass\", \"price\": 50000, \"quantity\": 1, \"subtotal\": 50000, \"membership_plan_id\": 1}]','37de940d-06a0-4961-93b6-e89b9d6388b9','GYMPINANG-37de940d-06a0-4961-93b6-e89b9d6388b9',50000,'pending',1,NULL,NULL,NULL,NULL,NULL,NULL,'5b0c735d-5bf6-4def-a5cc-34330b8ef759','https://app.sandbox.midtrans.com/snap/v4/redirection/5b0c735d-5bf6-4def-a5cc-34330b8ef759',NULL,NULL,NULL,'2026-11-11 18:35:29','2026-11-11 18:35:29'),
(8,15,'Fachrezi Bachri','fachrezibachri@gmail.com','0821833899','[{\"name\": \"VIP\", \"price\": 500000, \"quantity\": 1, \"subtotal\": 500000, \"membership_plan_id\": 4}]','9d2506d0-ac41-4c94-a63b-32eb6b84c673','GYMPINANG-9d2506d0-ac41-4c94-a63b-32eb6b84c673',500000,'paid',1,NULL,'2026-11-14 11:37:43','04c335a4-828d-46cb-9d79-811a6fe5e43f','settlement','qris','accept','d364b4f2-4f37-4228-b020-3b8e60d03c1d','https://app.sandbox.midtrans.com/snap/v4/redirection/d364b4f2-4f37-4228-b020-3b8e60d03c1d','{\"issuer\": \"gopay\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"order_id\": \"GYMPINANG-9d2506d0-ac41-4c94-a63b-32eb6b84c673\", \"expiry_time\": \"2026-11-14 11:52:26\", \"merchant_id\": \"M332158236\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"500000.00\", \"payment_type\": \"qris\", \"signature_key\": \"d1277771ea053fec23b7f188dccedce9dda0b5b68db6fa615218f8392851dc803f396a036d51826ffd45e463949f641ff978ca624d156bb3620a51c8e13e3160\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"04c335a4-828d-46cb-9d79-811a6fe5e43f\", \"settlement_time\": \"2026-11-14 11:37:34\", \"transaction_time\": \"2026-11-14 11:37:26\", \"transaction_type\": \"on-us\", \"transaction_status\": \"settlement\", \"merchant_cross_reference_id\": \"e886c807-dbc4-4d69-97f2-357ebf11eeda\"}','2026-11-14 11:37:43','2026-11-14 11:37:43','2026-11-14 11:37:22','2026-11-14 11:37:49'),
(9,16,'Fachrezi Bachri','fachreeezibachri@gmail.com','0821833899','[{\"name\": \"1 Month\", \"price\": 500000, \"quantity\": 1, \"subtotal\": 500000, \"membership_plan_id\": 7}]','22cd2e48-7096-4a16-b0ff-cc1db5ef533c','GYMPINANG-22cd2e48-7096-4a16-b0ff-cc1db5ef533c',500000,'paid',1,NULL,'2026-11-15 11:15:18','3192ea59-4fc7-4fdc-89fc-e8922a71a47f','settlement','qris','accept','ea231f52-b29c-4af1-a987-753dd055d02e','https://app.sandbox.midtrans.com/snap/v4/redirection/ea231f52-b29c-4af1-a987-753dd055d02e','{\"issuer\": \"gopay\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"order_id\": \"GYMPINANG-22cd2e48-7096-4a16-b0ff-cc1db5ef533c\", \"expiry_time\": \"2026-11-15 11:30:06\", \"merchant_id\": \"M332158236\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"500000.00\", \"payment_type\": \"qris\", \"signature_key\": \"3f5baa25d1112352e6fc86d5e44546ebcf1fe6b566adac40533f40e2b3ff1bf3478d86f343433d1de97fcf9a408d8d90010adf0b6e7ea0cfa94618ebe2cf589e\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"3192ea59-4fc7-4fdc-89fc-e8922a71a47f\", \"settlement_time\": \"2026-11-15 11:15:13\", \"transaction_time\": \"2026-11-15 11:15:06\", \"transaction_type\": \"on-us\", \"transaction_status\": \"settlement\", \"merchant_cross_reference_id\": \"e886c807-dbc4-4d69-97f2-357ebf11eeda\"}','2026-11-15 11:15:18','2026-11-15 11:15:18','2026-11-15 11:15:00','2026-11-15 11:15:22');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `personal_trainers`
--

DROP TABLE IF EXISTS `personal_trainers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_trainers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `experience_years` smallint unsigned NOT NULL DEFAULT '0',
  `certifications` json DEFAULT NULL,
  `certification_documents` json DEFAULT NULL,
  `session_price` bigint unsigned NOT NULL,
  `session_minutes` smallint unsigned NOT NULL DEFAULT '60',
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personal_trainers_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_trainers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `personal_trainers` WRITE;
/*!40000 ALTER TABLE `personal_trainers` DISABLE KEYS */;
INSERT INTO `personal_trainers` VALUES
(1,'Kak Bayu','Functional Training','Pendamping latihan fungsional untuk kekuatan, mobilitas, dan kebugaran harian.',4,'[\"Certified Personal Trainer\", \"Functional Training Specialist\"]',NULL,150000,180,NULL,1,'2026-11-14 23:03:16','2026-11-14 23:03:16'),
(2,'Kak Rina','Mobility & Yoga','Program privat untuk fleksibilitas, postur, mobilitas, dan pemulihan tubuh.',5,'[\"Yoga Teacher Certification\", \"Mobility Coach\"]',NULL,150000,120,NULL,1,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(3,'Kak Doni','Strength & Conditioning','Latihan kekuatan terukur untuk meningkatkan performa dan teknik gerakan.',6,'[\"Strength Coach\", \"Certified Personal Trainer\"]',NULL,175000,180,NULL,1,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(4,'Kak Sinta','Weight Management','Pendampingan latihan personal untuk membangun kebiasaan aktif dan progres berkelanjutan.',3,'[\"Weight Management Specialist\", \"Certified Personal Trainer\"]',NULL,150000,120,NULL,1,'2026-11-14 23:03:17','2026-11-14 23:03:17');
/*!40000 ALTER TABLE `personal_trainers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_user_id_unique` (`user_id`),
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES
(1,2,4,'Tempatnya bersih dan pelatihnya ramah.','approved','2026-11-11 05:13:16','2026-11-11 05:13:16',NULL),
(2,3,3,'AC dingin dan tempatnya terawat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16','Andi Contoh'),
(3,4,4,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 09:21:00','Sari Contoh'),
(4,5,3,'Harga terjangkau, cocok untuk pemula.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16','Budi Contoh'),
(5,6,3,'Harga terjangkau, cocok untuk pemula.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16','Dewi Contoh'),
(6,8,3,'Suasananya nyaman, tidak terlalu ramai di pagi hari.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16','User Demo'),
(7,9,4,'Tempatnya bersih dan alatnya cukup lengkap.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16','Rizky'),
(8,10,4,'Staf ramah dan membantu menjelaskan cara pakai alat.','approved','2026-09-20 00:15:16','2026-09-20 00:15:16','Maya Contoh'),
(9,11,5,'cihuyyy','approved','2026-09-20 05:55:27','2026-09-20 05:55:56','cukip');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('AqEbyMBQfFW5Gy3iqXq9f37oUmtyQTTQ2xdvP2rV',NULL,'127.0.0.1','okhttp/4.12.0','eyJfdG9rZW4iOiJGUXJWS0JHTkpXd05RRThSWmdKbDE2amZjR2FiVWNyM2NYR2ZhTzF1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1794748962),
('KQ9dY5U7QyrMpkVxOWux1PW5HNiAbI2grl6EoE11',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ1ekxKYndsM0FMY3Vxemt0VHB1VUx1MmJWYnVUcUtjWjR1ZllGZGp6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2dvbGRtaW5lLWNhY2hlLWx1bWlub3VzLm5ncm9rLWZyZWUuZGV2Iiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1794753862),
('OOOM7FnXFRKUAG6EYzRDBSSflONEJFNnXE3tcpD7',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI1OUFETmMyQ0RkWUo1bjYwemlMNlB1TzdkNGZSMTQzTk05dlhwNjJ0IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1794753951),
('Qm53Je9gKPoobMLlH89LuZgOHwqpaw6I4Lm8Myda',NULL,'127.0.0.1','','eyJfdG9rZW4iOiIzenhwR1FYWVdTQmw0WWtQc2FQWVFnWE9QQ2o4TmZCWWUyckFDc3ByIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1794748669);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `trainer_bookings`
--

DROP TABLE IF EXISTS `trainer_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainer_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `trainer_slot_id` bigint unsigned NOT NULL,
  `payment_id` bigint unsigned NOT NULL,
  `amount` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trainer_bookings_payment_id_trainer_slot_id_unique` (`payment_id`,`trainer_slot_id`),
  KEY `trainer_bookings_user_id_foreign` (`user_id`),
  KEY `trainer_bookings_trainer_slot_id_foreign` (`trainer_slot_id`),
  KEY `trainer_bookings_status_index` (`status`),
  CONSTRAINT `trainer_bookings_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trainer_bookings_trainer_slot_id_foreign` FOREIGN KEY (`trainer_slot_id`) REFERENCES `trainer_slots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trainer_bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainer_bookings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `trainer_bookings` WRITE;
/*!40000 ALTER TABLE `trainer_bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_bookings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `trainer_slots`
--

DROP TABLE IF EXISTS `trainer_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainer_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personal_trainer_id` bigint unsigned NOT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `held_until` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trainer_slots_personal_trainer_id_starts_at_unique` (`personal_trainer_id`,`starts_at`),
  KEY `trainer_slots_starts_at_index` (`starts_at`),
  KEY `trainer_slots_status_index` (`status`),
  KEY `trainer_slots_held_until_index` (`held_until`),
  CONSTRAINT `trainer_slots_personal_trainer_id_foreign` FOREIGN KEY (`personal_trainer_id`) REFERENCES `personal_trainers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainer_slots`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `trainer_slots` WRITE;
/*!40000 ALTER TABLE `trainer_slots` DISABLE KEYS */;
INSERT INTO `trainer_slots` VALUES
(1,1,'2026-11-15 09:00:00','2026-11-15 12:00:00','available',NULL,'2026-11-14 23:03:16','2026-11-14 23:03:16'),
(2,1,'2026-11-16 10:00:00','2026-11-16 13:00:00','available',NULL,'2026-11-14 23:03:16','2026-11-14 23:03:16'),
(3,1,'2026-11-17 11:00:00','2026-11-17 14:00:00','available',NULL,'2026-11-14 23:03:16','2026-11-14 23:03:16'),
(4,1,'2026-11-18 12:00:00','2026-11-18 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(5,1,'2026-11-19 13:00:00','2026-11-19 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(6,1,'2026-11-20 14:00:00','2026-11-20 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(7,1,'2026-11-21 15:00:00','2026-11-21 18:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(8,1,'2026-11-22 08:00:00','2026-11-22 11:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(9,1,'2026-11-23 09:00:00','2026-11-23 12:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(10,1,'2026-11-24 10:00:00','2026-11-24 13:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(11,1,'2026-11-25 11:00:00','2026-11-25 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(12,1,'2026-11-26 12:00:00','2026-11-26 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(13,1,'2026-11-27 13:00:00','2026-11-27 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(14,1,'2026-11-28 14:00:00','2026-11-28 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(15,2,'2026-11-15 10:00:00','2026-11-15 12:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(16,2,'2026-11-16 11:00:00','2026-11-16 13:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(17,2,'2026-11-17 12:00:00','2026-11-17 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(18,2,'2026-11-18 13:00:00','2026-11-18 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(19,2,'2026-11-19 14:00:00','2026-11-19 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(20,2,'2026-11-20 15:00:00','2026-11-20 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(21,2,'2026-11-21 08:00:00','2026-11-21 10:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(22,2,'2026-11-22 09:00:00','2026-11-22 11:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(23,2,'2026-11-23 10:00:00','2026-11-23 12:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(24,2,'2026-11-24 11:00:00','2026-11-24 13:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(25,2,'2026-11-25 12:00:00','2026-11-25 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(26,2,'2026-11-26 13:00:00','2026-11-26 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(27,2,'2026-11-27 14:00:00','2026-11-27 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(28,2,'2026-11-28 15:00:00','2026-11-28 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(29,3,'2026-11-15 11:00:00','2026-11-15 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(30,3,'2026-11-16 12:00:00','2026-11-16 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(31,3,'2026-11-17 13:00:00','2026-11-17 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(32,3,'2026-11-18 14:00:00','2026-11-18 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(33,3,'2026-11-19 15:00:00','2026-11-19 18:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(34,3,'2026-11-20 08:00:00','2026-11-20 11:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(35,3,'2026-11-21 09:00:00','2026-11-21 12:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(36,3,'2026-11-22 10:00:00','2026-11-22 13:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(37,3,'2026-11-23 11:00:00','2026-11-23 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(38,3,'2026-11-24 12:00:00','2026-11-24 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(39,3,'2026-11-25 13:00:00','2026-11-25 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(40,3,'2026-11-26 14:00:00','2026-11-26 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(41,3,'2026-11-27 15:00:00','2026-11-27 18:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(42,3,'2026-11-28 08:00:00','2026-11-28 11:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(43,4,'2026-11-15 12:00:00','2026-11-15 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(44,4,'2026-11-16 13:00:00','2026-11-16 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(45,4,'2026-11-17 14:00:00','2026-11-17 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(46,4,'2026-11-18 15:00:00','2026-11-18 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(47,4,'2026-11-19 08:00:00','2026-11-19 10:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(48,4,'2026-11-20 09:00:00','2026-11-20 11:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(49,4,'2026-11-21 10:00:00','2026-11-21 12:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(50,4,'2026-11-22 11:00:00','2026-11-22 13:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(51,4,'2026-11-23 12:00:00','2026-11-23 14:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(52,4,'2026-11-24 13:00:00','2026-11-24 15:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(53,4,'2026-11-25 14:00:00','2026-11-25 16:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(54,4,'2026-11-26 15:00:00','2026-11-26 17:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(55,4,'2026-11-27 08:00:00','2026-11-27 10:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17'),
(56,4,'2026-11-28 09:00:00','2026-11-28 11:00:00','available',NULL,'2026-11-14 23:03:17','2026-11-14 23:03:17');
/*!40000 ALTER TABLE `trainer_slots` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `payment_id` bigint unsigned DEFAULT NULL,
  `membership_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` smallint unsigned NOT NULL DEFAULT '1',
  `checkout_batch` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int unsigned NOT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `admin_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_invoice_number_unique` (`invoice_number`),
  KEY `transactions_user_id_foreign` (`user_id`),
  KEY `transactions_membership_id_foreign` (`membership_id`),
  KEY `transactions_checkout_batch_index` (`checkout_batch`),
  KEY `transactions_payment_id_foreign` (`payment_id`),
  CONSTRAINT `transactions_membership_id_foreign` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES
(10,16,9,13,'INV-20261115-0001',1,'22cd2e48-7096-4a16-b0ff-cc1db5ef533c',500000,NULL,'paid','2026-11-15 11:15:18',NULL,'2026-11-15 11:15:18','2026-11-15 11:15:18');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Admin CukipGym','admin@cukipgym.test','6281200000000',NULL,'$2y$12$CmWQfs/aRWFZsDCMOn6gd.3DskF8vbGG.9KopxaJF6BjYlSJMdMB2','admin','XSmDVDiFBIJ3ew3cBRFgoGfO71n3AV9IzkbrLoyOurl4lZQeWQCQKmDixg20','2026-11-11 05:13:15','2026-11-11 05:13:15'),
(2,'User Demo','user@cukipgym.test','6281300000000',NULL,'$2y$12$ZvOJ9U9/oRZymh3zvXjiBO7y2cHVX3UZ1sO16kZHAXcGCvYZPSAj.','user',NULL,'2026-11-11 05:13:15','2026-11-11 05:13:15'),
(3,'Andi Contoh','member1@example.com','6281200000010',NULL,'$2y$12$UbSUcKydabKqhR9.6QieMev3eownWMI2mu6ninhrXRAjD0Fnj0lKi','user',NULL,'2026-09-20 00:15:14','2026-09-20 00:15:14'),
(4,'Sari Contoh','member2@example.com','6281200000011',NULL,'$2y$12$8rEttaIx7m2n/Uae9TxjeOJEDbxE6HrY8ltSzHhw8urfhi3Qr2BnO','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(5,'Budi Contoh','member3@example.com','6281200000012',NULL,'$2y$12$Gn7T.0j/mBwMJ1524j67Wubt3lCIm6pmHOc4PuXS62SrGzC7XlH8W','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(6,'Dewi Contoh','member4@example.com','6281200000013',NULL,'$2y$12$NpTna4FvRkoadwr/5BJIjOVEjOJ30EofwsUWEsyGhIWkKmR7xGvB.','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(7,'Admin GymPinang','admin@gympinang.test',NULL,NULL,'$2y$12$aZ5EURSMWSzSsPdZnkl7GeqTwf8JHZV4EWrposkJ3OjUlv59led2q','admin',NULL,'2026-09-20 00:15:14','2026-09-20 00:15:14'),
(8,'User Demo','user@gympinang.test',NULL,NULL,'$2y$12$sc8MhSJ5a1zTQQzOXj8BHeRKu45fybNKRaX/6Nz.8zclAdkvCHM7i','user',NULL,'2026-09-20 00:15:14','2026-09-20 00:15:14'),
(9,'Rizky','member5@example.com',NULL,NULL,'$2y$12$mItQm36K1x9nAu4BY.OEIurMotl54LvxjQU5NPJ5kDTr20dd/soMS','user',NULL,'2026-09-20 00:15:15','2026-09-20 09:23:09'),
(10,'Maya Contoh','member6@example.com',NULL,NULL,'$2y$12$4Ndk38eV.AtPi2SXQAcMMOtqT8s4jshdrJrOeaFXz35K1mshDWfZq','user',NULL,'2026-09-20 00:15:15','2026-09-20 00:15:15'),
(11,'cukip','cukip404@gmail.com',NULL,NULL,'$2y$12$XYOcL0iB2MI8YFm3adnWoOZUAFN7.xgOso8Vm1IzQsQBn15pPfj26','user',NULL,'2026-09-20 04:50:20','2026-09-20 04:50:20'),
(15,'Fachrezi Bachri','fachrezibachri@gmail.com','0821833899',NULL,'$2y$12$jqAiXipEeddM62mSbwacnOfQO/NofJePUuD57JEhxsxjGDmXnBcVW','user','o9PORfw3VPqbwS6uylb1ytMQXN7rDijbNws2317fCjnomzgIMdtQG6UoPEfK','2026-11-14 11:37:43','2026-11-14 11:37:43'),
(16,'Fachrezi Bachri','fachreeezibachri@gmail.com','0821833899',NULL,'$2y$12$siV78rkHr6MkbfVURRLrweUXy051L27JIdA6qkPmqWY7j3/qPejP2','user',NULL,'2026-11-15 11:15:18','2026-11-15 11:15:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-11-15 22:00:02
