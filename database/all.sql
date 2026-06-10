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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.audits: ~33 rows (approximately)
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
	(33, 'App\\Models\\User', 11, 'created', 'App\\Models\\LoginActivity', 15, '[]', '{"user_id":11,"email":"antoniojrpiloton@gmail.com","ip_address":"::1","user_agent":"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36 Edg\\/149.0.0.0","device":"WebKit","platform":"Windows","browser":"Edge","status":"success","logged_in_at":"2026-06-09 17:50:36","id":15}', 'http://pos.dev.com/auth/google/callback?authuser=3&code=4%2F0AdkVLPwi6UfUzzDQi7wLVMzpTajVQiHF0a53JnHfFkha5i4_gUuRojCES2ZAJPWbe2BF3g&iss=https%3A%2F%2Faccounts.google.com&prompt=none&scope=email%20profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20openid&state=8DXyFolBYPr6xvk9se0TFQrt2mjbkl46ohP5t1GD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, '2026-06-09 09:50:36', '2026-06-09 09:50:36');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.backups: ~0 rows (approximately)
DELETE FROM `backups`;
INSERT INTO `backups` (`id`, `filename`, `filepath`, `backup_type`, `file_size`, `status`, `created_by`, `notes`, `created_at`, `deleted_at`, `updated_at`) VALUES
	(1, '2026-05-28-09-11-08.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-28-09-11-08.zip', 'db', 20376, 'completed', 1, NULL, '2026-05-28 01:11:10', NULL, '2026-05-28 01:11:10');

-- Dumping structure for table snsu_safetrack.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.cache: ~2 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('safetrack-cache-last_activity_11', 'b:1;', 1781060519),
	('safetrack-cache-spatie.permission.cache', 'a:3:{s:5:"alias";a:5:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:7:"details";s:1:"d";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:37:{i:0;a:5:{s:1:"a";i:1;s:1:"b";s:14:"dashboard.view";s:1:"c";s:14:"View dashboard";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:1;a:5:{s:1:"a";i:2;s:1:"b";s:12:"scanner.view";s:1:"c";s:19:"View scanner module";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:2;a:5:{s:1:"a";i:3;s:1:"b";s:9:"logs.view";s:1:"c";s:13:"View all logs";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:3;a:5:{s:1:"a";i:4;s:1:"b";s:15:"logs.users.view";s:1:"c";s:14:"View user logs";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:4;a:5:{s:1:"a";i:5;s:1:"b";s:13:"students.view";s:1:"c";s:13:"View students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:5;a:5:{s:1:"a";i:6;s:1:"b";s:15:"students.create";s:1:"c";s:15:"Create students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:6;a:5:{s:1:"a";i:7;s:1:"b";s:15:"students.update";s:1:"c";s:15:"Update students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:7;a:5:{s:1:"a";i:8;s:1:"b";s:15:"students.delete";s:1:"c";s:15:"Delete students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:8;a:5:{s:1:"a";i:9;s:1:"b";s:12:"parents.view";s:1:"c";s:12:"View parents";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:9;a:5:{s:1:"a";i:10;s:1:"b";s:14:"parents.create";s:1:"c";s:14:"Create parents";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:10;a:5:{s:1:"a";i:11;s:1:"b";s:14:"parents.update";s:1:"c";s:14:"Update parents";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:11;a:5:{s:1:"a";i:12;s:1:"b";s:14:"parents.delete";s:1:"c";s:14:"Delete parents";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:12;a:5:{s:1:"a";i:13;s:1:"b";s:14:"employees.view";s:1:"c";s:14:"View employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:13;a:5:{s:1:"a";i:14;s:1:"b";s:16:"employees.create";s:1:"c";s:16:"Create employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:14;a:5:{s:1:"a";i:15;s:1:"b";s:16:"employees.update";s:1:"c";s:16:"Update employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:15;a:5:{s:1:"a";i:16;s:1:"b";s:16:"employees.delete";s:1:"c";s:16:"Delete employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:16;a:5:{s:1:"a";i:17;s:1:"b";s:8:"sms.view";s:1:"c";s:14:"View SMS queue";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:17;a:5:{s:1:"a";i:18;s:1:"b";s:8:"sms.send";s:1:"c";s:17:"Send SMS messages";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:18;a:5:{s:1:"a";i:19;s:1:"b";s:17:"school-users.view";s:1:"c";s:17:"View school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:19;a:5:{s:1:"a";i:20;s:1:"b";s:19:"school-users.create";s:1:"c";s:19:"Create school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:20;a:5:{s:1:"a";i:21;s:1:"b";s:19:"school-users.update";s:1:"c";s:19:"Update school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:21;a:5:{s:1:"a";i:22;s:1:"b";s:19:"school-users.delete";s:1:"c";s:19:"Delete school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:22;a:5:{s:1:"a";i:23;s:1:"b";s:13:"settings.view";s:1:"c";s:13:"View settings";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:23;a:5:{s:1:"a";i:24;s:1:"b";s:15:"settings.update";s:1:"c";s:15:"Update settings";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:24;a:5:{s:1:"a";i:25;s:1:"b";s:16:"permissions.view";s:1:"c";s:16:"View permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:25;a:5:{s:1:"a";i:26;s:1:"b";s:18:"permissions.create";s:1:"c";s:18:"Create permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:26;a:5:{s:1:"a";i:27;s:1:"b";s:18:"permissions.update";s:1:"c";s:18:"Update permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:27;a:5:{s:1:"a";i:28;s:1:"b";s:18:"permissions.delete";s:1:"c";s:18:"Delete permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:28;a:5:{s:1:"a";i:29;s:1:"b";s:10:"roles.view";s:1:"c";s:10:"View roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:29;a:5:{s:1:"a";i:30;s:1:"b";s:12:"roles.create";s:1:"c";s:12:"Create roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:30;a:5:{s:1:"a";i:31;s:1:"b";s:12:"roles.update";s:1:"c";s:12:"Update roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:31;a:5:{s:1:"a";i:32;s:1:"b";s:12:"roles.delete";s:1:"c";s:12:"Delete roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:32;a:5:{s:1:"a";i:33;s:1:"b";s:12:"schools.view";s:1:"c";s:12:"View schools";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:33;a:5:{s:1:"a";i:34;s:1:"b";s:14:"schools.create";s:1:"c";s:14:"Create schools";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:34;a:5:{s:1:"a";i:35;s:1:"b";s:14:"schools.update";s:1:"c";s:14:"Update schools";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:35;a:5:{s:1:"a";i:36;s:1:"b";s:14:"schools.delete";s:1:"c";s:14:"Delete schools";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:36;a:5:{s:1:"a";i:37;s:1:"b";s:19:"support-center.view";s:1:"c";s:20:"Allow report support";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:3;i:1;i:4;}}}s:5:"roles";a:3:{i:0;a:4:{s:1:"a";i:4;s:1:"b";s:5:"admin";s:1:"c";s:5:"admin";s:1:"d";s:3:"web";}i:1;a:4:{s:1:"a";i:6;s:1:"b";s:2:"SA";s:1:"c";s:10:"SuperAdmin";s:1:"d";s:3:"web";}i:2;a:4:{s:1:"a";i:3;s:1:"b";s:9:"employees";s:1:"c";s:9:"employees";s:1:"d";s:3:"web";}}}', 1781141757);

-- Dumping structure for table snsu_safetrack.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.cache_locks: ~0 rows (approximately)
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.login_activities: 15 rows
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
	(15, 11, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-06-09 09:50:36', NULL, '2026-06-09 09:50:36', '2026-06-09 09:50:36');
/*!40000 ALTER TABLE `login_activities` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.migrations: ~2 rows (approximately)
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

-- Dumping data for table snsu_safetrack.model_has_roles: ~8 rows (approximately)
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

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

-- Dumping data for table snsu_safetrack.permissions: ~36 rows (approximately)
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
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.pos_categories: 30 rows
DELETE FROM `pos_categories`;
/*!40000 ALTER TABLE `pos_categories` DISABLE KEYS */;
INSERT INTO `pos_categories` (`id`, `name`, `description`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 'Beverages', 'Soft drinks, juices, coffee, water', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(2, 'Snacks', 'Chips, biscuits, candies', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(3, 'Canned Goods', 'Canned food products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(4, 'Instant Foods', 'Noodles, soups, ready-to-eat products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(5, 'Rice and Grains', 'Rice, corn, grains', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(6, 'Condiments', 'Sauces, vinegar, soy sauce, spices', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(7, 'Dairy Products', 'Milk, cheese, butter, yogurt', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(8, 'Frozen Foods', 'Frozen meat and frozen products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(9, 'Bread and Bakery', 'Bread, cakes, pastries', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(10, 'Meat and Poultry', 'Fresh and processed meat products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(11, 'Seafood', 'Fish and seafood products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(12, 'Fruits and Vegetables', 'Fresh produce', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(13, 'Personal Care', 'Soap, shampoo, toothpaste', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(14, 'Health and Beauty', 'Cosmetics and beauty products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(15, 'Medicines', 'OTC medicines and supplements', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(16, 'Baby Products', 'Diapers, milk, baby care items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(17, 'Household Supplies', 'Cleaning and household products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(18, 'Laundry Supplies', 'Detergents and fabric conditioners', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(19, 'School and Office Supplies', 'School and office items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(20, 'Pet Supplies', 'Pet food and pet care products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(21, 'Hardware', 'Tools and hardware supplies', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(22, 'Electrical Supplies', 'Electrical materials and accessories', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(23, 'Mobile Accessories', 'Chargers, cables, earphones', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(24, 'Clothing', 'Apparel and garments', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(25, 'Footwear', 'Shoes and slippers', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(26, 'Kitchenware', 'Kitchen tools and utensils', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(27, 'Toys', 'Toys and recreational items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(28, 'Gift Items', 'Gift products and souvenirs', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(29, 'Services', 'Non-inventory service items', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL),
	(30, 'Others', 'Miscellaneous products', NULL, NULL, '2026-06-10 09:03:39', '2026-06-10 01:03:39', 'active', 0, NULL);
/*!40000 ALTER TABLE `pos_categories` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.pos_products
DROP TABLE IF EXISTS `pos_products`;
CREATE TABLE IF NOT EXISTS `pos_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `category_id` (`category_id`),
  KEY `unit_id` (`unit_id`),
  KEY `barcode` (`barcode`),
  KEY `sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_products: ~6 rows (approximately)
DELETE FROM `pos_products`;
INSERT INTO `pos_products` (`id`, `tenant_id`, `category_id`, `unit_id`, `barcode`, `sku`, `name`, `description`, `cost_price`, `selling_price`, `wholesale_price`, `reorder_level`, `image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, NULL, NULL, NULL, '26212', 'Mega Sardines in Tomato Sauce | 155g', 'Mega Sardines offers the freshest and most delicious sardines, packed within 12 hours from catching to canning. Available in EASY-OPEN-CANS suited for your in-home convenience.', 18.00, 34.00, 23.00, 100, 'products/2DzT1ebRk9GmJXpz6eUWBalnXhlqmpdvPCxGMGMq.webp', 11, 11, '2026-06-10 08:57:45', '2026-06-10 02:27:45', 'active', 0, NULL),
	(2, 0, 5, 1, NULL, NULL, 'P20 Benteng Bigas Meron Na', NULL, 20.00, 20.00, 20.00, 100, NULL, 11, 11, '2026-06-10 09:07:57', '2026-06-10 01:07:57', 'active', 0, NULL),
	(3, 0, NULL, NULL, NULL, NULL, 'MARKER, Permanent, Black', NULL, 15.84, 18.00, 16.50, 100, 'products/aP4g0fHKizfp3GWznNHLj2DaeLHp6m3v3zueLl0B.jpg', 11, 11, '2026-06-10 09:13:18', '2026-06-10 02:07:28', 'active', 0, NULL),
	(4, 0, 30, 2, '14111531-RE-B01', NULL, 'RECORD BOOK, 300 pages', NULL, 89.71, 92.00, 91.00, 100, NULL, 11, 11, '2026-06-10 09:14:53', '2026-06-10 01:14:53', 'active', 0, NULL),
	(5, 0, 30, 16, '47131803-DS-A01', NULL, 'SOLBA DISINFECTANT SPRAY', NULL, 289.76, 298.00, 290.00, 100, 'products/3UHlZluiK9wHfPXHVpZHXLf98Lb4IQgz2rULdpbN.jpg', 11, 11, '2026-06-10 09:16:00', '2026-06-10 02:11:05', 'active', 0, NULL),
	(6, 0, 22, 2, '39101628-LB-L01', NULL, 'LIGHT EMITTING DIODE (LED), Light Bulb, 7 watts', NULL, 89.73, 100.00, 92.00, 100, 'products/8g9sd19pYV9xgK3arKctxGabQBmzDBEsOv6rt12N.jpg', 11, 11, '2026-06-10 09:17:19', '2026-06-10 02:28:15', 'active', 0, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_product_price_histories: ~5 rows (approximately)
DELETE FROM `pos_product_price_histories`;
INSERT INTO `pos_product_price_histories` (`id`, `tenant_id`, `product_id`, `cost_price`, `new_cost_price`, `selling_price`, `new_selling_price`, `wholesale_price`, `new_wholesale_price`, `reason`, `remarks`, `effective_date`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 1, 18.00, 18.00, 26.00, 26.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:20:22', 11, 11, '2026-06-10 10:20:22', '2026-06-10 02:20:22', 'active', 0, NULL),
	(2, 0, 5, 289.76, 289.76, 298.00, 298.00, 290.00, 290.00, NULL, 'Product price updated', '2026-06-10 10:11:05', 11, 11, '2026-06-10 10:11:05', '2026-06-10 02:11:05', 'active', 0, NULL),
	(3, 0, 1, 18.00, 18.00, 25.00, 25.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:21:21', 11, 11, '2026-06-10 10:21:21', '2026-06-10 02:21:21', 'active', 0, NULL),
	(4, 0, 1, 18.00, 18.00, 35.00, 35.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:25:05', 11, 11, '2026-06-10 10:25:05', '2026-06-10 02:25:05', 'active', 0, NULL),
	(5, 0, 1, 18.00, 18.00, 35.00, 34.00, 23.00, 23.00, NULL, 'Product price updated', '2026-06-10 10:27:45', 11, 11, '2026-06-10 10:27:45', '2026-06-10 02:27:45', 'active', 0, NULL),
	(6, 0, 6, 89.73, 89.73, 95.00, 100.00, 92.00, 92.00, NULL, 'Product price updated', '2026-06-10 10:28:15', 11, 11, '2026-06-10 10:28:15', '2026-06-10 02:28:15', 'active', 0, NULL);

-- Dumping structure for table snsu_safetrack.pos_sales
DROP TABLE IF EXISTS `pos_sales`;
CREATE TABLE IF NOT EXISTS `pos_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `cashier_id` bigint(20) unsigned NOT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `change_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('pending','partial','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `sale_date` datetime NOT NULL,
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
  KEY `customer_id` (`customer_id`),
  KEY `cashier_id` (`cashier_id`),
  KEY `invoice_no` (`invoice_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_sales: ~0 rows (approximately)
DELETE FROM `pos_sales`;

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
  PRIMARY KEY (`id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `business_code` (`business_code`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.pos_tenants: ~1 rows (approximately)
DELETE FROM `pos_tenants`;
INSERT INTO `pos_tenants` (`id`, `subscription_id`, `business_name`, `business_code`, `owner_name`, `email`, `phone`, `address`, `logo`, `subscription_start`, `subscription_end`, `trial_ends_at`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 'ANTONIO JR. PILOTON\'s Store', 'TEN-STWXURRU4L', 'ANTONIO JR. PILOTON', 'antoniojrpiloton@gmail.com', NULL, NULL, NULL, '2026-06-09', NULL, NULL, NULL, NULL, '2026-06-09 17:50:36', '2026-06-09 09:50:36', 'active', 0, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

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

-- Dumping data for table snsu_safetrack.qr_codes: ~15 rows (approximately)
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.roles: ~5 rows (approximately)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `details`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'students', 'students', 'web', '2025-07-16 15:55:56', '2025-07-16 15:55:56'),
	(2, 'parents', 'parents', 'web', '2025-07-16 15:56:01', '2025-07-16 15:56:01'),
	(3, 'employees', 'employees', 'web', '2025-07-16 15:56:06', '2025-07-16 15:56:06'),
	(4, 'admin', 'admin', 'web', '2025-07-16 15:56:12', '2025-07-16 15:56:12'),
	(6, 'SA', 'SuperAdmin', 'web', '2026-05-24 09:04:30', '2026-05-24 09:04:30');

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

-- Dumping data for table snsu_safetrack.scan_logs: ~18 rows (approximately)
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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

-- Dumping data for table snsu_safetrack.sessions: ~2 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('tUf4TglSlbt2BMc66t0SFIZ7pZJuGllIENocBV4T', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNWprc2RQZEduUVZUWjFzZnE0cFpzM0NyUjZMZU1XZld5dGZnalFVbSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9wb3MuZGV2LmNvbS9zYWxlcy9jcmVhdGUiO3M6NToicm91dGUiO3M6MTI6InNhbGVzLmNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781060274);

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

-- Dumping data for table snsu_safetrack.sms_queues: ~43 rows (approximately)
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
	(21, 0, '639855793446', 'Dear Mr/Mrs. DELA PENA, \nKHALIL DELA PEÅƒA just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:29:21 PM. Code: VC-0000000055', 'sent', 0, 0, '2026-05-22 15:29:21', '2026-05-26 00:31:10', 'active', 0, NULL),
	(22, 0, '639468019180', 'Dear Mr/Mrs. ANGOB, \nJV ANGOB just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:30:14 PM. Code: VC-0000000059', 'sent', 0, 0, '2026-05-22 15:30:14', '2026-05-22 07:33:56', 'active', 0, NULL),
	(23, 0, '639855793446', 'Dear Mr/Mrs. DELA PENA, \nKHALIL DELA PEÅƒA just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:30:18 PM. Code: VC-0000000060', 'sent', 0, 0, '2026-05-22 15:30:18', '2026-05-26 00:31:24', 'active', 0, NULL),
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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

-- Dumping data for table snsu_safetrack.support_ticket_replies: ~5 rows (approximately)
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.users: 8 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `is_super_admin`, `school_id`, `tenant_id`, `filepath`, `qr_code`, `nfc_code`, `conn_id`, `user_type`, `name`, `email`, `email_verified_at`, `avatar`, `fcm_token`, `profile_pic`, `password`, `remember_token`, `google_id`, `verified`, `created_by`, `updated_by`, `created_at`, `updated_at`, `last_activity_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 0, 0, '0', '0', NULL, 0, 0, 'Antonio Jr Piloton', 'apiloton1@snsu.edu.ph', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocICxZuvbmnED8B5vH9nAzZ75oj4EyyJOq04YOMO7jxaSaNGZqsv=s96-c', NULL, NULL, '$2y$12$KOu9VkzGkjxU5Ztn1Jdp/OptDZxOV.pTIgY9aFMn0TKRCDbez835.', '9acJqd3Qk9lG7a5sByjXlIyli8rGQBOo5nKORAOrkvzzwe6sQEAQzBTPYymb', '117952079880321133596', 1, NULL, NULL, '2026-05-25 11:23:26', '2026-06-09 09:50:21', '2026-06-09 17:50:13', NULL, 0, NULL),
	(2, 0, 1, 0, '0', '304854000001', NULL, 1, 0, 'NORVEN ESPINOSA', 'nespinosa@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$WWpdipcPO1wqNNF4HfHMROcP.XbpLzZb39a5hh5GCvLtYncSq0zSm', NULL, NULL, NULL, 1, 1, '2026-05-25 11:37:09', '2026-05-26 00:41:07', '2026-05-26 08:41:07', 'active', 0, NULL),
	(3, 0, 1, 0, '0', '304854000002', NULL, 2, 0, 'ESTER TURAJA', 'eturaja@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$RSojH6443ykSykVitn0ls.6v6D4Wq7zzGuNoZOZR7pTigonvvPGXm', NULL, NULL, NULL, 1, 1, '2026-05-25 11:56:06', '2026-05-26 02:25:19', '2026-05-26 10:25:19', 'active', 0, NULL),
	(8, 0, 1, 0, '0', '304854000006', NULL, 1, 0, 'ESTER TURAJA', 'eturaja1@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$PilyO0GK/GEdyoCSJS/1kOSRCCAncpm6aZSeCJqrLV9KfdnWU0WQO', NULL, NULL, NULL, 3, 3, '2026-05-26 10:11:39', '2026-05-26 02:15:17', NULL, 'active', 0, NULL),
	(6, 0, 1, 0, '0', '304854000004', NULL, 16, 0, 'ANTONIO JR PILOTON', 'apiloton@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$wUQYpeWx.jaclMtCSrZkV.hSxiWQV.IEPOZDRg3bG/seTnqSNAUDa', NULL, NULL, NULL, 1, 1, '2026-05-26 08:10:15', '2026-05-26 01:41:59', '2026-05-26 09:41:59', 'active', 0, NULL),
	(7, 0, 1, 0, '0', '304854000005', '0026063620', 1, 0, 'loreto PILOTON', 'lpiloton@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$smEgx4teawc9h4T4H.eBMuWPaRiy3W0oM4qmH/XBHYfAOAsxNgTn6', NULL, NULL, NULL, 1, 1, '2026-05-26 08:10:23', '2026-05-26 02:24:39', NULL, 'active', 0, NULL),
	(9, 0, 1, 0, 'users/students/9/8aca7554-0240-4906-852b-245dd1601201.jpeg', '304854000007', NULL, 1, 0, 'loreto PILOTON', 'lpiloton1@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$ZB4.FSshFYWmk8hPy85t1.tQu0Kg2i8m/x70IN7Sq3J9uyVh.uMEi', NULL, NULL, NULL, 1, 1, '2026-05-26 15:13:25', '2026-05-26 07:20:45', NULL, 'active', 0, NULL),
	(11, 0, 0, 0, '0', '0', NULL, 0, 0, 'ANTONIO JR. PILOTON', 'antoniojrpiloton@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c', NULL, NULL, '$2y$12$5PBNfpUmecYLi.ljhHdkkuJd8N5DzQCileGvaDOQfau5sKcCaZ8sO', 'c45C0ylA33m8FRi65X0SJfPggMAt6HcaQn2vySRo6OrtaIwcU3WPgfCwH3p3', '112090857259724913006', 1, NULL, NULL, '2026-06-09 17:50:36', '2026-06-10 02:56:59', '2026-06-10 10:56:59', NULL, 0, NULL);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.year_level: 0 rows
DELETE FROM `year_level`;
/*!40000 ALTER TABLE `year_level` DISABLE KEYS */;
/*!40000 ALTER TABLE `year_level` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
