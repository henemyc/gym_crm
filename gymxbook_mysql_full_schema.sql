-- GymXBook CRM - MySQL/MariaDB schema for the Laravel 12 migration set.
-- WARNING: This script DROPS the current tables in gxb_crm before recreating them.
-- Back up the database first if it contains anything you need to keep.

CREATE DATABASE IF NOT EXISTS `gxb_crm`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gxb_crm`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `lead_imports`;
DROP TABLE IF EXISTS `lead_activities`;
DROP TABLE IF EXISTS `follow_ups`;
DROP TABLE IF EXISTS `leads`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `migrations`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `role` varchar(24) NOT NULL DEFAULT 'sales',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(30) DEFAULT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'Asia/Kolkata',
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_last_activity_at_index` (`last_activity_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `gym_name` varchar(255) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `location` varchar(120) DEFAULT NULL,
  `source` varchar(80) NOT NULL DEFAULT 'Other',
  `priority` varchar(16) NOT NULL DEFAULT 'warm',
  `status` varchar(24) NOT NULL DEFAULT 'new',
  `notes` longtext DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_phone_index` (`phone`),
  KEY `leads_location_index` (`location`),
  KEY `leads_source_index` (`source`),
  KEY `leads_priority_index` (`priority`),
  KEY `leads_status_index` (`status`),
  KEY `leads_status_priority_index` (`status`, `priority`),
  KEY `leads_assigned_to_created_at_index` (`assigned_to`, `created_at`),
  CONSTRAINT `leads_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `follow_ups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `kind` varchar(20) NOT NULL DEFAULT 'follow_up',
  `due_at` datetime NOT NULL,
  `outcome` varchar(32) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `next_follow_up_at` datetime DEFAULT NULL,
  `meeting_at` datetime DEFAULT NULL,
  `meeting_with` varchar(120) DEFAULT NULL,
  `trial_at` datetime DEFAULT NULL,
  `trial_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `follow_ups_lead_id_foreign` (`lead_id`),
  KEY `follow_ups_assigned_to_foreign` (`assigned_to`),
  KEY `follow_ups_created_by_foreign` (`created_by`),
  KEY `follow_ups_kind_index` (`kind`),
  KEY `follow_ups_due_at_index` (`due_at`),
  KEY `follow_ups_completed_at_index` (`completed_at`),
  KEY `follow_ups_next_follow_up_at_index` (`next_follow_up_at`),
  KEY `follow_ups_meeting_at_index` (`meeting_at`),
  KEY `follow_ups_trial_at_index` (`trial_at`),
  KEY `follow_ups_completed_at_due_at_index` (`completed_at`, `due_at`),
  CONSTRAINT `follow_ups_lead_id_constraint` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follow_ups_assigned_to_constraint` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `follow_ups_created_by_constraint` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lead_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `event` varchar(40) NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_activities_lead_id_foreign` (`lead_id`),
  KEY `lead_activities_user_id_foreign` (`user_id`),
  KEY `lead_activities_event_index` (`event`),
  KEY `lead_activities_lead_id_created_at_index` (`lead_id`, `created_at`),
  CONSTRAINT `lead_activities_lead_id_constraint` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_activities_user_id_constraint` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lead_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(24) NOT NULL DEFAULT 'uploaded',
  `headers` json DEFAULT NULL,
  `mapping` json DEFAULT NULL,
  `summary` json DEFAULT NULL,
  `rows_total` int unsigned NOT NULL DEFAULT 0,
  `rows_valid` int unsigned NOT NULL DEFAULT 0,
  `rows_invalid` int unsigned NOT NULL DEFAULT 0,
  `rows_imported` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_imports_user_id_foreign` (`user_id`),
  KEY `lead_imports_status_index` (`status`),
  CONSTRAINT `lead_imports_user_id_constraint` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migrations already represented by this hand-built schema so Artisan will not try to recreate the tables.
INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('0001_01_01_000000_create_users_table', 1),
  ('0001_01_01_000001_create_cache_table', 1),
  ('0001_01_01_000002_create_jobs_table', 1),
  ('2026_10_07_000000_add_crm_fields_to_users_table', 2),
  ('2026_10_07_000100_create_leads_table', 2),
  ('2026_10_07_000200_create_follow_ups_table', 2),
  ('2026_10_07_000300_create_lead_activities_table', 2),
  ('2026_10_07_000400_create_lead_imports_table', 2),
  ('2026_10_13_000000_add_presence_timestamps_to_users_table', 3);

-- Demo admin login: admin@gymxbook.test / password
-- bcrypt cost 12; change this password before using the app beyond local testing.
INSERT INTO `users` (
  `name`, `email`, `email_verified_at`, `password`, `remember_token`,
  `created_at`, `updated_at`, `role`, `is_active`, `phone`, `timezone`, `notifications_enabled`
) VALUES (
  'Rhea Kapoor',
  'admin@gymxbook.test',
  CURRENT_TIMESTAMP,
  '$2y$12$TSlZcJcOOp7.u66PLlaG0unCM7vr0Rqori5nkyR1LfcudCXKYQxZa',
  NULL,
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  'admin',
  1,
  '9876501200',
  'Asia/Kolkata',
  1
);
