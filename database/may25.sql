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
DROP DATABASE IF EXISTS `snsu_safetrack`;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.audits: ~8 rows (approximately)
DELETE FROM `audits`;
INSERT INTO `audits` (`id`, `user_type`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `url`, `ip_address`, `user_agent`, `tags`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 7, '[]', '{"filename":"2026-05-25-16-26-22.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-16-26-22.zip","backup_type":"db","file_size":14587,"status":"completed","created_by":1,"id":7}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 08:26:24', '2026-05-25 08:26:24'),
	(2, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 8, '[]', '{"filename":"2026-05-25-16-26-28.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-16-26-28.zip","backup_type":"full","file_size":235549575,"status":"completed","created_by":1,"id":8}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 08:27:04', '2026-05-25 08:27:04'),
	(3, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 9, '[]', '{"filename":"2026-05-25-18-07-34.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-18-07-34.zip","backup_type":"db","file_size":18539,"status":"completed","created_by":1,"id":9}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 10:07:36', '2026-05-25 10:07:36'),
	(4, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 10, '[]', '{"filename":"2026-05-25-18-07-47.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-18-07-47.zip","backup_type":"db","file_size":18607,"status":"completed","created_by":1,"id":10}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 10:07:49', '2026-05-25 10:07:49'),
	(5, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 11, '[]', '{"filename":"2026-05-25-18-07-47.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-18-07-47.zip","backup_type":"full","file_size":18607,"status":"completed","created_by":1,"id":11}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 10:07:51', '2026-05-25 10:07:51'),
	(6, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 12, '[]', '{"filename":"2026-05-25-18-08-03.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-18-08-03.zip","backup_type":"db","file_size":18711,"status":"completed","created_by":1,"id":12}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 10:08:05', '2026-05-25 10:08:05'),
	(7, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 13, '[]', '{"filename":"2026-05-25-18-08-28.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-18-08-28.zip","backup_type":"db","file_size":18772,"status":"completed","created_by":1,"id":13}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 10:08:29', '2026-05-25 10:08:29'),
	(8, 'App\\Models\\User', 1, 'created', 'App\\Models\\Backup', 14, '[]', '{"filename":"2026-05-25-19-02-17.zip","filepath":"C:\\\\wamp64\\\\www\\\\saaskit\\\\storage\\\\app\\\\private\\\\SafeTrack\\\\2026-05-25-19-02-17.zip","backup_type":"db","file_size":19020,"status":"completed","created_by":1,"id":14}', 'http://saaskit.dev.com/sa/backups/generate', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, '2026-05-25 11:02:18', '2026-05-25 11:02:18');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.backups: ~10 rows (approximately)
DELETE FROM `backups`;
INSERT INTO `backups` (`id`, `filename`, `filepath`, `backup_type`, `file_size`, `status`, `created_by`, `notes`, `created_at`, `deleted_at`, `updated_at`) VALUES
	(3, '2026-05-25-15-53-26.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-15-53-26.zip', 'full', 235569253, 'completed', 1, NULL, '2026-05-25 07:53:59', NULL, '2026-05-25 08:26:10'),
	(4, '2026-05-25-15-54-59.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-15-54-59.zip', 'db', 59265, 'completed', 1, NULL, '2026-05-25 07:55:00', NULL, '2026-05-25 08:26:13'),
	(5, '2026-05-25-15-55-03.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-15-55-03.zip', 'files', 235512247, 'completed', 1, NULL, '2026-05-25 07:55:38', NULL, '2026-05-25 08:26:15'),
	(6, '2026-05-25-15-57-51.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-15-57-51.zip', 'full', 235575090, 'completed', 1, NULL, '2026-05-25 07:58:26', NULL, '2026-05-25 08:26:17'),
	(7, '2026-05-25-16-26-22.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-16-26-22.zip', 'db', 14587, 'completed', 1, NULL, '2026-05-25 08:26:24', '2026-05-25 08:26:24', '2026-05-25 08:26:24'),
	(8, '2026-05-25-16-26-28.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-16-26-28.zip', 'full', 235549575, 'completed', 1, NULL, '2026-05-25 08:27:04', '2026-05-25 08:27:04', '2026-05-25 08:27:04'),
	(9, '2026-05-25-18-07-34.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-18-07-34.zip', 'db', 18539, 'completed', 1, NULL, '2026-05-25 10:07:36', '2026-05-25 10:07:36', '2026-05-25 10:07:36'),
	(10, '2026-05-25-18-07-47.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-18-07-47.zip', 'db', 18607, 'completed', 1, NULL, '2026-05-25 10:07:49', '2026-05-25 10:07:49', '2026-05-25 10:07:49'),
	(11, '2026-05-25-18-07-47.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-18-07-47.zip', 'full', 18607, 'completed', 1, NULL, '2026-05-25 10:07:51', '2026-05-25 10:07:51', '2026-05-25 10:07:51'),
	(12, '2026-05-25-18-08-03.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-18-08-03.zip', 'db', 18711, 'completed', 1, NULL, '2026-05-25 10:08:05', '2026-05-25 10:08:05', '2026-05-25 10:08:05'),
	(13, '2026-05-25-18-08-28.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-18-08-28.zip', 'db', 18772, 'completed', 1, NULL, '2026-05-25 10:08:29', NULL, '2026-05-25 10:08:29'),
	(14, '2026-05-25-19-02-17.zip', 'C:\\wamp64\\www\\saaskit\\storage\\app\\private\\SafeTrack\\2026-05-25-19-02-17.zip', 'db', 19020, 'completed', 1, NULL, '2026-05-25 11:02:18', NULL, '2026-05-25 11:02:18');

-- Dumping structure for table snsu_safetrack.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.cache: ~9 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('safetrack-cache-platform.analytics.device-analytics', 'O:39:"Illuminate\\Database\\Eloquent\\Collection":2:{s:8:"\0*\0items";a:2:{i:0;O:24:"App\\Models\\LoginActivity":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:16:"login_activities";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:7:"browser";s:4:"Edge";s:5:"total";i:25;}s:11:"\0*\0original";a:2:{s:7:"browser";s:4:"Edge";s:5:"total";i:25;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:2:{s:12:"logged_in_at";s:8:"datetime";s:13:"logged_out_at";s:8:"datetime";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:10:{i:0;s:7:"user_id";i:1;s:5:"email";i:2;s:10:"ip_address";i:3;s:10:"user_agent";i:4;s:6:"device";i:5;s:8:"platform";i:6;s:7:"browser";i:7;s:6:"status";i:8;s:12:"logged_in_at";i:9;s:13:"logged_out_at";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}i:1;O:24:"App\\Models\\LoginActivity":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:16:"login_activities";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:7:"browser";s:6:"Chrome";s:5:"total";i:1;}s:11:"\0*\0original";a:2:{s:7:"browser";s:6:"Chrome";s:5:"total";i:1;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:2:{s:12:"logged_in_at";s:8:"datetime";s:13:"logged_out_at";s:8:"datetime";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:10:{i:0;s:7:"user_id";i:1;s:5:"email";i:2;s:10:"ip_address";i:3;s:10:"user_agent";i:4;s:6:"device";i:5;s:8:"platform";i:6;s:7:"browser";i:7;s:6:"status";i:8;s:12:"logged_in_at";i:9;s:13:"logged_out_at";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}}s:28:"\0*\0escapeWhenCastingToString";b:0;}', 1779710420),
	('safetrack-cache-platform.analytics.login-trends', 'O:39:"Illuminate\\Database\\Eloquent\\Collection":2:{s:8:"\0*\0items";a:1:{i:0;O:24:"App\\Models\\LoginActivity":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:16:"login_activities";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:4:"date";s:10:"2026-05-25";s:5:"total";i:26;}s:11:"\0*\0original";a:2:{s:4:"date";s:10:"2026-05-25";s:5:"total";i:26;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:2:{s:12:"logged_in_at";s:8:"datetime";s:13:"logged_out_at";s:8:"datetime";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:10:{i:0;s:7:"user_id";i:1;s:5:"email";i:2;s:10:"ip_address";i:3;s:10:"user_agent";i:4;s:6:"device";i:5;s:8:"platform";i:6;s:7:"browser";i:7;s:6:"status";i:8;s:12:"logged_in_at";i:9;s:13:"logged_out_at";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}}s:28:"\0*\0escapeWhenCastingToString";b:0;}', 1779710120),
	('safetrack-cache-platform.analytics.overview', 'O:28:"Illuminate\\Http\\JsonResponse":11:{s:7:"headers";O:50:"Symfony\\Component\\HttpFoundation\\ResponseHeaderBag":5:{s:10:"\0*\0headers";a:3:{s:13:"cache-control";a:1:{i:0;s:17:"no-cache, private";}s:4:"date";a:1:{i:0;s:29:"Mon, 25 May 2026 11:50:20 GMT";}s:12:"content-type";a:1:{i:0;s:16:"application/json";}}s:15:"\0*\0cacheControl";a:0:{}s:23:"\0*\0computedCacheControl";a:2:{s:8:"no-cache";b:1;s:7:"private";b:1;}s:10:"\0*\0cookies";a:0:{}s:14:"\0*\0headerNames";a:3:{s:13:"cache-control";s:13:"Cache-Control";s:4:"date";s:4:"Date";s:12:"content-type";s:12:"Content-Type";}}s:10:"\0*\0content";s:128:"{"total_users":"4","active_users":"2","total_schools":"4","active_sessions":"5","suspicious_activities":"3","today_logins":"26"}";s:10:"\0*\0version";s:3:"1.0";s:13:"\0*\0statusCode";i:200;s:13:"\0*\0statusText";s:2:"OK";s:10:"\0*\0charset";N;s:7:"\0*\0data";s:128:"{"total_users":"4","active_users":"2","total_schools":"4","active_sessions":"5","suspicious_activities":"3","today_logins":"26"}";s:11:"\0*\0callback";N;s:18:"\0*\0encodingOptions";i:0;s:8:"original";a:6:{s:11:"total_users";s:1:"4";s:12:"active_users";s:1:"2";s:13:"total_schools";s:1:"4";s:15:"active_sessions";s:1:"5";s:21:"suspicious_activities";s:1:"3";s:12:"today_logins";s:2:"26";}s:9:"exception";N;}', 1779710120),
	('safetrack-cache-platform.analytics.school-analytics', 'O:39:"Illuminate\\Database\\Eloquent\\Collection":2:{s:8:"\0*\0items";a:4:{i:0;O:17:"App\\Models\\School":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:6:"school";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:10:"SchoolName";s:28:"TUBAJON NATIONAL HIGH SCHOOL";s:11:"users_count";i:2;}s:11:"\0*\0original";a:2:{s:10:"SchoolName";s:28:"TUBAJON NATIONAL HIGH SCHOOL";s:11:"users_count";i:2;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:5:{s:9:"EnableNFC";s:7:"boolean";s:8:"EnableQR";s:7:"boolean";s:23:"EnableOfflineAttendance";s:7:"boolean";s:14:"OfficialTimeIn";s:12:"datetime:H:i";s:15:"OfficialTimeOut";s:12:"datetime:H:i";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:29:{i:0;s:10:"alias_name";i:1;s:4:"code";i:2;s:11:"SystemTitle";i:3;s:10:"SchoolName";i:4;s:10:"SchoolCode";i:5;s:14:"EducationLevel";i:6;s:6:"Region";i:7;s:8:"Division";i:8;s:7:"Address";i:9;s:13:"ContactNumber";i:10;s:12:"EmailAddress";i:11;s:11:"PrincipalID";i:12;s:11:"RegistrarID";i:13;s:4:"Logo";i:14;s:14:"OfficialTimeIn";i:15;s:15:"OfficialTimeOut";i:16;s:16:"LateGraceMinutes";i:17;s:9:"EnableNFC";i:18;s:8:"EnableQR";i:19;s:23:"EnableOfflineAttendance";i:20;s:19:"CurrentSchoolYearID";i:21;s:10:"ThemeColor";i:22;s:11:"cacert_path";i:23;s:8:"port_com";i:24;s:11:"python_path";i:25;s:16:"sms_failed_count";i:26;s:15:"sms_low_balance";i:27;s:18:"sms_last_failed_at";i:28;s:10:"total_sent";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}i:1;O:17:"App\\Models\\School":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:6:"school";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:10:"SchoolName";s:27:"LORETO NATIONAL HIGH SCHOOL";s:11:"users_count";i:0;}s:11:"\0*\0original";a:2:{s:10:"SchoolName";s:27:"LORETO NATIONAL HIGH SCHOOL";s:11:"users_count";i:0;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:5:{s:9:"EnableNFC";s:7:"boolean";s:8:"EnableQR";s:7:"boolean";s:23:"EnableOfflineAttendance";s:7:"boolean";s:14:"OfficialTimeIn";s:12:"datetime:H:i";s:15:"OfficialTimeOut";s:12:"datetime:H:i";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:29:{i:0;s:10:"alias_name";i:1;s:4:"code";i:2;s:11:"SystemTitle";i:3;s:10:"SchoolName";i:4;s:10:"SchoolCode";i:5;s:14:"EducationLevel";i:6;s:6:"Region";i:7;s:8:"Division";i:8;s:7:"Address";i:9;s:13:"ContactNumber";i:10;s:12:"EmailAddress";i:11;s:11:"PrincipalID";i:12;s:11:"RegistrarID";i:13;s:4:"Logo";i:14;s:14:"OfficialTimeIn";i:15;s:15:"OfficialTimeOut";i:16;s:16:"LateGraceMinutes";i:17;s:9:"EnableNFC";i:18;s:8:"EnableQR";i:19;s:23:"EnableOfflineAttendance";i:20;s:19:"CurrentSchoolYearID";i:21;s:10:"ThemeColor";i:22;s:11:"cacert_path";i:23;s:8:"port_com";i:24;s:11:"python_path";i:25;s:16:"sms_failed_count";i:26;s:15:"sms_low_balance";i:27;s:18:"sms_last_failed_at";i:28;s:10:"total_sent";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}i:2;O:17:"App\\Models\\School":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:6:"school";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:10:"SchoolName";s:38:"SURIGAO DEL NORTE NATIONAL HIGH SCHOOL";s:11:"users_count";i:0;}s:11:"\0*\0original";a:2:{s:10:"SchoolName";s:38:"SURIGAO DEL NORTE NATIONAL HIGH SCHOOL";s:11:"users_count";i:0;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:5:{s:9:"EnableNFC";s:7:"boolean";s:8:"EnableQR";s:7:"boolean";s:23:"EnableOfflineAttendance";s:7:"boolean";s:14:"OfficialTimeIn";s:12:"datetime:H:i";s:15:"OfficialTimeOut";s:12:"datetime:H:i";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:29:{i:0;s:10:"alias_name";i:1;s:4:"code";i:2;s:11:"SystemTitle";i:3;s:10:"SchoolName";i:4;s:10:"SchoolCode";i:5;s:14:"EducationLevel";i:6;s:6:"Region";i:7;s:8:"Division";i:8;s:7:"Address";i:9;s:13:"ContactNumber";i:10;s:12:"EmailAddress";i:11;s:11:"PrincipalID";i:12;s:11:"RegistrarID";i:13;s:4:"Logo";i:14;s:14:"OfficialTimeIn";i:15;s:15:"OfficialTimeOut";i:16;s:16:"LateGraceMinutes";i:17;s:9:"EnableNFC";i:18;s:8:"EnableQR";i:19;s:23:"EnableOfflineAttendance";i:20;s:19:"CurrentSchoolYearID";i:21;s:10:"ThemeColor";i:22;s:11:"cacert_path";i:23;s:8:"port_com";i:24;s:11:"python_path";i:25;s:16:"sms_failed_count";i:26;s:15:"sms_low_balance";i:27;s:18:"sms_last_failed_at";i:28;s:10:"total_sent";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}i:3;O:17:"App\\Models\\School":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:6:"school";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:10:"SchoolName";s:35:"Caraga Regional Science High School";s:11:"users_count";i:0;}s:11:"\0*\0original";a:2:{s:10:"SchoolName";s:35:"Caraga Regional Science High School";s:11:"users_count";i:0;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:5:{s:9:"EnableNFC";s:7:"boolean";s:8:"EnableQR";s:7:"boolean";s:23:"EnableOfflineAttendance";s:7:"boolean";s:14:"OfficialTimeIn";s:12:"datetime:H:i";s:15:"OfficialTimeOut";s:12:"datetime:H:i";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:29:{i:0;s:10:"alias_name";i:1;s:4:"code";i:2;s:11:"SystemTitle";i:3;s:10:"SchoolName";i:4;s:10:"SchoolCode";i:5;s:14:"EducationLevel";i:6;s:6:"Region";i:7;s:8:"Division";i:8;s:7:"Address";i:9;s:13:"ContactNumber";i:10;s:12:"EmailAddress";i:11;s:11:"PrincipalID";i:12;s:11:"RegistrarID";i:13;s:4:"Logo";i:14;s:14:"OfficialTimeIn";i:15;s:15:"OfficialTimeOut";i:16;s:16:"LateGraceMinutes";i:17;s:9:"EnableNFC";i:18;s:8:"EnableQR";i:19;s:23:"EnableOfflineAttendance";i:20;s:19:"CurrentSchoolYearID";i:21;s:10:"ThemeColor";i:22;s:11:"cacert_path";i:23;s:8:"port_com";i:24;s:11:"python_path";i:25;s:16:"sms_failed_count";i:26;s:15:"sms_low_balance";i:27;s:18:"sms_last_failed_at";i:28;s:10:"total_sent";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}}s:28:"\0*\0escapeWhenCastingToString";b:0;}', 1779710420),
	('safetrack-cache-platform.analytics.security-trends', 'O:39:"Illuminate\\Database\\Eloquent\\Collection":2:{s:8:"\0*\0items";a:1:{i:0;O:29:"App\\Models\\SuspiciousActivity":33:{s:13:"\0*\0connection";s:5:"mysql";s:8:"\0*\0table";s:21:"suspicious_activities";s:13:"\0*\0primaryKey";s:2:"id";s:10:"\0*\0keyType";s:3:"int";s:12:"incrementing";b:1;s:7:"\0*\0with";a:0:{}s:12:"\0*\0withCount";a:0:{}s:19:"preventsLazyLoading";b:0;s:10:"\0*\0perPage";i:15;s:6:"exists";b:1;s:18:"wasRecentlyCreated";b:0;s:28:"\0*\0escapeWhenCastingToString";b:0;s:13:"\0*\0attributes";a:2:{s:4:"date";s:10:"2026-05-25";s:5:"total";i:3;}s:11:"\0*\0original";a:2:{s:4:"date";s:10:"2026-05-25";s:5:"total";i:3;}s:10:"\0*\0changes";a:0:{}s:11:"\0*\0previous";a:0:{}s:8:"\0*\0casts";a:2:{s:4:"meta";s:5:"array";s:11:"detected_at";s:8:"datetime";}s:17:"\0*\0classCastCache";a:0:{}s:21:"\0*\0attributeCastCache";a:0:{}s:13:"\0*\0dateFormat";N;s:10:"\0*\0appends";a:0:{}s:19:"\0*\0dispatchesEvents";a:0:{}s:14:"\0*\0observables";a:0:{}s:12:"\0*\0relations";a:0:{}s:10:"\0*\0touches";a:0:{}s:27:"\0*\0relationAutoloadCallback";N;s:26:"\0*\0relationAutoloadContext";N;s:10:"timestamps";b:1;s:13:"usesUniqueIds";b:0;s:9:"\0*\0hidden";a:0:{}s:10:"\0*\0visible";a:0:{}s:11:"\0*\0fillable";a:7:{i:0;s:7:"user_id";i:1;s:4:"type";i:2;s:8:"severity";i:3;s:11:"description";i:4;s:10:"ip_address";i:5;s:4:"meta";i:6;s:11:"detected_at";}s:10:"\0*\0guarded";a:1:{i:0;s:1:"*";}}}s:28:"\0*\0escapeWhenCastingToString";b:0;}', 1779710120),
	('safetrack-cache-spatie.permission.cache', 'a:3:{s:5:"alias";a:5:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:7:"details";s:1:"d";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:37:{i:0;a:5:{s:1:"a";i:1;s:1:"b";s:14:"dashboard.view";s:1:"c";s:14:"View dashboard";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:1;a:5:{s:1:"a";i:2;s:1:"b";s:12:"scanner.view";s:1:"c";s:19:"View scanner module";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:2;a:5:{s:1:"a";i:3;s:1:"b";s:9:"logs.view";s:1:"c";s:13:"View all logs";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:3;a:5:{s:1:"a";i:4;s:1:"b";s:15:"logs.users.view";s:1:"c";s:14:"View user logs";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:4;a:5:{s:1:"a";i:5;s:1:"b";s:13:"students.view";s:1:"c";s:13:"View students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:5;a:5:{s:1:"a";i:6;s:1:"b";s:15:"students.create";s:1:"c";s:15:"Create students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:6;a:5:{s:1:"a";i:7;s:1:"b";s:15:"students.update";s:1:"c";s:15:"Update students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:7;a:5:{s:1:"a";i:8;s:1:"b";s:15:"students.delete";s:1:"c";s:15:"Delete students";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:8;a:5:{s:1:"a";i:9;s:1:"b";s:12:"parents.view";s:1:"c";s:12:"View parents";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:9;a:5:{s:1:"a";i:10;s:1:"b";s:14:"parents.create";s:1:"c";s:14:"Create parents";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:10;a:5:{s:1:"a";i:11;s:1:"b";s:14:"parents.update";s:1:"c";s:14:"Update parents";s:1:"d";s:3:"web";s:1:"r";a:3:{i:0;i:3;i:1;i:4;i:2;i:6;}}i:11;a:5:{s:1:"a";i:12;s:1:"b";s:14:"parents.delete";s:1:"c";s:14:"Delete parents";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:12;a:5:{s:1:"a";i:13;s:1:"b";s:14:"employees.view";s:1:"c";s:14:"View employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:13;a:5:{s:1:"a";i:14;s:1:"b";s:16:"employees.create";s:1:"c";s:16:"Create employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:14;a:5:{s:1:"a";i:15;s:1:"b";s:16:"employees.update";s:1:"c";s:16:"Update employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:15;a:5:{s:1:"a";i:16;s:1:"b";s:16:"employees.delete";s:1:"c";s:16:"Delete employees";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:16;a:5:{s:1:"a";i:17;s:1:"b";s:8:"sms.view";s:1:"c";s:14:"View SMS queue";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:17;a:5:{s:1:"a";i:18;s:1:"b";s:8:"sms.send";s:1:"c";s:17:"Send SMS messages";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:18;a:5:{s:1:"a";i:19;s:1:"b";s:17:"school-users.view";s:1:"c";s:17:"View school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:19;a:5:{s:1:"a";i:20;s:1:"b";s:19:"school-users.create";s:1:"c";s:19:"Create school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:20;a:5:{s:1:"a";i:21;s:1:"b";s:19:"school-users.update";s:1:"c";s:19:"Update school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:21;a:5:{s:1:"a";i:22;s:1:"b";s:19:"school-users.delete";s:1:"c";s:19:"Delete school users";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:22;a:5:{s:1:"a";i:23;s:1:"b";s:13:"settings.view";s:1:"c";s:13:"View settings";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:23;a:5:{s:1:"a";i:24;s:1:"b";s:15:"settings.update";s:1:"c";s:15:"Update settings";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:24;a:5:{s:1:"a";i:25;s:1:"b";s:16:"permissions.view";s:1:"c";s:16:"View permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:25;a:5:{s:1:"a";i:26;s:1:"b";s:18:"permissions.create";s:1:"c";s:18:"Create permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:26;a:5:{s:1:"a";i:27;s:1:"b";s:18:"permissions.update";s:1:"c";s:18:"Update permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:27;a:5:{s:1:"a";i:28;s:1:"b";s:18:"permissions.delete";s:1:"c";s:18:"Delete permissions";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:28;a:5:{s:1:"a";i:29;s:1:"b";s:10:"roles.view";s:1:"c";s:10:"View roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:29;a:5:{s:1:"a";i:30;s:1:"b";s:12:"roles.create";s:1:"c";s:12:"Create roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:30;a:5:{s:1:"a";i:31;s:1:"b";s:12:"roles.update";s:1:"c";s:12:"Update roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:31;a:5:{s:1:"a";i:32;s:1:"b";s:12:"roles.delete";s:1:"c";s:12:"Delete roles";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:32;a:5:{s:1:"a";i:33;s:1:"b";s:12:"store.view";s:1:"c";s:12:"View store";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:33;a:5:{s:1:"a";i:34;s:1:"b";s:14:"store.create";s:1:"c";s:14:"Create store";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:34;a:5:{s:1:"a";i:35;s:1:"b";s:14:"store.update";s:1:"c";s:14:"Update store";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:35;a:5:{s:1:"a";i:36;s:1:"b";s:14:"store.delete";s:1:"c";s:14:"Delete store";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:4;i:1;i:6;}}i:36;a:5:{s:1:"a";i:37;s:1:"b";s:19:"support-center.view";s:1:"c";s:20:"Allow report support";s:1:"d";s:3:"web";s:1:"r";a:2:{i:0;i:3;i:1;i:4;}}}s:5:"roles";a:3:{i:0;a:4:{s:1:"a";i:4;s:1:"b";s:5:"admin";s:1:"c";s:5:"admin";s:1:"d";s:3:"web";}i:1;a:4:{s:1:"a";i:6;s:1:"b";s:2:"SA";s:1:"c";s:10:"SuperAdmin";s:1:"d";s:3:"web";}i:2;a:4:{s:1:"a";i:3;s:1:"b";s:9:"employees";s:1:"c";s:9:"employees";s:1:"d";s:3:"web";}}}', 1779796351);

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.employees: 2 rows
DELETE FROM `employees`;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` (`id`, `school_id`, `UserID`, `PhoneNumber`, `FirstName`, `MiddleName`, `LastName`, `EmailAddress`, `Suffix`, `Address`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 2, '+639128941731', 'NORVEN', 'LUIB', 'ESPINOSA', NULL, NULL, 'Sitio Cayutan, Brgy. Cagniog', 1, 1, '2026-05-25 11:37:09', '2026-05-25 03:37:09', 'active', 0, NULL),
	(2, 1, 3, NULL, 'ESTER', NULL, 'TURAJA', NULL, NULL, 'SVT', 1, 1, '2026-05-25 11:56:06', '2026-05-25 03:56:07', 'active', 0, NULL);
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.login_activities: 25 rows
DELETE FROM `login_activities`;
/*!40000 ALTER TABLE `login_activities` DISABLE KEYS */;
INSERT INTO `login_activities` (`id`, `user_id`, `email`, `ip_address`, `user_agent`, `device`, `platform`, `browser`, `status`, `logged_in_at`, `logged_out_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 08:44:27', '2026-05-25 08:46:08', '2026-05-25 08:44:27', '2026-05-25 08:46:08'),
	(2, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 08:46:19', '2026-05-25 09:12:20', '2026-05-25 08:46:19', '2026-05-25 09:12:20'),
	(3, 4, 'antoniojrpiloton@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 09:12:29', '2026-05-25 09:12:36', '2026-05-25 09:12:29', '2026-05-25 09:12:36'),
	(4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:13:22', NULL, '2026-05-25 09:13:22', '2026-05-25 09:13:22'),
	(5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:13:32', NULL, '2026-05-25 09:13:32', '2026-05-25 09:13:32'),
	(6, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:13:49', NULL, '2026-05-25 09:13:49', '2026-05-25 09:13:49'),
	(7, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:13:52', NULL, '2026-05-25 09:13:52', '2026-05-25 09:13:52'),
	(8, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:15:57', NULL, '2026-05-25 09:15:57', '2026-05-25 09:15:57'),
	(9, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:15:59', NULL, '2026-05-25 09:15:59', '2026-05-25 09:15:59'),
	(10, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:00', NULL, '2026-05-25 09:16:00', '2026-05-25 09:16:00'),
	(11, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:02', NULL, '2026-05-25 09:16:02', '2026-05-25 09:16:02'),
	(12, NULL, '09128941732', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:11', NULL, '2026-05-25 09:16:11', '2026-05-25 09:16:11'),
	(13, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:16', NULL, '2026-05-25 09:16:16', '2026-05-25 09:16:16'),
	(14, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:19', NULL, '2026-05-25 09:16:19', '2026-05-25 09:16:19'),
	(15, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:20', NULL, '2026-05-25 09:16:20', '2026-05-25 09:16:20'),
	(16, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:26', NULL, '2026-05-25 09:16:26', '2026-05-25 09:16:26'),
	(17, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:28', NULL, '2026-05-25 09:16:28', '2026-05-25 09:16:28'),
	(18, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:32', NULL, '2026-05-25 09:16:32', '2026-05-25 09:16:32'),
	(19, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'failed', '2026-05-25 09:16:39', NULL, '2026-05-25 09:16:39', '2026-05-25 09:16:39'),
	(20, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 09:16:42', '2026-05-25 09:17:03', '2026-05-25 09:16:42', '2026-05-25 09:17:03'),
	(21, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 09:17:08', '2026-05-25 09:35:54', '2026-05-25 09:17:08', '2026-05-25 09:35:54'),
	(22, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 09:36:04', '2026-05-25 09:37:19', '2026-05-25 09:36:04', '2026-05-25 09:37:19'),
	(23, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'logout', '2026-05-25 09:38:16', '2026-05-25 09:41:48', '2026-05-25 09:38:16', '2026-05-25 09:41:48'),
	(24, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-05-25 09:42:03', NULL, '2026-05-25 09:42:03', '2026-05-25 09:42:03'),
	(25, 2, 'nespinosa@tnhs.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'WebKit', 'Windows', 'Chrome', 'success', '2026-05-25 09:48:11', NULL, '2026-05-25 09:48:11', '2026-05-25 09:48:11'),
	(26, 1, 'apiloton1@snsu.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'WebKit', 'Windows', 'Edge', 'success', '2026-05-25 11:49:57', NULL, '2026-05-25 11:49:57', '2026-05-25 11:49:57');
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

-- Dumping data for table snsu_safetrack.model_has_roles: ~5 rows (approximately)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(6, 'App\\Models\\User', 1),
	(3, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3),
	(4, 'App\\Models\\User', 3),
	(6, 'App\\Models\\User', 4);

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table snsu_safetrack.nfc_codes: 4 rows
DELETE FROM `nfc_codes`;
/*!40000 ALTER TABLE `nfc_codes` DISABLE KEYS */;
INSERT INTO `nfc_codes` (`id`, `school_id`, `UserID`, `nf_codes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 1, 3, '0018317828', 1, 1, '2026-05-25 10:23:10', '2026-05-25 02:23:10', 'active', 0, NULL),
	(2, 1, 4, '0026063620', 1, 1, '2026-05-25 10:23:21', '2026-05-25 02:23:21', 'active', 0, NULL),
	(3, 1, 2, '0017924612', 1, 1, '2026-05-25 10:23:30', '2026-05-25 02:23:30', 'active', 0, NULL),
	(4, 3, 12, '0017924612', 1, 1, '2026-05-25 11:14:30', '2026-05-25 03:14:30', 'active', 0, NULL);
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
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.parents: ~14 rows (approximately)
DELETE FROM `parents`;
INSERT INTO `parents` (`id`, `school_id`, `UserID`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `PhoneNumber`, `Address`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 0, 'JOCELYN', 'BERO', 'MORALES', NULL, '+639813581109', 'Espina St', 1, 1, '2026-05-22 10:13:42', '2026-05-22 02:13:51', 'active', 0, NULL),
	(2, 0, 5, 'PARENT1', 'BERO', 'P1_L', NULL, '+639306069019', 'STC', 1, 1, '2026-05-22 14:20:08', '2026-05-22 06:25:12', 'active', 0, NULL),
	(3, 0, 0, 'JOCELYN', 'BERO', 'MORALES', NULL, '+639813581109', 'STC', 1, 1, '2026-05-22 14:23:53', '2026-05-22 06:23:53', 'active', 0, NULL),
	(4, 0, 7, 'KAREEN', 'BASUL', 'MAPOY', NULL, '+639700920157', 'Address', 1, 1, '2026-05-22 14:33:22', '2026-05-22 06:34:27', 'active', 0, NULL),
	(5, 0, 0, 'Janeli', 'Managa', 'Olaco', NULL, '+639464233996', 'STA. CRUZ, TUBAJON', 8, 8, '2026-05-22 15:09:24', '2026-05-22 07:09:24', 'active', 0, NULL),
	(6, 0, 0, 'RIZEL', 'CURTINA', 'IMALAY', NULL, '+639070708095', 'P-5 SAN VICENTE, TUBAJON, DINAGAT ISLANDS', 11, 11, '2026-05-22 15:09:48', '2026-05-22 07:09:48', 'active', 0, NULL),
	(7, 0, 0, 'Rosden', NULL, 'Garcia', NULL, '+639858224872', 'STA. CRUZ, TUBAJON', 8, 8, '2026-05-22 15:11:25', '2026-05-22 07:11:25', 'active', 0, NULL),
	(8, 0, 0, 'ARLENE', 'CREZALDO', 'DIAZ', NULL, '+639506975822', 'SAN VICENTE, TUBAJON, DINAGAT ISLANDS', 14, 14, '2026-05-22 15:11:26', '2026-05-22 07:11:26', 'active', 0, NULL),
	(9, 0, 0, 'Lorna', NULL, 'Kiasan', NULL, '+639641012016', 'STA. CRUZ, TUBAJON', 8, 8, '2026-05-22 15:12:51', '2026-05-22 07:12:51', 'active', 0, NULL),
	(10, 0, 0, 'LIZIL', NULL, 'LOBERIANO', NULL, '+639100891547', 'SAN ROQUE,TUBAJON DINAGAT ISLANDS', 14, 14, '2026-05-22 15:13:10', '2026-05-22 07:13:10', 'active', 0, NULL),
	(11, 0, 0, 'JELLAN', 'DE ORO', 'PACUNLA', NULL, '+639817101537', 'SAN ROQUE,TUBAJON,DINAGAT ISLANDS', 9, 9, '2026-05-22 15:13:28', '2026-05-22 07:13:28', 'active', 0, NULL),
	(12, 0, 0, 'MICHELLE', 'DELGADO', 'DELA PENA', NULL, '+639855793446', 'P-3 STA. CRUZ, TUBAJON, PDI', 11, 11, '2026-05-22 15:13:33', '2026-05-22 07:13:33', 'active', 0, NULL),
	(13, 0, 0, 'CHARLYN', NULL, 'ANGOB', NULL, '+639468019180', 'SAN VICENTE, TUBAJON, DINAGAT ISLANDS', 14, 14, '2026-05-22 15:14:23', '2026-05-22 07:14:23', 'active', 0, NULL),
	(14, 0, 0, 'RIZELLA', NULL, 'DELA PENA', NULL, '+639305900202', 'STA CRUZ, TUBAJON DINAGAT ISLANDS', 14, 14, '2026-05-22 15:15:56', '2026-05-22 07:15:56', 'active', 0, NULL),
	(15, 3, 87, 'loreto P', 'new mabini2', 'loreto P', NULL, '+639128941731', 'Sitio Cayutan, Brgy. Cagniog', 1, 1, '2026-05-25 09:50:47', '2026-05-25 01:50:47', 'active', 0, NULL);

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
	(33, 'store.view', 'View store', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(34, 'store.create', 'Create store', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(35, 'store.update', 'Update store', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
	(36, 'store.delete', 'Delete store', 'web', '2026-05-25 03:54:10', '2026-05-25 03:54:10'),
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.qr_codes: ~12 rows (approximately)
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
	(13, 1, '304854', NULL, 2, 0, 0, '2026-05-25 11:56:06', '2026-05-25 03:56:06', 'active', 0, NULL);

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

-- Dumping data for table snsu_safetrack.role_has_permissions: ~102 rows (approximately)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.scan_logs: ~0 rows (approximately)
DELETE FROM `scan_logs`;

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
	(1, '0', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'TUBAJON NATIONAL HIGH SCHOOL', '304854', 'INTEGRATED', 'CARAGA', 'Dinagat Islands', 'SAN VICENTE, TUBAJON, DINAGAT ISLANDS', '+639128941731', '@tnhs.edu.ph', NULL, NULL, 'store/Dh5Ux3SGGfc55zBc3WIyndKabOTvbe3Y5TxrQYbP.png', NULL, NULL, 15, 1, 1, 1, NULL, '#0088f0', 1, 1, '2026-05-25 11:25:26', '2026-05-25 11:50:10', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL),
	(2, 'LNHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'LORETO NATIONAL HIGH SCHOOL', '12345', 'INTEGRATED', 'CARAGA', 'Dinagat Islands', 'LORATION, DINAGAT ISLANDS', '+639128941731', '@lnhs.edu.ph', NULL, NULL, 'store/FSFIQsbFgLXoywTw3Z0Yr7fNs4ef56w7cdwqLLh3.jpg', NULL, NULL, 15, 1, 1, 1, NULL, '#00bd03', 1, 1, '2026-05-25 13:34:13', '2026-05-25 05:40:25', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL),
	(3, 'SNNHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'SURIGAO DEL NORTE NATIONAL HIGH SCHOOL', '12346', 'INTEGRATED', 'CARAGA', 'SURIGAO CITY', 'SURIGAO CITY', '+639128941731', '@snnhs.edu.ph', NULL, NULL, 'store/EI7QcLGXYpZuG9fUechj5RObJvKhBeCAw9YwRIHQ.jpg', '07:00:00', '17:00:00', 15, 1, 1, 1, NULL, '#007bff', 1, 1, '2026-05-25 13:39:59', '2026-05-25 05:39:59', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL),
	(4, 'CRSHS', '0', NULL, 'SAFETRACK: A QR and NFC-Based Student Monitoring System', 'Caraga Regional Science High School', '654321', 'INTEGRATED', 'CARAGA', 'SURIGAO CITY', 'SURIGAO CITY', '+639128941731', '@crshs.edu.ph', NULL, NULL, 'store/7lorUNvirlFD3zLx4OOUStN3WdyStreMSHPFCUDc.jpg', '07:00:00', '17:00:00', 15, 1, 1, 1, NULL, '#e0a500', 1, 1, '2026-05-25 13:44:27', '2026-05-25 05:44:27', 'active', NULL, NULL, NULL, NULL, 0, 0, 'api', NULL, NULL, NULL, NULL, 0, NULL);
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
	('4lzN2kztjUK96HVNxN6nvSWqHbffL2YAQTHNBxUj', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMDluV2RaMjV5UXZqdmw0NWt5SzJ2eG45VVR4eTNXa0hFc1VqV3d3cyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9zYWFza2l0LmRldi5jb20vc2Evc3lzdGVtLXNldHRpbmdzIjtzOjU6InJvdXRlIjtzOjI0OiJzYS5zeXN0ZW0tc2V0dGluZ3MuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1779709741),
	('8o7vbNuJzRnv3u0mfgZC6TjuOE6ATCENimeZ7UNl', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiclF5ZDVBd1BDMU14WUxtcGpBM0JWWkNhOHVuVTBSUVl6czZVS3ZlTSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovL3NhYXNraXQuZGV2LmNvbS9zdXBwb3J0LWNlbnRlciI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwOi8vc2Fhc2tpdC5kZXYuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779709782),
	('SmbUilNU6l3q2nXlAJ94h31z3ts9BL7Dbnd20A0A', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNlFNZTBHME9RUWpDd2MwU1lQVEF1dGhoYWJrZ3ZGMjVod2pGY3M3SyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vc2Fhc2tpdC5kZXYuY29tL3NhL2FjdGl2aXR5LWxvZ3MiO3M6NToicm91dGUiO3M6MjI6InNhLmFjdGl2aXR5LWxvZ3MuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1779709958),
	('tNXsitzPcNYhqIhjBZy8I3UlOWituC9jwcAvi0YU', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiU3FTRGtWaDl4SXVkb3o2V0IzdkhnYkRDN08wd2JFa1ptNHRRZFdSVSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vc2Fhc2tpdC5kZXYuY29tL3N1cHBvcnQtY2VudGVyIjtzOjU6InJvdXRlIjtzOjIwOiJzdXBwb3J0LWNlbnRlci5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czo5OiJzY2hvb2xfaWQiO2k6MTtzOjExOiJzY2hvb2xfbmFtZSI7czoyODoiVFVCQUpPTiBOQVRJT05BTCBISUdIIFNDSE9PTCI7fQ==', 1779709628),
	('uZnEn4XblW9WRlOvYZOvD8CpnIuztN52X2M1gVDw', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidjhRUEtoSFZPRkgxY1hTWTdscWV1Y09wU3R0WGliQTAzc0Q2TGVXbSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cDovL3NhYXNraXQuZGV2LmNvbS9zYS9zeXN0ZW0tc2V0dGluZ3MiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyODoiaHR0cDovL3NhYXNraXQuZGV2LmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779709785);

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.sms_queues: ~23 rows (approximately)
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
	(21, 0, '639855793446', 'Dear Mr/Mrs. DELA PENA, \nKHALIL DELA PEÅƒA just entered TUBAJON NATIONAL HIGH SCHOOL (LATE) @ May 22, 2026 03:29:21 PM. Code: VC-0000000055', 'failed', 0, 0, '2026-05-22 15:29:21', '2026-05-25 11:02:00', 'active', 0, NULL),
	(22, 0, '639468019180', 'Dear Mr/Mrs. ANGOB, \nJV ANGOB just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:30:14 PM. Code: VC-0000000059', 'sent', 0, 0, '2026-05-22 15:30:14', '2026-05-22 07:33:56', 'active', 0, NULL),
	(23, 0, '639855793446', 'Dear Mr/Mrs. DELA PENA, \nKHALIL DELA PEÅƒA just entered TUBAJON NATIONAL HIGH SCHOOL @ May 22, 2026 03:30:18 PM. Code: VC-0000000060', 'failed', 0, 0, '2026-05-22 15:30:18', '2026-05-25 11:03:32', 'active', 0, NULL),
	(24, 3, '639128941731', 'Dear Mr/Mrs. loreto P,\nloreto PILOTON just entered LORETO NATIONAL HIGH SCHOOL (LATE) @ May 25, 2026 11:14:33 AM. Code: VC-0000000014', 'failed', 0, 0, '2026-05-25 11:14:33', '2026-05-25 11:03:36', 'active', 0, NULL);

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
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','locked','unlocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.students: ~0 rows (approximately)
DELETE FROM `students`;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.support_tickets: ~1 rows (approximately)
DELETE FROM `support_tickets`;
INSERT INTO `support_tickets` (`id`, `ticket_no`, `school_id`, `user_id`, `assigned_to`, `subject`, `description`, `priority`, `status`, `category`, `resolved_at`, `attachment`, `response_time_minutes`, `is_incident`, `source`, `ip_address`, `user_agent`, `status_record`, `archived`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
	(1, 'ST-2026-000001', NULL, 1, NULL, 'sadasd', 'asdasd', 'low', 'closed', 'dasdasd', '2026-05-25 19:23:38', NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:20:05', '2026-05-25 11:25:57'),
	(2, 'ST-2026-000002', NULL, 1, NULL, 'hljklhjkl', 'lhjklhjkl', 'medium', 'open', 'dasdasd', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:33:03', '2026-05-25 11:33:03'),
	(3, 'ST-2026-000003', NULL, 1, NULL, 'hljklhjkl', '41451', 'low', 'in_progress', 'dasdasd', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:33:57', '2026-05-25 11:35:13'),
	(4, 'ST-2026-000004', NULL, 1, NULL, 'hljklhjkl', 'rjhgjh', 'high', 'in_progress', 'student_management', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:38:24', '2026-05-25 11:38:34'),
	(5, 'ST-2026-000005', NULL, 1, NULL, 'hljklhjklgk', 'kghjkghjk', 'medium', 'open', 'qr_nfc', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:42:33', '2026-05-25 11:42:33'),
	(6, 'ST-2026-000006', NULL, 1, NULL, 'hljklhjklgk', 'kghjkhjk', 'medium', 'open', 'qr_nfc', NULL, NULL, NULL, 0, 'web', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'active', 0, 1, 1, '2026-05-25 11:48:46', '2026-05-25 11:48:46');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.support_ticket_replies: ~1 rows (approximately)
DELETE FROM `support_ticket_replies`;
INSERT INTO `support_ticket_replies` (`id`, `ticket_id`, `user_id`, `message`, `attachment`, `is_internal`, `status_record`, `archived`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Hello po', NULL, 0, 'active', 0, 1, 1, '2026-05-25 11:21:04', '2026-05-25 11:21:04'),
	(2, 1, 1, 'jjkk', NULL, 0, 'active', 0, 1, 1, '2026-05-25 11:25:44', '2026-05-25 11:25:44'),
	(3, 1, 1, 'jghjghj', NULL, 0, 'active', 0, 1, 1, '2026-05-25 11:31:22', '2026-05-25 11:31:22');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.suspicious_activities: 3 rows
DELETE FROM `suspicious_activities`;
/*!40000 ALTER TABLE `suspicious_activities` DISABLE KEYS */;
INSERT INTO `suspicious_activities` (`id`, `user_id`, `type`, `severity`, `description`, `ip_address`, `meta`, `detected_at`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'failed_logins', 'critical', 'Multiple failed login attempts detected.', '::1', '{"attempts": 5}', '2026-05-25 09:15:57', '2026-05-25 09:15:57', '2026-05-25 09:15:57'),
	(2, 2, 'multiple_sessions', 'warning', 'Multiple simultaneous active sessions detected.', NULL, '{"sessions": 3}', '2026-05-25 09:16:20', '2026-05-25 09:16:20', '2026-05-25 09:16:20'),
	(3, 2, 'multiple_sessions', 'warning', 'Multiple simultaneous active sessions detected.', NULL, '{"sessions": 8}', '2026-05-25 09:48:11', '2026-05-25 09:48:11', '2026-05-25 09:48:11');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.system_settings: 1 rows
DELETE FROM `system_settings`;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` (`id`, `cacert_path`, `python_path`, `port_com`, `sms_failed_count`, `sms_low_balance`, `total_sent`, `sms_provider`, `sms_api_url`, `sms_api_key`, `sms_api_device_id`, `sms_last_failed_at`, `gsm_enabled`, `sms_enabled`, `created_at`, `updated_at`) VALUES
	(1, 'C:\\wamp64\\bin\\php\\php8.4.15\\extras\\ssl\\cacert.pem', 'C:/Users/USER/AppData/Local/Programs/Python/Python313/python.exe', 'COM3', 6, 0, 0, 'gsm', NULL, NULL, NULL, '2026-05-25 18:50:39', 1, 1, '2026-05-25 10:03:34', '2026-05-25 10:51:55');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;

-- Dumping structure for table snsu_safetrack.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `is_super_admin` int(10) unsigned NOT NULL DEFAULT '0',
  `school_id` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table snsu_safetrack.users: 4 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `is_super_admin`, `school_id`, `filepath`, `qr_code`, `nfc_code`, `conn_id`, `user_type`, `name`, `email`, `email_verified_at`, `avatar`, `fcm_token`, `profile_pic`, `password`, `remember_token`, `google_id`, `verified`, `created_by`, `updated_by`, `created_at`, `updated_at`, `last_activity_at`, `status`, `archived`, `deleted_at`) VALUES
	(1, 0, 0, '0', '0', NULL, 0, 0, 'Antonio Jr Piloton', 'apiloton1@snsu.edu.ph', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocICxZuvbmnED8B5vH9nAzZ75oj4EyyJOq04YOMO7jxaSaNGZqsv=s96-c', NULL, NULL, '$2y$12$KOu9VkzGkjxU5Ztn1Jdp/OptDZxOV.pTIgY9aFMn0TKRCDbez835.', 'lVT0UqakqeUCPknHHmDwBzp5V02Pq6pSKwB4xVYsUW92aJM6e00EGYuqCgcE', '117952079880321133596', 1, NULL, NULL, '2026-05-25 11:23:26', '2026-05-25 11:52:38', '2026-05-25 19:52:38', NULL, 0, NULL),
	(2, 0, 1, '0', '304854000001', NULL, 1, 0, 'NORVEN ESPINOSA', 'nespinosa@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$WWpdipcPO1wqNNF4HfHMROcP.XbpLzZb39a5hh5GCvLtYncSq0zSm', NULL, NULL, NULL, 1, 1, '2026-05-25 11:37:09', '2026-05-25 11:47:08', '2026-05-25 19:47:08', 'active', 0, NULL),
	(3, 0, 1, '0', '304854000002', NULL, 2, 0, 'ESTER TURAJA', 'eturaja@tnhs.edu.ph', NULL, NULL, NULL, NULL, '$2y$12$RSojH6443ykSykVitn0ls.6v6D4Wq7zzGuNoZOZR7pTigonvvPGXm', NULL, NULL, NULL, 1, 1, '2026-05-25 11:56:06', '2026-05-25 04:09:15', NULL, 'active', 0, NULL),
	(4, 0, 0, '0', '0', NULL, 0, 0, 'ANTONIO JR. PILOTON', 'antoniojrpiloton@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c', NULL, NULL, '$2y$12$7WV/LwhSMtEVOMNnsIYNM.67OPvPQatw09wfqAxDzHIDAAGY0uuRa', '8EepkQ0GGeAbCLjgrx5HZuTFM0GyctXTqqB0N2xng3gLRzIswoBhSh101gZW', '112090857259724913006', 1, NULL, NULL, '2026-05-25 17:12:29', '2026-05-25 09:12:36', NULL, NULL, 0, NULL);
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
