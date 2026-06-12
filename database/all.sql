-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.36 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for snsu_safetrack
CREATE DATABASE IF NOT EXISTS `snsu_safetrack` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `snsu_safetrack`;

-- Dumping structure for table snsu_safetrack.attendance_risk_scores
DROP TABLE IF EXISTS `attendance_risk_scores`;
CREATE TABLE IF NOT EXISTS `attendance_risk_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `total_lates` int(11) DEFAULT '0',
  `total_absences` int(11) DEFAULT '0',
  `risk_level` enum('low','medium','high') DEFAULT 'low',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.attendance_risk_scores: 0 rows
DELETE FROM `attendance_risk_scores`;
/*!40000 ALTER TABLE `attendance_risk_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_risk_scores` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.audits
DROP TABLE IF EXISTS `audits`;
CREATE TABLE IF NOT EXISTS `audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `old_values` text COLLATE utf8mb4_unicode_ci,
  `new_values` text COLLATE utf8mb4_unicode_ci,
  `url` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(1023) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.audits: ~54 rows (approximately)
DELETE FROM `audits`;
INSERT INTO `audits` (`id`, `user_type`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `url`, `ip_address`, `user_agent`, `tags`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 1, 'created', 'App\\Models\\Students', 1, '[]', '{"LRN":"154542121454","FirstName":"loreto","MiddleName":"stude","LastName":"PILOTON","Suffix":null,"PhoneNumber":"+639128941731","GuardianID":"16","YearLevel":"8","Strand":"HUMSS","created_by":1,"updated_by":1,"status":"active","archived":0,"school_id":1,"id":1}', 'http://saaskit.dev.com/students/create', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-26 07:13:25', '2026-05-26 07:13:25'),
	(2, 'App\\Models\\User', 1, 'created', 'App\\Models\\User', 9, '[]', '{"conn_id":1,"school_id":1,"name":"loreto PILOTON","email":"lpiloton1@tnhs.edu.ph","password":"$2y$12$ZB4.FSshFYWmk8hPy85t1.tQu0Kg2i8m\\/x70IN7Sq3J9uyVh.uMEi","qr_code":"304854000007","created_by":1,"updated_by":1,"status":"active","archived":0,"id":9}', 'http://saaskit.dev.com/students/create', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-26 07:13:25', '2026-05-26 07:13:25'),
	(3, 'App\\Models\\User', 1, 'updated', 'App\\Models\\Students', 1, '{"UserID":null}', '{"UserID":9}', 'http://saaskit.dev.com/students/create', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-26 07:13:25', '2026-05-26 07:13:25'),
	(4, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 9, '{"filepath":"0"}', '{"filepath":"users\\/students\\/9\\/70fd9bb3-a729-4af0-bd55-019ca1c8ef0c.jpeg"}', 'http://saaskit.dev.com/users/upload', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-26 07:18:26', '2026-05-26 07:18:26'),
	(5, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 9, '{"filepath":"users\\/students\\/9\\/70fd9bb3-a729-4af0-bd55-019ca1c8ef0c.jpeg"}', '{"filepath":"users\\/students\\/9\\/8aca7554-0240-4906-852b-245dd1601201.jpeg"}', 'http://saaskit.dev.com/users/upload', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-26 07:20:45', '2026-05-26 07:20:45'),
	(6, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 1, '[]', '{"filename":"2026-05-28-09-11-08.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-28-09-11-08.zip","backup_type":"db","file_size":20376,"status":"completed","created_by":1,"id":1}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-28 01:11:10', '2026-05-28 01:11:10'),
	(7, 'App\\Models\\User', 1, 'created', 'App\\Models\\LoginActivity', 1, '[]', '{"user_id":1,"email":"apiloton1@snsu.edu.ph","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:29:43","id":1}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPx6zbQGom77FtyS-ptc4oR4WWQzim2Edrr23IJq8Om5A5NG48AWugpIOsLWRmi_lQ&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=TUGcJiqZmHtmenmVSFpiN5aNxsfPdyVaLe18PU6u', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:29:43', '2026-06-09 09:29:43'),
	(8, 'App\\Models\\User', 1, 'updated', 'App\\Models\\LoginActivity', 1, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-09 17:30:55"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:30:55', '2026-06-09 09:30:55'),
	(9, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 1, '{"remember_token":"oFYER6ZMlfwIv2T4o13Fsb5aIs4agOHtsKGKnEBNYZ6PX66GYHojrK188zDN"}', '{"remember_token":"XGSrczUrXTsImd2lz3XUoMkHZaYfGNLCRTDvreWbaDNHzoGOZNvDO4jzcST8"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:30:55', '2026-06-09 09:30:55'),
	(10, 'App\\Models\\User', 4, 'created', 'App\\Models\\LoginActivity', 2, '[]', '{"user_id":4,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:31:02","id":2}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPwl05C2-KN3ojTKF413A3-cV9HYYUWqAkqxsSNVoV_zOOU1PuiJN4OEo0QBnmOQVg&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&state=JLIvfzH4Dbhf5oMGbOWZIvFsAPfumoTnh2dlPXgC', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:31:02', '2026-06-09 09:31:02'),
	(11, 'App\\Models\\User', 4, 'updated', 'App\\Models\\LoginActivity', 2, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-09 17:31:05"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:31:05', '2026-06-09 09:31:05'),
	(12, 'App\\Models\\User', 4, 'updated', 'App\\Models\\User', 4, '{"remember_token":"8EepkQ0GGeAbCLjgrx5HZuTFM0GyctXTqqB0N2xng3gLRzIswoBhSh101gZW"}', '{"remember_token":"6piHSTJSvHrCjQ1zE6TO7uYV917dNmF0oUsV5Hedv73DoNE6wit5gSAzcdOB"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:31:05', '2026-06-09 09:31:05'),
	(13, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 3, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:31:29","id":3}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPzHAhOqo-o7ax2d9yGmKwKzJGdHzmRoO5EnAXTq0d1zquZjFI3NIserS84Mj0FBzg&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=YF67ygBpltCfvzUaanvt7MuPF5b70omypCBdqrz8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:31:29', '2026-06-09 09:31:29'),
	(14, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 4, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:31:35","id":4}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPy_l6QhjOkZEnrPlS33JXF-UJzqMYJ4cHPHVglsLLipO3cEhCw-ywmYQLNMMjftlg&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=vjjUCZNLZb8yBj4JXYm5C3bAepBfJpG22Dw4gH2o', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:31:35', '2026-06-09 09:31:35'),
	(15, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 5, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:34:26","id":5}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPyth8zip0fv5gwP-OB0Gx7GmHV9Tb56jAzTbpc63tDtbZY9q6a7YosvQsyWAP2TwA&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=0yXVZKSZ9iTKnLJm2plT5JCNWpjGU5Zk4W4TYijQ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:34:26', '2026-06-09 09:34:26'),
	(16, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 6, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:35:34","id":6}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPzyHmkokfE7cD35rpkOy3Bbn6ILV24Tlu2Recqm5CmxfWumiq5jfERXNWSJjdfplw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=0fbzN4rjX6bLJP7aYekQ4vdiPFKVLv6Y1fqPAZAT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:35:34', '2026-06-09 09:35:34'),
	(17, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 7, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:36:10","id":7}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPzI7v8OU4wIJa4OHvYBYawk4kkdFdaEOsXiMorNJW0-zz9mVGuIbEHlHVTg1TbtkA&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=lSQ9wXNbJKioKue8JlmqHXx1f9vc5o1IwPkcu9HB', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:36:10', '2026-06-09 09:36:10'),
	(18, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 8, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:36:32","id":8}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPzosvZ4_zloDQqx7_iRZkdcdN-hw2_Uk4ihWWaKO1nb2aXui3bTBxYh-7soVJj0rg&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=GmjuPf55qkzoI3ny1HlgDSaKFBwHYu0XzmKCQ0cW', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:36:32', '2026-06-09 09:36:32'),
	(19, NULL, NULL, 'created', 'App\\Models\\User', 10, '[]', '{"name":"ANTONIO JR. PILOTON","email":"antoniojrpiloton@gmail.com","google_id":"112090857259724913006","avatar":"https:\\/\\/lh3.googleusercontent.com\\/a\\/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c","verified":1,"password":"$2y$12$7F9c6xAEidrM5KVU5k1HfuILUDG58jkWVV3jp8zUNCKVKkkCrG9Pm","id":10}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPxV9fHxOC4_l4TYhN2SnieLqqkiRPsJvc0Ktgs7RwNDdg-b1YHBge1jd8kHwHIw3g&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=xdtaCXFqmxxiorRyQfm2VWZM9SOv8Fuv9cbiUXfI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:38:00', '2026-06-09 09:38:00'),
	(20, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 9, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:38:00","id":9}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPxV9fHxOC4_l4TYhN2SnieLqqkiRPsJvc0Ktgs7RwNDdg-b1YHBge1jd8kHwHIw3g&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=xdtaCXFqmxxiorRyQfm2VWZM9SOv8Fuv9cbiUXfI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:38:00', '2026-06-09 09:38:00'),
	(21, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 10, '[]', '{"user_id":10,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:39:53","id":10}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPx2PUV3NxKz6xHmaA-wuvrgQ3EcOp0RoO8VpyKqxV45Zk3h2UAiC46q3VP3FpRP9w&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=puOxvrTsqo8e6nv2tLRYuSXaEe0RIXhZlzvXMXu0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:39:53', '2026-06-09 09:39:53'),
	(22, 'App\\Models\\User', 1, 'created', 'App\\Models\\LoginActivity', 11, '[]', '{"user_id":1,"email":"apiloton1@snsu.edu.ph","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:42:35","id":11}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPxHBSnb8I9vmDnAN3S6udd1HPMYVhnMs78g_izk4nld5WkcFYuOxkiK_p9Fn9AbKw&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=y5jetKTZwB2TQXnAtWSZ0CBO106esrVf5IXiJ206', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:42:35', '2026-06-09 09:42:35'),
	(23, 'App\\Models\\User', 1, 'updated', 'App\\Models\\LoginActivity', 11, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-09 17:42:37"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:42:37', '2026-06-09 09:42:37'),
	(24, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 1, '{"remember_token":"XGSrczUrXTsImd2lz3XUoMkHZaYfGNLCRTDvreWbaDNHzoGOZNvDO4jzcST8"}', '{"remember_token":"rW30YsjBFXakA3uEaJjdQSD9417bMFRGd9z57GUTWibbR0CLMl98dZfhkhxH"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:42:37', '2026-06-09 09:42:37'),
	(25, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 12, '[]', '{"user_id":10,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-09 17:42:43","id":12}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPzAjG3Clvhiq3iv7XWBHHYnMvvsDUjw0VuOawzP5ZSA9JnDSSGLGmxI2Lbe1bLnoQ&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&state=Vw8XYwv51mSTnELKbjWDkp3ZRnYuAMsJS6bVjwbO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:42:43', '2026-06-09 09:42:43'),
	(26, 'App\\Models\\User', 10, 'updated', 'App\\Models\\User', 10, '{"remember_token":null}', '{"remember_token":"558uILeLu57khROHif7Rj0fCBixYknR7E8qJykRQOxj5hdQAX7LtiqXrAAvD"}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPy9-sKJV91Lh5qk3WTebPIDp0IMxXq-kWWbuTtQj1KoqXqf35bdHDZXRo3_j07Bjg&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=6icCWOhO1wrW9RvkXnY3iNVcvHKdXi9cYXirGakT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:47:54', '2026-06-09 09:47:54'),
	(27, 'App\\Models\\User', 10, 'created', 'App\\Models\\LoginActivity', 13, '[]', '{"user_id":10,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:47:54","id":13}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPy9-sKJV91Lh5qk3WTebPIDp0IMxXq-kWWbuTtQj1KoqXqf35bdHDZXRo3_j07Bjg&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=6icCWOhO1wrW9RvkXnY3iNVcvHKdXi9cYXirGakT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:47:54', '2026-06-09 09:47:54'),
	(28, 'App\\Models\\User', 1, 'created', 'App\\Models\\LoginActivity', 14, '[]', '{"user_id":1,"email":"apiloton1@snsu.edu.ph","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:50:13","id":14}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPykn-KekWRwNSxiv8X3ukO0t7altrx7hcVO3mUf68syO8lgBV2h-Jl1L0y6s_kCyQ&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid&state=9zyCWLjdABQ7tZdH1og9e98dFdpkUFXwp6jDPfI4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:13', '2026-06-09 09:50:13'),
	(29, 'App\\Models\\User', 1, 'updated', 'App\\Models\\LoginActivity', 14, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-09 17:50:21"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:21', '2026-06-09 09:50:21'),
	(30, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 1, '{"remember_token":"rW30YsjBFXakA3uEaJjdQSD9417bMFRGd9z57GUTWibbR0CLMl98dZfhkhxH"}', '{"remember_token":"9acJqd3Qk9lG7a5sByjXlIyli8rGQBOo5nKORAOrkvzzwe6sQEAQzBTPYymb"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:21', '2026-06-09 09:50:21'),
	(31, NULL, NULL, 'created', 'App\\Models\\User', 11, '[]', '{"name":"ANTONIO JR. PILOTON","email":"antoniojrpiloton@gmail.com","google_id":"112090857259724913006","avatar":"https:\\/\\/lh3.googleusercontent.com\\/a\\/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c","verified":1,"password":"$2y$12$5PBNfpUmecYLi.ljhHdkkuJd8N5DzQCileGvaDOQfau5sKcCaZ8sO","id":11}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPwi6UfUzzDQi7wLVMzpTajVQiHF0a53JnHfFkha5i4_gUuRojCES2ZAJPWbe2BF3g&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid&state=8DXyFolBYPr6xvk9se0TFQrt2mjbkl46ohP5t1GD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:36', '2026-06-09 09:50:36'),
	(32, 'App\\Models\\User', 11, 'updated', 'App\\Models\\User', 11, '{"remember_token":null}', '{"remember_token":"c45C0ylA33m8FRi65X0SJfPggMAt6HcaQn2vySRo6OrtaIwcU3WPgfCwH3p3"}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPwi6UfUzzDQi7wLVMzpTajVQiHF0a53JnHfFkha5i4_gUuRojCES2ZAJPWbe2BF3g&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid&state=8DXyFolBYPr6xvk9se0TFQrt2mjbkl46ohP5t1GD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:36', '2026-06-09 09:50:36'),
	(33, 'App\\Models\\User', 11, 'created', 'App\\Models\\LoginActivity', 15, '[]', '{"user_id":11,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:50:36","id":15}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPwi6UfUzzDQi7wLVMzpTajVQiHF0a53JnHfFkha5i4_gUuRojCES2ZAJPWbe2BF3g&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid&state=8DXyFolBYPr6xvk9se0TFQrt2mjbkl46ohP5t1GD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:36', '2026-06-09 09:50:36'),
	(34, 'App\\Models\\User', 11, 'updated', 'App\\Models\\LoginActivity', 15, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-11 12:43:23"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 04:43:23', '2026-06-11 04:43:23'),
	(35, 'App\\Models\\User', 11, 'updated', 'App\\Models\\User', 11, '{"remember_token":"c45C0ylA33m8FRi65X0SJfPggMAt6HcaQn2vySRo6OrtaIwcU3WPgfCwH3p3"}', '{"remember_token":"pLOxNuqLmdbY0uBUYUT5tAoVaHZ39q1X8p6sQIP6G6bNCOFkmvNGgsn0Kugg"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 04:43:23', '2026-06-11 04:43:23'),
	(36, 'App\\Models\\User', 1, 'created', 'App\\Models\\LoginActivity', 16, '[]', '{"user_id":1,"email":"apiloton1@snsu.edu.ph","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-11 12:48:02","id":16}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPxBCVkNACTGXHYGYT0bs-Jd_ETkRETE5ep0mYNNBISXipTuKZ_UrphZKEYw9Iq6mA&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&state=F3IG0OdsEj52HYzzUGUSZhoczx5G52gje7TfMnab', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 04:48:02', '2026-06-11 04:48:02'),
	(37, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 2, '[]', '{"filename":"2026-06-11-13-58-51.zip","filepath":"C:\\\\wamp64\\\\www\\\\saas_pos\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-06-11-13-58-51.zip","backup_type":"db","file_size":51145,"status":"completed","created_by":1,"id":2}', 'http://pos.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 05:58:53', '2026-06-11 05:58:53'),
	(40, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 17, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-11 14:18:18","id":17}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPyHXpSeZroYxCM-kxo91qPoqdjJwo2U4KNISo6dizUPPJXcUW7vYfY50e5mbwaxqQ&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=Q4BHCDIiFYtfuVZCjkadxEiu0QYemEHxa3rSKAWM', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:18:18', '2026-06-11 06:18:18'),
	(41, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 18, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-11 14:18:38","id":18}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPwvSrmWUtZrEk9Qs4jzfp0Iny45M51CFTGAWnUeEeW9s7Dc6fVAz34c95rhsalAJA&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&state=DShNagfuPwcxsBj1wadZtJfioWItoK8kmrsumi43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:18:38', '2026-06-11 06:18:38'),
	(42, 'App\\Models\\User', 1, 'created', 'App\\Models\\User', 1, '[]', '{"name":"Antonio Jr Piloton","email":"apiloton1@snsu.edu.ph","google_id":"117952079880321133596","avatar":"https:\\/\\/lh3.googleusercontent.com\\/a\\/ACg8ocICxZuvbmnED8B5vH9nAzZ75oj4EyyJOq04YOMO7jxaSaNGZqsv=s96-c","verified":1,"password":"$2y$12$XxNZYMM3BRXkyG7uxpcJ..t20eaiHvmc94Bso6fQ59X7lMeaxFmSy","id":1}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPxF6xcSP_lI6KjweMEexP1JPoR4D8pt0TpO3fvaWl5E0BOUmSXd4CSoAo8yBf7GmQ&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=RfMhiw1IcBcQuxrDo8RaLA7dJoAOIDP3yQhOqF5Z', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:22:46', '2026-06-11 06:22:46'),
	(43, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 1, '{"remember_token":null}', '{"remember_token":"BHOV95JsCAhFG9ZW6REINeQLmNY8GEx4cQ4DYjYe5NPKmK5h7gqfHowmd0W0"}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPxF6xcSP_lI6KjweMEexP1JPoR4D8pt0TpO3fvaWl5E0BOUmSXd4CSoAo8yBf7GmQ&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=RfMhiw1IcBcQuxrDo8RaLA7dJoAOIDP3yQhOqF5Z', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:22:46', '2026-06-11 06:22:46'),
	(44, 'App\\Models\\User', 1, 'created', 'App\\Models\\LoginActivity', 19, '[]', '{"user_id":1,"email":"apiloton1@snsu.edu.ph","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-11 14:22:46","id":19}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPxF6xcSP_lI6KjweMEexP1JPoR4D8pt0TpO3fvaWl5E0BOUmSXd4CSoAo8yBf7GmQ&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=RfMhiw1IcBcQuxrDo8RaLA7dJoAOIDP3yQhOqF5Z', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:22:46', '2026-06-11 06:22:46'),
	(45, 'App\\Models\\User', 1, 'updated', 'App\\Models\\LoginActivity', 19, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-11 14:23:03"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:23:03', '2026-06-11 06:23:03'),
	(46, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 1, '{"remember_token":"BHOV95JsCAhFG9ZW6REINeQLmNY8GEx4cQ4DYjYe5NPKmK5h7gqfHowmd0W0"}', '{"remember_token":"NSmnG9ZakF7RvzZC7U1r5LyI7SHlk0J5H1qLQGXZXfWXpA9XB9sXP1fqwg7l"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:23:03', '2026-06-11 06:23:03'),
	(47, NULL, NULL, 'created', 'App\\Models\\LoginActivity', 20, '[]', '{"user_id":null,"email":null,"ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"failed","logged_in_at":"2026-06-11 14:23:09","id":20}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPzSj5EIvfl3egdrmSeQ6U4LbQT6xY4FyzLAbo3J6UeT3Ceqw_M32WKhqqgrxxcFIA&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid&state=L9iUWQ1cgkYuDwopR7E88gM6qdWCIcYuCth7JR9z', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:23:09', '2026-06-11 06:23:09'),
	(48, 'App\\Models\\User', 1, 'created', 'App\\Models\\LoginActivity', 21, '[]', '{"user_id":1,"email":"apiloton1@snsu.edu.ph","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-11 14:25:46","id":21}', 'http://pos.dev.com/auth/google/callback?authuser=0&code=4%2F0AdkVLPwnMDwLkYUyKYxnwToYBJdyO4iBYRPlIOSWkKqays4fVS-3HS85JVa4nW5k0VJohw&hd=snsu.edu.ph&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=wvxLWENaVecqfu5tMJU0qmuflXFE5B9bTBvcjIFL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:25:46', '2026-06-11 06:25:46'),
	(49, 'App\\Models\\User', 1, 'updated', 'App\\Models\\LoginActivity', 21, '{"status":"success","logged_out_at":null}', '{"status":"logout","logged_out_at":"2026-06-11 14:25:50"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:25:50', '2026-06-11 06:25:50'),
	(50, 'App\\Models\\User', 1, 'updated', 'App\\Models\\User', 1, '{"remember_token":"NSmnG9ZakF7RvzZC7U1r5LyI7SHlk0J5H1qLQGXZXfWXpA9XB9sXP1fqwg7l"}', '{"remember_token":"VNcae30NHgfl3aSRItLJ4rdf0u1zxK2ckbIAWRIOOTK44puXUxhHcYLSJ68r"}', 'http://pos.dev.com/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:25:50', '2026-06-11 06:25:50'),
	(51, NULL, NULL, 'created', 'App\\Models\\User', 2, '[]', '{"name":"ANTONIO JR. PILOTON","email":"antoniojrpiloton@gmail.com","google_id":"112090857259724913006","avatar":"https:\\/\\/lh3.googleusercontent.com\\/a\\/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c","verified":1,"password":"$2y$12$Z.dOCfp9yKBwL5BmCLOyXOit5bzLTms.lzpTnWKMNd7UHRG1qNSX6","id":2}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPxFvs5A9LhkU8cx7p-sbDCXhFZjp8mUHrhMO-zz27UO0EPsUrGtOMUrHJAEY6yGsw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=MP1lIguH3vZFJK8aDV5KAo6k2M5k2M2QX9c0xXsb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:25:58', '2026-06-11 06:25:58'),
	(52, 'App\\Models\\User', 2, 'updated', 'App\\Models\\User', 2, '{"remember_token":null}', '{"remember_token":"yDgHb7RtPhP5glyRxjW3AVwdB19UU1NV3s7CBshFIMPzw58E2Ub22BYuAJge"}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPxFvs5A9LhkU8cx7p-sbDCXhFZjp8mUHrhMO-zz27UO0EPsUrGtOMUrHJAEY6yGsw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=MP1lIguH3vZFJK8aDV5KAo6k2M5k2M2QX9c0xXsb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:25:58', '2026-06-11 06:25:58'),
	(53, 'App\\Models\\User', 2, 'created', 'App\\Models\\LoginActivity', 22, '[]', '{"user_id":2,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-11 14:25:58","id":22}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPxFvs5A9LhkU8cx7p-sbDCXhFZjp8mUHrhMO-zz27UO0EPsUrGtOMUrHJAEY6yGsw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid&state=MP1lIguH3vZFJK8aDV5KAo6k2M5k2M2QX9c0xXsb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 06:25:58', '2026-06-11 06:25:58'),
	(54, NULL, NULL, 'created', 'App\\Models\\User', 3, '[]', '{"tenant_id":1,"name":"ANTONIO JR. PILOTON","email":"antoniojrpiloton@gmail.com","google_id":"112090857259724913006","avatar":"https:\\/\\/lh3.googleusercontent.com\\/a\\/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c","verified":1,"password":"$2y$12$RYW2A3QIydsMUY99to4OnOMGrQxet5Lnvyi2DtfzTYm7jZnQ6oIqm","id":3}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPw9Z_JOdKVM-uSkJA0YkbyiypmFfKZCzDe2n6x3GSGLAqp-zvZZ0KcOh0Y-QWwyBw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=VNJb6sHSZOsZtTId5vWcNq8ZZ7zJrpVyf5at8PYK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 07:36:53', '2026-06-11 07:36:53'),
	(55, 'App\\Models\\User', 3, 'updated', 'App\\Models\\User', 3, '{"remember_token":null}', '{"remember_token":"fY82EnXs7AeU4yGOxRE8rR1IrgC5Hgg3vGgxNN1eu9dDR6Fk4PB01aUpTyK6"}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPw9Z_JOdKVM-uSkJA0YkbyiypmFfKZCzDe2n6x3GSGLAqp-zvZZ0KcOh0Y-QWwyBw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=VNJb6sHSZOsZtTId5vWcNq8ZZ7zJrpVyf5at8PYK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 07:36:53', '2026-06-11 07:36:53'),
	(56, 'App\\Models\\User', 3, 'created', 'App\\Models\\LoginActivity', 23, '[]', '{"user_id":3,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-11 15:36:53","id":23}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPw9Z_JOdKVM-uSkJA0YkbyiypmFfKZCzDe2n6x3GSGLAqp-zvZZ0KcOh0Y-QWwyBw&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20openid%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&state=VNJb6sHSZOsZtTId5vWcNq8ZZ7zJrpVyf5at8PYK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-11 07:36:53', '2026-06-11 07:36:53');

-- Dumping structure for table snsu_safetrack.backups
DROP TABLE IF EXISTS `backups`;
CREATE TABLE IF NOT EXISTS `backups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filepath` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `backup_type` enum('full','db','files') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` enum('processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_backup_type` (`backup_type`),
  KEY `idx_status` (`status`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.backups: ~2 rows (approximately)
DELETE FROM `backups`;
INSERT INTO `backups` (`id`, `filename`, `filepath`, `backup_type`, `file_size`, `status`, `created_by`, `notes`, `created_at`, `deleted_at`, `updated_at`) VALUES
	(1, '2026-05-28-09-11-08.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-28-09-11-08.zip', 'db', 20376, 'completed', 1, NULL, '2026-05-28 01:11:10', NULL, '2026-05-28 01:11:10'),
	(2, '2026-06-11-13-58-51.zip', 'C:\\wamp64\\www\\saas_pos\\storage\\app\\private\\SafeTrack\\2026-06-11-13-58-51.zip', 'db', 51145, 'completed', 1, NULL, '2026-06-11 05:58:53', NULL, '2026-06-11 05:58:53');

-- Dumping structure for table snsu_safetrack.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.cache: ~1 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('safetrack-cache-last_activity_3', 'b:1;', 1781255652);

-- Dumping structure for table snsu_safetrack.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.cache_locks: ~1 rows (approximately)
DELETE FROM `cache_locks`;
INSERT INTO `cache_locks` (`key`, `owner`, `expiration`) VALUES
	('safetrack-a-qr-nfc-based-student-monitoring-and-alert-system-cache-framework\\schedule-7378ebb1f16e1d5e04a75a9faf10f26f7a954f0c', 'W5uQecleWa814o8g', 1778990640);

-- Dumping structure for table snsu_safetrack.classes
DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Semester` bigint(20) unsigned NOT NULL DEFAULT '0',
  `AYFrom` bigint(20) unsigned NOT NULL DEFAULT '0',
  `AYTo` bigint(20) unsigned NOT NULL DEFAULT '0',
  `GradeLevelID` bigint(20) unsigned NOT NULL,
  `StrandID` bigint(20) unsigned DEFAULT NULL,
  `AdviserID` bigint(20) unsigned DEFAULT NULL,
  `SectionName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Capacity` int(11) DEFAULT '50',
  `Room` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.classes: ~16 rows (approximately)
DELETE FROM `classes`;
INSERT INTO `classes` (`id`, `Semester`, `AYFrom`, `AYTo`, `GradeLevelID`, `StrandID`, `AdviserID`, `SectionName`, `Capacity`, `Room`, `IsActive`, `created_at`, `updated_at`, `created_by`, `updated_by`, `status`, `archived`, `deleted_at`) VALUES
	(1, 2, 2025, 2026, 1, 0, 2, 'A', 50, 'EB101', 1, '2026-05-12 09:17:39', '2026-05-12 09:17:39', 1, 1, 'active', 0, NULL),
	(2, 2, 2025, 2026, 1, 0, 1, 'B', 50, NULL, 1, '2026-05-12 09:19:55', '2026-05-12 09:19:55', 1, 1, 'active', 0, NULL),
	(3, 2, 2025, 2026, 2, 0, 2, 'A', 50, NULL, 1, '2026-05-12 09:24:35', '2026-05-12 09:24:35', 1, 1, 'active', 0, NULL),
	(4, 2, 2025, 2026, 2, 0, 2, 'B', 50, NULL, 1, '2026-05-12 09:24:44', '2026-05-12 09:24:44', 1, 1, 'active', 0, NULL),
	(5, 2, 2025, 2026, 3, 0, 1, 'A', 50, NULL, 1, '2026-05-12 09:24:57', '2026-05-12 09:24:57', 1, 1, 'active', 0, NULL),
	(6, 2, 2025, 2026, 3, 0, 2, 'B', 50, NULL, 1, '2026-05-12 09:25:04', '2026-05-12 09:25:04', 1, 1, 'active', 0, NULL),
	(7, 2, 2025, 2026, 4, 0, 2, 'A', 50, NULL, 1, '2026-05-12 09:25:15', '2026-05-12 09:25:15', 1, 1, 'active', 0, NULL),
	(8, 2, 2025, 2026, 4, 0, 2, 'B', 50, NULL, 1, '2026-05-12 09:25:24', '2026-05-12 09:25:24', 1, 1, 'active', 0, NULL),
	(9, 2, 2025, 2026, 5, 1, 2, 'A', 50, NULL, 1, '2026-05-12 09:25:35', '2026-05-12 09:25:35', 1, 1, 'active', 0, NULL),
	(10, 2, 2025, 2026, 5, 3, 2, 'A', 50, NULL, 1, '2026-05-12 09:26:16', '2026-05-12 09:26:16', 1, 1, 'active', 0, NULL),
	(11, 2, 2025, 2026, 5, 6, 2, 'A', 50, NULL, 1, '2026-05-12 09:26:29', '2026-05-12 09:26:29', 1, 1, 'active', 0, NULL),
	(12, 2, 2025, 2026, 6, 1, 2, 'A', 50, NULL, 1, '2026-05-12 09:26:39', '2026-05-12 09:26:39', 1, 1, 'active', 0, NULL),
	(13, 2, 2025, 2026, 6, 6, 2, 'A', 50, NULL, 1, '2026-05-12 09:26:48', '2026-05-12 09:26:48', 1, 1, 'active', 0, NULL),
	(14, 2, 2025, 2026, 6, 3, 1, 'A', 50, NULL, 1, '2026-05-12 09:26:57', '2026-05-12 09:26:57', 1, 1, 'active', 0, NULL),
	(15, 1, 2026, 2027, 1, 0, 2, 'A', 50, NULL, 1, '2026-05-12 11:23:11', '2026-05-12 11:23:11', 1, 1, 'active', 0, NULL),
	(16, 1, 2026, 2027, 1, 0, 1, 'B', 50, NULL, 1, '2026-05-12 11:23:25', '2026-05-12 11:23:25', 1, 1, 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.class_schedules
DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE IF NOT EXISTS `class_schedules` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `SubjectID` bigint(20) NOT NULL,
  `SectionID` bigint(20) NOT NULL,
  `TeacherID` bigint(20) NOT NULL,
  `DayOfWeek` enum('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY') DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `Room` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.class_schedules: 0 rows
DELETE FROM `class_schedules`;
/*!40000 ALTER TABLE `class_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_schedules` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.employees
DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `UserID` int(11) NOT NULL DEFAULT '0',
  `PhoneNumber` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FirstName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `MiddleName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EmailAddress` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Suffix` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.employees: 1 rows
DELETE FROM `employees`;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` (`id`, `school_id`, `UserID`, `PhoneNumber`, `FirstName`, `MiddleName`, `LastName`, `EmailAddress`, `Suffix`, `Address`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 8, '+639868273741', 'ESTER', NULL, 'TURAJA', NULL, NULL, 'SVT', 1, 1, '2026-05-26 07:27:55', '2026-05-26 02:11:39', 'active', 0, NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.enrollments
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `StudentID` bigint(20) unsigned DEFAULT NULL,
  `AYFrom` year(4) DEFAULT NULL,
  `AYTo` year(4) DEFAULT NULL,
  `Semester` tinyint(4) DEFAULT NULL,
  `GradeLevelID` bigint(20) unsigned DEFAULT NULL,
  `StrandID` bigint(20) unsigned DEFAULT NULL,
  `ClassID` bigint(20) unsigned DEFAULT NULL,
  `EnrollmentStatus` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'ENROLLED',
  `EnrollmentDate` date DEFAULT NULL,
  `Remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grade` (`GradeLevelID`),
  KEY `idx_section` (`ClassID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.enrollments: ~0 rows (approximately)
DELETE FROM `enrollments`;

-- Dumping structure for table snsu_safetrack.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table snsu_safetrack.grades
DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `EnrollmentID` bigint(20) NOT NULL,
  `SubjectID` bigint(20) NOT NULL,
  `Quarter` enum('1ST','2ND','3RD','4TH') DEFAULT NULL,
  `InitialGrade` decimal(5,2) DEFAULT NULL,
  `FinalGrade` decimal(5,2) DEFAULT NULL,
  `Remarks` enum('PASSED','FAILED') DEFAULT NULL,
  `TeacherID` bigint(20) DEFAULT NULL,
  `IsSubmitted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.grades: 0 rows
DELETE FROM `grades`;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.grade_levels
DROP TABLE IF EXISTS `grade_levels`;
CREATE TABLE IF NOT EXISTS `grade_levels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `GradeLevel` varchar(50) DEFAULT NULL,
  `EducationLevel` enum('JHS','SHS') DEFAULT NULL,
  `HasSemester` tinyint(1) DEFAULT '0',
  `IsActive` tinyint(1) DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.grade_levels: 6 rows
DELETE FROM `grade_levels`;
/*!40000 ALTER TABLE `grade_levels` DISABLE KEYS */;
INSERT INTO `grade_levels` (`id`, `GradeLevel`, `EducationLevel`, `HasSemester`, `IsActive`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, '7', 'JHS', 0, 1, 1, 1, '2026-05-11 19:14:21', '2026-05-11 11:14:21', 'active', 0, NULL),
	(2, '8', 'JHS', 0, 1, 1, 1, '2026-05-11 19:14:30', '2026-05-11 11:14:30', 'active', 0, NULL),
	(3, '9', 'JHS', 0, 1, 1, 1, '2026-05-11 19:14:35', '2026-05-11 11:14:35', 'active', 0, NULL),
	(4, '10', 'JHS', 0, 1, 1, 1, '2026-05-11 19:14:39', '2026-05-11 11:14:39', 'active', 0, NULL),
	(5, '11', 'SHS', 1, 1, 1, 1, '2026-05-11 19:14:46', '2026-05-11 11:14:46', 'active', 0, NULL),
	(6, '12', 'SHS', 1, 1, 1, 1, '2026-05-11 19:14:59', '2026-05-11 11:14:59', 'active', 0, NULL);
/*!40000 ALTER TABLE `grade_levels` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table snsu_safetrack.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table snsu_safetrack.knowledge_base_articles
DROP TABLE IF EXISTS `knowledge_base_articles`;
CREATE TABLE IF NOT EXISTS `knowledge_base_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `views` bigint(20) unsigned NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_base_articles_slug_unique` (`slug`),
  KEY `knowledge_base_articles_status_category_index` (`status`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.knowledge_base_articles: ~0 rows (approximately)
DELETE FROM `knowledge_base_articles`;

-- Dumping structure for table snsu_safetrack.login_activities
DROP TABLE IF EXISTS `login_activities`;
CREATE TABLE IF NOT EXISTS `login_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('success','failed','logout') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `logged_in_at` timestamp NULL DEFAULT NULL,
  `logged_out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_activities_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.login_activities: 23 rows
DELETE FROM `login_activities`;
/*!40000 ALTER TABLE `login_activities` DISABLE KEYS */;
INSERT INTO `login_activities` (`id`, `user_id`, `email`, `ip_address`, `user_agent`, `device`, `platform`, `browser`, `status`, `logged_in_at`, `logged_out_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-09 09:29:43', '2026-06-09 09:30:55', '2026-06-09 09:29:43', '2026-06-09 09:30:55'),
	(2, 4, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-09 09:31:02', '2026-06-09 09:31:05', '2026-06-09 09:31:02', '2026-06-09 09:31:05'),
	(3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:31:29', NULL, '2026-06-09 09:31:29', '2026-06-09 09:31:29'),
	(4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:31:35', NULL, '2026-06-09 09:31:35', '2026-06-09 09:31:35'),
	(5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:34:26', NULL, '2026-06-09 09:34:26', '2026-06-09 09:34:26'),
	(6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:35:34', NULL, '2026-06-09 09:35:34', '2026-06-09 09:35:34'),
	(7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:36:10', NULL, '2026-06-09 09:36:10', '2026-06-09 09:36:10'),
	(8, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:36:32', NULL, '2026-06-09 09:36:32', '2026-06-09 09:36:32'),
	(9, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:38:00', NULL, '2026-06-09 09:38:00', '2026-06-09 09:38:00'),
	(10, 10, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:39:53', NULL, '2026-06-09 09:39:53', '2026-06-09 09:39:53'),
	(11, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-09 09:42:35', '2026-06-09 09:42:37', '2026-06-09 09:42:35', '2026-06-09 09:42:37'),
	(12, 10, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-09 09:42:43', NULL, '2026-06-09 09:42:43', '2026-06-09 09:42:43'),
	(13, 10, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-06-09 09:47:54', NULL, '2026-06-09 09:47:54', '2026-06-09 09:47:54'),
	(14, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-09 09:50:13', '2026-06-09 09:50:21', '2026-06-09 09:50:13', '2026-06-09 09:50:21'),
	(15, 11, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-09 09:50:36', '2026-06-11 04:43:23', '2026-06-09 09:50:36', '2026-06-11 04:43:23'),
	(16, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-06-11 04:48:02', NULL, '2026-06-11 04:48:02', '2026-06-11 04:48:02'),
	(17, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-11 06:18:18', NULL, '2026-06-11 06:18:18', '2026-06-11 06:18:18'),
	(18, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-11 06:18:38', NULL, '2026-06-11 06:18:38', '2026-06-11 06:18:38'),
	(19, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-11 06:22:46', '2026-06-11 06:23:03', '2026-06-11 06:22:46', '2026-06-11 06:23:03'),
	(20, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-06-11 06:23:09', NULL, '2026-06-11 06:23:09', '2026-06-11 06:23:09'),
	(21, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-06-11 06:25:46', '2026-06-11 06:25:50', '2026-06-11 06:25:46', '2026-06-11 06:25:50'),
	(22, 2, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-06-11 06:25:58', NULL, '2026-06-11 06:25:58', '2026-06-11 06:25:58'),
	(23, 3, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-06-11 07:36:53', NULL, '2026-06-11 07:36:53', '2026-06-11 07:36:53');
/*!40000 ALTER TABLE `login_activities` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.migrations: ~3 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2026_05_25_163042_create_login_activities_table', 1),
	(2, '2026_05_25_170020_create_suspicious_activities_table', 2),
	(3, '2026_05_25_175650_create_system_settings_table', 3);

-- Dumping structure for table snsu_safetrack.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.model_has_permissions: ~0 rows (approximately)
DELETE FROM `model_has_permissions`;

-- Dumping structure for table snsu_safetrack.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.model_has_roles: ~9 rows (approximately)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(6, 'App\\Models\\User', 1),
	(3, 'App\\Models\\User', 2),
	(4, 'App\\Models\\User', 3),
	(6, 'App\\Models\\User', 4),
	(3, 'App\\Models\\User', 5),
	(2, 'App\\Models\\User', 6),
	(1, 'App\\Models\\User', 7),
	(3, 'App\\Models\\User', 8),
	(1, 'App\\Models\\User', 9);

-- Dumping structure for table snsu_safetrack.nfc_codes
DROP TABLE IF EXISTS `nfc_codes`;
CREATE TABLE IF NOT EXISTS `nfc_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `UserID` int(11) DEFAULT NULL,
  `nf_codes` varchar(50) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.nfc_codes: 8 rows
DELETE FROM `nfc_codes`;
/*!40000 ALTER TABLE `nfc_codes` DISABLE KEYS */;
INSERT INTO `nfc_codes` (`id`, `school_id`, `UserID`, `nf_codes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 3, '0018317828', 1, 1, '2026-05-25 10:23:10', '2026-05-25 02:23:10', 'active', 0, NULL),
	(2, 1, 4, '0026063620', 1, 1, '2026-05-25 10:23:21', '2026-05-25 02:23:21', 'active', 0, NULL),
	(3, 1, 2, '0017924612', 1, 1, '2026-05-25 10:23:30', '2026-05-25 02:23:30', 'active', 0, NULL),
	(4, 3, 12, '0017924612', 1, 1, '2026-05-25 11:14:30', '2026-05-25 03:14:30', 'active', 0, NULL),
	(5, 1, 7, '0026063620', 1, 1, '2026-05-26 08:11:52', '2026-05-26 00:11:52', 'active', 0, NULL),
	(6, 1, 7, '0026063620', 1, 1, '2026-05-26 08:12:20', '2026-05-26 00:12:20', 'active', 0, NULL),
	(7, 1, 7, '0026063620', 1, 1, '2026-05-26 08:12:50', '2026-05-26 00:12:50', 'active', 0, NULL),
	(8, 1, 7, '0026063620', 1, 1, '2026-05-26 10:25:03', '2026-05-26 02:25:03', 'active', 0, NULL);
/*!40000 ALTER TABLE `nfc_codes` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.parents
DROP TABLE IF EXISTS `parents`;
CREATE TABLE IF NOT EXISTS `parents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `UserID` int(11) NOT NULL DEFAULT '0',
  `FirstName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `MiddleName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Suffix` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PhoneNumber` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Remark` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.parents: ~16 rows (approximately)
DELETE FROM `parents`;
INSERT INTO `parents` (`id`, `school_id`, `UserID`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `PhoneNumber`, `Address`, `Remark`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 0, 'JOCELYN', 'BERO', 'MORALES', NULL, '+639813581109', 'Espina St', NULL, 1, 1, '2026-05-22 10:13:42', '2026-05-22 02:13:51', 'active', 0, NULL),
	(2, 0, 5, 'PARENT1', 'BERO', 'P1_L', NULL, '+639306069019', 'STC', NULL, 1, 1, '2026-05-22 14:20:08', '2026-05-22 06:25:12', 'active', 0, NULL),
	(3, 0, 0, 'JOCELYN', 'BERO', 'MORALES', NULL, '+639813581109', 'STC', NULL, 1, 1, '2026-05-22 14:23:53', '2026-05-22 06:23:53', 'active', 0, NULL),
	(4, 0, 7, 'KAREEN', 'BASUL', 'MAPOY', NULL, '+639700920157', 'Address', NULL, 1, 1, '2026-05-22 14:33:22', '2026-05-22 06:34:27', 'active', 0, NULL),
	(5, 0, 0, 'Janeli', 'Managa', 'Olaco', NULL, '+639464233996', 'STA. CRUZ, TUBAJON', NULL, 8, 8, '2026-05-22 15:09:24', '2026-05-22 07:09:24', 'active', 0, NULL),
	(6, 0, 0, 'RIZEL', 'CURTINA', 'IMALAY', NULL, '+639070708095', 'P-5 SAN VICENTE, TUBAJON, DINAGAT ISLANDS', NULL, 11, 11, '2026-05-22 15:09:48', '2026-05-22 07:09:48', 'active', 0, NULL),
	(7, 0, 0, 'Rosden', NULL, 'Garcia', NULL, '+639858224872', 'STA. CRUZ, TUBAJON', NULL, 8, 8, '2026-05-22 15:11:25', '2026-05-22 07:11:25', 'active', 0, NULL),
	(8, 0, 0, 'ARLENE', 'CREZALDO', 'DIAZ', NULL, '+639506975822', 'SAN VICENTE, TUBAJON, DINAGAT ISLANDS', NULL, 14, 14, '2026-05-22 15:11:26', '2026-05-22 07:11:26', 'active', 0, NULL),
	(9, 0, 0, 'Lorna', NULL, 'Kiasan', NULL, '+639641012016', 'STA. CRUZ, TUBAJON', NULL, 8, 8, '2026-05-22 15:12:51', '2026-05-22 07:12:51', 'active', 0, NULL),
	(10, 0, 0, 'LIZIL', NULL, 'LOBERIANO', NULL, '+639100891547', 'SAN ROQUE,TUBAJON DINAGAT ISLANDS', NULL, 14, 14, '2026-05-22 15:13:10', '2026-05-22 07:13:10', 'active', 0, NULL),
	(11, 0, 0, 'JELLAN', 'DE ORO', 'PACUNLA', NULL, '+639817101537', 'SAN ROQUE,TUBAJON,DINAGAT ISLANDS', NULL, 9, 9, '2026-05-22 15:13:28', '2026-05-22 07:13:28', 'active', 0, NULL),
	(12, 0, 0, 'MICHELLE', 'DELGADO', 'DELA PENA', NULL, '+639855793446', 'P-3 STA. CRUZ, TUBAJON, PDI', NULL, 11, 11, '2026-05-22 15:13:33', '2026-05-22 07:13:33', 'active', 0, NULL),
	(13, 0, 0, 'CHARLYN', NULL, 'ANGOB', NULL, '+639468019180', 'SAN VICENTE, TUBAJON, DINAGAT ISLANDS', NULL, 14, 14, '2026-05-22 15:14:23', '2026-05-22 07:14:23', 'active', 0, NULL),
	(14, 0, 0, 'RIZELLA', NULL, 'DELA PENA', NULL, '+639305900202', 'STA CRUZ, TUBAJON DINAGAT ISLANDS', NULL, 14, 14, '2026-05-22 15:15:56', '2026-05-22 07:15:56', 'active', 0, NULL),
	(15, 3, 87, 'loreto P', 'new mabini2', 'loreto P', NULL, '+639128941731', 'Sitio Cayutan, Brgy. Cagniog', NULL, 1, 1, '2026-05-25 09:50:47', '2026-05-25 01:50:47', 'active', 0, NULL),
	(16, 1, 6, 'ANTONIO JR', 'LUIB', 'PILOTON', NULL, '+639128941731', 'Sitio Cayutan, Brgy. Cagniog', NULL, 1, 1, '2026-05-26 08:10:14', '2026-05-26 00:10:15', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table snsu_safetrack.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.permissions: ~37 rows (approximately)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `details`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'dashboard.view', 'View dashboard', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(2, 'scanner.view', 'View scanner module', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(3, 'logs.view', 'View all logs', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(4, 'logs.users.view', 'View user logs', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(5, 'students.view', 'View students', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(6, 'students.create', 'Create students', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(7, 'students.update', 'Update students', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(8, 'students.delete', 'Delete students', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(9, 'parents.view', 'View parents', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(10, 'parents.create', 'Create parents', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(11, 'parents.update', 'Update parents', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(12, 'parents.delete', 'Delete parents', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(13, 'employees.view', 'View employees', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(14, 'employees.create', 'Create employees', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(15, 'employees.update', 'Update employees', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(16, 'employees.delete', 'Delete employees', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(17, 'sms.view', 'View SMS queue', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(18, 'sms.send', 'Send SMS messages', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(19, 'school-users.view', 'View school users', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(20, 'school-users.create', 'Create school users', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(21, 'school-users.update', 'Update school users', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(22, 'school-users.delete', 'Delete school users', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(23, 'settings.view', 'View settings', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(24, 'settings.update', 'Update settings', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(25, 'permissions.view', 'View permissions', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(26, 'permissions.create', 'Create permissions', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(27, 'permissions.update', 'Update permissions', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(28, 'permissions.delete', 'Delete permissions', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(29, 'roles.view', 'View roles', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(30, 'roles.create', 'Create roles', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(31, 'roles.update', 'Update roles', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(32, 'roles.delete', 'Delete roles', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(33, 'schools.view', 'View schools', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(34, 'schools.create', 'Create schools', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(35, 'schools.update', 'Update schools', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(36, 'schools.delete', 'Delete schools', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(37, 'support-center.view', 'Allow report support', 'web', '2026-05-25 11:45:19', '2026-05-25 11:45:19');

-- Dumping structure for table snsu_safetrack.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.personal_access_tokens: ~39 rows (approximately)
DELETE FROM `personal_access_tokens`;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(133, 'App\\Models\\User', 50, 'mobile', 'cc9dedec3c97a5bec51b3936d60b25b54618fa52e66867255fd0dcb36b61a4ae', '["*"]', '2025-07-17 08:02:19', NULL, '2025-07-16 17:39:00', '2025-07-17 08:02:19'),
	(134, 'App\\Models\\User', 50, 'mobile', '1220b4c233861c4d20b0e366499c4984d9d6d89f70b78b1762f3d0e96fa238c0', '["*"]', '2025-07-17 08:25:42', NULL, '2025-07-17 08:02:28', '2025-07-17 08:25:42'),
	(135, 'App\\Models\\User', 50, 'mobile', '28b7d3282f8b3434be979732e8bea2ea63fac0bc3c1a96b40d208ba81ec7b3b0', '["*"]', '2025-07-17 19:00:42', NULL, '2025-07-17 17:34:55', '2025-07-17 19:00:42'),
	(136, 'App\\Models\\User', 50, 'mobile', '49839ae906287a92eb94798cbac4ba80bb02d7bad2c688ac530962bad8c2a2ad', '["*"]', '2025-07-17 19:29:45', NULL, '2025-07-17 19:19:53', '2025-07-17 19:29:45'),
	(137, 'App\\Models\\User', 50, 'mobile', 'cf98d93465bdac285f637b6736c79c97ec80ec5bc70791370499100cbf7df245', '["*"]', '2025-07-18 01:16:35', NULL, '2025-07-17 21:26:51', '2025-07-18 01:16:35'),
	(138, 'App\\Models\\User', 50, 'mobile', '1e68cab4ed2bc91a628dfb691657f3d80b59ab555882305ac33c0dc4aafd5f3d', '["*"]', '2025-07-18 06:15:30', NULL, '2025-07-18 01:18:22', '2025-07-18 06:15:30'),
	(139, 'App\\Models\\User', 50, 'mobile', 'bc44a6fbe4f7bbb954b86197af7429d52281c7521f770a25b72fbaddade1f0bc', '["*"]', '2025-07-25 01:25:37', NULL, '2025-07-24 19:27:47', '2025-07-25 01:25:37'),
	(140, 'App\\Models\\User', 65, 'mobile', '081b88eac80c7e29e53a74278a6a8097ec58fb4fa498b81a2f3ebe96e96b24ce', '["*"]', '2025-07-24 20:50:44', NULL, '2025-07-24 20:50:27', '2025-07-24 20:50:44'),
	(141, 'App\\Models\\User', 65, 'mobile', '4d1542b044375312dc63ee70357d705c6b5b80c0c2cbfa5c74c865845d9b41f2', '["*"]', '2025-07-24 20:59:23', NULL, '2025-07-24 20:50:56', '2025-07-24 20:59:23'),
	(142, 'App\\Models\\User', 50, 'mobile', '5e7b313edf909efea2c3c35ebc7cde7e8812e293f076012f7f216d2b28d2d1d6', '["*"]', '2025-07-24 21:27:34', NULL, '2025-07-24 21:27:34', '2025-07-24 21:27:34'),
	(143, 'App\\Models\\User', 50, 'mobile', '10ab8548ce57c5080adb8aea6f5d83a1483ede25cd5fda14c4e08f97e8bf1ebc', '["*"]', '2025-07-24 21:29:43', NULL, '2025-07-24 21:27:47', '2025-07-24 21:29:43'),
	(144, 'App\\Models\\User', 65, 'mobile', '88fe8d501abac180258abd8b6d0a08dfe29bd97b9e0832ca56c5b266ee16bea4', '["*"]', '2025-07-24 21:45:54', NULL, '2025-07-24 21:45:01', '2025-07-24 21:45:54'),
	(145, 'App\\Models\\User', 50, 'mobile', 'e82424507bb03565e1091a1b210a423f4589e23f9d8d44c0a23501968003a45e', '["*"]', '2025-07-24 21:46:54', NULL, '2025-07-24 21:45:06', '2025-07-24 21:46:54'),
	(146, 'App\\Models\\User', 89, 'mobile', '68d551312ccea8097e504d75ab736a1ade51ee7af0333b8aa4182d2792ee0217', '["*"]', '2025-07-24 21:53:15', NULL, '2025-07-24 21:52:39', '2025-07-24 21:53:15'),
	(147, 'App\\Models\\User', 89, 'mobile', 'eaac356cf8c9eaa5508a864f9b24962aa98f9a15f1c8a927c65f6b54e802cafe', '["*"]', '2025-07-24 21:54:44', NULL, '2025-07-24 21:53:28', '2025-07-24 21:54:44'),
	(148, 'App\\Models\\User', 50, 'mobile', 'bc6702900430e01474ff5a5da80e1a05cfdde98a0383267e34947695f24c3d4b', '["*"]', '2025-07-24 21:59:49', NULL, '2025-07-24 21:53:33', '2025-07-24 21:59:49'),
	(149, 'App\\Models\\User', 89, 'mobile', 'd265dc7fc98e85f79243b18a5a02ad2439ca52680c5009d69a253b1a205a1895', '["*"]', '2025-07-24 23:35:26', NULL, '2025-07-24 22:06:29', '2025-07-24 23:35:26'),
	(150, 'App\\Models\\User', 50, 'mobile', '4bb587e59a6b66f6d3ae228417039786642bf66b99cf722ae86f710781497de0', '["*"]', '2025-07-24 23:14:21', NULL, '2025-07-24 22:47:02', '2025-07-24 23:14:21'),
	(151, 'App\\Models\\User', 50, 'mobile', 'ee6398d34c6b35131289354db185d51d924998198b5fd1e3f226df53fe91b1e2', '["*"]', '2025-07-25 00:26:33', NULL, '2025-07-25 00:16:41', '2025-07-25 00:26:33'),
	(152, 'App\\Models\\User', 50, 'mobile', '8adffc38baca2024be656bee84da11c5d5ff81ceb5b5f078b00622d6b4799f41', '["*"]', '2025-07-25 00:28:13', NULL, '2025-07-25 00:28:08', '2025-07-25 00:28:13'),
	(153, 'App\\Models\\User', 50, 'mobile', '59e8eb1f0838a80994d1798136d80fd887f6705924966c8c0be2c9b9f45076b3', '["*"]', '2025-07-25 00:38:38', NULL, '2025-07-25 00:32:36', '2025-07-25 00:38:38'),
	(154, 'App\\Models\\User', 50, 'mobile', '3d014541f8f764d127caa75a7abfb1650de7911190506e6f70ed160bea589635', '["*"]', '2025-08-01 00:52:15', NULL, '2025-08-01 00:27:44', '2025-08-01 00:52:15'),
	(155, 'App\\Models\\User', 50, 'mobile', '580fd7af6a3bcf8ece91fcd5ed5e6d6f34bf1038e5eae29cf0aea58ffe017d2a', '["*"]', '2025-08-01 00:54:47', NULL, '2025-08-01 00:52:32', '2025-08-01 00:54:47'),
	(156, 'App\\Models\\User', 100, 'mobile', 'ab94c5d5446e8ce0c6b4f4b2c023d3294dc05d9bdd3569f62820d3c9ad75434e', '["*"]', '2025-08-01 00:54:56', NULL, '2025-08-01 00:54:54', '2025-08-01 00:54:56'),
	(157, 'App\\Models\\User', 50, 'mobile', '2b4d7d74e33d7e037f9713f49fcc3378d41006dc41a41ff7667a828e168c4ad0', '["*"]', '2025-08-01 00:57:52', NULL, '2025-08-01 00:55:01', '2025-08-01 00:57:52'),
	(158, 'App\\Models\\User', 50, 'mobile', '4092c33ff628090ff82b4ae7bc38a5629871870e6da78e342f1d1f564500618a', '["*"]', '2025-08-01 00:58:51', NULL, '2025-08-01 00:58:05', '2025-08-01 00:58:51'),
	(159, 'App\\Models\\User', 50, 'mobile', '947d388d3694e3100d577dbb5bbb90046b59d127fe7484d6cd76621c8e84416c', '["*"]', '2025-08-01 01:07:18', NULL, '2025-08-01 00:59:06', '2025-08-01 01:07:18'),
	(160, 'App\\Models\\User', 50, 'mobile', '5e7354da200ea8bb79bf4e13aa9c8c7581d514d6bb6bb2498dd1bccea40c2df9', '["*"]', '2025-08-01 01:14:05', NULL, '2025-08-01 01:10:25', '2025-08-01 01:14:05'),
	(161, 'App\\Models\\User', 50, 'mobile', 'ae98686ab4b3f6d978a6d34c6736d0259e9fcfdffd23283ff2643b012b55505f', '["*"]', '2025-08-01 10:09:24', NULL, '2025-08-01 10:04:14', '2025-08-01 10:09:24'),
	(162, 'App\\Models\\User', 65, 'mobile', 'ce669017d8237c201bb8d850d16046bb10a458ef64d5515fe89bd04956579d88', '["*"]', '2025-08-01 14:25:05', NULL, '2025-08-01 14:24:59', '2025-08-01 14:25:05'),
	(163, 'App\\Models\\User', 50, 'mobile', 'ce4e3bfb1e47681935601dd3ddeadc7be8bb26a57e89dd110ab34f6595a96d58', '["*"]', '2025-08-01 14:33:29', NULL, '2025-08-01 14:25:10', '2025-08-01 14:33:29'),
	(164, 'App\\Models\\User', 50, 'mobile', '94ef8fe24e842dc800501c83d6e7db246da43e7369c47d9eebdb5802e46088fd', '["*"]', '2025-08-01 15:01:04', NULL, '2025-08-01 14:36:27', '2025-08-01 15:01:04'),
	(165, 'App\\Models\\User', 50, 'mobile', 'b75deb0039917a31f79e93d8b68093d39f28cc89cc7da446500386de87d21265', '["*"]', '2025-08-01 15:34:47', NULL, '2025-08-01 15:01:19', '2025-08-01 15:34:47'),
	(166, 'App\\Models\\User', 50, 'mobile', 'ba8b72c6646d4f0c74ca02de9eb85540e663a950fd30b93271fc078902688a59', '["*"]', NULL, NULL, '2026-01-12 05:30:18', '2026-01-12 05:30:18'),
	(167, 'App\\Models\\User', 50, 'mobile', '293b7999ec2f166987344df1c1864b4bec7c9ff26545b777bab6396a7a46bb8b', '["*"]', '2026-01-12 06:02:12', NULL, '2026-01-12 05:33:15', '2026-01-12 06:02:12'),
	(168, 'App\\Models\\User', 50, 'mobile', 'f513e78345b2e8141c25e014417c33f6d97c0aa031ff2b96ca4781dd904f974e', '["*"]', '2026-01-12 06:05:30', NULL, '2026-01-12 06:02:39', '2026-01-12 06:05:30'),
	(169, 'App\\Models\\User', 50, 'mobile', 'a5edc5da7dc241dcbcaf6ad1096b7988c720a5890877af9ddc65aa6f9fd5218a', '["*"]', '2026-01-12 06:17:30', NULL, '2026-01-12 06:05:37', '2026-01-12 06:17:30'),
	(170, 'App\\Models\\User', 50, 'mobile', '838af8bc119353b0f65eeb656020ecbe111a5f7fc9e5a7b011bdf9df9dd5c824', '["*"]', NULL, NULL, '2026-02-17 09:34:41', '2026-02-17 09:34:41'),
	(171, 'App\\Models\\User', 50, 'mobile', '3153871347e9c1649f672b13cf0c3cb5e1613b2d4b1f0e980244028d9a6e1fec', '["*"]', '2026-02-17 09:36:42', NULL, '2026-02-17 09:35:27', '2026-02-17 09:36:42');

-- Dumping structure for table snsu_safetrack.pos_branches
DROP TABLE IF EXISTS `pos_branches`;
CREATE TABLE IF NOT EXISTS `pos_branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `branch_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_main_branch` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `branch_code` (`branch_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_branches: ~0 rows (approximately)
DELETE FROM `pos_branches`;

-- Dumping structure for table snsu_safetrack.pos_categories
DROP TABLE IF EXISTS `pos_categories`;
CREATE TABLE IF NOT EXISTS `pos_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_categories: 31 rows
DELETE FROM `pos_categories`;
/*!40000 ALTER TABLE `pos_categories` DISABLE KEYS */;
INSERT INTO `pos_categories` (`id`, `tenant_id`, `name`, `description`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 'Beverages', 'Soft drinks, juices, coffee, water', NULL, NULL, '2026-06-10 09:03:39', '2026-06-11 12:48:57', 'active', 0, NULL),
	(2, 1, 'Snacks-edited', 'Chips, biscuits, candies-edited', 3, 3, '2026-06-11 20:48:06', '2026-06-11 12:48:59', 'active', 0, NULL),
	(3, 1, 'Canned Goods', 'Canned food products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-11 12:48:58', 'active', 0, NULL),
	(4, 1, 'Instant Foods', 'Noodles, soups, ready-to-eat products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-11 12:48:59', 'active', 0, NULL),
	(5, 1, 'Rice and Grains', 'Rice, corn, grains', NULL, NULL, '2026-06-10 09:03:39', '2026-06-11 12:49:00', 'active', 0, NULL),
	(6, 0, 'Condiments', 'Sauces, vinegar, soy sauce, spices', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(7, 0, 'Dairy Products', 'Milk, cheese, butter, yogurt', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(8, 0, 'Frozen Foods', 'Frozen meat and frozen products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(9, 0, 'Bread and Bakery', 'Bread, cakes, pastries', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(10, 0, 'Meat and Poultry', 'Fresh and processed meat products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(11, 0, 'Seafood', 'Fish and seafood products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(12, 0, 'Fruits and Vegetables', 'Fresh produce', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(13, 0, 'Personal Care', 'Soap, shampoo, toothpaste', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(14, 0, 'Health and Beauty', 'Cosmetics and beauty products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(15, 0, 'Medicines', 'OTC medicines and supplements', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(16, 0, 'Baby Products', 'Diapers, milk, baby care items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(17, 0, 'Household Supplies', 'Cleaning and household products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(18, 0, 'Laundry Supplies', 'Detergents and fabric conditioners', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(19, 0, 'School and Office Supplies', 'School and office items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(20, 0, 'Pet Supplies', 'Pet food and pet care products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(21, 0, 'Hardware', 'Tools and hardware supplies', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(22, 0, 'Electrical Supplies', 'Electrical materials and accessories', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(23, 0, 'Mobile Accessories', 'Chargers, cables, earphones', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(24, 0, 'Clothing', 'Apparel and garments', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(25, 0, 'Footwear', 'Shoes and slippers', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(26, 0, 'Kitchenware', 'Kitchen tools and utensils', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(27, 0, 'Toys', 'Toys and recreational items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(28, 0, 'Gift Items', 'Gift products and souvenirs', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(29, 0, 'Services', 'Non-inventory service items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(30, 0, 'Others', 'Miscellaneous products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(33, 1, 'Condiments', 'Condiments', 3, 3, '2026-06-11 20:50:02', '2026-06-11 12:50:02', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_categories` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_inventory_movements
DROP TABLE IF EXISTS `pos_inventory_movements`;
CREATE TABLE IF NOT EXISTS `pos_inventory_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `movement_type` enum('purchase','sale','adjustment','return','transfer') NOT NULL,
  `reference_type` varchar(50) NOT NULL,
  `reference_id` bigint(20) unsigned NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_inventory_movements: 0 rows
DELETE FROM `pos_inventory_movements`;
/*!40000 ALTER TABLE `pos_inventory_movements` DISABLE KEYS */;
INSERT INTO `pos_inventory_movements` (`id`, `tenant_id`, `product_id`, `movement_type`, `reference_type`, `reference_id`, `qty`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 3, 'sale', 'sale', 1, -2.00, 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(2, 1, 2, 'sale', 'sale', 1, -5.00, 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(3, 1, 3, 'sale', 'sale', 2, -6.00, 3, 3, '2026-06-12 16:01:01', '2026-06-12 08:01:01', 'active', 0, NULL),
	(4, 1, 5, 'sale', 'sale', 2, -8.00, 3, 3, '2026-06-12 16:01:01', '2026-06-12 08:01:01', 'active', 0, NULL),
	(5, 1, 5, 'sale', 'sale', 3, -7.00, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(6, 1, 2, 'sale', 'sale', 3, -6.00, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(7, 1, 3, 'sale', 'sale', 3, -11.00, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(8, 1, 1, 'sale', 'sale', 3, -3.00, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(9, 1, 1, 'sale', 'sale', 4, -3.00, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(10, 1, 3, 'sale', 'sale', 4, -5.00, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(11, 1, 5, 'sale', 'sale', 4, -1.00, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(12, 1, 2, 'sale', 'sale', 4, -4.00, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(13, 1, 2, 'sale', 'sale', 5, -4.00, 3, 3, '2026-06-12 16:12:57', '2026-06-12 08:12:57', 'active', 0, NULL),
	(14, 1, 5, 'sale', 'sale', 6, -4.00, 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(15, 1, 3, 'sale', 'sale', 6, -3.00, 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(16, 1, 2, 'sale', 'sale', 6, -3.00, 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(17, 1, 2, 'sale', 'sale', 7, -2.00, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(18, 1, 3, 'sale', 'sale', 7, -3.00, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(19, 1, 5, 'sale', 'sale', 7, -4.00, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(20, 1, 1, 'sale', 'sale', 8, -4.00, 3, 3, '2026-06-12 16:59:35', '2026-06-12 08:59:35', 'active', 0, NULL),
	(21, 1, 3, 'sale', 'sale', 8, -3.00, 3, 3, '2026-06-12 16:59:35', '2026-06-12 08:59:35', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_inventory_movements` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_payments
DROP TABLE IF EXISTS `pos_payments`;
CREATE TABLE IF NOT EXISTS `pos_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `tendered_amount` decimal(20,6) DEFAULT NULL,
  `change_amount` decimal(20,6) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `notes` varchar(150) DEFAULT '',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_payments: 7 rows
DELETE FROM `pos_payments`;
/*!40000 ALTER TABLE `pos_payments` DISABLE KEYS */;
INSERT INTO `pos_payments` (`id`, `sale_id`, `payment_method`, `amount`, `reference_number`, `tendered_amount`, `change_amount`, `payment_date`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 'gcash', 1000.00, '6456456456', 1000.000000, 0.000000, '2026-06-12 15:58:25', '5412123', 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(2, 1, 'cash', 469.00, NULL, 469.000000, 0.000000, '2026-06-12 15:58:25', '5412123', 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(3, 2, 'cash', 6292.50, NULL, 6292.500000, 0.000000, '2026-06-12 16:01:01', NULL, 3, 3, '2026-06-12 16:01:01', '2026-06-12 08:01:01', 'active', 0, NULL),
	(4, 3, 'cash', 5000.00, NULL, 5000.000000, 0.000000, '2026-06-12 16:01:56', NULL, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(5, 3, 'gcash', 4000.00, '456456456', 4000.000000, 0.000000, '2026-06-12 16:01:56', NULL, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(6, 3, 'bank_transfer', 312.25, '7878787', 312.250000, 0.000000, '2026-06-12 16:01:56', NULL, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(7, 4, 'cash', 3431.13, NULL, 3431.130000, 0.000000, '2026-06-12 16:08:58', NULL, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(8, 5, 'cash', 1000.00, NULL, 1000.000000, 265.000000, '2026-06-12 16:12:57', NULL, 3, 3, '2026-06-12 16:12:57', '2026-06-12 08:12:57', 'active', 0, NULL),
	(9, 6, 'cash', 1000.00, NULL, 1000.000000, 0.000000, '2026-06-12 16:44:22', '454545', 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(10, 6, 'gcash', 1000.00, '12345', 1000.000000, 0.000000, '2026-06-12 16:44:22', '454545', 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(11, 6, 'bank_transfer', 958.00, '565', 958.000000, 0.000000, '2026-06-12 16:44:22', '454545', 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(12, 7, 'cash', 4000.00, NULL, 4000.000000, 486.250000, '2026-06-12 16:45:07', NULL, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(13, 8, 'cash', 2000.00, NULL, 2000.000000, 535.750000, '2026-06-12 16:59:35', NULL, 3, 3, '2026-06-12 16:59:35', '2026-06-12 08:59:35', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_payments` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_products
DROP TABLE IF EXISTS `pos_products`;
CREATE TABLE IF NOT EXISTS `pos_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_on_hand` double unsigned NOT NULL DEFAULT '0',
  `tenant_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `wholesale_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reorder_level` int(11) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=586 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_products: ~585 rows (approximately)
DELETE FROM `pos_products`;
INSERT INTO `pos_products` (`id`, `stock_on_hand`, `tenant_id`, `category_id`, `unit_id`, `barcode`, `sku`, `name`, `description`, `cost_price`, `selling_price`, `wholesale_price`, `reorder_level`, `image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 990, 1, 7, 2, '4800361410816', 'SKU-OCNIJIHQ', 'bear brand (w) 33g', 'Bear Brand | Milks', 18.00, 22.00, 20.00, 7, 'products/WHyV8rPu32iyhUFZKw6IT7fNmyW4tPAIinoCap8C.jpg', 1, 1, '2026-06-11 15:37:26', '2026-06-12 08:59:35', 'active', 0, NULL),
	(2, 1976, 1, 2, 28, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 'SkyFlakes | Crackers (Appetizers)', 147.00, 183.75, 169.05, 19, 'products/u9U30Qu573ReL1vT640sOJiOWiB4YTJ3mckNcfFo.webp', 1, 1, '2026-06-11 15:37:26', '2026-06-12 08:45:07', 'active', 0, NULL),
	(3, 467, 1, 9, 28, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 'My.san | Biscuits and crackers', 367.00, 458.75, 422.05, 12, 'products/wgco0gFPW75RCF3H70tcR6GunmZZIKGwQFgqx76b.webp', 1, 1, '2026-06-11 15:37:26', '2026-06-12 08:59:35', 'active', 0, NULL),
	(4, 0, 1, 1, 1, '4806502720615', 'SKU-GKJH6SBT', 'Gardenia White Bread Classic 600G', 'Gardenia | Sliced breads, White breads, Enriched white breads', 147.00, 183.75, 169.05, 10, 'products/agazOcy3AVuzeJNMaZADBDB1VhQhNOKxnbZXgSmM.png', 1, 1, '2026-06-11 15:37:26', '2026-06-12 06:05:11', 'active', 0, NULL),
	(5, 126, 1, 1, 15, '4800016068010', 'SKU-NXZS5YOI', 'c2 apple 500ml', 'C2 | Green teas, Sweetened beverages, Flavored green teas, Green apple tea, Ready-to-drink teas', 354.00, 442.50, 407.10, 17, 'products/dRO0Hn97Qs3sUwTfxMms19kVD43ALK7rISUEiyh2.jpg', 1, 1, '2026-06-11 15:37:26', '2026-06-12 08:45:07', 'active', 0, NULL),
	(6, 0, 1, 1, 1, '4800092113338', 'SKU-EUS9E0E8', 'rebisco crackers (g) 33g', 'Rebisco | Crackers (Appetizers)', 58.00, 72.50, 66.70, 13, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(7, 0, 1, 1, 1, '8997035600027', 'SKU-XDBIRTO4', 'pocari sweat 500ml', 'Pocari | Carbonated soft drinks without fruit juice with sugar and artificial sweeteners, Dietary drink for sport, Energy drink with sugar', 294.00, 367.50, 338.10, 18, 'products/dt6dZLXtIt9CO2r1RZgFaRabvrLnvG0kYYdtI6Af.webp', 1, 1, '2026-06-11 15:37:26', '2026-06-12 06:07:05', 'active', 0, NULL),
	(8, 0, 1, 1, 16, '4801981116072', 'SKU-GCQEGEUZ', 'Coca Cola original taste', 'coca cola | Colas', 355.00, 443.75, 408.25, 5, 'products/UQSDoK9t9Djzu8l6e49wBkNtTXYhAO3L9Ao8ix5S.webp', 1, 1, '2026-06-11 15:37:26', '2026-06-12 06:35:16', 'active', 0, NULL),
	(9, 0, 1, 1, 1, '4801981118502', 'SKU-RFZMPZ3P', 'Coke Mismo 290mL', 'Coca Cola | Colas, Sweetened beverages', 454.00, 567.50, 522.10, 15, 'products/p8FT7b9re8MhOUKJ200qC8cudB2JER8EOoKwFvIA.webp', 1, 1, '2026-06-11 15:37:26', '2026-06-12 06:37:54', 'active', 0, NULL),
	(10, 0, 1, 1, 1, '0039000086639', 'SKU-SVRPPIAJ', 'Vienna Sausage', 'Libby\'s | Canned foods, Chicken preparations, Pork sausages, Chicken sausages, Industrial knacks', 242.00, 302.50, 278.30, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(11, 0, 1, 1, 1, '0748485100401', 'SKU-ZUA0ZRGO', 'century tuna (p) 155g', 'Century Pacific Food Inc | Canned tunas', 152.00, 190.00, 174.80, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(12, 0, 1, 1, 1, '4800010075069', 'SKU-4QKZCFNT', 'cream o (b) vanilla 30g', 'Jack n\' Jill, Jackwill | Chocolate sandwich cookies, Cookies with milk chocolate', 257.00, 321.25, 295.55, 16, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(13, 0, 1, 1, 1, '8996001440124', 'SKU-WQER3O7D', 'Energen Minuman Serbuk Sereal Susu Vanilla 34g', 'Energen | Hot beverages', 384.00, 480.00, 441.60, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(14, 0, 1, 1, 1, '4807770122163', 'SKU-RX3TAPZJ', 'Butter Coconut', 'Nissin | Biscuits', 200.00, 250.00, 230.00, 9, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(15, 0, 1, 1, 1, '4809010626011', 'SKU-BLJVQXRU', 'Cheese Cake', 'Lemon Square | Cheesecakes', 296.00, 370.00, 340.40, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(16, 0, 1, 1, 1, '4807770121326', 'SKU-BZKFQY93', 'Monde Special Mamon Classic', '', 66.00, 82.50, 75.90, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(17, 0, 1, 1, 1, '4803925033551', 'SKU-S3BG33BX', 'Sting Energy Drink Strawberry', 'Pepsi | Artificially sweetened beverages, Energy drinks', 488.00, 610.00, 561.20, 16, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(18, 0, 1, 1, 1, '0750515031043', 'SKU-GRIETMUG', 'Sky Flakes', 'M.Y. San | Appetizers, Biscuits', 329.00, 411.25, 378.35, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(19, 0, 1, 1, 1, '4800361413480', 'SKU-J9SBB4FS', 'milo 24g', 'NestlÃ© | Flavoured Drinks', 135.00, 168.75, 155.25, 17, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(20, 0, 1, 1, 1, '4807770270024', 'SKU-E4Y6ZSUC', 'lm chicken 55g', 'Lucky Me! | Instant noodle soups, Chicken-noodles', 268.00, 335.00, 308.20, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(21, 0, 1, 1, 1, '8998666001719', 'SKU-M0EAF2ED', 'kopiko twin (bwn) 40g', 'Kopiko | Coffees, Coffee drinks', 275.00, 343.75, 316.25, 6, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(22, 0, 1, 1, 1, '4803925061141', 'SKU-UQWRIFKL', 'Gatorade blue bolt flavor', 'Gatorade | Energy drinks, Sweetened beverages, Gatorade', 179.00, 223.75, 205.85, 13, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(23, 0, 1, 1, 1, '4800010076073', 'SKU-DWXTTI0W', 'Peanut Butter Sandwich Cookies', 'Presto | Assortments of biscuits', 431.00, 538.75, 495.65, 7, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(24, 0, 1, 1, 1, '0054028367911', 'SKU-NDLPDFC4', 'Probiotic Drink', 'Yakult | Milks, Yogurt drinks', 227.00, 283.75, 261.05, 18, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(25, 0, 1, 1, 1, '4800194104869', 'SKU-YEUWOSAA', 'crispy patata (bwn) 24g', 'Oishi | Potato crisps, Potato Snacks, Savory Snacks', 428.00, 535.00, 492.20, 6, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(26, 0, 1, 1, 1, '8851717905407', 'SKU-FK4RPMXY', 'Delight Plus Probiotic Fiber', 'Dutch Mill | Milks, Probiotic Drink', 445.00, 556.25, 511.75, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(27, 0, 1, 1, 1, '0748485100081', 'SKU-FJC0YH7K', 'Century Tuna Flakes in Oil', 'Century Tuna | tuna flakes in oil', 253.00, 316.25, 290.95, 19, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(28, 0, 1, 1, 1, '4800137365326', 'SKU-1RKIRUO4', 'Maya (The Original Hotcake Mix)', 'Maya | Cakes, Cake mixes', 273.00, 341.25, 313.95, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(29, 0, 1, 1, 1, '4806504710126', 'SKU-CWWBJYYZ', 'mega sardines (r) 155g', 'MEGA | Sardines in tomato sauce', 162.00, 202.50, 186.30, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(30, 0, 1, 1, 1, '0748485200040', 'SKU-YSENTTGC', 'Spanish Style Sardines', '555 | Canned sardines', 197.00, 246.25, 226.55, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(31, 0, 1, 1, 1, '4800016663802', 'SKU-TT3JRRBR', 'nova (r) cheddar 38g', 'Jack \'n Jill, Nova | Crisps, Plain salty snacks', 176.00, 220.00, 202.40, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(32, 0, 1, 1, 1, '8992760121014', 'SKU-WN8GQFCC', 'oreo (w) vanilla 27.6g', 'OREO | Filled biscuits', 405.00, 506.25, 465.75, 9, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(33, 0, 1, 1, 1, '4800361339568', 'SKU-X1QXUBGL', 'nescafe stick (r) classic 1.9g', 'NescafÃ© | Coffees, Instant coffees', 211.00, 263.75, 242.65, 11, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(34, 0, 1, 1, 1, '4800016671807', 'SKU-LW0L4M7Q', 'Piatto', 'JACK\'nJILL | Crisps', 89.00, 111.25, 102.35, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(35, 0, 1, 1, 1, '4808680021355', 'SKU-CURCDEU1', 'ladys choice (b) mayonnaise 80ml', 'Lady\'s Choice | Mayonnaises, Groceries', 84.00, 105.00, 96.60, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(36, 0, 1, 1, 1, '4801981127177', 'SKU-DYDZXQ8F', 'Coca-Cola Original Taste', 'Coca-Cola | Colas', 191.00, 238.75, 219.65, 17, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(37, 0, 1, 1, 1, '4808887010015', 'SKU-YEOYBM06', 'Purefoods Corned Beef', 'Purefoods, San Miguel | Beef dishes, Corned beef', 23.00, 28.75, 26.45, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(38, 0, 1, 1, 1, '4807770270017', 'SKU-JF7V666T', 'Instant Mami Noodles', 'Lucky | Instant noodle soups', 497.00, 621.25, 571.55, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(39, 0, 1, 1, 1, '4800274003518', 'SKU-AAY2TXOQ', 'Oatmeal', 'QUAKER | Cereals and potatoes', 334.00, 417.50, 384.10, 9, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(40, 0, 1, 1, 1, '4800194153225', 'SKU-JKOHMCNK', 'bread pan (g) cheese & onion 24g', 'Bread Pan, Oishi | Breads, Plain salty snacks', 451.00, 563.75, 518.65, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(41, 0, 1, 1, 1, '4800092115707', 'SKU-UURQP2PK', 'Wafer time', 'Rebisco | Wafers, Chocolate biscuits', 375.00, 468.75, 431.25, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(42, 0, 1, 1, 1, '4800092552779', 'SKU-U6LN8SZ0', 'doowee donut (b) 42g', 'DOOWEE DONUT | Doughnuts', 379.00, 473.75, 435.85, 18, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(43, 0, 1, 1, 1, '4800361396677', 'SKU-HM3KFSVQ', 'Bear brand Adult plus', 'NestlÃ©', 318.00, 397.50, 365.70, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(44, 0, 1, 1, 1, '4800216120013', 'SKU-VWNDQSWB', 'clover (b) cheesier 24g', 'Leslies | Corn chips', 49.00, 61.25, 56.35, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(45, 0, 1, 1, 1, '4806521793836', 'SKU-DALBOGXU', 'Wl Panda 3d biscuit', '| Biscuits', 155.00, 193.75, 178.25, 7, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(46, 0, 1, 1, 1, '4806529230463', 'SKU-HIAQXVFN', 'Banana Chips', 'Memberâ€™s Value S&R', 264.00, 330.00, 303.60, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(47, 0, 1, 1, 1, '8998666001726', 'SKU-DHXJAQRP', 'kopiko twin (w) blanca 40g', 'PT Torabika Eka Semesta | Coffees, Creamer, Sweetened beverages', 108.00, 135.00, 124.20, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(48, 0, 1, 1, 1, '48025522', 'SKU-16SGFCZM', 'magic sarap 8g', 'Maggi | Culinary plants, Herbs and spices', 310.00, 387.50, 356.50, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(49, 0, 1, 1, 1, '48036214', 'SKU-GMS4MQAR', 'star margarine (y) 100g', 'fortified | Salted spreads, Margarines', 113.00, 141.25, 129.95, 17, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(50, 0, 1, 1, 1, '4806502720301', 'SKU-F7QW15CE', 'High Fiber Whole Wheat Bread', 'Gardenia | Wheat breads', 49.00, 61.25, 56.35, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(51, 0, 1, 1, 1, '4800361061322', 'SKU-6FN3CAAL', 'All purpose  Cream', 'NestlÃ© | Creams', 117.00, 146.25, 134.55, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(52, 0, 1, 1, 1, '4800110093888', 'SKU-7IZERRMT', 'Fortified milk', 'Selecta | Milks', 18.00, 22.50, 20.70, 19, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(53, 0, 1, 1, 1, '4806502727423', 'SKU-QNKIWHDY', 'Neu Bake Wheaten bread', 'Gardenia | Wheat breads', 394.00, 492.50, 453.10, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(54, 0, 1, 1, 1, '4800361415293', 'SKU-PJIO6DUJ', 'Milo Twin Pack', 'NestlÃ© | Cocoa and its products, Flavoured milks, Instant beverages', 118.00, 147.50, 135.70, 16, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(55, 0, 1, 1, 1, '4800361395687', 'SKU-HTCEBVOX', 'Chuckie', 'NestlÃ© | Chocolate milks, Choco', 491.00, 613.75, 564.65, 7, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(56, 0, 1, 1, 1, '4807770271229', 'SKU-SYCXJJDS', 'Pancit Canton', 'Lucky Me | Instant noodles', 217.00, 271.25, 249.55, 7, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(57, 0, 1, 1, 1, '4801981107971', 'SKU-BZQQ539F', 'Wilkins Pure Purified Drinking Water', 'Wilkins | Waters', 480.00, 600.00, 552.00, 20, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(58, 0, 1, 1, 1, '0748485401492', 'SKU-LYT6Q31A', 'Birch Tree Fortified Milk', 'Birch Tree | Milks', 240.00, 300.00, 276.00, 18, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(59, 0, 1, 1, 1, '4800092113482', 'SKU-QYYQMM7M', 'hansel crackers 32g', 'Rebisco | Appetizers, Plain biscuit', 256.00, 320.00, 294.40, 13, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(60, 0, 1, 1, 1, '4800016052132', 'SKU-F6IBC8JV', 'C2 APPLE 455ML', 'C2 | Green teas, Iced teas', 28.00, 35.00, 32.20, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(61, 0, 1, 1, 1, '4807770273674', 'SKU-9UEUADE4', 'lm pc kalamansi 80g', 'LuckyMe! | Instant noodles, Instant pasta', 23.00, 28.75, 26.45, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(62, 0, 1, 1, 1, '4800024562692', 'SKU-ROFNCPUM', 'dm pj ace 220ml', 'Del Monte | Unsweetened beverages, Pineapple juices', 272.00, 340.00, 312.80, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(63, 0, 1, 1, 1, '4800016644801', 'SKU-WSIFZ4DL', 'piattos (b) cheese 45g', 'Jack-n-Jill | Mustard crisps', 37.00, 46.25, 42.55, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(64, 0, 1, 1, 1, '4800361316934', 'SKU-K1BQY3WX', 'Savor Liquid Seasoning Chilimansi', 'Maggi | Condiments', 51.00, 63.75, 58.65, 13, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(65, 0, 1, 1, 1, '4800092113291', 'SKU-YMJA3QHM', 'HANSEL SANDWICH MOCHA', 'Rebisco | Sandwich biscuits, Cream-Filled Biscuits', 448.00, 560.00, 515.20, 16, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(66, 0, 1, 1, 1, '4800361410892', 'SKU-MZEQTVLY', 'Fortified Chocomilk Drink', 'NestlÃ©, Nestle Bear Brand | Milk powders', 10.00, 12.50, 11.50, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(67, 0, 1, 1, 1, '4800344001949', 'SKU-6VYGUWBJ', 'Silver swan', '', 106.00, 132.50, 121.90, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(68, 0, 1, 1, 1, '0748485800233', 'SKU-LRYSYZR2', 'Canned Meat Loaf', 'Argentina | Canned Meat Loaf', 287.00, 358.75, 330.05, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(69, 0, 1, 1, 1, '4800092551604', 'SKU-LTOGZGHL', 'doowee donut (r) 42g', '| Doughnuts', 450.00, 562.50, 517.50, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(70, 0, 1, 1, 1, '0748485100418', 'SKU-E3TXS4KF', 'century tuna (h) 155g', 'Century | Tunas in oil, tuna flakes in oil', 309.00, 386.25, 355.35, 18, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(71, 0, 1, 1, 1, '4800361388313', 'SKU-S6EDOJPI', 'Chuckie 180mL', 'NestlÃ© | Beverages, Candies', 389.00, 486.25, 447.35, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(72, 0, 1, 1, 1, '4800365100485', 'SKU-KL7FZSPB', 'Super crunch Cheese ring', 'Super crunch, super crunch | Crisps', 345.00, 431.25, 396.75, 11, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(73, 0, 1, 1, 1, '0745125547008', 'SKU-H7QYH3S4', 'Creatine Monohydrate', 'Athlene | Bodybuilding supplements', 411.00, 513.75, 472.65, 17, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(74, 0, 1, 1, 1, '4801032745046', 'SKU-XZIPVV3V', 'San Mig Light in Can', '', 177.00, 221.25, 203.55, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(75, 0, 1, 1, 1, '4800092553189', 'SKU-GI6MUQRZ', 'Whoopie Marshmallow', '| Cakes', 272.00, 340.00, 312.80, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(76, 0, 1, 1, 1, '4800888218889', 'SKU-YXPSFQC2', 'Liquid Seasoning Chili', 'Knorr, Unilever | Soy sauces', 488.00, 610.00, 561.20, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(77, 0, 1, 1, 1, '4800274310357', 'SKU-2YTJZLEU', 'Instant Banana and honey', 'Quaker | Breakfast cereals', 66.00, 82.50, 75.90, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(78, 0, 1, 1, 1, '4800016627279', 'SKU-OOEUVONY', 'berry knots (p) 25g', 'Jack n\' Jill', 454.00, 567.50, 522.10, 5, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(79, 0, 1, 1, 1, '4801981118601', 'SKU-WTTEAFLI', 'Sprite Lemon-Lime Carbonated Flavored Drink', 'Sprite | Sodas, Sweetened beverages, Lemon-lime-drink', 296.00, 370.00, 340.40, 20, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(80, 0, 1, 1, 1, '4800361423601', 'SKU-5SDQAU4A', 'Original', 'Nescafe | Coffee drinks, Coffee milks', 390.00, 487.50, 448.50, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(81, 0, 1, 1, 1, '4800274020010', 'SKU-DWYPMIDV', 'quaker oat', 'Quaker', 302.00, 377.50, 347.30, 20, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(82, 0, 1, 1, 1, '4806502725900', 'SKU-MBN2F6DN', 'High FIBER WHEAT BREAD', 'Gardenia | Breakfast biscuit with cereals fortified with vitamins and chemical elements', 36.00, 45.00, 41.40, 10, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(83, 0, 1, 1, 1, '4800148532106', 'SKU-GOSI7MZI', 'Super Loaf', 'Marby | Breads', 18.00, 22.50, 20.70, 16, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(84, 0, 1, 1, 1, '8888440000048', 'SKU-HSDJQCFW', 'Pure milk', 'Cowhead | UHT Milks', 445.00, 556.25, 511.75, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(85, 0, 1, 1, 1, '4800148531598', 'SKU-E5QVZEWM', 'Whole Wheat Bread', 'Marby | White breads, Whole Wheat breads', 451.00, 563.75, 518.65, 6, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(86, 0, 1, 1, 1, '8996001440049', 'SKU-TS1V1FBH', 'ENERGEN COKLAT 34g', 'Energen | Breakfast cereals', 134.00, 167.50, 154.10, 15, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(87, 0, 1, 1, 1, '4800016653094', 'SKU-SNAHWYS4', 'Mr. chips', 'Jack \'n Jill | Romaine lettuce', 208.00, 260.00, 239.20, 18, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(88, 0, 1, 1, 1, '4800110094465', 'SKU-LLWVB6G3', 'Fortified Low Fat Milk', 'Selecta | Semi-skimmed milks, UHT Milks', 382.00, 477.50, 439.30, 16, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(89, 0, 1, 1, 1, '4800092550904', 'SKU-2W72DCFR', 'Fudgee bar', 'Fudgee Barr | Cakes', 353.00, 441.25, 405.95, 20, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(90, 0, 1, 1, 1, '4800361379557', 'SKU-F30EWPW2', 'nestea lemon 20g', 'SociÃ©tÃ© des Produits NestlÃ© S.A. | Iced teas', 185.00, 231.25, 212.75, 20, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(91, 0, 1, 1, 1, '4800092660634', 'SKU-O6WLPLTW', 'Choco Mucho Caramel', 'Choco Mucho, Multirich, Multirich Foods Corp. | Candy chocolate bars, Milk chocolate with caramel', 300.00, 375.00, 345.00, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(92, 0, 1, 1, 1, '4800092113536', 'SKU-28RK7H1N', 'combi (b) choco 30g', 'Rebisco | Chocolate biscuits', 240.00, 300.00, 276.00, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(93, 0, 1, 1, 1, '4806521791696', 'SKU-95OSTSBU', 'EC Crunchy Choco Flakes', 'W.L. Foods | Crisps', 31.00, 38.75, 35.65, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(94, 0, 1, 1, 1, '4800575140158', 'SKU-CZOLC0UM', 'alaska evaporada (y) 140ml', 'alaska | Milk substitutes, Evaporated milks', 299.00, 373.75, 343.85, 14, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(95, 0, 1, 1, 1, '4800016671500', 'SKU-SY2LZEY4', 'Piattos Sour Cream and Onion', 'Jack N Jill | Potato crisps', 190.00, 237.50, 218.50, 9, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(96, 0, 1, 1, 1, '4800016052040', 'SKU-OVT3NPI6', 'Apple green tea', '| Teas', 333.00, 416.25, 382.95, 13, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(97, 0, 1, 1, 1, '4800024577139', 'SKU-L34IDA79', 'dm ketchup sweet 320g', 'Del Monte', 36.00, 45.00, 41.40, 12, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(98, 0, 1, 1, 1, '4801668602034', 'SKU-BHLLVNTI', 'datu puti soy sauce 200ml', '| Soy sauces', 446.00, 557.50, 512.90, 9, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(99, 0, 1, 1, 1, '4806504710119', 'SKU-DS1HHFNG', 'Sardines in Tomato Sauce', 'Mega | Sardines in tomato sauce', 69.00, 86.25, 79.35, 8, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(100, 0, 1, 1, 1, '0748485800431', 'SKU-XJM2OXQV', 'argentina corned beef 150g', 'Argentina | Meals with meat, Canned meats, Corned beef', 171.00, 213.75, 196.65, 20, NULL, 1, 1, '2026-06-11 15:37:26', '2026-06-11 07:37:26', 'active', 0, NULL),
	(101, 0, 1, 1, 1, '4807770100703', 'SKU-HJXGSMY2', 'Bread Stix', 'Nissin | Crackers (Appetizers), Breadsticks', 481.00, 601.25, 553.15, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(102, 0, 1, 1, 1, '4807770273711', 'SKU-AMKAZSBO', 'lm pc sweet & spicy 80g', 'Lucky Me | Instant noodles, Foods', 67.00, 83.75, 77.05, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(103, 0, 1, 1, 1, '4807770272646', 'SKU-UFA4YDKN', 'Lucky me jjamppong artificial spicy seafood flavor noodles', '| Instant noodles', 266.00, 332.50, 305.90, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(104, 0, 1, 1, 1, '8851028002505', 'SKU-3QQFOD85', 'Vitamilk Double Choco Shake Soy Milk Drink', 'vitamilk, à¹„à¸§à¸•à¸²à¸¡à¸´à¹‰à¸¥à¸„à¹Œ | Chocolate soy-based drinks', 232.00, 290.00, 266.80, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(105, 0, 1, 1, 1, '4807770273698', 'SKU-6EEM9Z6C', 'lm pc chilimansi 80g', 'Lucky Me | Instant noodles', 136.00, 170.00, 156.40, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(106, 0, 1, 1, 1, '4807770120473', 'SKU-8V3VJPKF', 'Butter Coconut', 'Nissin | Crackers (Appetizers)', 306.00, 382.50, 351.90, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(107, 0, 1, 1, 1, '4801981127207', 'SKU-ZOI5XBKY', 'tru-orange', 'Royal | Sodas, Softdrink', 149.00, 186.25, 171.35, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(108, 0, 1, 1, 1, '4807770120206', 'SKU-MNA3E4O4', 'Wafer Choco', 'Monde Nissin, Nissin, Nissin Wafer, Nissin Wafer Choco | Chocolate stuffed wafers', 372.00, 465.00, 427.80, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(109, 0, 1, 1, 1, '4806502726822', 'SKU-D3KZTZNE', 'Neu Bake White Bread', 'Gardenia | Wheat breads', 227.00, 283.75, 261.05, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(110, 0, 1, 1, 1, '8851013740498', 'SKU-PGF73YBU', 'Mixed Veggie & Mixed Fruit Juice', 'Del Monte, Tipco | Squeezed multifruit juices', 127.00, 158.75, 146.05, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(111, 0, 1, 1, 1, '4807770121333', 'SKU-GHHI3JBL', 'Monde Special Mamon Classic', 'Monde, Monde Nissin | Sponge cakes, Mamon', 475.00, 593.75, 546.25, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(112, 0, 1, 1, 1, '4807770273704', 'SKU-NLIL2CI3', 'âœ“ 6/2/26- (pc)- 15.50', 'Lucky Me | Instant noodles', 254.00, 317.50, 292.10, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(113, 0, 1, 1, 1, '12743995', 'SKU-ZZC5CIVU', 'Cheezee', '', 431.00, 538.75, 495.65, 17, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(114, 0, 1, 1, 1, '8993175539241', 'SKU-YTX6LKH5', 'richoco wafer (bwn) chocolate 48g', 'Nabati | Baon Favorites, Chocolate Wafers, Wafer Snacks', 88.00, 110.00, 101.20, 16, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(115, 0, 1, 1, 1, '8410128100155', 'SKU-XYIWSSGL', 'Nonfat Yogurt with Strawberry Bits', 'Pascual | Yogurts, non-fat', 429.00, 536.25, 493.35, 7, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(116, 0, 1, 1, 1, '4800361417174', 'SKU-TBWJT219', 'Nestea cleanse', 'Nestea | Sugar free instant Ice Teas', 117.00, 146.25, 134.55, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(117, 0, 1, 1, 1, '4801958846001', 'SKU-C9WEC1T5', 'at seasonings, kaya bagay sa kahit', 'Ginisa', 461.00, 576.25, 530.15, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(118, 0, 1, 1, 1, '0748485801490', 'SKU-O5EMO5AV', 'argentina meat loaf 150g', '', 97.00, 121.25, 111.55, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(119, 0, 1, 1, 1, '0750515030008', 'SKU-WGY3ZM71', 'Skyflakes Fit Crackers with Flax Seeds', 'M.Y. San | Crackers (Appetizers)', 67.00, 83.75, 77.05, 10, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(120, 0, 1, 1, 1, '0038000138430', 'SKU-DQMPO6E6', 'Original Light', 'Pringles | Stacked extruded potato crisps', 159.00, 198.75, 182.85, 12, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(121, 0, 1, 1, 1, '4807770100406', 'SKU-RC79YNQG', 'Eggnog Cookies', 'Monde Nissin | Biscuits', 241.00, 301.25, 277.15, 17, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(122, 0, 1, 1, 1, '4808680221250', 'SKU-PZJ3BJ72', 'Cream of Mushroom', 'Knorr', 153.00, 191.25, 175.95, 14, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(123, 0, 1, 1, 1, '0748485700021', 'SKU-EADB3KRE', '555 tuna afritada 155g', '555 | tuna flakes in tomato sauce, Tuna preparations', 364.00, 455.00, 418.60, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(124, 0, 1, 1, 1, '0037600130783', 'SKU-0NXSCSH2', 'SPAM', 'Hormel | Prepared meats, Canned meats', 12.00, 15.00, 13.80, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(125, 0, 1, 1, 1, '4806504710812', 'SKU-O0MBESVI', 'FRIED SARDINES WITH TAUSI', 'MEGA | Sardines in oil', 264.00, 330.00, 303.60, 16, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(126, 0, 1, 1, 1, '4806018405808', 'SKU-ZZSJANQV', 'Lemon Square Whatta Tops Cookies \'n Cream Flavor (Cupcakes with Toppings)', 'Lemon Square | Biscuits', 485.00, 606.25, 557.75, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(127, 0, 1, 1, 1, '4802222046141', 'SKU-F7CH2RGZ', 'Banana chips', 'Sm Bonus | Banana crisps', 124.00, 155.00, 142.60, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(128, 0, 1, 1, 1, '4800361399159', 'SKU-CY491DUV', 'Greek Flavored Yogurt', 'SociÃ©tÃ© des Produits NestlÃ© S.A. | Greek-style yogurts', 449.00, 561.25, 516.35, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(129, 0, 1, 1, 1, '4800092660962', 'SKU-J7RGOOBR', 'Choco Mucho Dark Chocolate', 'Multirich Foods | Chocolate candies, Bars, Dark chocolate wafers rolls', 430.00, 537.50, 494.50, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(130, 0, 1, 1, 1, '4806502721476', 'SKU-V1PM9STX', 'Chocolate Chip Loaf', 'Gardenia | Breads', 334.00, 417.50, 384.10, 7, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(131, 0, 1, 1, 1, '7622300743000', 'SKU-4VCFOVPQ', 'Tiger Vanilla', 'Mondelez | Crackers(Breakfast)', 50.00, 62.50, 57.50, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(132, 0, 1, 1, 1, '4800016622533', 'SKU-WBEBB7N2', 'vcut (o) spicy barbeque 25g', 'Jack \'n Jill, Universal Robina, Vcut | Barbecue crisps, Wavy potato crisps', 228.00, 285.00, 262.20, 17, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(133, 0, 1, 1, 1, '4800016113055', 'SKU-CZ72LB56', 'dewberry (b) bluebeeries 33g', 'Dewberry, Jack \'n Jill, Universal Robina | Sandwich biscuits', 367.00, 458.75, 422.05, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(134, 0, 1, 1, 1, '4800016073588', 'SKU-FBEAU3F1', 'blue lychee 350ml', '', 358.00, 447.50, 411.70, 8, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(135, 0, 1, 1, 1, '4800014141081', 'SKU-84KAYWK6', 'Summit Natural Dringking water', '', 206.00, 257.50, 236.90, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(136, 0, 1, 1, 1, '4800092113413', 'SKU-0E48HE2U', 'bravo 30g', 'Rebisco | Biscuits', 366.00, 457.50, 420.90, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(137, 0, 1, 1, 1, '4800092113253', 'SKU-KJZKBOUG', 'rebisco (bwn) chocolate 32g', 'rebisco | Tartlet biscuits with chocolate', 214.00, 267.50, 246.10, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(138, 0, 1, 1, 1, '4801668100271', 'SKU-HWVJBEZP', 'All-around sarsa', 'Mang Tomas', 252.00, 315.00, 289.80, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(139, 0, 1, 1, 1, '4807770101533', 'SKU-LGBCI5Y7', '4807770101533', '| Biscuits, Orange cream filled chocolate sandwich cookies', 368.00, 460.00, 423.20, 14, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(140, 0, 1, 1, 1, '4806512230593', 'SKU-VVSHG0AH', 'Flour', 'Magnolia', 267.00, 333.75, 307.05, 16, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(141, 0, 1, 1, 1, '0014285000075', 'SKU-JDHOXP3A', 'Ufc ketchup', '| Ketchup', 140.00, 175.00, 161.00, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(142, 0, 1, 1, 1, '4800361416726', 'SKU-I0C1GEVJ', 'nescafe twin (b) creamy white 40g', 'NestlÃ© | Hot beverages, Coffees, Instant coffees', 229.00, 286.25, 263.35, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(143, 0, 1, 1, 1, '4800274300013', 'SKU-Q4UE6XOO', 'Instant Oats Banana And Honey', 'Quaker | Rolled oats, Oat', 297.00, 371.25, 341.55, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(144, 0, 1, 1, 1, '4805358373037', 'SKU-XLPTNKFR', 'Daily Quezo', 'Magnolia', 323.00, 403.75, 371.45, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(145, 0, 1, 1, 1, '4801668602027', 'SKU-Q8QHFVGU', 'dp vinegar 200ml', 'Papa Banaba Ketchup', 499.00, 623.75, 573.85, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(146, 0, 1, 1, 1, '4800016555985', 'SKU-ECPTWMJB', 'Xtra Big Pancit Canton Kalamansi', 'Payless | Instant noodles', 195.00, 243.75, 224.25, 7, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(147, 0, 1, 1, 1, '4800194105958', 'SKU-S23VPRMG', 'Oishi Prawn Crackers (Sweet Extra Flavor)', 'Oishi | Prawn crackers', 496.00, 620.00, 570.40, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(148, 0, 1, 1, 1, '96104026', 'SKU-SDNXCFJ7', 'Fisherman\'s Friend mandarin & ginger', 'Fisherman\'s Friend | Lozenges', 201.00, 251.25, 231.15, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(149, 0, 1, 1, 1, '8996001305201', 'SKU-RXQ53UYH', 'Valmer', 'Mayora', 399.00, 498.75, 458.85, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(150, 0, 1, 1, 1, '4800818808302', 'SKU-Y2HEMOZU', 'Frutos', 'Columbia\'s, Frutos | Candies', 362.00, 452.50, 416.30, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(151, 0, 1, 1, 1, '6294002417131', 'SKU-HTW7P5FS', 'Peanut Butter Creamy', '| Peanut butters', 121.00, 151.25, 139.15, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(152, 0, 1, 1, 1, '4800016138560', 'SKU-WFLTP8KS', 'tortillos (o) 25g', 'Universal robina | Corn chips, Baon Favorites, Savory Snacks', 261.00, 326.25, 300.15, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(153, 0, 1, 1, 1, '4800092553011', 'SKU-JMXNJYPQ', 'doowee donut (p) 42g', 'DOOWEE DONUT', 283.00, 353.75, 325.45, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(154, 0, 1, 1, 1, '0748485800011', 'SKU-M1LXH1PP', 'Corned Beef', 'Argentina | Beef dishes, Canned meats, Corned beef, Ready to eat', 43.00, 53.75, 49.45, 16, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(155, 0, 1, 1, 1, '4808680652757', 'SKU-FNZP5FB5', 'Mayonnaise', 'Best Foods', 480.00, 600.00, 552.00, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(156, 0, 1, 1, 1, '0729461168158', 'SKU-XB3G2WSF', 'cheese ring 25g', 'Regent | Snacks, Cheeses, Processed cheeses', 463.00, 578.75, 532.45, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(157, 0, 1, 1, 1, '4800016012280', 'SKU-URVVLQQZ', 'Great Taste Premium Coffee Classic 100g', '', 120.00, 150.00, 138.00, 17, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(158, 0, 1, 1, 1, '4800016561436', 'SKU-YE63IGQY', 'Xtra Big Sweet and Spicy Pancit Canton', 'Payless | Instant noodles', 73.00, 91.25, 83.95, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(159, 0, 1, 1, 1, '7612100064276', 'SKU-CH5FO8S6', 'Ovalteenies', 'Ovalteen', 309.00, 386.25, 355.35, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(160, 0, 1, 1, 1, '4800523220802', 'SKU-YS5JSF4R', 'moby (y) 25g', '', 56.00, 70.00, 64.40, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(161, 0, 1, 1, 1, '8997035600560', 'SKU-PTVPTDDL', 'Pocari sweat', '| Beverages', 456.00, 570.00, 524.40, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(162, 0, 1, 1, 1, '4806530070010', 'SKU-GVT0CJWO', 'Yellowfin Tuna Chunks in Olive Oil', 'General Nutrifoods, Gold Seas | Tuna in olive oil, Tuna chunks', 279.00, 348.75, 320.85, 10, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(163, 0, 1, 1, 1, '4801668505038', 'SKU-G4F3FW90', 'Patis', 'Datu Puti', 176.00, 220.00, 202.40, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(164, 0, 1, 1, 1, '4800049720107', 'SKU-JWLKZI8F', 'Natures Spring Dringking Water 350mL', 'Nature\'s Spring | Bottled water flavoured without sugar or artificial sweeteners, Purified-water', 490.00, 612.50, 563.50, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(165, 0, 1, 1, 1, '4801981120642', 'SKU-CYR5HINX', 'Zero Sugar', 'Coca-Cola | Diet cola soft drink', 429.00, 536.25, 493.35, 14, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(166, 0, 1, 1, 1, '4801668505045', 'SKU-M0N4YM5F', 'Datu Puti Patis 1L', '', 447.00, 558.75, 514.05, 5, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(167, 0, 1, 1, 1, '4800016560910', 'SKU-CYNIAKUL', 'nissisn ramen spicy seafood 59g', 'Nissin | Instant noodle soups', 131.00, 163.75, 150.65, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(168, 0, 1, 1, 1, '4800010075878', 'SKU-MACNF9CU', 'presto (y) 30g', 'Jack \'N Jill | Chocolate sandwich cookies', 146.00, 182.50, 167.90, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(169, 0, 1, 1, 1, '4800016079559', 'SKU-YVAKFXI8', 'Hello Chocolate Wafer sandwich', '', 206.00, 257.50, 236.90, 12, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(170, 0, 1, 1, 1, '4800523441955', 'SKU-VXX6GTRM', 'loaded (y) chocolate 32g', 'Stateline | Chocolate snack bar dairy filling', 388.00, 485.00, 446.20, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(171, 0, 1, 1, 1, '8120001415893', 'SKU-XQPHGZTM', 'Luncheon Meat', 'FOUNDERS | Canned meats', 334.00, 417.50, 384.10, 10, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(172, 0, 1, 1, 1, '4800040241229', 'SKU-T5DKF0J3', 'Breakfast Cocoa', 'Ricoa | Chocolates, Cocoa powders', 498.00, 622.50, 572.70, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(173, 0, 1, 1, 1, '8996001410226', 'SKU-HDECNZLD', 'KOPIKO PREMIUM 3IN1', 'KOPIKO | Coffees, Instant powder for coffee with milk', 319.00, 398.75, 366.85, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(174, 0, 1, 1, 1, '4806501599687', 'SKU-Q3BZLIO4', 'Anchor protein', '| Powdered millet milks', 113.00, 141.25, 129.95, 7, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(175, 0, 1, 1, 1, '4807770123009', 'SKU-TWJOCHFF', 'Wheat Bread', 'Monde', 68.00, 85.00, 78.20, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(176, 0, 1, 1, 1, '4800249008951', 'SKU-EIL13VWH', 'San Marino Paella', '| Canned tunas, Rice dishes', 409.00, 511.25, 470.35, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(177, 0, 1, 1, 1, '4806014098721', 'SKU-F0EEOI2U', 'Diced Tomatoes', 'Dona Elena | Canned tomatoes', 363.00, 453.75, 417.45, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(178, 0, 1, 1, 1, '4800361423588', 'SKU-1BKMHTVF', 'NescafÃ© Creamy White 3-in-1 Coffee Mix (Sugar Free)', 'Nescafe | 3-in-1 Coffee, Diabetic-Friendly Options, Keto-Friendly (in moderation), Sugar-Free Coffee', 104.00, 130.00, 119.60, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(179, 0, 1, 1, 1, '4800194104937', 'SKU-LNVYEILC', 'Tater Thina Potato Snack (Cucumber Flavor)', 'Oishi | Potato crisps', 223.00, 278.75, 256.45, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(180, 0, 1, 1, 1, '4800148532113', 'SKU-31REGQIY', 'Pinoy Loaf (John Pullman)', 'Marby', 35.00, 43.75, 40.25, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(181, 0, 1, 1, 1, '4806505854874', 'SKU-EESLX1JY', 'Popsnaks Crunchy Popnik', 'Centennial Food Corporation, Popsnaks | Crackers (Appetizers), Puffed salty snacks made from maize', 142.00, 177.50, 163.30, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(182, 0, 1, 1, 1, '0748485900032', 'SKU-UDONVIN1', 'FRESCA TUNA CALDERETA 175G', '', 138.00, 172.50, 158.70, 14, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(183, 0, 1, 1, 1, '4809013041354', 'SKU-ORRI7GKV', 'alibaba (y) 30g', '', 296.00, 370.00, 340.40, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(184, 0, 1, 1, 1, '0748485915036', 'SKU-IQ0PQZSX', 'birch tree twin (w) milk 64g', 'Birch Tree', 260.00, 325.00, 299.00, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(185, 0, 1, 1, 1, '0748485102573', 'SKU-CZFUU2ZS', 'chunky chili corned tuna', 'century', 389.00, 486.25, 447.35, 19, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(186, 0, 1, 1, 1, '0892241000693', 'SKU-FEKKWFEH', 'Uncle Lee\'s Organic White Tea', 'Uncle Lee\'s Tea | Teas', 436.00, 545.00, 501.40, 18, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(187, 0, 1, 1, 1, '0750515087033', 'SKU-0TCCKEBT', 'Deluxe Cracker Sandwich Tiramisu', 'M.Y. San, M.Y. San Grahams | Sandwich biscuits', 346.00, 432.50, 397.90, 15, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(188, 0, 1, 1, 1, '8720608618200', 'SKU-S5XNXVKJ', 'GREEN TEA Lively Fresh', 'Lipton | Green teas, Tea bags', 191.00, 238.75, 219.65, 7, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(189, 0, 1, 1, 1, '7622202327605', 'SKU-TNE7JKMS', 'cheese', 'Eden | Cheeses', 28.00, 35.00, 32.20, 9, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(190, 0, 1, 1, 1, '9556174813683', 'SKU-SSRIVQIJ', 'Whole grain rolled oats', 'Quaker | Food', 454.00, 567.50, 522.10, 13, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(191, 0, 1, 1, 1, '4800148533974', 'SKU-ZNDM1PUY', 'Hopia Hapon', 'Marby | Pastries', 427.00, 533.75, 491.05, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(192, 0, 1, 1, 1, '39011169', 'SKU-AZSTNL7V', 'Hazelnut Spread with Dipper Stick and Cocoa', 'GoNutt | Sweet snacks', 36.00, 45.00, 41.40, 10, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(193, 0, 1, 1, 1, '8908013252077', 'SKU-G9YCPQCY', 'Crunchy Granola Happy Berries', 'Fit & Flex | Crunchy mueslis with fruits', 40.00, 50.00, 46.00, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(194, 0, 1, 1, 1, '18065024', 'SKU-JRRYOUXA', 'Neubake Wheaten Bread', 'Gardenia | Wheat breads, Loaf bread', 329.00, 411.25, 378.35, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(195, 0, 1, 1, 1, '4808647020094', 'SKU-J2PZHNX3', 'Eden Cheese', 'Eden | Processed cheeses, Cheese spreads', 159.00, 198.75, 182.85, 20, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(196, 0, 1, 1, 1, '4800016022265', 'SKU-ZEOAQREU', 'great taste twin (w) 50g', 'Great Taste | Cofee', 300.00, 375.00, 345.00, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(197, 0, 1, 1, 1, '4800888602268', 'SKU-GYS8BELQ', 'Real Mayonnaise', 'Lady\'s Choice | Mayonnaises, Groceries', 77.00, 96.25, 88.55, 17, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(198, 0, 1, 1, 1, '8850389113004', 'SKU-6GYRP8BN', 'Mogu Mogu', 'Mogu mogu, Sappe | Sweetened beverages', 55.00, 68.75, 63.25, 11, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(199, 0, 1, 1, 1, '4800024577122', 'SKU-QEUHJEJ9', 'dm ketchup original 320g', 'Del Monte | Ketchup', 105.00, 131.25, 120.75, 6, NULL, 1, 1, '2026-06-11 15:37:27', '2026-06-11 07:37:27', 'active', 0, NULL),
	(200, 0, 1, 1, 1, '4800049720114', 'SKU-MVIGTZHF', 'Natures Spring Purified Drinking Water', '| Waters', 449.00, 561.25, 516.35, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(201, 0, 1, 1, 1, '4800016561184', 'SKU-NDESOUKV', 'nc creamy seafood 45g', 'Nissin | Instant noodles', 450.00, 562.50, 517.50, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(202, 0, 1, 1, 1, '4800361415347', 'SKU-BRWDPPYW', 'nescafe twin (r) 40g', 'Nescafe | Coffees, Coffee drinks, 3in1 instant coffee, Powdered coffee mix', 419.00, 523.75, 481.85, 16, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(203, 0, 1, 1, 1, '4800016022258', 'SKU-TX3L1FNP', 'Great Taste Choco Coffee Mix Twin Pack', 'Universal Robina | Coffees, Aromatised instant coffees, Cappuccino', 293.00, 366.25, 336.95, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(204, 0, 1, 1, 1, '4807770101694', 'SKU-DRJWHEAS', 'Butter Coconut', 'Nissin | Biscuits, Butter and Coconut Biscuit, Coconut biscuits', 381.00, 476.25, 438.15, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(205, 0, 1, 1, 1, '8996001600269', 'SKU-KUYRMDYF', 'Mountain Mineral Water', 'Le Minerale | Natural mineral waters, Drinking water', 42.00, 52.50, 48.30, 15, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(206, 0, 1, 1, 1, '4800092113260', 'SKU-P1JPKVAL', 'rebisco (p) strawberry 32g', 'Rebisco | Biscuits', 346.00, 432.50, 397.90, 10, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(207, 0, 1, 1, 1, '8996001603444', 'SKU-KO5GAGZK', 'energen champion twin 64g', 'Energen | Beverages, Chocolate beverage, Powdered Chocolate Malt Drink', 475.00, 593.75, 546.25, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(208, 0, 1, 1, 1, '4800163443043', 'SKU-NDWS4RUS', 'Sardines in Tomato Sauce', 'Ligo | Sardines in tomato sauce', 247.00, 308.75, 284.05, 6, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(209, 0, 1, 1, 1, '4800194153683', 'SKU-XGOR5D2W', 'bread pan (y) 24g', 'Oishi | Biscuits, Toasted bread', 341.00, 426.25, 392.15, 13, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(210, 0, 1, 1, 1, '4801981164851', 'SKU-JNJC7CZ8', 'Minute Maid Apple Juice', 'Minute Maid | Non-alcoholic beverages, Apple juices', 144.00, 180.00, 165.60, 8, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(211, 0, 1, 1, 1, '4800016082917', 'SKU-3Y2WVNSX', 'Onion Chives Flavored Crackers', 'Magic Flakes | Palmiers', 215.00, 268.75, 247.25, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(212, 0, 1, 1, 1, '8857122621017', 'SKU-HK9MQXBV', 'Wrigley\'s Doublemint Gum', 'Doublemint | Chewing gum', 488.00, 610.00, 561.20, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(213, 0, 1, 1, 1, '4800016963827', 'SKU-TCH8I724', 'Cloud 9 Overload', 'Jack \'n Jill | Caramel chocolate bars', 467.00, 583.75, 537.05, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(214, 0, 1, 1, 1, '4800274330355', 'SKU-ND765V00', 'Instant Oatmeal Chocolate Flavor', 'Quaker | Oat', 189.00, 236.25, 217.35, 15, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(215, 0, 1, 1, 1, '4800249006612', 'SKU-NYTDWNU3', 'san marino (r) 150g', 'San Marino | Canned fishes', 458.00, 572.50, 526.70, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(216, 0, 1, 1, 1, '4800274040025', 'SKU-CFETK0XA', 'Quaker Oats', 'Quaker', 423.00, 528.75, 486.45, 14, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(217, 0, 1, 1, 1, '4800216121911', 'SKU-UJ3KFQVK', 'Cheezy corn crunch spicy', 'Leslie\'s | Corn chips', 408.00, 510.00, 469.20, 8, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(218, 0, 1, 1, 1, '4803925260117', 'SKU-D0DFACGJ', 'Tropicana Twister Juicy Pulp', 'Pepsi-Cola | Orange nectars', 13.00, 16.25, 14.95, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(219, 0, 1, 1, 1, '8410128100162', 'SKU-76UAWCNK', 'Pasteurized Yogurt Original , Strawberry flavor', 'Pascual | Sweetened yogurts', 34.00, 42.50, 39.10, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(220, 0, 1, 1, 1, '4803925241161', 'SKU-UH841JCP', 'GATORADE', '', 166.00, 207.50, 190.90, 8, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(221, 0, 1, 1, 1, '4803925260124', 'SKU-JRIRU49R', 'Tropicana Twister', 'Pepsi | Orange juices, Orange nectars', 172.00, 215.00, 197.80, 6, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(222, 0, 1, 1, 1, '4800350108878', 'SKU-KUJL7NCH', 'Pocari Sweat Ion Supply Drink', 'pocari sweat | Electrolyte Drinks, Functional Beverages, Sports Drinks', 216.00, 270.00, 248.40, 9, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(223, 0, 1, 1, 1, '6932475320069', 'SKU-PHWZHSXP', 'slimming coffee', 'lishou | Coffees, Slimming coffee', 10.00, 12.50, 11.50, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(224, 0, 1, 1, 1, '4805358324664', 'SKU-7PGIHM9O', 'Cheese Squeeze Cheddar', 'Magnolia | Cheese spreads', 233.00, 291.25, 267.95, 8, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(225, 0, 1, 1, 1, '4801958390108', 'SKU-2NQYQBH0', 'crispiy fry (r) original 62g', 'Ajinomoto', 107.00, 133.75, 123.05, 7, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(226, 0, 1, 1, 1, '8410128100421', 'SKU-BMZ4HPAZ', 'Greek Style Plain Yogurt', 'Pascual', 48.00, 60.00, 55.20, 8, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(227, 0, 1, 1, 1, '4800040211222', 'SKU-BMOKLFSZ', 'Flat Tops', 'RICOA | Confectioneries, Chocolates', 123.00, 153.75, 141.45, 7, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(228, 0, 1, 1, 1, '4800016560927', 'SKU-CMTFOQYE', 'nc spicy hot beef 45g', 'Nissin Cup Noodles | Instant noodles, Cup Noodles, Spicy Noodles', 221.00, 276.25, 254.15, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(229, 0, 1, 1, 1, '0074305401169', 'SKU-F735TJVQ', 'Apple Cider Vinegar', 'Bragg | Vinegars', 160.00, 200.00, 184.00, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(230, 0, 1, 1, 1, '8996001350584', 'SKU-80VIEIT4', 'cal cheese (y) cheese 48g', 'Mayora | Wafers', 62.00, 77.50, 71.30, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(231, 0, 1, 1, 1, '4800014148080', 'SKU-EKGBQ5BD', 'Natural Drinking Water', 'Asia Brewery, Summit | Natural mineral waters', 401.00, 501.25, 461.15, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(232, 0, 1, 1, 1, '4800194116169', 'SKU-I95EHWRJ', 'Oishi Spicy Seafood Curls', '| Shrimps', 347.00, 433.75, 399.05, 16, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(233, 0, 1, 1, 1, '4800016551611', 'SKU-R5VFYYUK', 'Yakisoba instant pancit savory beef flavor with real vegetables', 'Nissin', 350.00, 437.50, 402.50, 9, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(234, 0, 1, 1, 1, '7622210892126', 'SKU-PS2B6UYX', 'eden original 45g', 'Creamy & Cheesy taste', 495.00, 618.75, 569.25, 7, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(235, 0, 1, 1, 1, '93698078', 'SKU-VPGMOIF7', 'Tic Tac Orange', 'Ferrero | Candies, fr:Bonbons dragÃ©ifiÃ©s', 53.00, 66.25, 60.95, 7, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(236, 0, 1, 1, 1, '4800194179881', 'SKU-FK31DMJB', 'Martyâ€™s Crackling Plain Salted', 'Oishi | Crisps, Chicharon, Cracklings', 131.00, 163.75, 150.65, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(237, 0, 1, 1, 1, '9556007000556', 'SKU-YLAKN0IV', 'Non-Dairy Soya With Calcium', 'Soyfresh | Soy-based drinks', 62.00, 77.50, 71.30, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(238, 0, 1, 1, 1, '4800092113741', 'SKU-ZIBNJNON', 'Rebisco maxi mix', '', 313.00, 391.25, 359.95, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(239, 0, 1, 1, 1, '4800249006650', 'SKU-MTLMGI6V', 'San Marino', 'San Marino | Canned tunas', 262.00, 327.50, 301.30, 16, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(240, 0, 1, 1, 1, '4800361331265', 'SKU-YENTJEDN', 'Nestle Chuckie', 'NestlÃ© | Chocolate milks', 342.00, 427.50, 393.30, 8, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(241, 0, 1, 1, 1, '4800110095776', 'SKU-QO5WAKUA', 'Sterelized Milk', 'Selecta', 456.00, 570.00, 524.40, 10, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(242, 0, 1, 1, 1, '9331275010700', 'SKU-8OIGB6SX', 'rondoletti', '', 251.00, 313.75, 288.65, 13, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(243, 0, 1, 1, 1, '4800361015103', 'SKU-ODYIJAIY', 'Nestle Fresh Milk', 'NestlÃ© | Milks', 282.00, 352.50, 324.30, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(244, 0, 1, 1, 1, '4800092113314', 'SKU-0DTOXLEZ', 'Hansel Milk Sandwhich', 'Rebisco | Biscuits or cookies variety packs', 127.00, 158.75, 146.05, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(245, 0, 1, 1, 1, '6902131112925', 'SKU-QQ8TIUAT', 'MaLing Chicken Luncheon Meat', 'MaLing | Canned meats', 413.00, 516.25, 474.95, 14, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(246, 0, 1, 1, 1, '48042857', 'SKU-8QPWITEU', 'yakult light', 'Yakult | Fermented milk drinks', 388.00, 485.00, 446.20, 13, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(247, 0, 1, 1, 1, '4800016552120', 'SKU-AGETNDLZ', 'nissin cup chicken 40g', '', 390.00, 487.50, 448.50, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(248, 0, 1, 1, 1, '8851717907845', 'SKU-SUCR4T0R', 'Loadingâ€¦', 'Dutchmill | Yogurt drinks', 308.00, 385.00, 354.20, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(249, 0, 1, 1, 1, '4800361331289', 'SKU-BBJTGXQT', 'Bear Brand sterilized', 'NestlÃ© | UHT Milks, Full Cream Milk, Liquid Milk, Ready-to-Drink', 311.00, 388.75, 357.65, 9, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(250, 0, 1, 1, 1, '4002309006567', 'SKU-ERZGX27R', 'Ceres Choco Hazelnut Choco Spread', 'Goya | Chocolate spreads', 368.00, 460.00, 423.20, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(251, 0, 1, 1, 1, '4800110097602', 'SKU-RDAA2A5G', 'Premium Apple Juice', 'Sunkist | Apple juices', 197.00, 246.25, 226.55, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(252, 0, 1, 1, 1, '0748485100036', 'SKU-RIWDVLIG', 'Century Tuna Premium Red', 'Century | Canned tunas, Tunas in brine', 425.00, 531.25, 488.75, 17, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(253, 0, 1, 1, 1, '4801981127191', 'SKU-HHMBUJND', 'sprite', 'Sprite | Sodas', 155.00, 193.75, 178.25, 17, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(254, 0, 1, 1, 1, '4800092660801', 'SKU-OLKVLXVP', 'Choco Mucho Peanut Butter', 'Multirich Foods | Chocolate biscuity bars', 496.00, 620.00, 570.40, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(255, 0, 1, 1, 1, '4800361392549', 'SKU-YTPLPQAL', 'Nestea Cranberry', 'Nestle Phils. | Beverages, Powdered Juice Drink', 22.00, 27.50, 25.30, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(256, 0, 1, 1, 1, '8995227502234', 'SKU-LCEXEOSB', 'Lola Remedios', '| Dietary supplements', 389.00, 486.25, 447.35, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(257, 0, 1, 1, 1, '4800010075243', 'SKU-YPBPYQKO', 'cream o (bwn) choco 30g', 'Jack n\' Jill | Biscuit with a chocolate bar covering', 70.00, 87.50, 80.50, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(258, 0, 1, 1, 1, '4800361381581', 'SKU-8UVY2DRK', 'Non-Fat Milk', 'NestlÃ© | Dairy drinks, Milks', 136.00, 170.00, 156.40, 13, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(259, 0, 1, 1, 1, '4806526075135', 'SKU-1GBCKLWD', 'Oat Choco Original Flavor', 'Coco | Cocoa and its products, Chocolate cereal bars, Oat', 22.00, 27.50, 25.30, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(260, 0, 1, 1, 1, '4807770122644', 'SKU-LCI6IV5D', 'nissin wafer (r) double chocolate 55g', 'Nissin', 157.00, 196.25, 180.55, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(261, 0, 1, 1, 1, '9555192507833', 'SKU-QEUQGI4F', '9555192507833', '', 347.00, 433.75, 399.05, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(262, 0, 1, 1, 1, '4800194152600', 'SKU-B7Y9Q98E', 'Oishi Crab Nuts Roasted Cashews', 'Oishi | Salty snacks, Roasted cashew nuts', 442.00, 552.50, 508.30, 17, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(263, 0, 1, 1, 1, '4800365881315', 'SKU-WANHF4IF', 'Super Delights Brownie Bites', 'Super delight | Pastries, Chocolate cakes, Baked Goods, Chocolate Snacks, Pasalubong', 372.00, 465.00, 427.80, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(264, 0, 1, 1, 1, '4800024556899', 'SKU-DK7E4KSH', 'del monte tomato paste 150g', 'Del Monte | Tomato pastes', 404.00, 505.00, 464.60, 9, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(265, 0, 1, 1, 1, '8129001415872', 'SKU-YEQEKFD8', 'Creamy Peanut Butter', 'Founders | Peanut butters', 401.00, 501.25, 461.15, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(266, 0, 1, 1, 1, '4807770122965', 'SKU-NZGBGBRH', 'Fluffy Soft White Bread (Corrected)', 'Monde', 381.00, 476.25, 438.15, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(267, 0, 1, 1, 1, '0748485700038', 'SKU-EGIKSUCF', '555 tuna spicy caldereta 155g', '555 | Tunas in oil, tuna flakes in oil', 165.00, 206.25, 189.75, 6, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(268, 0, 1, 1, 1, '4800016633799', 'SKU-VEZ8QKW0', 'ROADHOUSE BARBEQUE Flavored Potato Crisps', 'Piattos | Potato crisps', 445.00, 556.25, 511.75, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(269, 0, 1, 1, 1, '8850343011322', 'SKU-BRPIWQSA', 'Mayonnaise', 'Heinz [Seriously] Good', 345.00, 431.25, 396.75, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(270, 0, 1, 1, 1, '8997035600010', 'SKU-FDONLK6W', 'pocari sweat 350ml', '| Dietary drink for sport', 40.00, 50.00, 46.00, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(271, 0, 1, 1, 1, '4800361380737', 'SKU-DNDSGA7N', 'Coffee mate', 'NestlÃ© | Coffees', 80.00, 100.00, 92.00, 6, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(272, 0, 1, 1, 1, '4801981120000', 'SKU-JQ6LKW4Y', 'Coca-Cola', 'Coca-Cola, The Coca-Cola Company | Colas, Sweetened beverages', 44.00, 55.00, 50.60, 17, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(273, 0, 1, 1, 1, '4807770100529', 'SKU-G6RGFPGD', 'NISSIN BUTTER COCONUT', 'MONDE | Biscuits, Butter and Coconut Biscuit, Coconut biscuits', 115.00, 143.75, 132.25, 18, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(274, 0, 1, 1, 1, '8801056000332', 'SKU-1BHOMGDM', 'Mandarin orange', 'Lotte', 110.00, 137.50, 126.50, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(275, 0, 1, 1, 1, '4800523220284', 'SKU-6WNQUBPZ', 'Moby Chocolate', 'Moby | Crisps, Chocolate biscuits, Puffed corn cakes', 134.00, 167.50, 154.10, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(276, 0, 1, 1, 1, '4800092331732', 'SKU-ZCCCA6ET', 'dingdong (g) chips & curls mix 95g', 'Oing Dong | Crisps, Peanuts, Mixed dried fruits', 413.00, 516.25, 474.95, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(277, 0, 1, 1, 1, '0037600240345', 'SKU-DV865WSI', 'SPAM', 'Hormel | Canned meats, Luncheon meat', 174.00, 217.50, 200.10, 10, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(278, 0, 1, 1, 1, '4800047820014', 'SKU-Y5ZTTCQ7', 'Isopropyl alcohol', 'GreenCross', 200.00, 250.00, 230.00, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(279, 0, 1, 1, 1, '4800092115288', 'SKU-HOXBUOSG', 'Rebisco Fiesta Pastillas', '', 468.00, 585.00, 538.20, 10, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(280, 0, 1, 1, 1, '4800361403764', 'SKU-WAUFJ7WG', 'nescafe original 20g', 'NestlÃ© | Beverages, Coffees, Instant coffees', 303.00, 378.75, 348.45, 15, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(281, 0, 1, 1, 1, '8885013130058', 'SKU-AWPYDSI2', 'Chocolate Crispy', 'Aice | Ice cream bars, vanilla-ice-cream-bars', 110.00, 137.50, 126.50, 7, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(282, 0, 1, 1, 1, '4800054210327', 'SKU-XIJG6VNM', 'NutriBar Apple Cinnamon', 'Growers | Cereal bars', 404.00, 505.00, 464.60, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(283, 0, 1, 1, 1, '7622300742164', 'SKU-T1XBTYMO', 'Tiger Chocolate', 'Mondelez, Tiger | Chocolate biscuits', 353.00, 441.25, 405.95, 14, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(284, 0, 1, 1, 1, '4800092110528', 'SKU-SI94UJZY', 'Hansel Choco', 'Hansel | Chocolate sandwich cookies', 48.00, 60.00, 55.20, 16, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(285, 0, 1, 1, 1, '4800249008517', 'SKU-JBNXJX0Q', 'san marino tuna (r) 85g', 'San Marino | Tunas in oil, tuna flakes in oil', 25.00, 31.25, 28.75, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(286, 0, 1, 1, 1, '4808647020100', 'SKU-M1EMHM9I', 'Eden Original', 'Eden, Mondelez | Cheeses', 471.00, 588.75, 541.65, 19, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(287, 0, 1, 1, 1, '4800194179898', 'SKU-QPQSYBA2', 'martys (r) spicy vinegar 90g', 'Oishi | Salty snacks, Crackers & Puffs, Vegetarian Chicharon', 422.00, 527.50, 485.30, 15, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(288, 0, 1, 1, 1, '4806504712083', 'SKU-NLQPUAJ3', 'Tuna Flakes Spanish Style', '', 170.00, 212.50, 195.50, 14, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(289, 0, 1, 1, 1, '4804889008012', 'SKU-SSBAT6S8', 'Hopia Ube', 'eng bee tin | Hopia-ube, Purple Yam Cake', 456.00, 570.00, 524.40, 17, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(290, 0, 1, 1, 1, '4800194153232', 'SKU-PNI4AP9C', 'Bread Pan Onion & Garlic', 'Oishi | Biscuits, Crackers', 220.00, 275.00, 253.00, 17, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(291, 0, 1, 1, 1, '4808591800049', 'SKU-AKOOSST6', 'ovaltine', 'Ovaltine', 370.00, 462.50, 425.50, 5, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(292, 0, 1, 1, 1, '0748485900056', 'SKU-9GHJPR4T', 'Afritada', '', 448.00, 560.00, 515.20, 12, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(293, 0, 1, 1, 1, '4800552888127', 'SKU-RFUSMTV2', 'Chips Delight Assorted Cookies', '', 78.00, 97.50, 89.70, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(294, 0, 1, 1, 1, '4806018405655', 'SKU-LGWHP6TG', 'Lava Cake Chocolate', 'Lemon Square | Chocolate cakes', 388.00, 485.00, 446.20, 10, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(295, 0, 1, 1, 1, '4800092113352', 'SKU-VXI4AA5G', 'Frootees Strawberry Cookie Sandwich', 'Frootees | Biscuits, Frootes', 85.00, 106.25, 97.75, 16, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(296, 0, 1, 1, 1, '4800289821787', 'SKU-PRPR2CCS', 'Green Stevia', 'GX International | Sugar substitutes', 128.00, 160.00, 147.20, 11, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(297, 0, 1, 1, 1, '4800111000748', 'SKU-CQR1LHRL', 'Marble Cake Slice', 'Goldilocks | Cakes, Shortcake', 256.00, 320.00, 294.40, 20, NULL, 1, 1, '2026-06-11 15:37:29', '2026-06-11 07:37:29', 'active', 0, NULL),
	(298, 0, 1, 1, 1, '4800024037480', 'SKU-YZUPI66C', 'Fiesta Fruit Cocktail in Extra Light Syrup', 'Del Monte | Canned fruit cocktail in light syrup', 279.00, 348.75, 320.85, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(299, 0, 1, 1, 1, '4808680021997', 'SKU-QPRCS4XH', 'Ceasar Dressing', 'Lady\'s Choice, Unilever Phils. | Salad dressings', 49.00, 61.25, 56.35, 20, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(300, 0, 1, 1, 1, '4806506520808', 'SKU-R0UB2UNE', 'Lightwater', 'Suncoast | Beverages, Enhanced-water', 321.00, 401.25, 369.15, 16, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(301, 0, 1, 1, 1, '4800593120316', 'SKU-DS9IYKP1', 'Capri diced tomatoes', 'Capri | Canned tomatoes', 345.00, 431.25, 396.75, 14, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(302, 0, 1, 1, 1, '4802222042235', 'SKU-FQQRZNUJ', 'Frozen Strawberry', 'SM Bonus | Frozen strawberries', 111.00, 138.75, 127.65, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(303, 0, 1, 1, 1, '4802222039969', 'SKU-GFIQ4V7I', 'Creamy Peanut Butter', 'SM Bonus | Peanut butters, Sweetened peanut butters', 210.00, 262.50, 241.50, 13, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(304, 0, 1, 1, 1, '4800361417198', 'SKU-2FCQ67WH', 'Lemon Cucumber Green Tea', 'Nestea | Green teas, Powdered Ice Teas, Sugar-free beverages', 200.00, 250.00, 230.00, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(305, 0, 1, 1, 1, '4800313030147', 'SKU-SIBILEHJ', 'Tocino', 'Pampanga\'s Best | Pork belly', 326.00, 407.50, 374.90, 16, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(306, 0, 1, 1, 1, '8998666001467', 'SKU-ZI5SCKNM', 'Double Cups Original', 'Kopiko | Beverages, 3-in-1 Coffee mix', 303.00, 378.75, 348.45, 20, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(307, 0, 1, 1, 1, '4800016964466', 'SKU-KGQKKDUW', 'Cloud 9 Almond Fudge', 'Universal Robina Corporation | Chocolate almond Fudge', 350.00, 437.50, 402.50, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(308, 0, 1, 1, 1, '4800024582461', 'SKU-RBASYPKL', 'Del Monte Sweet Chili Sauce', 'Del Monte | Hot sauces', 22.00, 27.50, 25.30, 7, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(309, 0, 1, 1, 1, '4801688103818', 'SKU-OSJQXG6D', 'snacku 25g', '', 306.00, 382.50, 351.90, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(310, 0, 1, 1, 1, '4805358601086', 'SKU-QDMWNRNT', 'Magnolia Fresh Milk', 'Magnolia | Milks', 403.00, 503.75, 463.45, 20, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(311, 0, 1, 1, 1, '4806018403712', 'SKU-DIHINPQD', 'Lemon Square Whatta Tops Chocolahat Flavor (Cupcakes with Toppings)', 'Lemon Square | Fairy cakes, Baon Favorites, Chocolate Snacks, Snack Cakes', 264.00, 330.00, 303.60, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(312, 0, 1, 1, 1, '4808887030013', 'SKU-GRRPFAWF', 'PureFoods Luncheon Meat', 'PureFoods Hormel', 182.00, 227.50, 209.30, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(313, 0, 1, 1, 1, '4800010012033', 'SKU-X7LWXHZ0', 'Granules Strong', '', 162.00, 202.50, 186.30, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(314, 0, 1, 1, 1, '4808680653273', 'SKU-LC9NZO6B', 'Super Chunky Peanut Spread', 'Lady\'s Choice', 41.00, 51.25, 47.15, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(315, 0, 1, 1, 1, '4800016112560', 'SKU-IKKXE0GQ', 'Cream-O cake sandwich', 'JacknJill', 362.00, 452.50, 416.30, 9, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(316, 0, 1, 1, 1, '4800194136648', 'SKU-UCJTWS0C', 'Fishda Fish Kropeck', 'Oishi | Crisps', 488.00, 610.00, 561.20, 11, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(317, 0, 1, 1, 1, '8801123602193', 'SKU-RIJWZMCT', 'Luncheon Meat', 'lotte', 126.00, 157.50, 144.90, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(318, 0, 1, 1, 1, '4807770121210', 'SKU-3ICFB8EW', 'Nissin Stick Wafer 22g', '', 51.00, 63.75, 58.65, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(319, 0, 1, 1, 1, '4800016552113', 'SKU-9CVAMOND', 'nc seafood 40g', 'Nissin | Instant noodle soups', 332.00, 415.00, 381.80, 7, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(320, 0, 1, 1, 1, '4801981116966', 'SKU-ZYULJDXA', 'Coca cola ligth', 'Coca-Cola', 173.00, 216.25, 198.95, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(321, 0, 1, 1, 1, '4806502720417', 'SKU-1CXRQO8P', 'Enriched White Bread', 'Gardenia Classic', 38.00, 47.50, 43.70, 13, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(322, 0, 1, 1, 1, '4800216122215', 'SKU-7GEQHTXK', 'Chili and Cheese', 'Leslie\'s | Corn chips', 442.00, 552.50, 508.30, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(323, 0, 1, 1, 1, '9314057013403', 'SKU-6X9XRV3Z', 'Pastilles Original Menthol', 'Valda | Menthol candy', 389.00, 486.25, 447.35, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(324, 0, 1, 1, 1, '4801981119981', 'SKU-Z8NLY8DA', 'Wilkins Pure Purified Drinking Water', 'Coca-Cola | Waters', 396.00, 495.00, 455.40, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(325, 0, 1, 1, 1, '4800361393645', 'SKU-SY4F4ZQV', 'Nescafe Classic', '| Coffees', 389.00, 486.25, 447.35, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(326, 0, 1, 1, 1, '0750515017818', 'SKU-XZB6Z0NA', 'Fita Spreads', 'M.Y. San', 167.00, 208.75, 192.05, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(327, 0, 1, 1, 1, '0750515018303', 'SKU-PMSAPMDC', 'Sky Flakes Crackers', 'M.y.san skyflakes', 383.00, 478.75, 440.45, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(328, 0, 1, 1, 1, '4800016680267', 'SKU-D88TI4CC', 'Calbee Honey Butter', 'Calbee', 376.00, 470.00, 432.40, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(329, 0, 1, 1, 1, '1200108000042', 'SKU-SQIYNYVJ', 'Lays Classic 180g', 'Lay\'s | Potato crisps', 242.00, 302.50, 278.30, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(330, 0, 1, 1, 1, '0841699100288', 'SKU-MOYKTUET', 'Mang Tomas Siga', '', 296.00, 370.00, 340.40, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(331, 0, 1, 1, 1, '4807770273612', 'SKU-QGC0XPKE', 'Lucky Me! Go Cup Mini Instant Noodle Soup Spicy Bulalo Flavor', 'Lucky Me! | Instant noodles', 167.00, 208.75, 192.05, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(332, 0, 1, 1, 1, '7622300601782', 'SKU-DC8D9UFA', 'tang mango 19g', 'Tang', 254.00, 317.50, 292.10, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(333, 0, 1, 1, 1, '4800016078590', 'SKU-3QQFUMNF', 'cream o (w) 30g', '', 97.00, 121.25, 111.55, 14, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(334, 0, 1, 1, 1, '4808680210902', 'SKU-HENYFHEH', 'Knorr Nido Oriental Style', 'Knorr', 77.00, 96.25, 88.55, 19, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(335, 0, 1, 1, 1, '8998666001702', 'SKU-OXUVQYTO', 'kopiko twin (b) black 40g', 'Kopiko | Beverages, Coffees', 246.00, 307.50, 282.90, 13, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(336, 0, 1, 1, 1, '8852008000030', 'SKU-FSCJN8F7', 'Lotte Xylitol Line Mint Flavor', 'Lotte', 183.00, 228.75, 210.45, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(337, 0, 1, 1, 1, '4808680022017', 'SKU-JSXUGAG0', '4808680022017', '', 214.00, 267.50, 246.10, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(338, 0, 1, 1, 1, '4800194152594', 'SKU-LV8MCECC', 'Oishi Grab Nuts Roasted Almonds', 'Oishi | Salty snacks, Roasted nuts, Roasted almonds', 391.00, 488.75, 449.65, 20, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(339, 0, 1, 1, 1, '4800110006819', 'SKU-AHYM4JFO', 'Hotcake', '', 245.00, 306.25, 281.75, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(340, 0, 1, 1, 1, '4800098122952', 'SKU-KZS60FHN', 'Yum Yum Strawberry Snack 30g', 'Island Biscuit | Biscuit Stick strawberry', 204.00, 255.00, 234.60, 14, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(341, 0, 1, 1, 1, '4800318000701', 'SKU-JOV4M2CN', 'Scotts orange', '', 319.00, 398.75, 366.85, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(342, 0, 1, 1, 1, '4800016091117', 'SKU-VUANOUEO', 'Swiss Miss Dark Chocolate', '', 50.00, 62.50, 57.50, 19, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(343, 0, 1, 1, 1, '4800888602299', 'SKU-IUHVOEE3', 'Lady\'s Choice Sandwich Spread', 'lady\'s choice', 129.00, 161.25, 148.35, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(344, 0, 1, 1, 1, '4800361413992', 'SKU-B1OLYBNH', 'nescafe twin (bwn) creamy latte 40g', 'NescafÃ© | 3-in-1 Coffee, Dessert Coffee, Flavored Coffee', 384.00, 480.00, 441.60, 11, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(345, 0, 1, 1, 1, '4800575480254', 'SKU-9ZL611CJ', 'alaska crema all purpose 250ml', 'Alaska', 212.00, 265.00, 243.80, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(346, 0, 1, 1, 1, '0748485800738', 'SKU-6FLHK53S', 'argentina beef loaf 150g', 'Argentina | Beef, Meatloafs', 420.00, 525.00, 483.00, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(347, 0, 1, 1, 1, '4800361339186', 'SKU-IZDX1LMN', 'nescafe (r) 20g', '', 12.00, 15.00, 13.80, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(348, 0, 1, 1, 1, '4806524222456', 'SKU-IGOGCGIH', 'Shapea (crispy green pea snack)', 'OK | Snacks', 367.00, 458.75, 422.05, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(349, 0, 1, 1, 1, '4800009091094', 'SKU-HZTTOQPV', 'Tropical Fruit Cocktail', 'Dole | Fruits in syrup, Fruit Cocktail', 300.00, 375.00, 345.00, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(350, 0, 1, 1, 1, '4800024555496', 'SKU-WOFLYOMH', 'dm ts original 200g', 'Del Monte | Tomato sauces', 348.00, 435.00, 400.20, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(351, 0, 1, 1, 1, '4801668602218', 'SKU-WUC6CA7L', 'Datu Puti Toyo 100ml', '', 197.00, 246.25, 226.55, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(352, 0, 1, 1, 1, '8996001311059', 'SKU-HRC8E8PW', 'malkist', 'mayora', 124.00, 155.00, 142.60, 18, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(353, 0, 1, 1, 1, '4800365881414', 'SKU-XUVM2AYN', 'Super Delights Butterscotch Bites', 'Prifood, Super Delights | Fudge, Butterscotch fudge', 433.00, 541.25, 497.95, 19, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(354, 0, 1, 1, 1, '4800249044287', 'SKU-EUTSLBQX', 'Danes Herb & Garlic Mayo', 'CDO Foodsphere Inc | Herbs, Garlic, Mayonnaises', 429.00, 536.25, 493.35, 9, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(355, 0, 1, 1, 1, '4800527123055', 'SKU-BSYR4AHY', 'youngs town sardines (r) 155g', 'youngs town', 151.00, 188.75, 173.65, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(356, 0, 1, 1, 1, '4806510070146', 'SKU-PZULVYQ2', 'Mustard', 'McCormick', 84.00, 105.00, 96.60, 19, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(357, 0, 1, 1, 1, '4805358810860', 'SKU-ZKSYBWGD', 'Magnolia Chicken Spread', 'Magnolia | Spreads', 69.00, 86.25, 79.35, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(358, 0, 1, 1, 1, '0748485102245', 'SKU-EW8GHJ95', 'century tuna (p) 95g', 'Century Tuna | Tunas in oil, tuna flakes in oil', 58.00, 72.50, 66.70, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(359, 0, 1, 1, 1, '4806018298660', 'SKU-WBCJSGMW', 'Busog Meal Jumbo Tuna Omelette', '7-Eleven, Busog Meal | Rices, Microwave meals, Vegetables from Spain', 141.00, 176.25, 162.15, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(360, 0, 1, 1, 1, '4807770272660', 'SKU-QSRHXM4W', 'Mac & Cheese', 'Lucky Me, Lucky Me! | Instant noodles', 108.00, 135.00, 124.20, 7, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(361, 0, 1, 1, 1, '0037600840842', 'SKU-V9JUIFFV', 'SKIPPY', 'Skippy', 197.00, 246.25, 226.55, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(362, 0, 1, 1, 1, '4800066122168', 'SKU-OIDKRSFB', 'Corned beef', 'Holiday', 461.00, 576.25, 530.15, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(363, 0, 1, 1, 1, '0745110669265', 'SKU-BE6JSVJA', 'Salvaro: Tasty and Crunchy', 'Fredo\'s | Flakes, Coconut flakes, Salvaro', 136.00, 170.00, 156.40, 18, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(364, 0, 1, 1, 1, '4807770274305', 'SKU-ACOEIJKG', 'lm pc extra hot chili 75g', 'Lucky Me! | Instant noodle soups', 217.00, 271.25, 249.55, 9, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(365, 0, 1, 1, 1, '4806537026805', 'SKU-Z7JKWTBQ', 'Peanut Milk Chocolate', 'Choc Nut | Candy chocolate bars, Milk chocolate bar, Peanut milk chocolate', 40.00, 50.00, 46.00, 14, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(366, 0, 1, 1, 1, '4800092551444', 'SKU-RZNKME5G', 'fudgee barr (g) macapuno 41g', 'Rebisco | Desserts, Cupcakes & Cake Bars, Snack Cakes', 314.00, 392.50, 361.10, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(367, 0, 1, 1, 1, '0748485700090', 'SKU-UEORWCTM', '555 TUNA Spicy BICOL Express', '555 | Tunas in oil, tuna flakes in oil', 46.00, 57.50, 52.90, 13, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(368, 0, 1, 1, 1, '4800058345674', 'SKU-ZFWCDYD2', 'Meat Loaf', 'Philips | Meatloafs', 230.00, 287.50, 264.50, 18, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(369, 0, 1, 1, 1, '4804888900089', 'SKU-BFEKEP0E', 'Zest-O Big 250', 'Zest-O | Mangoes, Sweetened beverages', 233.00, 291.25, 267.95, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(370, 0, 1, 1, 1, '0745125547022', 'SKU-SBF6R1HO', 'Active Whey Protein Chocolate', 'Athlene | Bodybuilding supplements', 361.00, 451.25, 415.15, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(371, 0, 1, 1, 1, '4800098121139', 'SKU-EYOIWWYP', 'Milky Marie Biscuits', '', 302.00, 377.50, 347.30, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(372, 0, 1, 1, 1, '8850389101391', 'SKU-K7QXCLAX', 'Mogu Mogu Grape Flavored Drink with Nata de Coco', 'Mogu mogu | Sweetened beverages, Grape Juice Drink with Nata de Coco', 278.00, 347.50, 319.70, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(373, 0, 1, 1, 1, '4800092553028', 'SKU-4V0CZBQ0', 'Dowee Donut Strawberry', 'Doowee Donut', 225.00, 281.25, 258.75, 19, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(374, 0, 1, 1, 1, '4800016551581', 'SKU-NG0T2Z0X', 'nissisn ramen beef 55g', 'Ramen Beef', 202.00, 252.50, 232.30, 13, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(375, 0, 1, 1, 1, '4800092552182', 'SKU-TWTCIZQ0', 'Choco Topps Sarap', 'Suncrest Foods Inc.', 475.00, 593.75, 546.25, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(376, 0, 1, 1, 1, '0716221050587', 'SKU-ZT4Z5KMW', 'Dried Mangoes', 'Profood Cebu | Dried mangoes', 67.00, 83.75, 77.05, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(377, 0, 1, 1, 1, '4800016641503', 'SKU-EZNOV36R', 'Chippy Flavored Chili & Cheese Corn Chips', 'Jack \'n Jill | Corn chips', 424.00, 530.00, 487.60, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(378, 0, 1, 1, 1, '8852756346053', 'SKU-FWGYFD7F', 'Corn Flakes', 'Kellogg\'s | Corn flakes', 489.00, 611.25, 562.35, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(379, 0, 1, 1, 1, '4170573077252', 'SKU-UALBGGUS', 'Fita Crackers', 'Fita Crackers | Crackers (Appetizers)', 401.00, 501.25, 461.15, 10, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(380, 0, 1, 1, 1, '4800289822081', 'SKU-SZ0XAMLP', 'TrueTea', 'GX International', 471.00, 588.75, 541.65, 12, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(381, 0, 1, 1, 1, '4800016043246', 'SKU-RPS4RPQJ', 'Nips Peanut Deluxe', '', 36.00, 45.00, 41.40, 16, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(382, 0, 1, 1, 1, '4806018403873', 'SKU-UE6IRV7V', 'Choochoo cheesy milk', 'Lemon Square | Biscuits', 431.00, 538.75, 495.65, 7, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(383, 0, 1, 1, 1, '4804880314181', 'SKU-OH39I70X', 'Pur palm oil cooking', '', 318.00, 397.50, 365.70, 16, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(384, 0, 1, 1, 1, '4800111000649', 'SKU-A4OV6QL2', 'Fluffy Mamon', 'Goldilocks | Cakes', 420.00, 525.00, 483.00, 20, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(385, 0, 1, 1, 1, '0748485401225', 'SKU-ULDOT3TG', 'Angel kremdensada', 'Angel', 467.00, 583.75, 537.05, 20, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(386, 0, 1, 1, 1, '0748485700014', 'SKU-LMATGAQO', 'Tuna Adobo', '555 | Tunas in oil, tuna flakes in oil', 225.00, 281.25, 258.75, 9, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(387, 0, 1, 1, 1, '0748485800035', 'SKU-RSONKGJU', 'Corned beef', 'Argentina | Beef dishes, Canned meats, Corned beef', 30.00, 37.50, 34.50, 9, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(388, 0, 1, 1, 1, '4710587185330', 'SKU-ACD3LGU6', 'Peach', 'Rico | Juice beverage', 433.00, 541.25, 497.95, 6, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(389, 0, 1, 1, 1, '4800092330131', 'SKU-V0U5ROGY', 'dingdong (o) mixed nuts 100g', 'Rebisco | Mix of grains and dried fruit, Mixed dried fruits', 104.00, 130.00, 119.60, 8, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(390, 0, 1, 1, 1, '0014113940184', 'SKU-4KATW97O', 'Classic Roasted Salted Pistachios', 'Wonderful | Pistachios (roasted and salted), Salted pistachios, Unshelled pistachios', 320.00, 400.00, 368.00, 5, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(391, 0, 1, 1, 1, '4801688103719', 'SKU-0CRRBK1W', 'Regent Sweet Corn', 'Regent | Crisps, Puffed corn cakes', 377.00, 471.25, 433.55, 15, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(392, 0, 1, 1, 1, '8996001410547', 'SKU-A9R5LOAI', 'Kopiko Brown Coffee Mix (Creamy Coffee Mix with Brown Sugar', 'Kopiko | Coffees, Instant coffees, 3-in-1 Coffee, Breakfast Staples', 100.00, 125.00, 115.00, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(393, 0, 1, 1, 1, '4800092552229', 'SKU-LDJV8YST', 'Chunkee Sandwich choco', 'Mrs.Goodman | Biscuits', 172.00, 215.00, 197.80, 19, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(394, 0, 1, 1, 1, '8996001350836', 'SKU-S1HPV5UJ', 'wafello (bwn) chocolate 48g', 'Wafello | Wafers, Biscuits & cookies, Chocolate snacks', 71.00, 88.75, 81.65, 17, NULL, 1, 1, '2026-06-11 15:37:31', '2026-06-11 07:37:31', 'active', 0, NULL),
	(395, 0, 1, 1, 1, '0748485400846', 'SKU-SPWDYCSJ', 'Angel Coffee Creamer', 'Century Pacific | Non-dairy creamer', 55.00, 68.75, 63.25, 18, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(396, 0, 1, 1, 1, '9556174811214', 'SKU-MR49TATQ', 'Oat cereal drink', 'Quaker', 415.00, 518.75, 477.25, 7, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(397, 0, 1, 1, 1, '0748485401621', 'SKU-T2UAIYYO', 'birch tree (bwn) 29g', 'Birch Tree | Beverages, Dairies, Instant beverages', 20.00, 25.00, 23.00, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(398, 0, 1, 1, 1, '8851028002314', 'SKU-FQX0COCM', 'Vita Milk double choco', '', 333.00, 416.25, 382.95, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(399, 0, 1, 1, 1, '0748485102252', 'SKU-8GRBHVLX', 'Century Tuna hot and spicy', 'Century Pacific Food | Canned tunas', 474.00, 592.50, 545.10, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(400, 0, 1, 1, 1, '4800365701064', 'SKU-REEEXB7U', 'Super crunchy special chicharon', 'PRIFOOD', 75.00, 93.75, 86.25, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(401, 0, 1, 1, 1, '4800523441832', 'SKU-BVWPZ0SU', 'tomi (y) 25g', 'Stateline, Tomi | Corn chips', 96.00, 120.00, 110.40, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(402, 0, 1, 1, 1, '4808680030760', 'SKU-KSFKZL7S', '4808680030760', '', 77.00, 96.25, 88.55, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(403, 0, 1, 1, 1, '4800016557217', 'SKU-SHAPNEEL', 'nissin cup sotanghon chicken 30g', 'Nissin | Soups, Noodles', 132.00, 165.00, 151.80, 8, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(404, 0, 1, 1, 1, '4800016680540', 'SKU-6BQSLVNF', 'Honey Butter Potato Chips', 'Calbee | c', 336.00, 420.00, 386.40, 8, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(405, 0, 1, 1, 1, '4800010079951', 'SKU-8SYQDJWC', 'Sandwich cookie aux peanut butter', 'Jack n\' Jill', 168.00, 210.00, 193.20, 8, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(406, 0, 1, 1, 1, '4800110093895', 'SKU-0KAOS90W', 'Selecta Fortified Milk', 'Selecta | Milks', 126.00, 157.50, 144.90, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(407, 0, 1, 1, 1, '8901233026497', 'SKU-VLNHLLMI', 'Cadbury Milk Lickables 20g', 'Cadbury', 160.00, 200.00, 184.00, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(408, 0, 1, 1, 1, '8901233028637', 'SKU-DFNCQZSU', 'Cadbury Dairy Milk Shots 18g', 'Cadbury | Sweet snacks', 255.00, 318.75, 293.25, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(409, 0, 1, 1, 1, '4800040314213', 'SKU-2ABPSKDU', 'Fibisco Chocolate Chip Cookies', 'Fibisco', 210.00, 262.50, 241.50, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(410, 0, 1, 1, 1, '4800092115660', 'SKU-2OLIPOA5', 'Rebisco Crackers with honey butter', '', 349.00, 436.25, 401.35, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(411, 0, 1, 1, 1, '4800092331626', 'SKU-OITKOFDA', 'hiho (o) 70g', 'Hi-Ho', 103.00, 128.75, 118.45, 6, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(412, 0, 1, 1, 1, '4806014000144', 'SKU-UZ8BCEB2', 'jolly whole kernels 425g', 'jolly | Canned corn', 203.00, 253.75, 233.45, 6, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(413, 0, 1, 1, 1, '4800194106771', 'SKU-DY6CAW1J', 'Potato Fries Cheese Flavor', 'Oishi | Potato crisps', 446.00, 557.50, 512.90, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(414, 0, 1, 1, 1, '4800194106788', 'SKU-9QFMYNXR', 'Potato Crisps', '', 150.00, 187.50, 172.50, 7, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(415, 0, 1, 1, 1, '9557727929202', 'SKU-KEVUVTXG', 'DAIRY MILK CORPORATION', 'Doreen', 115.00, 143.75, 132.25, 15, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(416, 0, 1, 1, 1, '4803925061110', 'SKU-KIZOGIWD', 'Gatorade', '', 31.00, 38.75, 35.65, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(417, 0, 1, 1, 1, '4806026205803', 'SKU-CVTIOB5W', 'Clara Ole JalapeÃ±o cheese', '', 89.00, 111.25, 102.35, 19, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(418, 0, 1, 1, 1, '4806026205797', 'SKU-DBV4I9TF', 'CHEDDAR CHEESE', 'Clara Ole', 272.00, 340.00, 312.80, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(419, 0, 1, 1, 1, '1200108000103', 'SKU-HCAE62C0', 'lays stax extra cheese', 'Lay\'s', 39.00, 48.75, 44.85, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(420, 0, 1, 1, 1, '0071615901105', 'SKU-GL32JB9W', 'corned beef & hot peppers', '| Beef dishes, Canned meats, Corned beef', 123.00, 153.75, 141.45, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(421, 0, 1, 1, 1, '4800249011319', 'SKU-EUGGJCJS', 'Classic Corned Beef', 'Hughlands | Canned meats, Corned beef', 374.00, 467.50, 430.10, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(422, 0, 1, 1, 1, '8850124045461', 'SKU-VSKDMZAN', 'Starbucks Frappuccino', 'Starbucks', 396.00, 495.00, 455.40, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(423, 0, 1, 1, 1, '4804888800440', 'SKU-8A8OEQS9', 'Zesto Choc-o', '', 179.00, 223.75, 205.85, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(424, 0, 1, 1, 1, '4804888901086', 'SKU-KIHKSKJZ', 'Zest.O Choc. O', '', 126.00, 157.50, 144.90, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(425, 0, 1, 1, 1, '4800016074776', 'SKU-CF0KSKNJ', 'C2 Milk Tea Wintermelon', '', 285.00, 356.25, 327.75, 18, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(426, 0, 1, 1, 1, '0731126011020', 'SKU-OAT72JPS', 'Cream Pure Corntarch', '', 42.00, 52.50, 48.30, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(427, 0, 1, 1, 1, '4801668500286', 'SKU-MQXJB8UB', 'Soy sauce', 'Datu Puti | Soy sauces', 54.00, 67.50, 62.10, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(428, 0, 1, 1, 1, '4806504712045', 'SKU-P0F684AV', 'Premium Flakes in Oil', 'Mega Tuna | tuna flakes in oil', 395.00, 493.75, 454.25, 16, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(429, 0, 1, 1, 1, '9002490218119', 'SKU-RTRNVUJN', 'redbull energy drink', 'RedBull | Energy drinks', 216.00, 270.00, 248.40, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(430, 0, 1, 1, 1, '4800049720510', 'SKU-FTTUCPGT', 'Natures Spring Alkaline Water ph9 1L', '', 339.00, 423.75, 389.85, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(431, 0, 1, 1, 1, '4800024583475', 'SKU-UDXZNJC4', 'Fit N Right Burn Aplle Juice Drink 1L', 'Del Monte', 110.00, 137.50, 126.50, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(432, 0, 1, 1, 1, '4806517043457', 'SKU-JRRZH9XX', 'Goya Everyday Instant Powder Chocolate Drink', 'Goya | Milks, Chocolates, Instant beverages, Dehydrated beverages, Goya, Goya-everyday-instant-powder-choco, Milk-chocolate-powder', 355.00, 443.75, 408.25, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(433, 0, 1, 1, 1, '8991001260284', 'SKU-7QXBMLIK', '7+ Oat bar', 'Nutritional Goodness By S.A.', 208.00, 260.00, 239.20, 9, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(434, 0, 1, 1, 1, '4803925033155', 'SKU-AJBJMTJZ', 'Rootbeer', 'Mug', 375.00, 468.75, 431.25, 19, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(435, 0, 1, 1, 1, '4801981120963', 'SKU-M5NYRM7I', 'Coca Cola light', 'coca-cola | Diet cola soft drink', 99.00, 123.75, 113.85, 7, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(436, 0, 1, 1, 1, '4801981112647', 'SKU-QDQDAGEL', 'Coca-Cola No Sugar', 'Coca-Cola | Diet cola soft drink', 468.00, 585.00, 538.20, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(437, 0, 1, 1, 1, '4801032281094', 'SKU-XY5V082J', 'San Mig Lychee Bottle', '', 227.00, 283.75, 261.05, 8, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(438, 0, 1, 1, 1, '4800092116513', 'SKU-5D7LZQHJ', 'cm cookies (pp) double chocolate 33g', 'Rebisco | Cocoa and its products, Biscuit with a chocolate bar covering', 374.00, 467.50, 430.10, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(439, 0, 1, 1, 1, '4800016138577', 'SKU-UD1GDNFZ', 'tortillos chili flavored corn snacks', 'granny goose', 43.00, 53.75, 49.45, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(440, 0, 1, 1, 1, '4806533323373', 'SKU-6DPNV5E1', 'Goya Black Cookie Crunch', '', 75.00, 93.75, 86.25, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(441, 0, 1, 1, 1, '4800016105098', 'SKU-YWCSF0BJ', 'Kack n jill Chiz Curls', 'Jack \'n Jill', 370.00, 462.50, 425.50, 9, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(442, 0, 1, 1, 1, '4800365101239', 'SKU-HADNQVBF', 'Super Crunch Sweet Corn', 'Super Crunch', 452.00, 565.00, 519.80, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(443, 0, 1, 1, 1, '4800216125919', 'SKU-UDQJGACS', 'Leslie\'s Cheezy Corn Crunch', 'Leslie\'s, Leslieâ€™s (Cheezy) | Corn snacks, Filipino snacks', 379.00, 473.75, 435.85, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(444, 0, 1, 1, 1, '4710022045427', 'SKU-ZDOZYVAA', 'His and Her Roasted Mix Nuts', '', 78.00, 97.50, 89.70, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(445, 0, 1, 1, 1, '4800631001928', 'SKU-RMQBLXKB', 'Assorted Nuts', 'BOY BAWANG | Appetizers, Peanuts', 121.00, 151.25, 139.15, 15, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(446, 0, 1, 1, 1, '0840069674626', 'SKU-ZJTIRLJJ', 'Choco Mucho Cookies and Cream', '', 103.00, 128.75, 118.45, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(447, 0, 1, 1, 1, '4804888902793', 'SKU-4FVQEMKG', 'Zesto Dalandan 1.5L', '| Sodas', 491.00, 613.75, 564.65, 7, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(448, 0, 1, 1, 1, '4803925051104', 'SKU-MFCMUDGP', 'Tropicana Twister Orange', '| Fruit-based beverages', 274.00, 342.50, 315.10, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(449, 0, 1, 1, 1, '4800016561856', 'SKU-L7TJVMBI', 'Nissin Ramen Tonkotsu', '', 180.00, 225.00, 207.00, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(450, 0, 1, 1, 1, '80883296', 'SKU-4HDNNYFW', 'TIC TAC SPEARMINT FLAVOURED CANDIES', 'Tic Tac | Candies', 311.00, 388.75, 357.65, 18, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(451, 0, 1, 1, 1, '7622300858575', 'SKU-BFB8GGNY', 'Tang Dalandan', '| Orange juices, Kalamansi Juices', 165.00, 206.25, 189.75, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(452, 0, 1, 1, 1, '4806525680019', 'SKU-8FKSFSLZ', 'Pantoja Jacobina', '', 48.00, 60.00, 55.20, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(453, 0, 1, 1, 1, '4800016556807', 'SKU-Z6OUFT0I', 'Xtra Big Extra Hot', 'Payless | Instant noodles', 495.00, 618.75, 569.25, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(454, 0, 1, 1, 1, '9556174811207', 'SKU-CEFYX3Y2', 'Quaker Oat Cereal Drink Original', 'Quaker | Rolled oats', 370.00, 462.50, 425.50, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(455, 0, 1, 1, 1, '4808680651026', 'SKU-GZHNDYUR', 'Ladys choice Tuna Spread', '', 250.00, 312.50, 287.50, 10, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(456, 0, 1, 1, 1, '4806525944036', 'SKU-IYGC4DBV', 'Tasty palamig CUCUMBER LEMONADE', '| Beverages', 292.00, 365.00, 335.80, 20, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(457, 0, 1, 1, 1, '4805358323032', 'SKU-UI3TAOVV', 'Magnolia Quickmelt', 'Magnolia', 404.00, 505.00, 464.60, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(458, 0, 1, 1, 1, '7622300239527', 'SKU-G7FFWL5C', 'eden melt sarap', '', 362.00, 452.50, 416.30, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(459, 0, 1, 1, 1, '7622201704223', 'SKU-QKZAKZHK', 'spacedunk oreo', 'Oreo | Biscuits', 22.00, 27.50, 25.30, 20, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(460, 0, 1, 1, 1, '8998389132035', 'SKU-B2LKNTW5', 'Olezz Strawberry', 'Kokola', 102.00, 127.50, 117.30, 13, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(461, 0, 1, 1, 1, '8998389131045', 'SKU-9UPQLRHB', 'Crackers Garlic Bread', '', 288.00, 360.00, 331.20, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(462, 0, 1, 1, 1, '8991001661746', 'SKU-QHI99ATZ', 'delfi treasures cookies n cream', '', 118.00, 147.50, 135.70, 9, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(463, 0, 1, 1, 1, '6921211116103', 'SKU-W2VAUPWY', 'chupa chups cool cola mini', 'Chupa Chups | Lollipops', 127.00, 158.75, 146.05, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(464, 0, 1, 1, 1, '6921211116080', 'SKU-ZZRSSGAH', 'chupa chups panda bears', 'Chupa Chups | Candies', 56.00, 70.00, 64.40, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(465, 0, 1, 1, 1, '4800016043222', 'SKU-ICEYYSME', 'Nips Fruit & Nuts', 'Nips', 462.00, 577.50, 531.30, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(466, 0, 1, 1, 1, '6922877760686', 'SKU-EPOMZYQC', 'Skippy Crunchy', 'Skippy | Peanut butters, Nut butters', 53.00, 66.25, 60.95, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(467, 0, 1, 1, 1, '4806503244080', 'SKU-6T411SGK', 'family brand sardines', '| Sardines in oil and chili', 193.00, 241.25, 221.95, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(468, 0, 1, 1, 1, '4806504721016', 'SKU-OVOBJUDY', 'mega tuna', '| Canned tunas', 152.00, 190.00, 174.80, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(469, 0, 1, 1, 1, '4800016666452', 'SKU-TWISE5K1', 'Jack n Jill Mr.Chips Manamis Mais', 'Jackâ€™nJill', 480.00, 600.00, 552.00, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(470, 0, 1, 1, 1, '4801981109197', 'SKU-HINWWPJR', 'Minute maid nutri orange mango', 'Minute Maid', 125.00, 156.25, 143.75, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(471, 0, 1, 1, 1, '0750515031029', 'SKU-LRBETTD4', 'SkyFlakes Cracker Sandwich Tsokolate', 'SkyFlakes | Cracker sandwich', 272.00, 340.00, 312.80, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(472, 0, 1, 1, 1, '0052100009841', 'SKU-VONZ53EZ', 'Oregano', '', 274.00, 342.50, 315.10, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(473, 0, 1, 1, 1, '4801668607954', 'SKU-8FPOY7UE', 'Crab and Corn Soup', 'UFC', 402.00, 502.50, 462.30, 11, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(474, 0, 1, 1, 1, '5901062000807', 'SKU-O5SWD7S1', 'Mleko', 'Mlekovita', 271.00, 338.75, 311.65, 18, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(475, 0, 1, 1, 1, '4800035101415', 'SKU-O7WW9TWZ', 'Lily\'s Classic Peanut butter', '', 282.00, 352.50, 324.30, 8, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(476, 0, 1, 1, 1, '4800016628283', 'SKU-CC2JRQQY', 'pretzels (r) chocolate 28g', '', 430.00, 537.50, 494.50, 16, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(477, 0, 1, 1, 1, '4800249909333', 'SKU-UK0RJJQE', 'cdo meat loaf 150g', '', 112.00, 140.00, 128.80, 5, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(478, 0, 1, 1, 1, '9555139200025', 'SKU-R2DWEKFY', 'Easy Pha-Max (Wheatgrass Honey)', '', 147.00, 183.75, 169.05, 6, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(479, 0, 1, 1, 1, '4801962413152', 'SKU-8GCATULF', 'Berocca (Multivitamins and Minerals)', '| Multivitamin', 81.00, 101.25, 93.15, 19, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(480, 0, 1, 1, 1, '8996001351826', 'SKU-U25PD99M', 'wafello (bwn) 21g', 'Wafello', 55.00, 68.75, 63.25, 9, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(481, 0, 1, 1, 1, '4804888889728', 'SKU-KPGFQH0F', 'ZEsto Big 25o', '', 23.00, 28.75, 26.45, 7, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(482, 0, 1, 1, 1, '8120001446804', 'SKU-5N86R3RZ', 'ð—™ð—¼ð˜‚ð—»ð—±ð—²ð—¿ð˜€ ð— ð˜‚ð˜€ð—µð—¿ð—¼ð—¼ð—º ðŸ°ðŸ¬ðŸ¬ð—´', '', 258.00, 322.50, 296.70, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(483, 0, 1, 1, 1, '4806534440253', 'SKU-PKZJ0QB0', 'Matcha Bun', 'Fuwa Fuwa | Breads, Fairy cakes, Matcha bun', 369.00, 461.25, 424.35, 15, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(484, 0, 1, 1, 1, '4800611029942', 'SKU-FAPQAGB3', 'Sunkist Yoghurt Zero', 'Sunkist | Sugar-free colas with artificial sweeteners and without caffeine', 218.00, 272.50, 250.70, 12, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(485, 0, 1, 1, 1, '4901515004865', 'SKU-S9QN0DG4', 'Kikkoman oyster', 'Kikkoman', 320.00, 400.00, 368.00, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(486, 0, 1, 1, 1, '4800575120167', 'SKU-ICC0SRQ7', 'Alaska Classic Sweetened Condensed Filled Milk', 'Alaska | Dairies, Baking Ingredients, Coffee Sweeteners, Dessert Toppings', 466.00, 582.50, 535.90, 17, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(487, 0, 1, 1, 1, '4806030202904', 'SKU-SY84SZUG', 'jolly mushroom pieces & stems 100g', 'Jolly', 317.00, 396.25, 364.55, 18, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(488, 0, 1, 1, 1, '0748485401508', 'SKU-39JUBHA9', '0748485401508', '', 127.00, 158.75, 146.05, 19, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(489, 0, 1, 1, 1, '4800249909548', 'SKU-BXBJGTGV', 'haja', '', 55.00, 68.75, 63.25, 15, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(490, 0, 1, 1, 1, '0748485401768', 'SKU-ROL4AJP2', 'Birch Tree Adult Boost', 'Century Pacific Food | Milks', 43.00, 53.75, 49.45, 14, NULL, 1, 1, '2026-06-11 15:37:35', '2026-06-11 07:37:35', 'active', 0, NULL),
	(491, 0, 1, 1, 1, '0748485402062', 'SKU-QEIYMGAQ', 'All Purpose Creamer', 'Angel', 188.00, 235.00, 216.20, 13, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(492, 0, 1, 1, 1, '8801498100096', 'SKU-W1A1VGRZ', 'Traditional Seasoned Laver', 'nkf | Traditional seasoned laver, Traditional-seasoned-laver', 168.00, 210.00, 193.20, 9, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(493, 0, 1, 1, 1, '9555192508960', 'SKU-XDH8ZKUU', 'Doublemint Chewy Mints, Strawberry Flavor', 'Wrigley\'s | Candies', 466.00, 582.50, 535.90, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(494, 0, 1, 1, 1, '4800552888059', 'SKU-XO3FRAKM', 'Chocolate Chip Cookies', 'Chips Delight', 213.00, 266.25, 244.95, 19, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(495, 0, 1, 1, 1, '0096785015889', 'SKU-G2HZQKRI', 'Ginataang Tulingan', 'SABA', 311.00, 388.75, 357.65, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(496, 0, 1, 1, 1, '4800016074417', 'SKU-UDEVHAVS', 'B\'lue', 'DURBI', 484.00, 605.00, 556.60, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(497, 0, 1, 1, 1, '4801668601884', 'SKU-T0DQFYV0', 'Soy sauce', 'Datu Puti | Soy sauces', 33.00, 41.25, 37.95, 20, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(498, 0, 1, 1, 1, '0020800306348', 'SKU-6XJFZLZN', 'ood spice 14g fresh', '', 65.00, 81.25, 74.75, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(499, 0, 1, 1, 1, '4805358616080', 'SKU-NAJQRGC2', 'Non Fat Milk', 'Magnolia', 217.00, 271.25, 249.55, 6, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(500, 0, 1, 1, 1, '4800049720176', 'SKU-ROP7HU6U', 'ph9 Drinking Water', 'Nature\'s Spring | Drinking water', 96.00, 120.00, 110.40, 6, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(501, 0, 1, 1, 1, '4803925033124', 'SKU-J7TT5URX', '7 up Regular in can', '7up', 242.00, 302.50, 278.30, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(502, 0, 1, 1, 1, '4800056012035', 'SKU-YJKGJVCW', 'Butter', 'Queensland', 275.00, 343.75, 316.25, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(503, 0, 1, 1, 1, '8885013130751', 'SKU-L51HBTUL', 'Fruit Twister', 'Aice', 26.00, 32.50, 29.90, 5, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(504, 0, 1, 1, 1, '4800523443263', 'SKU-AHTINRYJ', 'Choco Chum White Choco Burst', '', 20.00, 25.00, 23.00, 6, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(505, 0, 1, 1, 1, '0748485102238', 'SKU-PUYWPLMN', 'Century Tuna with Calamansi', 'Century | Canned tunas', 290.00, 362.50, 333.50, 17, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(506, 0, 1, 1, 1, '4801668608371', 'SKU-U6HBFHDS', 'Papa', '', 343.00, 428.75, 394.45, 15, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(507, 0, 1, 1, 1, '4806018292958', 'SKU-BN1MIGLU', 'Salmon Mayo Onigiri', '7-Eleven | Onigiri', 166.00, 207.50, 190.90, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(508, 0, 1, 1, 1, '4806018298677', 'SKU-KU9P1OMP', 'Jumbo Beef Giniling', '7-Eleven Philippines | Rices, Microwave meals, Vegetables from Spain', 105.00, 131.25, 120.75, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(509, 0, 1, 1, 1, '7622300808365', 'SKU-LT9JC2OO', 'tang four seasons 19g', '', 425.00, 531.25, 488.75, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(510, 0, 1, 1, 1, '4800111005613', 'SKU-XANKHHDT', 'Healthtinapay Monay', 'Goldilocks', 425.00, 531.25, 488.75, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(511, 0, 1, 1, 1, '8851019001104', 'SKU-XNOZDPBU', 'Almond Koka (Almond Milk)', 'Glico', 228.00, 285.00, 262.20, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(512, 0, 1, 1, 1, '4800016115714', 'SKU-2HUG9VEQ', 'Krrrrunch! Flavored Corn Snack - Cheese Crunch', 'Granny Goose (URC PH)', 491.00, 613.75, 564.65, 9, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(513, 0, 1, 1, 1, '4801668608210', 'SKU-IZMHNKJR', 'Coconut Cream', 'UFC | Coconut creams', 216.00, 270.00, 248.40, 19, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(514, 0, 1, 1, 1, '0025500110370', 'SKU-PMASXOXV', 'Folgers hazelnut', 'Folgers | Coffees', 116.00, 145.00, 133.40, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(515, 0, 1, 1, 1, '4805358602083', 'SKU-XJKJVTOJ', 'Low Fat Milk', 'Magnolia', 73.00, 91.25, 83.95, 7, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(516, 0, 1, 1, 1, '4806501599670', 'SKU-ECAA4ZXN', 'high-protein fortified powdered milk drink', 'Anchor', 61.00, 76.25, 70.15, 15, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(517, 0, 1, 1, 1, '4809013526318', 'SKU-RITOMRLO', 'Gulaman RED', 'Mr. Hat Gulaman, Seanluc | Seaweed products, Gelatin, Plant-based gelatin', 451.00, 563.75, 518.65, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(518, 0, 1, 1, 1, '0748485780405', 'SKU-ELZJQKW3', 'Unmeat Meat-Free Luncheon Meat-Style', 'Unmeat', 383.00, 478.75, 440.45, 13, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(519, 0, 1, 1, 1, '4800016562259', 'SKU-7ZSQIZTM', 'nissin pasta cheesy ham & bacon 60g', 'Nissin', 431.00, 538.75, 495.65, 20, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(520, 0, 1, 1, 1, '4903001039047', 'SKU-V2LDMRTU', 'Soba Noodle Sauce', 'Yamasa', 120.00, 150.00, 138.00, 13, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(521, 0, 1, 1, 1, '4805358289031', 'SKU-MQI59QRY', 'Magnolia Butter-licious Unsalted', 'Magnolia', 178.00, 222.50, 204.70, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(522, 0, 1, 1, 1, '4809012803977', 'SKU-NRF3TB7E', 'Pro Matrix 7 Multiple source Protein', 'Pro Matrix 7', 378.00, 472.50, 434.70, 7, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(523, 0, 1, 1, 1, '8935217412112', 'SKU-SD38XL5Q', 'Sá»¯a TH true yogurt vá»‹ DÃ¢u 180', 'True Happiness', 26.00, 32.50, 29.90, 15, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(524, 0, 1, 1, 1, '4800016115011', 'SKU-QE9ZTVLY', 'chicken skin', 'Jack \'n Jill | Chicken and its products, Crisps', 359.00, 448.75, 412.85, 19, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(525, 0, 1, 1, 1, '4894514047028', 'SKU-BBQOPDOY', 'Chocolate Banana Flavoured Crisy Crepes', 'Meadows', 40.00, 50.00, 46.00, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(526, 0, 1, 1, 1, '4807770273049', 'SKU-HCI9XW5Y', 'Baked Mac Style Instant Pasta', 'Lucky Me! | Instant noodle soups', 268.00, 335.00, 308.20, 5, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(527, 0, 1, 1, 1, '4800361424813', 'SKU-QNPNYPAY', 'Sterilized Save 15', 'NestlÃ©', 417.00, 521.25, 479.55, 5, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(528, 0, 1, 1, 1, '4800016551574', 'SKU-FACRZYIC', 'nissin ramen chicken 55g', 'Nissim', 484.00, 605.00, 556.60, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(529, 0, 1, 1, 1, '4800365881087', 'SKU-9KMHEBIJ', 'Super Delight Brownie Bites', 'Super Delight', 290.00, 362.50, 333.50, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(530, 0, 1, 1, 1, '4800040311212', 'SKU-SGYMF2MZ', 'Butter Cookies', 'Fibisco', 269.00, 336.25, 309.35, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(531, 0, 1, 1, 1, '4800016113017', 'SKU-LAL5M44M', 'Jack â€™n Jill Dewberry Strawberry (27g)', 'Jack â€™n Jill | Biscuit with milk, Cream cookies', 243.00, 303.75, 279.45, 10, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(532, 0, 1, 1, 1, '4800024573544', 'SKU-CI9A3ZEG', 'Sweet style spaghetti sauce', '', 100.00, 125.00, 115.00, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(533, 0, 1, 1, 1, '4806525940328', 'SKU-J3KNP1AA', 'Gulaman king white', '', 15.00, 18.75, 17.25, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(534, 0, 1, 1, 1, '4800086043627', 'SKU-GWP9XLH9', 'Cookies & Cream Ice Cream', 'Selecta', 34.00, 42.50, 39.10, 15, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(535, 0, 1, 1, 1, '4800137261130', 'SKU-T6YGMW0K', 'Maya All-Purpose Flour', 'Maya', 140.00, 175.00, 161.00, 9, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(536, 0, 1, 1, 1, '4800066122212', 'SKU-TQA6FJCR', 'Corned Beef', 'Holiday', 98.00, 122.50, 112.70, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(537, 0, 1, 1, 1, '4807770272820', 'SKU-LMGWKBSJ', 'Bulalo Bone Marrow Flavor', 'Lucky Me', 265.00, 331.25, 304.75, 19, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(538, 0, 1, 1, 1, '4806500071580', 'SKU-FBKHOYVX', 'young', '', 162.00, 202.50, 186.30, 19, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(539, 0, 1, 1, 1, '4800110098234', 'SKU-LTIDWKQL', 'Non-fat milk', 'Selecta', 309.00, 386.25, 355.35, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(540, 0, 1, 1, 1, '4806504713295', 'SKU-PC95QBPF', 'Mega Prime Quality Whole kernel corn', 'Mega Prime', 328.00, 410.00, 377.20, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(541, 0, 1, 1, 1, '4800058345681', 'SKU-GSTZKMYV', 'Meat Loaf', 'Federated Distributors, Philips | Meatloafs', 416.00, 520.00, 478.40, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(542, 0, 1, 1, 1, '4891208040181', 'SKU-SERVZH7U', 'rinbee (r) cheese 24g', 'Oishi | Snacks, Cheese Sticks, Chips, Corn Snacks, Crackers', 211.00, 263.75, 242.65, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(543, 0, 1, 1, 1, '4806534440666', 'SKU-YFSMSWV9', 'Bread (Milky)', 'Fuwa Fuwa', 381.00, 476.25, 438.15, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(544, 0, 1, 1, 1, '0710535411942', 'SKU-PFUPQCRY', 'Crunchy Corn Chips Sweet Corn Flavor', 'Alibaba, Frank & David | Flavoured corn crisps', 448.00, 560.00, 515.20, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(545, 0, 1, 1, 1, '4800194104982', 'SKU-WOVMYKZF', 'onion rings (g) 16g', '', 81.00, 101.25, 93.15, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(546, 0, 1, 1, 1, '8622271515285', 'SKU-9ERS2WQE', 'Seleco Nori', 'Seleco | Snacks, Seaweeds and their products, vegan', 78.00, 97.50, 89.70, 18, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(547, 0, 1, 1, 1, '4808887031133', 'SKU-VJACMTFV', 'Luncheon Meat', 'Purefoods', 99.00, 123.75, 113.85, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(548, 0, 1, 1, 1, '4800575145054', 'SKU-L9OLJE6M', 'alaska all purpose creamer 250ml', 'Alaska', 126.00, 157.50, 144.90, 5, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(549, 0, 1, 1, 1, '8850006590348', 'SKU-QGPOZMPM', 'tender care', '', 69.00, 86.25, 79.35, 9, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(550, 0, 1, 1, 1, '8888240056627', 'SKU-2GMNQZP4', 'Classic Instant Coffee Mix', 'Old Town White Coffee', 31.00, 38.75, 35.65, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(551, 0, 1, 1, 1, '4800575240490', 'SKU-PA9WCPEV', 'ALASKA FRESH MILK BUY 2', 'ALASKA', 13.00, 16.25, 14.95, 7, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(552, 0, 1, 1, 1, '4806030207633', 'SKU-6CB63RA5', 'Granola Dark Choco Decadence', 'Daily Fix', 127.00, 158.75, 146.05, 10, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(553, 0, 1, 1, 1, '8801043015011', 'SKU-ONYPCO4E', 'neoguri spicy seafood', '| Noodles, ramen, ramyun', 367.00, 458.75, 422.05, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(554, 0, 1, 1, 1, '4804888999342', 'SKU-HVTGALN4', 'Fish Sauce (Patis)', 'Lorin\'s', 87.00, 108.75, 100.05, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(555, 0, 1, 1, 1, '0745240323068', 'SKU-FD5J47UE', 'Vanilla Syrup', 'B Coffee Co.', 264.00, 330.00, 303.60, 18, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(556, 0, 1, 1, 1, '4801981164868', 'SKU-13IOOW5S', 'minute maid (o) 180ml', '', 117.00, 146.25, 134.55, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(557, 0, 1, 1, 1, '4800631682172', 'SKU-QTSZX6T7', 'Merienda Time', 'biscuits', 258.00, 322.50, 296.70, 20, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(558, 0, 1, 1, 1, '4800194105859', 'SKU-4BBPZCVM', 'Oishi Baked Porky Popps Lean Pork Skin Classic Vinegar Flavor', 'Oishi | Pork rind, Chicharon, Keto Snacks, Low Carb Snacks', 354.00, 442.50, 407.10, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(559, 0, 1, 1, 1, '1503254322243', 'SKU-CCENTEPW', 'Sandwich Loaf', '| Breads', 34.00, 42.50, 39.10, 19, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(560, 0, 1, 1, 1, '4806532870007', 'SKU-UE515AMH', 'Chili Garlic Sauce', 'TFC Hao Hao (PH)', 36.00, 45.00, 41.40, 5, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(561, 0, 1, 1, 1, '9331275012063', 'SKU-RGTINGBI', 'Roasted Choc-Hazelnut CrÃ©me Filled Wafer Rolls', 'CafÃ© Se Lucca', 257.00, 321.25, 295.55, 18, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(562, 0, 1, 1, 1, '4805358801325', 'SKU-BY80MRTN', 'Real Mayonnaise', 'Magnolia', 427.00, 533.75, 491.05, 13, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(563, 0, 1, 1, 1, '14800006003875', 'SKU-ETDBYOWF', 'KA-KA', 'Keiko | Candies', 477.00, 596.25, 548.55, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(564, 0, 1, 1, 1, '01502152', 'SKU-DMIA6KRX', 'Corn Pops', '', 17.00, 21.25, 19.55, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(565, 0, 1, 1, 1, '4800092772214', 'SKU-XAAMRTXQ', 'criss cross (o) 20g', 'Criss Cross, Suncrest, Suncrest Foods Incorporated | Crackers (Appetizers), Puffed salty snacks made from potato', 335.00, 418.75, 385.25, 5, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(566, 0, 1, 1, 1, '4806528120413', 'SKU-HJXXUAJT', 'MyChoco Alkaline Chocolate Drink', 'Alliance In Motion Global', 371.00, 463.75, 426.65, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(567, 0, 1, 1, 1, '4800016084034', 'SKU-DJVXZGOH', 'wafrets cheese bar', '', 81.00, 101.25, 93.15, 16, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(568, 0, 1, 1, 1, '4800274312009', 'SKU-MGSWST14', 'quaker oats', '| Rolled oats', 314.00, 392.50, 361.10, 10, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(569, 0, 1, 1, 1, '4806018265457', 'SKU-EEIAVTBA', 'Busog Meal Sisig with Egg Roll and Burger Patty', '7-Eleven, Busog Meal | Microwave meals, Nems, Burger patty meal, Microwave foods, Rice meal', 479.00, 598.75, 550.85, 10, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(570, 0, 1, 1, 1, '4806018315312', 'SKU-T4EDUUYG', 'Rice Meal Express Beefless Tapsilog', '7-Eleven, Green Rebel, Rice Meal Express | Eggs, Microwave meals, Fried rice', 392.00, 490.00, 450.80, 8, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(571, 0, 1, 1, 1, '4806030203673', 'SKU-GCM6BJQ5', 'STRAWBERRY & YOGURT GRANOLA', 'DailyFix | dailyfix', 365.00, 456.25, 419.75, 20, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(572, 0, 1, 1, 1, '0745125547473', 'SKU-VOQFTAZK', 'Active Pure Isolate, Vanilla', 'Athlene', 161.00, 201.25, 185.15, 9, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(573, 0, 1, 1, 1, '4800434000388', 'SKU-127DCIFQ', 'topps (p) 32g', '', 43.00, 53.75, 49.45, 6, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(574, 0, 1, 1, 1, '0748485802077', 'SKU-H6XOGZR1', 'wow ulam afritada 155g', '', 361.00, 451.25, 415.15, 9, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(575, 0, 1, 1, 1, '0748485802084', 'SKU-LICRST7Z', 'wow ulam caldereta 155g', '', 488.00, 610.00, 561.20, 12, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(576, 0, 1, 1, 1, '4801958342008', 'SKU-LVTFCWL2', 'AJI NO MOTO', '| Condiments', 234.00, 292.50, 269.10, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(577, 0, 1, 1, 1, '4800110098227', 'SKU-MWFJHCEI', 'Selecta Adult Active', 'Selecta | Dairy drinks, Dietary supplements', 75.00, 93.75, 86.25, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(578, 0, 1, 1, 1, '7640153867629', 'SKU-5KVTPQX4', 'Danayo BLUEBERRY', 'Danayo', 320.00, 400.00, 368.00, 17, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(579, 0, 1, 1, 1, '4801981139743', 'SKU-VR7YCW0V', 'Minute Maid Pineapple Juice Drink', 'Coca-Cola Company | Fruit nectars, Sodas, Pineapple juices', 39.00, 48.75, 44.85, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(580, 0, 1, 1, 1, '4800186001275', 'SKU-0BCJ8R2J', 'Whole Grain Premium Instant Oats', 'Golden Oats | Oat', 235.00, 293.75, 270.25, 7, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(581, 0, 1, 1, 1, '8129001415865', 'SKU-WBBEKSPT', 'Hazelnut Spread With Cocoa', 'Founders', 164.00, 205.00, 188.60, 7, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(582, 0, 1, 1, 1, '4800344009969', 'SKU-8UBIYYUM', 'silver swan patis 100ml', '', 349.00, 436.25, 401.35, 14, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(583, 0, 1, 1, 1, '0748485804170', 'SKU-1KWM8T5A', 'argentina pork giniling 150g', 'Argentina', 106.00, 132.50, 121.90, 20, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(584, 0, 1, 1, 1, '4800249886030', 'SKU-YOEFPJD0', 'Ulam Burger CDO', 'CDO', 228.00, 285.00, 262.20, 11, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL),
	(585, 0, 1, 1, 1, '4804888624107', 'SKU-82QUNUSC', 'Hazelnut coffee Drink', 'Bo\'s Coffee', 386.00, 482.50, 443.90, 15, NULL, 1, 1, '2026-06-11 15:37:38', '2026-06-11 07:37:38', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.pos_product_price_histories
DROP TABLE IF EXISTS `pos_product_price_histories`;
CREATE TABLE IF NOT EXISTS `pos_product_price_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `new_cost_price` decimal(15,2) DEFAULT NULL,
  `selling_price` decimal(15,2) DEFAULT NULL,
  `new_selling_price` decimal(15,2) DEFAULT NULL,
  `wholesale_price` decimal(15,2) DEFAULT NULL,
  `new_wholesale_price` decimal(15,2) DEFAULT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `effective_date` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_product_price_history_product` FOREIGN KEY (`product_id`) REFERENCES `pos_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_product_price_histories: ~8 rows (approximately)
DELETE FROM `pos_product_price_histories`;
INSERT INTO `pos_product_price_histories` (`id`, `tenant_id`, `product_id`, `cost_price`, `new_cost_price`, `selling_price`, `new_selling_price`, `wholesale_price`, `new_wholesale_price`, `reason`, `remarks`, `effective_date`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 1, 18.00, 18.00, 26.00, 26.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:20:22', 11, 11, '2026-06-10 10:20:22', '2026-06-10 02:20:22', 'active', 0, NULL),
	(2, 0, 5, 289.76, 289.76, 298.00, 298.00, 290.00, 290.00, NULL, 'Product price updated', '2026-06-10 10:11:05', 11, 11, '2026-06-10 10:11:05', '2026-06-10 02:11:05', 'active', 0, NULL),
	(3, 0, 1, 18.00, 18.00, 25.00, 25.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:21:21', 11, 11, '2026-06-10 10:21:21', '2026-06-10 02:21:21', 'active', 0, NULL),
	(4, 0, 1, 18.00, 18.00, 35.00, 35.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:25:05', 11, 11, '2026-06-10 10:25:05', '2026-06-10 02:25:05', 'active', 0, NULL),
	(5, 0, 1, 18.00, 18.00, 35.00, 34.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:27:45', 11, 11, '2026-06-10 10:27:45', '2026-06-10 02:27:45', 'active', 0, NULL),
	(6, 0, 6, 89.73, 89.73, 95.00, 100.00, 92.00, 92.00, NULL, 'Product price updated', '2026-06-10 10:28:15', 11, 11, '2026-06-10 10:28:15', '2026-06-10 02:28:15', 'active', 0, NULL),
	(7, 1, 1, 311.72, 311.72, 407.50, 410.00, 374.90, 374.90, NULL, 'Product price updated', '2026-06-12 13:43:25', 3, 3, '2026-06-12 13:43:25', '2026-06-12 05:43:25', 'active', 0, NULL),
	(8, 1, 1, 311.72, 18.00, 410.00, 22.00, 374.90, 20.00, NULL, 'Product price updated', '2026-06-12 13:44:58', 3, 3, '2026-06-12 13:44:58', '2026-06-12 05:44:58', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.pos_sales
DROP TABLE IF EXISTS `pos_sales`;
CREATE TABLE IF NOT EXISTS `pos_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `cashier_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `sale_date` datetime DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `payment_method` varchar(50) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `discount_type` varchar(20) DEFAULT '0.00',
  `discount_holder` varchar(40) DEFAULT '0.00',
  `discount_id_no` varchar(40) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `reference_number` varchar(50) DEFAULT '0.00',
  `notes` text,
  `sale_status` enum('completed','voided','refunded') NOT NULL DEFAULT 'completed',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_invoice_no` (`invoice_no`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_sale_date` (`sale_date`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_sales: 8 rows
DELETE FROM `pos_sales`;
/*!40000 ALTER TABLE `pos_sales` DISABLE KEYS */;
INSERT INTO `pos_sales` (`id`, `tenant_id`, `customer_id`, `cashier_id`, `invoice_no`, `sale_date`, `subtotal`, `payment_method`, `discount_amount`, `discount_type`, `discount_holder`, `discount_id_no`, `tax_amount`, `total_amount`, `reference_number`, `notes`, `sale_status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, NULL, 3, 'S20260612-311819', '2026-06-12 15:58:25', 1836.25, 'split', 367.25, 'senior', '14545', '6456456', 157.39, 1469.00, '6456456456', '5412123', 'completed', 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(2, 1, NULL, 3, 'S20260612-371196', '2026-06-12 16:01:01', 6292.50, 'cash', 0.00, NULL, NULL, NULL, 674.20, 6292.50, NULL, NULL, 'completed', 3, 3, '2026-06-12 16:01:01', '2026-06-12 08:01:01', 'active', 0, NULL),
	(3, 1, NULL, 3, 'S20260612-680640', '2026-06-12 16:01:56', 9312.25, 'split', 0.00, NULL, NULL, NULL, 997.74, 9312.25, '456456456, 7878787', NULL, 'completed', 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(4, 1, NULL, 3, 'S20260612-141605', '2026-06-12 16:08:58', 3537.25, 'cash', 106.12, 'manual', NULL, NULL, 367.62, 3431.13, NULL, NULL, 'completed', 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(5, 1, NULL, 3, 'S20260612-598760', '2026-06-12 16:12:57', 735.00, 'cash', 0.00, NULL, NULL, NULL, 78.75, 735.00, NULL, NULL, 'completed', 3, 3, '2026-06-12 16:12:57', '2026-06-12 08:12:57', 'active', 0, NULL),
	(6, 1, NULL, 3, 'S20260612-846626', '2026-06-12 16:44:22', 3697.50, 'split', 739.50, 'senior', '12145', '4545', 316.93, 2958.00, '12345, 565', '454545', 'completed', 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(7, 1, NULL, 3, 'S20260612-216891', '2026-06-12 16:45:07', 3513.75, 'cash', 0.00, NULL, NULL, NULL, 376.47, 3513.75, NULL, NULL, 'completed', 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(8, 1, NULL, 3, 'S20260612-187220', '2026-06-12 16:59:35', 1464.25, 'cash', 0.00, NULL, NULL, NULL, 156.88, 1464.25, NULL, NULL, 'completed', 3, 3, '2026-06-12 16:59:35', '2026-06-12 08:59:35', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_sales` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_sale_items
DROP TABLE IF EXISTS `pos_sale_items`;
CREATE TABLE IF NOT EXISTS `pos_sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_sale_items: 19 rows
DELETE FROM `pos_sale_items`;
/*!40000 ALTER TABLE `pos_sale_items` DISABLE KEYS */;
INSERT INTO `pos_sale_items` (`id`, `sale_id`, `product_id`, `barcode`, `sku`, `product_name`, `qty`, `unit_price`, `discount_amount`, `tax_amount`, `line_total`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 2.00, 458.75, 0.00, 0.00, 917.50, 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(2, 1, 2, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 5.00, 183.75, 0.00, 0.00, 918.75, 3, 3, '2026-06-12 15:58:25', '2026-06-12 07:58:25', 'active', 0, NULL),
	(3, 2, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 6.00, 458.75, 0.00, 0.00, 2752.50, 3, 3, '2026-06-12 16:01:01', '2026-06-12 08:01:01', 'active', 0, NULL),
	(4, 2, 5, '4800016068010', 'SKU-NXZS5YOI', 'c2 apple 500ml', 8.00, 442.50, 0.00, 0.00, 3540.00, 3, 3, '2026-06-12 16:01:01', '2026-06-12 08:01:01', 'active', 0, NULL),
	(5, 3, 5, '4800016068010', 'SKU-NXZS5YOI', 'c2 apple 500ml', 7.00, 442.50, 0.00, 0.00, 3097.50, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(6, 3, 2, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 6.00, 183.75, 0.00, 0.00, 1102.50, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(7, 3, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 11.00, 458.75, 0.00, 0.00, 5046.25, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(8, 3, 1, '4800361410816', 'SKU-OCNIJIHQ', 'bear brand (w) 33g', 3.00, 22.00, 0.00, 0.00, 66.00, 3, 3, '2026-06-12 16:01:56', '2026-06-12 08:01:56', 'active', 0, NULL),
	(9, 4, 1, '4800361410816', 'SKU-OCNIJIHQ', 'bear brand (w) 33g', 3.00, 22.00, 0.00, 0.00, 66.00, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(10, 4, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 5.00, 458.75, 0.00, 0.00, 2293.75, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(11, 4, 5, '4800016068010', 'SKU-NXZS5YOI', 'c2 apple 500ml', 1.00, 442.50, 0.00, 0.00, 442.50, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(12, 4, 2, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 4.00, 183.75, 0.00, 0.00, 735.00, 3, 3, '2026-06-12 16:08:58', '2026-06-12 08:08:58', 'active', 0, NULL),
	(13, 5, 2, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 4.00, 183.75, 0.00, 0.00, 735.00, 3, 3, '2026-06-12 16:12:57', '2026-06-12 08:12:57', 'active', 0, NULL),
	(14, 6, 5, '4800016068010', 'SKU-NXZS5YOI', 'c2 apple 500ml', 4.00, 442.50, 0.00, 0.00, 1770.00, 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(15, 6, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 3.00, 458.75, 0.00, 0.00, 1376.25, 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(16, 6, 2, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 3.00, 183.75, 0.00, 0.00, 551.25, 3, 3, '2026-06-12 16:44:22', '2026-06-12 08:44:22', 'active', 0, NULL),
	(17, 7, 2, '0750515018402', 'SKU-CS7ENT4R', 'SkyFlakes Crackers 10 Single pack Condensada Falvor', 2.00, 183.75, 0.00, 0.00, 367.50, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(18, 7, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 3.00, 458.75, 0.00, 0.00, 1376.25, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(19, 7, 5, '4800016068010', 'SKU-NXZS5YOI', 'c2 apple 500ml', 4.00, 442.50, 0.00, 0.00, 1770.00, 3, 3, '2026-06-12 16:45:07', '2026-06-12 08:45:07', 'active', 0, NULL),
	(20, 8, 1, '4800361410816', 'SKU-OCNIJIHQ', 'bear brand (w) 33g', 4.00, 22.00, 0.00, 0.00, 88.00, 3, 3, '2026-06-12 16:59:35', '2026-06-12 08:59:35', 'active', 0, NULL),
	(21, 8, 3, '0750515017429', 'SKU-SPTJEFX2', 'fita crackers 30g', 3.00, 458.75, 0.00, 0.00, 1376.25, 3, 3, '2026-06-12 16:59:35', '2026-06-12 08:59:35', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_sale_items` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_stock_transactions
DROP TABLE IF EXISTS `pos_stock_transactions`;
CREATE TABLE IF NOT EXISTS `pos_stock_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `transaction_type` enum('IN','OUT','ADJUSTMENT','RETURN_IN','RETURN_OUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `stock_before` decimal(15,2) NOT NULL DEFAULT '0.00',
  `stock_after` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `reference_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_product` (`product_id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_reference` (`reference_type`,`reference_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_stock_transactions: ~5 rows (approximately)
DELETE FROM `pos_stock_transactions`;
INSERT INTO `pos_stock_transactions` (`id`, `tenant_id`, `product_id`, `transaction_type`, `quantity`, `stock_before`, `stock_after`, `unit_cost`, `reference_type`, `reference_id`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 1, 'IN', 60.00, 0.00, 60.00, 327.00, 'STOCK_RECEIVING', NULL, NULL, 3, 3, '2026-06-11 15:54:56', '2026-06-11 07:54:56', 'active', 0, NULL),
	(2, 1, 1, 'ADJUSTMENT', 40.00, 60.00, 100.00, NULL, 'STOCK_ADJUSTMENT', NULL, 'Physical Count', 3, NULL, '2026-06-11 20:26:16', '2026-06-11 12:26:16', 'active', 0, NULL),
	(3, 1, 1, 'ADJUSTMENT', 1.00, 100.00, 101.00, NULL, 'STOCK_ADJUSTMENT', NULL, 'Physical Count - none', 3, NULL, '2026-06-11 20:29:57', '2026-06-11 12:29:57', 'active', 0, NULL),
	(4, 1, 1, 'IN', 120.00, 92.00, 212.00, 300.00, 'STOCK_RECEIVING', NULL, NULL, 3, 3, '2026-06-12 13:42:34', '2026-06-12 05:42:34', 'active', 0, NULL),
	(5, 1, 2, 'IN', 30.00, 0.00, 30.00, 147.00, 'STOCK_RECEIVING', NULL, NULL, 3, 3, '2026-06-12 13:49:35', '2026-06-12 05:49:35', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.pos_subscriptions
DROP TABLE IF EXISTS `pos_subscriptions`;
CREATE TABLE IF NOT EXISTS `pos_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `billing_cycle` enum('monthly','quarterly','semi_annual','annual','lifetime') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `duration_days` int(10) unsigned NOT NULL DEFAULT '30',
  `max_users` int(10) unsigned DEFAULT NULL,
  `max_products` int(10) unsigned DEFAULT NULL,
  `max_branches` int(10) unsigned DEFAULT NULL,
  `max_storage_mb` int(10) unsigned DEFAULT NULL,
  `allow_inventory` tinyint(1) NOT NULL DEFAULT '1',
  `allow_reports` tinyint(1) NOT NULL DEFAULT '1',
  `allow_multi_branch` tinyint(1) NOT NULL DEFAULT '0',
  `allow_api_access` tinyint(1) NOT NULL DEFAULT '0',
  `trial_days` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_status` (`status`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_subscriptions: ~3 rows (approximately)
DELETE FROM `pos_subscriptions`;
INSERT INTO `pos_subscriptions` (`id`, `name`, `description`, `price`, `billing_cycle`, `duration_days`, `max_users`, `max_products`, `max_branches`, `max_storage_mb`, `allow_inventory`, `allow_reports`, `allow_multi_branch`, `allow_api_access`, `trial_days`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 'Free Trial', '14-day free trial.', 0.00, 'monthly', 14, 1, 100, 1, 512, 1, 1, 0, 0, 14, 1, NULL, NULL, '2026-06-11 12:58:47', '2026-06-11 04:58:47', 'active', 0, NULL),
	(2, 'Basic', 'For small stores.', 199.00, 'monthly', 30, 3, 1000, 1, 1024, 1, 1, 0, 0, 0, 2, NULL, NULL, '2026-06-11 12:58:47', '2026-06-11 04:58:47', 'active', 0, NULL),
	(3, 'Premium', 'For growing businesses.', 499.00, 'monthly', 30, 10, 10000, 3, 5120, 1, 1, 1, 0, 0, 3, NULL, NULL, '2026-06-11 12:58:47', '2026-06-11 04:58:47', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.pos_tenants
DROP TABLE IF EXISTS `pos_tenants`;
CREATE TABLE IF NOT EXISTS `pos_tenants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscription_start` date DEFAULT NULL,
  `subscription_end` date DEFAULT NULL,
  `trial_ends_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_tenants: ~1 rows (approximately)
DELETE FROM `pos_tenants`;
INSERT INTO `pos_tenants` (`id`, `subscription_id`, `business_name`, `business_code`, `owner_name`, `email`, `phone`, `address`, `logo`, `subscription_start`, `subscription_end`, `trial_ends_at`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 'ANTONIO JR. PILOTON\'s Store', 'TEN-RES5CGOS5G', 'ANTONIO JR. PILOTON', 'antoniojrpiloton@gmail.com', NULL, NULL, NULL, '2026-06-11', NULL, NULL, NULL, NULL, '2026-06-11 15:36:53', '2026-06-11 07:36:53', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.pos_units
DROP TABLE IF EXISTS `pos_units`;
CREATE TABLE IF NOT EXISTS `pos_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '0',
  `description` varchar(50) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_units: 28 rows
DELETE FROM `pos_units`;
/*!40000 ALTER TABLE `pos_units` DISABLE KEYS */;
INSERT INTO `pos_units` (`id`, `tenant_id`, `name`, `description`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 'kg', 'Kilograms', 1, 1, '2026-06-10 09:01:07', '2026-06-10 01:01:07', 'active', 0, NULL),
	(2, 1, 'Piece', 'Individual item', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(3, 1, 'Pack', 'Packaged items', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(4, 1, 'Box', 'Box of items', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(5, 1, 'Case', 'Case of products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(6, 1, 'Bundle', 'Bundle of items', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(7, 1, 'Set', 'Set of products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(8, 1, 'Dozen', '12 pieces', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(9, 1, 'Gram', 'Weight in grams', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(10, 1, 'Kilogram', 'Weight in kilograms', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(11, 1, 'Ounce', 'Weight in ounces', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(12, 1, 'Pound', 'Weight in pounds', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(13, 1, 'Milliliter', 'Volume in milliliters', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(14, 1, 'Liter', 'Volume in liters', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(15, 1, 'Bottle', 'Bottled products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(16, 1, 'Can', 'Canned products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(17, 1, 'Sachet', 'Sachet products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(18, 1, 'Meter', 'Length in meters', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(19, 1, 'Centimeter', 'Length in centimeters', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(20, 1, 'Foot', 'Length in feet', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(21, 1, 'Inch', 'Length in inches', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(22, 1, 'Roll', 'Rolled products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(23, 1, 'Tray', 'Tray of products', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(24, 1, 'Carton', 'Carton packaging', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(25, 1, 'Pouch', 'Pouch packaging', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(26, 1, 'Tube', 'Tube packaging', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(27, 1, 'Jar', 'Jar packaging', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL),
	(28, 1, 'Bag', 'Bag packaging', NULL, NULL, '2026-06-10 09:02:00', '2026-06-10 01:02:00', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_units` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_users
DROP TABLE IF EXISTS `pos_users`;
CREATE TABLE IF NOT EXISTS `pos_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `branch_id` (`branch_id`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_users: ~0 rows (approximately)
DELETE FROM `pos_users`;

-- Dumping structure for table snsu_safetrack.qr_codes
DROP TABLE IF EXISTS `qr_codes`;
CREATE TABLE IF NOT EXISTS `qr_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `last_number` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.qr_codes: ~18 rows (approximately)
DELETE FROM `qr_codes`;
INSERT INTO `qr_codes` (`id`, `school_id`, `prefix`, `UserID`, `last_number`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 2, '304855', NULL, 1, 0, 0, '2026-05-25 10:40:00', '2026-05-25 02:40:00', 'active', 0, NULL),
	(2, 2, '304855', NULL, 2, 0, 0, '2026-05-25 10:42:07', '2026-05-25 02:42:07', 'active', 0, NULL),
	(3, 2, '304855', NULL, 3, 0, 0, '2026-05-25 10:43:43', '2026-05-25 02:43:43', 'active', 0, NULL),
	(4, 2, '304855', NULL, 4, 0, 0, '2026-05-25 10:44:40', '2026-05-25 02:44:40', 'active', 0, NULL),
	(5, 2, '304855', NULL, 5, 0, 0, '2026-05-25 10:44:54', '2026-05-25 02:44:54', 'active', 0, NULL),
	(6, 2, '304855', NULL, 6, 0, 0, '2026-05-25 10:45:17', '2026-05-25 02:45:17', 'active', 0, NULL),
	(7, 2, '304855', NULL, 7, 0, 0, '2026-05-25 10:48:58', '2026-05-25 02:48:58', 'active', 0, NULL),
	(8, 2, '304855', NULL, 8, 0, 0, '2026-05-25 10:49:24', '2026-05-25 02:49:24', 'active', 0, NULL),
	(9, 2, '304855', NULL, 9, 0, 0, '2026-05-25 10:49:43', '2026-05-25 02:49:43', 'active', 0, NULL),
	(10, 3, '304856', NULL, 1, 0, 0, '2026-05-25 10:59:19', '2026-05-25 02:59:19', 'active', 0, NULL),
	(11, 3, '304856', NULL, 2, 0, 0, '2026-05-25 11:14:10', '2026-05-25 03:14:10', 'active', 0, NULL),
	(12, 1, '304854', NULL, 1, 0, 0, '2026-05-25 11:37:09', '2026-05-25 03:37:09', 'active', 0, NULL),
	(13, 1, '304854', NULL, 2, 0, 0, '2026-05-25 11:56:06', '2026-05-25 03:56:06', 'active', 0, NULL),
	(14, 1, '304854', NULL, 3, 0, 0, '2026-05-26 07:27:55', '2026-05-25 23:27:55', 'active', 0, NULL),
	(15, 1, '304854', NULL, 4, 0, 0, '2026-05-26 08:10:15', '2026-05-26 00:10:15', 'active', 0, NULL),
	(16, 1, '304854', NULL, 5, 0, 0, '2026-05-26 08:10:23', '2026-05-26 00:10:23', 'active', 0, NULL),
	(17, 1, '304854', NULL, 6, 0, 0, '2026-05-26 10:11:39', '2026-05-26 02:11:39', 'active', 0, NULL),
	(18, 1, '304854', NULL, 7, 0, 0, '2026-05-26 15:13:25', '2026-05-26 07:13:25', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.roles: ~6 rows (approximately)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `details`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'students', 'students', 'web', '2025-07-16 15:55:56', '2025-07-16 15:55:56'),
	(2, 'parents', 'parents', 'web', '2025-07-16 15:56:01', '2025-07-16 15:56:01'),
	(3, 'employees', 'employees', 'web', '2025-07-16 15:56:06', '2025-07-16 15:56:06'),
	(4, 'admin', 'admin', 'web', '2025-07-16 15:56:12', '2025-07-16 15:56:12'),
	(6, 'SA', 'SuperAdmin', 'web', '2026-05-24 09:04:30', '2026-05-24 09:04:30'),
	(7, 'tenants', 'tenants', 'web', '2026-06-11 06:15:25', '2026-06-11 06:15:25');

-- Dumping structure for table snsu_safetrack.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`) USING BTREE,
  KEY `role_has_permissions_role_id_foreign` (`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.role_has_permissions: ~104 rows (approximately)
DELETE FROM `role_has_permissions`;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(5, 3),
	(6, 3),
	(7, 3),
	(8, 3),
	(9, 3),
	(10, 3),
	(11, 3),
	(37, 3),
	(1, 4),
	(2, 4),
	(3, 4),
	(4, 4),
	(5, 4),
	(6, 4),
	(7, 4),
	(8, 4),
	(9, 4),
	(10, 4),
	(11, 4),
	(12, 4),
	(13, 4),
	(14, 4),
	(15, 4),
	(16, 4),
	(17, 4),
	(18, 4),
	(19, 4),
	(20, 4),
	(21, 4),
	(22, 4),
	(23, 4),
	(24, 4),
	(25, 4),
	(26, 4),
	(27, 4),
	(28, 4),
	(29, 4),
	(30, 4),
	(31, 4),
	(32, 4),
	(33, 4),
	(34, 4),
	(35, 4),
	(36, 4),
	(37, 4),
	(12, 5),
	(14, 5),
	(15, 5),
	(18, 5),
	(1, 6),
	(2, 6),
	(3, 6),
	(4, 6),
	(5, 6),
	(6, 6),
	(7, 6),
	(8, 6),
	(9, 6),
	(10, 6),
	(11, 6),
	(12, 6),
	(13, 6),
	(14, 6),
	(15, 6),
	(16, 6),
	(17, 6),
	(18, 6),
	(19, 6),
	(20, 6),
	(21, 6),
	(22, 6),
	(23, 6),
	(24, 6),
	(25, 6),
	(26, 6),
	(27, 6),
	(28, 6),
	(29, 6),
	(30, 6),
	(31, 6),
	(32, 6),
	(33, 6),
	(34, 6),
	(35, 6),
	(36, 6),
	(18, 16),
	(22, 16),
	(6, 17),
	(7, 17),
	(8, 17),
	(9, 17),
	(10, 17),
	(11, 17),
	(12, 17),
	(13, 17),
	(14, 17),
	(15, 17),
	(16, 17),
	(17, 17),
	(18, 17),
	(19, 17),
	(20, 17),
	(21, 17),
	(22, 17);

-- Dumping structure for table snsu_safetrack.scan_logs
DROP TABLE IF EXISTS `scan_logs`;
CREATE TABLE IF NOT EXISTS `scan_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `VerificationCode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `UserID` int(11) DEFAULT '0',
  `Mode` int(11) DEFAULT '0',
  `lat` decimal(10,6) DEFAULT NULL,
  `lng` decimal(10,6) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `scan_type` enum('qr','nfc') COLLATE utf8mb4_unicode_ci DEFAULT 'qr',
  `direction` enum('entry','exit') COLLATE utf8mb4_unicode_ci DEFAULT 'entry',
  `attendance_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gate_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.scan_logs: ~21 rows (approximately)
DELETE FROM `scan_logs`;
INSERT INTO `scan_logs` (`id`, `school_id`, `VerificationCode`, `UserID`, `Mode`, `lat`, `lng`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`, `scan_type`, `direction`, `attendance_status`, `gate_name`, `remarks`) VALUES
	(1, 1, 'VC-0000000001', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:12:57', '2026-05-26 00:12:57', 'active', 0, NULL, 'nfc', 'entry', 'late', NULL, NULL),
	(2, 1, 'VC-0000000002', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:33:26', '2026-05-26 00:33:26', 'active', 0, NULL, 'nfc', 'exit', 'early_out', NULL, NULL),
	(3, 1, 'VC-0000000003', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:33:31', '2026-05-26 00:33:31', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(4, 1, '004', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:37:51', '2026-05-26 00:37:51', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(5, 1, '005', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:37:58', '2026-05-26 00:37:58', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(6, 1, 'VC00006', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:38:48', '2026-05-26 00:38:48', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(7, 1, 'VC00007', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:38:50', '2026-05-26 00:38:50', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(8, 1, 'VC00008', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:51:30', '2026-05-26 00:51:30', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(9, 1, 'VC00009', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:51:33', '2026-05-26 00:51:33', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(10, 1, 'VC00010', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:51:40', '2026-05-26 00:51:40', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(11, 1, 'VC00011', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:51:41', '2026-05-26 00:51:41', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(12, 1, 'VC00012', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:53:57', '2026-05-26 00:53:57', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(13, 1, 'VC00013', 7, 1, NULL, NULL, 7, 7, '2026-05-26 08:54:01', '2026-05-26 00:54:01', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(14, 1, 'VC00014', 7, 0, NULL, NULL, 7, 7, '2026-05-26 08:59:24', '2026-05-26 00:59:24', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(15, 1, 'VC00015', 7, 1, NULL, NULL, 7, 7, '2026-05-26 09:04:04', '2026-05-26 01:04:04', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(16, 1, 'VC00016', 7, 0, NULL, NULL, 7, 7, '2026-05-26 09:30:47', '2026-05-26 01:30:47', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(17, 1, 'VC00017', 7, 1, NULL, NULL, 7, 7, '2026-05-26 09:32:18', '2026-05-26 01:32:18', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(18, 1, 'VC00018', 7, 0, NULL, NULL, 7, 7, '2026-05-26 10:25:09', '2026-05-26 02:25:09', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(19, 1, 'VC00019', 7, 1, NULL, NULL, 7, 7, '2026-05-26 10:25:23', '2026-05-26 02:25:23', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL),
	(20, 1, 'VC00020', 7, 0, NULL, NULL, 7, 7, '2026-05-26 10:25:30', '2026-05-26 02:25:30', 'active', 0, NULL, 'nfc', 'exit', 'present', NULL, NULL),
	(21, 1, 'VC00021', 7, 1, NULL, NULL, 7, 7, '2026-05-26 10:25:32', '2026-05-26 02:25:32', 'active', 0, NULL, 'nfc', 'entry', 'present', NULL, NULL);

-- Dumping structure for table snsu_safetrack.school
DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `alias_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SystemTitle` varchar(150) NOT NULL DEFAULT '0',
  `SchoolName` varchar(255) NOT NULL,
  `SchoolCode` varchar(100) DEFAULT NULL,
  `EducationLevel` enum('JHS','SHS','INTEGRATED') DEFAULT 'INTEGRATED',
  `Region` varchar(100) DEFAULT NULL,
  `Division` varchar(100) DEFAULT NULL,
  `Address` text,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `EmailAddress` varchar(150) DEFAULT NULL,
  `PrincipalID` int(11) DEFAULT NULL,
  `RegistrarID` bigint(20) DEFAULT NULL,
  `Logo` varchar(255) DEFAULT NULL,
  `OfficialTimeIn` time DEFAULT '07:00:00',
  `OfficialTimeOut` time DEFAULT '17:00:00',
  `LateGraceMinutes` int(11) DEFAULT '15',
  `EnableNFC` tinyint(1) DEFAULT '1',
  `EnableQR` tinyint(1) DEFAULT '1',
  `EnableOfflineAttendance` tinyint(1) DEFAULT '1',
  `CurrentSchoolYearID` bigint(20) DEFAULT NULL,
  `ThemeColor` varchar(20) DEFAULT '#004D1A',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cacert_path` longtext,
  `python_path` longtext,
  `port_com` varchar(20) DEFAULT NULL,
  `sms_failed_count` int(11) DEFAULT NULL,
  `sms_low_balance` int(11) DEFAULT '0',
  `total_sent` int(11) DEFAULT '0',
  `sms_provider` varchar(20) DEFAULT 'api',
  `sms_api_url` longtext,
  `sms_api_key` longtext,
  `sms_api_device_id` longtext,
  `sms_last_failed_at` datetime DEFAULT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.school: 4 rows
DELETE FROM `school`;
/*!40000 ALTER TABLE `school` DISABLE KEYS */;
INSERT INTO `school` (`id`, `alias_name`, `code`, `slug`, `SystemTitle`, `SchoolName`, `SchoolCode`, `EducationLevel`, `Region`, `Division`, `Address`, `ContactNumber`, `EmailAddress`, `PrincipalID`, `RegistrarID`, `Logo`, `OfficialTimeIn`, `OfficialTimeOut`, `LateGraceMinutes`, `EnableNFC`, `EnableQR`, `EnableOfflineAttendance`, `CurrentSchoolYearID`, `ThemeColor`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `cacert_path`, `python_path`, `port_com`, `sms_failed_count`, `sms_low_balance`, `total_sent`, `sms_provider`, `sms_api_url`, `sms_api_key`, `sms_api_device_id`, `sms_last_failed_at`, `archived`, `deleted_at`) VALUES
	(1, 'TNHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'TUBAJON NATIONAL HIGH SCHOOL', '304854', 'INTEGRATED', 'CARAGA', 'Dinagat Islands', 'SAN VICENTE, TUBAJON, DINAGAT ISLANDS', '+639128941731', '@tnhs.edu.ph', NULL, NULL, 'schools/Dh5Ux3SGGfc55zBc3WIyndKabOTvbe3Y5TxrQYbP.png', '07:00:00', '17:00:00', 15, 1, 1, 1, NULL, '#f00074', 1, 1, '2026-05-25 11:25:26', '2026-05-26 02:17:57', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL),
	(2, 'LNHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'LORETO NATIONAL HIGH SCHOOL', '12345', 'INTEGRATED', 'CARAGA', 'Dinagat Islands', 'LORATION, DINAGAT ISLANDS', '+639128941731', '@lnhs.edu.ph', NULL, NULL, 'schools/FSFIQsbFgLXoywTw3Z0Yr7fNs4ef56w7cdwqLLh3.jpg', NULL, NULL, 15, 1, 1, 1, NULL, '#00bd03', 1, 1, '2026-05-25 13:34:13', '2026-05-25 05:40:25', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL),
	(3, 'SNNHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'SURIGAO DEL NORTE NATIONAL HIGH SCHOOL', '12346', 'INTEGRATED', 'CARAGA', 'SURIGAO CITY', 'SURIGAO CITY', '+639128941731', '@snnhs.edu.ph', NULL, NULL, 'schools/EI7QcLGXYpZuG9fUechj5RObJvKhBeCAw9YwRIHQ.jpg', '07:00:00', '17:00:00', 15, 1, 1, 1, NULL, '#007bff', 1, 1, '2026-05-25 13:39:59', '2026-05-26 01:35:20', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL),
	(4, 'CRSHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'Caraga Regional Science High School', '654321', 'INTEGRATED', 'CARAGA', 'SURIGAO CITY', 'SURIGAO CITY', '+639128941731', '@crshs.edu.ph', NULL, NULL, 'schools/7lorUNvirlFD3zLx4OOUStN3WdyStreMSHPFCUDc.jpg', NULL, NULL, 15, 1, 1, 1, NULL, '#009699', 1, 1, '2026-05-25 13:44:27', '2026-05-26 01:33:55', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL);
/*!40000 ALTER TABLE `school` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.school_years
DROP TABLE IF EXISTS `school_years`;
CREATE TABLE IF NOT EXISTS `school_years` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `Semester` int(11) NOT NULL DEFAULT '0',
  `AYFrom` year(4) DEFAULT NULL,
  `AYTo` year(4) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.school_years: 4 rows
DELETE FROM `school_years`;
/*!40000 ALTER TABLE `school_years` DISABLE KEYS */;
INSERT INTO `school_years` (`id`, `Semester`, `AYFrom`, `AYTo`, `StartDate`, `EndDate`, `IsActive`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, '2026', '2027', '2026-06-11', '2027-03-31', 1, 1, 1, '2026-05-12 16:45:46', '2026-05-12 08:49:57', 'active', 0, NULL),
	(2, 0, '2025', '2026', '2026-06-15', '2026-08-22', 1, 1, 1, '2026-05-12 16:46:45', '2026-05-12 08:49:58', 'active', 0, NULL),
	(3, 0, '2024', '2025', '2026-04-26', '2026-05-30', 1, 1, 1, '2026-05-12 16:47:08', '2026-05-12 08:47:08', 'active', 0, NULL),
	(4, 0, '2023', '2024', '2026-04-26', '2026-05-30', 1, 1, 1, '2026-05-12 16:47:58', '2026-05-12 08:49:59', 'active', 0, NULL);
/*!40000 ALTER TABLE `school_years` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.sessions: ~1 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('DLtIDPIugN9pZbcgrznxvsVbIlCDKhKHq0LGjYvq', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWk5yeTdkOFgzcE9zQzZvNWdTbTN6eVF5aFdlOUZMMXRqS2ZUQkRxMSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MztzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMToiaHR0cDovL3Bvcy5kZXYuY29tL3NhbGVzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoxMjoic2FsZXMuY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781255352);

-- Dumping structure for table snsu_safetrack.sms_queues
DROP TABLE IF EXISTS `sms_queues`;
CREATE TABLE IF NOT EXISTS `sms_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `PhoneNumber` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Message` longtext COLLATE utf8mb4_unicode_ci,
  `remark` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.sms_queues: ~45 rows (approximately)
DELETE FROM `sms_queues`;
INSERT INTO `sms_queues` (`id`, `school_id`, `PhoneNumber`, `Message`, `remark`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, '639813581109', 'Dear Mr/Mrs. MORALES, \nMARK LOUIEJE MORALES just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 10:14:36 AM. Code: VC-0000000026', 'sent', 0, 0, '2026-05-22 10:14:36', '2026-05-22 02:14:48', 'active', 0, NULL),
	(2, 0, '639813581109', 'Dear Mr/Mrs. MORALES, \nMARK LOUIEJE MORALES just left TUBAJON NATIONAL HIGH SCHOOL (EARLY OUT) @ May 22, 2026 10:14:40 AM. Code: VC-0000000027', 'sent', 0, 0, '2026-05-22 10:14:40', '2026-05-22 02:15:12', 'active', 0, NULL),
	(3, 0, '639813581109', 'Dear Mr/Mrs. MORALES, \nMARK LOUIEJE MORALES just left TUBAJON NATIONAL HIGH SCHOOL (EARLY OUT) @ May 22, 2026 02:24:46 PM. Code: VC-0000000031', 'sent', 0, 0, '2026-05-22 14:24:46', '2026-05-22 06:25:03', 'active', 0, NULL),
	(4, 0, '639813581109', 'Dear Mr/Mrs. MORALES, \nMARK LOUIEJE MORALES just left TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 02:31:37 PM. Code: VC-0000000033', 'sent', 0, 0, '2026-05-22 14:31:37', '2026-05-22 06:31:57', 'active', 0, NULL),
	(5, 0, '639306069019', 'Dear Mr/Mrs. P1_L, \nFNAME LNAME just left TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 02:32:10 PM. Code: VC-0000000035', 'sent', 0, 0, '2026-05-22 14:32:10', '2026-05-22 06:32:51', 'active', 0, NULL),
	(6, 0, '639700920157', 'Dear Mr/Mrs. MAPOY, \nRHOB RYDWYN MAPOY just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 02:35:18 PM. Code: VC-0000000038', 'sent', 0, 0, '2026-05-22 14:35:18', '2026-05-22 06:35:35', 'active', 0, NULL),
	(7, 0, '639700920157', 'Dear Mr/Mrs. MAPOY, \nRHOB RYDWYN MAPOY just left TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 02:35:22 PM. Code: VC-0000000039', 'sent', 0, 0, '2026-05-22 14:35:22', '2026-05-22 06:35:48', 'active', 0, NULL),
	(8, 0, '639464233996', 'Dear Mr/Mrs. Olaco, \nEvonie Cabahit just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:16:55 PM. Code: VC-0000000040', 'sent', 0, 0, '2026-05-22 15:16:55', '2026-05-22 07:17:20', 'active', 0, NULL),
	(9, 0, '639464233996', 'Dear Mr/Mrs. Olaco, \nEvonie Cabahit just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:16:57 PM. Code: VC-0000000041', 'sent', 0, 0, '2026-05-22 15:16:57', '2026-05-22 07:17:33', 'active', 0, NULL),
	(10, 0, '639070708095', 'Dear Mr/Mrs. IMALAY, \nRUSHEY GIE IMALAY just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:17:07 PM. Code: VC-0000000042', 'sent', 0, 0, '2026-05-22 15:17:07', '2026-05-22 07:17:47', 'active', 0, NULL),
	(11, 0, '639858224872', 'Dear Mr/Mrs. Garcia, \nJustin Ganaganag just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:17:11 PM. Code: VC-0000000043', 'sent', 0, 0, '2026-05-22 15:17:11', '2026-05-22 07:19:31', 'active', 0, NULL),
	(12, 0, '639641012016', 'Dear Mr/Mrs. Kiasan, \nAlthea Dita just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:17:14 PM. Code: VC-0000000044', 'sent', 0, 0, '2026-05-22 15:17:14', '2026-05-22 07:19:44', 'active', 0, NULL),
	(13, 0, '639813581109', 'Dear Mr/Mrs. MORALES, \nMARK LOUIJE MORALES just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:17:17 PM. Code: VC-0000000045', 'sent', 0, 0, '2026-05-22 15:17:17', '2026-05-22 07:19:57', 'active', 0, NULL),
	(14, 0, '639464233996', 'Dear Mr/Mrs. Olaco, \nEvonie Cabahit just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:17:40 PM. Code: VC-0000000046', 'sent', 0, 0, '2026-05-22 15:17:40', '2026-05-22 07:20:10', 'active', 0, NULL),
	(15, 0, '639070708095', 'Dear Mr/Mrs. IMALAY, \nRUSHEY GIE IMALAY just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:17:48 PM. Code: VC-0000000047', 'sent', 0, 0, '2026-05-22 15:17:48', '2026-05-22 07:20:24', 'active', 0, NULL),
	(16, 0, '639813581109', 'Dear Mr/Mrs. MORALES, \nMARK LOUIJE MORALES just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:17:52 PM. Code: VC-0000000048', 'sent', 0, 0, '2026-05-22 15:17:52', '2026-05-22 07:20:37', 'active', 0, NULL),
	(17, 0, '639641012016', 'Dear Mr/Mrs. Kiasan, \nAlthea Dita just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:17:57 PM. Code: VC-0000000049', 'sent', 0, 0, '2026-05-22 15:17:57', '2026-05-22 07:20:50', 'active', 0, NULL),
	(18, 0, '639858224872', 'Dear Mr/Mrs. Garcia, \nJustin Ganaganag just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:18:02 PM. Code: VC-0000000050', 'sent', 0, 0, '2026-05-22 15:18:02', '2026-05-22 07:21:03', 'active', 0, NULL),
	(19, 0, '639817101537', 'Dear Mr/Mrs. PACUNLA, \nLYCA REMOLISAN just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:18:31 PM. Code: VC-0000000051', 'sent', 0, 0, '2026-05-22 15:18:31', '2026-05-22 07:21:17', 'active', 0, NULL),
	(20, 0, '639468019180', 'Dear Mr/Mrs. ANGOB, \nJV ANGOB just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:29:17 PM. Code: VC-0000000054', 'sent', 0, 0, '2026-05-22 15:29:17', '2026-05-22 07:29:31', 'active', 0, NULL),
	(21, 0, '639855793446', 'Dear Mr/Mrs. DELA PENA, \nKHALIL DELA PEÃ…Æ’A just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:29:21 PM. Code: VC-0000000055', 'sent', 0, 0, '2026-05-22 15:29:21', '2026-05-26 00:31:10', 'active', 0, NULL),
	(22, 0, '639468019180', 'Dear Mr/Mrs. ANGOB, \nJV ANGOB just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:30:14 PM. Code: VC-0000000059', 'sent', 0, 0, '2026-05-22 15:30:14', '2026-05-22 07:33:56', 'active', 0, NULL),
	(23, 0, '639855793446', 'Dear Mr/Mrs. DELA PENA, \nKHALIL DELA PEÃ…Æ’A just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:30:18 PM. Code: VC-0000000060', 'sent', 0, 0, '2026-05-22 15:30:18', '2026-05-26 00:31:24', 'active', 0, NULL),
	(24, 3, '639128941731', 'Dear Mr/Mrs. loreto P,\nloreto PILOTON just entered LORETO NATIONAL HIGH SCHOOL (LATE) @ May 25, 2026 11:14:33 AM. Code: VC-0000000014', 'sent', 0, 0, '2026-05-25 11:14:33', '2026-05-26 00:31:38', 'active', 0, NULL),
	(25, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 26, 2026 08:12:57 AM. Code: VC-0000000001', 'sent', NULL, NULL, '2026-05-26 08:12:57', '2026-05-26 00:31:53', 'active', 0, NULL),
	(26, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL (EARLY OUT) @ May 26, 2026 08:33:26 AM. Code: VC-0000000002', 'sent', NULL, NULL, '2026-05-26 08:33:27', '2026-05-26 00:34:29', 'active', 0, NULL),
	(27, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:33:31 AM. Code: VC-0000000003', 'sent', NULL, NULL, '2026-05-26 08:33:31', '2026-05-26 00:34:44', 'active', 0, NULL),
	(28, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:37:51 AM. Code: 004', 'sent', NULL, NULL, '2026-05-26 08:37:51', '2026-05-26 00:47:17', 'active', 0, NULL),
	(29, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:37:58 AM. Code: 005', 'sent', NULL, NULL, '2026-05-26 08:37:58', '2026-05-26 00:47:44', 'active', 0, NULL),
	(30, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:38:48 AM. Code: VC00006', 'sent', NULL, NULL, '2026-05-26 08:38:48', '2026-05-26 00:49:34', 'active', 0, NULL),
	(31, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:38:50 AM. Code: VC00007', 'sent', NULL, NULL, '2026-05-26 08:38:50', '2026-05-26 00:50:01', 'active', 0, NULL),
	(32, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:51:30 AM. Code: VC00008', 'sent', NULL, NULL, '2026-05-26 08:51:30', '2026-05-26 00:51:56', 'active', 0, NULL),
	(33, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:51:33 AM. Code: VC00009', 'sent', NULL, NULL, '2026-05-26 08:51:33', '2026-05-26 00:52:23', 'active', 0, NULL),
	(34, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:51:40 AM. Code: VC00010', 'sent', NULL, NULL, '2026-05-26 08:51:40', '2026-05-26 00:52:50', 'active', 0, NULL),
	(35, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:51:41 AM. Code: VC00011', 'sent', NULL, NULL, '2026-05-26 08:51:41', '2026-05-26 00:53:18', 'active', 0, NULL),
	(36, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:53:57 AM. Code: VC00012', 'sent', NULL, NULL, '2026-05-26 08:53:57', '2026-05-26 00:54:20', 'active', 0, NULL),
	(37, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:54:01 AM. Code: VC00013', 'sent', NULL, NULL, '2026-05-26 08:54:01', '2026-05-26 00:54:47', 'active', 0, NULL),
	(38, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 08:59:24 AM. Code: VC00014', 'sent', NULL, NULL, '2026-05-26 08:59:24', '2026-05-26 01:02:53', 'active', 0, NULL),
	(39, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 09:04:04 AM. Code: VC00015', 'sent', NULL, NULL, '2026-05-26 09:04:04', '2026-05-26 01:04:30', 'active', 0, NULL),
	(40, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 09:30:47 AM. Code: VC00016', 'sent', NULL, NULL, '2026-05-26 09:30:47', '2026-05-26 01:31:11', 'active', 0, NULL),
	(41, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 09:32:18 AM. Code: VC00017', 'sent', NULL, NULL, '2026-05-26 09:32:18', '2026-05-26 01:32:43', 'active', 0, NULL),
	(42, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 10:25:09 AM. Code: VC00018', 'sent', NULL, NULL, '2026-05-26 10:25:09', '2026-05-26 02:25:32', 'active', 0, NULL),
	(43, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 10:25:23 AM. Code: VC00019', 'pending', NULL, NULL, '2026-05-26 10:25:23', '2026-05-26 05:44:17', 'active', 0, NULL),
	(44, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just left TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 10:25:30 AM. Code: VC00020', 'sent', NULL, NULL, '2026-05-26 10:25:30', '2026-05-26 02:27:20', 'active', 0, NULL),
	(45, 1, '639128941731', 'Dear Mr/Mrs. PILOTON,\n\nloreto PILOTON just entered TUBAJON NATIONAL HIGH SCHOOL @ May 26, 2026 10:25:32 AM. Code: VC00021', 'sent', NULL, NULL, '2026-05-26 10:25:32', '2026-05-26 02:28:25', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.strands
DROP TABLE IF EXISTS `strands`;
CREATE TABLE IF NOT EXISTS `strands` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `StrandCode` varchar(20) DEFAULT NULL,
  `StrandName` varchar(100) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `StrandCode` (`StrandCode`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.strands: 9 rows
DELETE FROM `strands`;
/*!40000 ALTER TABLE `strands` DISABLE KEYS */;
INSERT INTO `strands` (`id`, `StrandCode`, `StrandName`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 'STEM', 'Science, Technology, Engineering and Mathematics', 1, 1, '2026-05-12 16:22:54', '2026-05-12 08:22:54', 'active', 0, NULL),
	(2, 'HUMSS', 'Humanities and Social Sciences', 1, 1, '2026-05-12 16:23:03', '2026-05-12 08:23:03', 'active', 0, NULL),
	(3, 'ABM', 'Accountancy, Business and Management', 1, 1, '2026-05-12 16:23:10', '2026-05-12 08:23:10', 'active', 0, NULL),
	(4, 'GAS', 'General Academic Strand', 1, 1, '2026-05-12 16:23:16', '2026-05-12 08:23:16', 'active', 0, NULL),
	(5, 'TVL-HE', 'Home Economics', 1, 1, '2026-05-12 16:23:26', '2026-05-12 08:23:26', 'active', 0, NULL),
	(6, 'TVL-ICT', 'Information and Communications Technology', 1, 1, '2026-05-12 16:23:36', '2026-05-12 08:23:36', 'active', 0, NULL),
	(7, 'TVL-IA', 'Industrial Arts', 1, 1, '2026-05-12 16:23:44', '2026-05-12 08:23:44', 'active', 0, NULL),
	(8, 'TVL-AFA', 'Agri-Fishery Arts', 1, 1, '2026-05-12 16:24:08', '2026-05-12 08:24:08', 'active', 0, NULL),
	(0, 'N/A', 'N/A', 0, 0, '0000-00-00 00:00:00', '2026-05-12 08:58:08', 'active', 0, NULL);
/*!40000 ALTER TABLE `strands` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.students
DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT '0',
  `GuardianID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `LRN` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `FirstName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `MiddleName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Suffix` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Sex` enum('Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Section` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PhoneNumber` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Strand` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CurrentStatus` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `YearLevel` int(11) DEFAULT NULL,
  `BirthDate` date DEFAULT NULL,
  `BirthPlace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Religion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Remark` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` tinyint(4) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.students: ~1 rows (approximately)
DELETE FROM `students`;
INSERT INTO `students` (`id`, `school_id`, `GuardianID`, `UserID`, `LRN`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `Sex`, `Section`, `PhoneNumber`, `filepath`, `Strand`, `CurrentStatus`, `YearLevel`, `BirthDate`, `BirthPlace`, `Religion`, `Remark`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 16, 9, '154542121454', 'loreto', 'stude', 'PILOTON', NULL, NULL, NULL, '+639128941731', NULL, 'HUMSS', NULL, 8, NULL, NULL, NULL, NULL, 1, 1, '2026-05-26 15:13:25', '2026-05-26 07:13:25', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.students_contact_person
DROP TABLE IF EXISTS `students_contact_person`;
CREATE TABLE IF NOT EXISTS `students_contact_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `StudentID` int(11) NOT NULL DEFAULT '0',
  `UserID` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.students_contact_person: 0 rows
DELETE FROM `students_contact_person`;
/*!40000 ALTER TABLE `students_contact_person` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_contact_person` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.subjects
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `SubjectCode` varchar(50) DEFAULT NULL,
  `SubjectName` varchar(255) DEFAULT NULL,
  `GradeLevelID` bigint(20) NOT NULL,
  `StrandID` bigint(20) DEFAULT NULL,
  `SemesterID` bigint(20) DEFAULT NULL,
  `IsCore` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.subjects: 0 rows
DELETE FROM `subjects`;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.support_tickets
DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','pending','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_minutes` int(11) DEFAULT NULL,
  `is_incident` tinyint(1) NOT NULL DEFAULT '0',
  `source` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `status_record` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_no_unique` (`ticket_no`),
  KEY `support_tickets_status_priority_index` (`status`,`priority`),
  KEY `support_tickets_school_status_index` (`school_id`,`status`),
  KEY `support_tickets_assigned_status_index` (`assigned_to`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.support_tickets: ~9 rows (approximately)
DELETE FROM `support_tickets`;
INSERT INTO `support_tickets` (`id`, `ticket_no`, `school_id`, `user_id`, `assigned_to`, `subject`, `description`, `priority`, `status`, `category`, `resolved_at`, `attachment`, `response_time_minutes`, `is_incident`, `source`, `ip_address`, `user_agent`, `status_record`, `archived`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
	(1, 'ST-2026-000001', NULL, 1, NULL, 'sadasd', 'asdasd', 'low', 'closed', 'dasdasd', '2026-05-25 19:23:38', NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:20:05', '2026-05-25 11:25:57'),
	(2, 'ST-2026-000002', NULL, 1, NULL, 'hljklhjkl', 'lhjklhjkl', 'medium', 'open', 'dasdasd', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:33:03', '2026-05-25 11:33:03'),
	(3, 'ST-2026-000003', NULL, 1, NULL, 'hljklhjkl', '41451', 'low', 'in_progress', 'dasdasd', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:33:57', '2026-05-25 11:35:13'),
	(4, 'ST-2026-000004', NULL, 1, NULL, 'hljklhjkl', 'rjhgjh', 'high', 'in_progress', 'student_management', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:38:24', '2026-05-25 11:38:34'),
	(5, 'ST-2026-000005', NULL, 1, NULL, 'hljklhjklgk', 'kghjkghjk', 'medium', 'open', 'qr_nfc', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:42:33', '2026-05-25 11:42:33'),
	(6, 'ST-2026-000006', NULL, 1, NULL, 'hljklhjklgk', 'kghjkhjk', 'medium', 'open', 'qr_nfc', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:48:46', '2026-05-25 11:48:46'),
	(7, 'ST-2026-000007', NULL, 1, NULL, 'hljklhjkl', 'jfghjfghj', 'high', 'open', 'attendance', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 12:01:00', '2026-05-25 12:01:00'),
	(8, 'ST-2026-000008', NULL, 1, NULL, 'hljklhjklgk', 'kghjkhjk', 'medium', 'open', 'student_management', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 12:06:20', '2026-05-25 12:06:20'),
	(9, 'ST-2026-000009', NULL, 1, NULL, 'hljklhjkl', 'kghjkghjkhjk', 'critical', 'closed', 'qr_nfc', '2026-05-26 10:23:54', NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-26 02:23:39', '2026-05-26 02:23:57');

-- Dumping structure for table snsu_safetrack.support_ticket_replies
DROP TABLE IF EXISTS `support_ticket_replies`;
CREATE TABLE IF NOT EXISTS `support_ticket_replies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `status_record` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_replies_ticket_created_index` (`ticket_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.support_ticket_replies: ~6 rows (approximately)
DELETE FROM `support_ticket_replies`;
INSERT INTO `support_ticket_replies` (`id`, `ticket_id`, `user_id`, `message`, `attachment`, `is_internal`, `status_record`, `archived`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Hello po', NULL, 0, 'active', 0, 1, 1, '2026-05-25 11:21:04', '2026-05-25 11:21:04'),
	(2, 1, 1, 'jjkk', NULL, 0, 'active', 0, 1, 1, '2026-05-25 11:25:44', '2026-05-25 11:25:44'),
	(3, 1, 1, 'jghjghj', NULL, 0, 'active', 0, 1, 1, '2026-05-25 11:31:22', '2026-05-25 11:31:22'),
	(4, 9, 1, 'kghjkghjk', NULL, 0, 'active', 0, 1, 1, '2026-05-26 02:23:43', '2026-05-26 02:23:43'),
	(5, 9, 1, '.m,.', NULL, 0, 'active', 0, 1, 1, '2026-05-26 02:23:45', '2026-05-26 02:23:45'),
	(6, 9, 1, 'lhjkljkl', NULL, 0, 'active', 0, 1, 1, '2026-05-26 02:23:48', '2026-05-26 02:23:48');

-- Dumping structure for table snsu_safetrack.suspicious_activities
DROP TABLE IF EXISTS `suspicious_activities`;
CREATE TABLE IF NOT EXISTS `suspicious_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'warning',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `detected_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suspicious_activities_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.suspicious_activities: 6 rows
DELETE FROM `suspicious_activities`;
/*!40000 ALTER TABLE `suspicious_activities` DISABLE KEYS */;
INSERT INTO `suspicious_activities` (`id`, `user_id`, `type`, `severity`, `description`, `ip_address`, `meta`, `detected_at`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'failed_logins', 'critical', 'Multiple failed login attempts detected.', '::1', '{"attempts": 5}', '2026-05-25 09:15:57', '2026-05-25 09:15:57', '2026-05-25 09:15:57'),
	(2, 2, 'multiple_sessions', 'warning', 'Multiple simultaneous active sessions detected.', NULL, '{"sessions": 3}', '2026-05-25 09:16:20', '2026-05-25 09:16:20', '2026-05-25 09:16:20'),
	(3, 2, 'multiple_sessions', 'warning', 'Multiple simultaneous active sessions detected.', NULL, '{"sessions": 8}', '2026-05-25 09:48:11', '2026-05-25 09:48:11', '2026-05-25 09:48:11'),
	(4, 2, 'multiple_sessions', 'warning', 'Multiple simultaneous active sessions detected.', NULL, '{"sessions": 9}', '2026-05-25 12:09:08', '2026-05-25 12:09:08', '2026-05-25 12:09:08'),
	(5, NULL, 'failed_logins', 'critical', 'Multiple failed login attempts detected.', '::1', '{"attempts": 5}', '2026-06-09 09:36:10', '2026-06-09 09:36:10', '2026-06-09 09:36:10'),
	(6, 10, 'multiple_sessions', 'warning', 'Multiple simultaneous active sessions detected.', NULL, '{"sessions": 3}', '2026-06-09 09:47:54', '2026-06-09 09:47:54', '2026-06-09 09:47:54');
/*!40000 ALTER TABLE `suspicious_activities` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.system_settings
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cacert_path` longtext COLLATE utf8mb4_unicode_ci,
  `python_path` longtext COLLATE utf8mb4_unicode_ci,
  `port_com` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_failed_count` int(11) DEFAULT '0',
  `sms_low_balance` int(11) DEFAULT '0',
  `total_sent` int(11) DEFAULT '0',
  `sms_provider` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api',
  `sms_api_url` longtext COLLATE utf8mb4_unicode_ci,
  `sms_api_key` longtext COLLATE utf8mb4_unicode_ci,
  `sms_api_device_id` longtext COLLATE utf8mb4_unicode_ci,
  `sms_last_failed_at` datetime DEFAULT NULL,
  `gsm_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `sms_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `error_message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.system_settings: 1 rows
DELETE FROM `system_settings`;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` (`id`, `cacert_path`, `python_path`, `port_com`, `sms_failed_count`, `sms_low_balance`, `total_sent`, `sms_provider`, `sms_api_url`, `sms_api_key`, `sms_api_device_id`, `sms_last_failed_at`, `gsm_enabled`, `sms_enabled`, `remark`, `error_message`, `created_at`, `updated_at`) VALUES
	(1, 'C:\\wamp64\\bin\\php\\php8.4.15\\extras\\ssl\\cacert.pem', 'C:/Users/USER/AppData/Local/Programs/Python/Python313/python.exe', 'COM5', 6, 0, 23, 'gsm', NULL, NULL, NULL, '2026-05-25 18:50:39', 1, 1, '1', '', '2026-05-25 10:03:34', '2026-05-26 02:28:25');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `system_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_super_admin` int(10) unsigned NOT NULL DEFAULT '0',
  `school_id` int(11) NOT NULL DEFAULT '0',
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `filepath` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `qr_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `nfc_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conn_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_type` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `avatar` longtext COLLATE utf8mb4_unicode_ci,
  `fcm_token` longtext COLLATE utf8mb4_unicode_ci,
  `profile_pic` longtext COLLATE utf8mb4_unicode_ci,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` longtext COLLATE utf8mb4_unicode_ci,
  `verified` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_activity_at` datetime DEFAULT NULL,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.users: 2 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `system_name`, `is_super_admin`, `school_id`, `tenant_id`, `filepath`, `qr_code`, `nfc_code`, `conn_id`, `user_type`, `name`, `email`, `email_verified_at`, `avatar`, `fcm_token`, `profile_pic`, `password`, `remember_token`, `google_id`, `verified`, `created_by`, `updated_by`, `created_at`, `updated_at`, `last_activity_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, '0', 0, 0, 0, '0', '0', NULL, 0, 0, 'Antonio Jr Piloton', 'apiloton1@snsu.edu.ph', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocICxZuvbmnED8B5vH9nAzZ75oj4EyyJOq04YOMO7jxaSaNGZqsv=s96-c', NULL, NULL, '$2y$12$XxNZYMM3BRXkyG7uxpcJ..t20eaiHvmc94Bso6fQ59X7lMeaxFmSy', 'VNcae30NHgfl3aSRItLJ4rdf0u1zxK2ckbIAWRIOOTK44puXUxhHcYLSJ68r', '117952079880321133596', 1, NULL, NULL, '2026-06-11 14:22:46', '2026-06-11 06:25:50', '2026-06-11 14:22:46', NULL, 0, NULL),
	(3, '0', 0, 0, 1, '0', '0', NULL, 0, 0, 'ANTONIO JR. PILOTON', 'antoniojrpiloton@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c', NULL, NULL, '$2y$12$RYW2A3QIydsMUY99to4OnOMGrQxet5Lnvyi2DtfzTYm7jZnQ6oIqm', 'fY82EnXs7AeU4yGOxRE8rR1IrgC5Hgg3vGgxNN1eu9dDR6Fk4PB01aUpTyK6', '112090857259724913006', 1, NULL, NULL, '2026-06-11 15:36:53', '2026-06-12 09:09:12', '2026-06-12 17:09:12', NULL, 0, NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.user_phones
DROP TABLE IF EXISTS `user_phones`;
CREATE TABLE IF NOT EXISTS `user_phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `UserID` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.user_phones: 0 rows
DELETE FROM `user_phones`;
/*!40000 ALTER TABLE `user_phones` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_phones` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.year_level
DROP TABLE IF EXISTS `year_level`;
CREATE TABLE IF NOT EXISTS `year_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `YearLevel` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.year_level: 0 rows
DELETE FROM `year_level`;
/*!40000 ALTER TABLE `year_level` DISABLE KEYS */;
/*!40000 ALTER TABLE `year_level` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
