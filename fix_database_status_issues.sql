-- SQL fix for Data Truncation, Missing Columns, and Missing Tables
-- Please upload this file to your database via phpMyAdmin or your DB tool

-- 1. Students: Convert status from restrictive ENUM to VARCHAR to allow 'SLC', 'promoted-to...', etc.
ALTER TABLE students MODIFY COLUMN status VARCHAR(255) DEFAULT 'active';

-- 2. Subjects: Convert status from restrictive ENUM to VARCHAR for flexibility
ALTER TABLE subjects MODIFY COLUMN status VARCHAR(255) DEFAULT 'active';

-- 3. Teachers: Add status column if it's missing (it was missing from the SQL dump)
DROP PROCEDURE IF EXISTS AddTeacherStatusColumn;
DELIMITER //
CREATE PROCEDURE AddTeacherStatusColumn()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'teachers' 
        AND COLUMN_NAME = 'status'
    ) THEN
        ALTER TABLE teachers ADD COLUMN status VARCHAR(255) DEFAULT 'active' AFTER phone;
    END IF;
END //
DELIMITER ;
CALL AddTeacherStatusColumn();
DROP PROCEDURE AddTeacherStatusColumn;

-- 4. Create Principal Remarks table if it's missing
CREATE TABLE IF NOT EXISTS `principal_remarks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `percentage_min` decimal(8,2) NOT NULL,
  `percentage_max` decimal(8,2) NOT NULL,
  `remark` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create Question Bank table if it's missing
CREATE TABLE IF NOT EXISTS `question_bank` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED DEFAULT 1,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `class_level` varchar(255) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','short','long','true_false','fill_blank') NOT NULL,
  `difficulty_level` enum('easy','medium','hard') NOT NULL,
  `marks` int(11) NOT NULL DEFAULT 1,
  `explanation` text DEFAULT NULL,
  `correct_answer` text DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `chapter` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) DEFAULT 'active',
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create Question Bank Options table if it's missing
CREATE TABLE IF NOT EXISTS `question_bank_options` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_letter` char(1) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`question_id`) REFERENCES `question_bank`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
