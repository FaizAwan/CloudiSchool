<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$db   = 'u296329189_cloudischoool';
$user = 'u296329189_cloudischoool';
$pass = 'Cloudischool@2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to database successfully.\n";

    // 1. Create subjects table
    $sql1 = "CREATE TABLE IF NOT EXISTS `subjects` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `school_id` bigint(20) unsigned DEFAULT NULL,
        `subject_name` varchar(255) NOT NULL,
        `subject_code` varchar(255) DEFAULT NULL,
        `class_id` bigint(20) unsigned NOT NULL,
        `term` varchar(255) DEFAULT NULL,
        `term_marks` varchar(255) DEFAULT NULL,
        `total_marks` decimal(8,2) NOT NULL DEFAULT '100.00',
        `passing_marks` decimal(8,2) NOT NULL DEFAULT '33.00',
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `sort_order` int(11) NOT NULL DEFAULT '0',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `subjects_tenant_id_index` (`tenant_id`),
        KEY `subjects_school_id_index` (`school_id`),
        KEY `subjects_class_id_index` (`class_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql1);
    echo "Table 'subjects' created or already exists.\n";

    // 2. Create parents table (if missing)
    $sql2 = "CREATE TABLE IF NOT EXISTS `parents` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `school_id` bigint(20) unsigned DEFAULT NULL,
        `parentName` varchar(255) NOT NULL,
        `is_commandercityschool_employee` enum('Yes','No') DEFAULT 'No',
        `phone` varchar(255) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `status` varchar(255) DEFAULT NULL,
        `status_other` text DEFAULT NULL,
        `status_business_name` varchar(255) DEFAULT NULL,
        `status_private_job_detail` text DEFAULT NULL,
        `status_government_job_detail` text DEFAULT NULL,
        `status_unemployed_reason` text DEFAULT NULL,
        `status_staff_detail` text DEFAULT NULL,
        `resident_country` varchar(255) DEFAULT NULL,
        `resident_city` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `parents_tenant_id_index` (`tenant_id`),
        KEY `parents_school_id_index` (`school_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql2);
    echo "Table 'parents' created or already exists.\n";

    // 3. Create teachers table (if missing)
    $sql3 = "CREATE TABLE IF NOT EXISTS `teachers` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `school_id` bigint(20) unsigned DEFAULT NULL,
        `teacherName` varchar(255) NOT NULL,
        `teacher_name` varchar(255) DEFAULT NULL,
        `email` varchar(255) NOT NULL,
        `phone` varchar(255) DEFAULT NULL,
        `class_id` bigint(20) unsigned DEFAULT NULL,
        `className` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `teachers_email_unique` (`email`),
        KEY `teachers_tenant_id_index` (`tenant_id`),
        KEY `teachers_school_id_index` (`school_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql3);
    echo "Table 'teachers' created or already exists.\n";

    // 4. Create academicyears table (if missing)
    $sql4 = "CREATE TABLE IF NOT EXISTS `academicyears` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `session` varchar(255) NOT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT '0',
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `academicyears_session_unique` (`session`),
        KEY `academicyears_tenant_id_index` (`tenant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql4);
    echo "Table 'academicyears' created or already exists.\n";

    // 5. Create manual_exam_entries table (if missing)
    $sql5 = "CREATE TABLE IF NOT EXISTS `manual_exam_entries` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `class_id` bigint(20) unsigned NOT NULL,
        `student_id` bigint(20) unsigned NOT NULL,
        `subject` varchar(255) NOT NULL,
        `term` varchar(255) NOT NULL,
        `data` json DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `manual_exam_entries_tenant_id_index` (`tenant_id`),
        KEY `manual_exam_entries_class_id_index` (`class_id`),
        KEY `manual_exam_entries_student_id_index` (`student_id`),
        KEY `manual_exam_entries_subject_index` (`subject`),
        KEY `manual_exam_entries_term_index` (`term`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql5);
    echo "Table 'manual_exam_entries' created or already exists.\n";

    // 6. Create exam_terms table (if missing)
    $sql6 = "CREATE TABLE IF NOT EXISTS `exam_terms` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id" . "` varchar(255) DEFAULT NULL,
        `term_name` varchar(255) NOT NULL,
        `display_name` varchar(255) DEFAULT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT '1',
        `sort_order` int(11) NOT NULL DEFAULT '0',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `exam_terms_tenant_id_index` (`tenant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql6);
    echo "Table 'exam_terms' created or already exists.\n";

    // 7. Create challans
    $sql7 = "CREATE TABLE IF NOT EXISTS `challans` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `student_id` bigint(20) unsigned DEFAULT NULL,
        `challan_no` varchar(255) DEFAULT NULL,
        `amount` decimal(10,2) DEFAULT '0.00',
        `status` varchar(255) DEFAULT 'unpaid',
        `due_date` date DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql7);
    echo "Table 'challans' created or already exists.\n";

    // 8. Create attendances
    $sql8 = "CREATE TABLE IF NOT EXISTS `attendances` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(255) DEFAULT NULL,
        `student_id` bigint(20) unsigned DEFAULT NULL,
        `attendance_date` date DEFAULT NULL,
        `status` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql8);
    echo "Table 'attendances' created or already exists.\n";

    echo "All critical tables handled successfully.";

} catch (\PDOException $e) {
     echo "Error: " . $e->getMessage();
}
