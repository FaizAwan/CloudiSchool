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

    $tables = [
        "subjects" => "CREATE TABLE IF NOT EXISTS `subjects` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "parents" => "CREATE TABLE IF NOT EXISTS `parents` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "teachers" => "CREATE TABLE IF NOT EXISTS `teachers` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "students" => "CREATE TABLE IF NOT EXISTS `students` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `school_id` bigint(20) unsigned DEFAULT NULL,
            `studentName` varchar(255) NOT NULL,
            `class_id` bigint(20) unsigned NOT NULL,
            `parent_id` bigint(20) unsigned DEFAULT NULL,
            `grno` varchar(255) DEFAULT NULL,
            `session` varchar(255) DEFAULT NULL,
            `gender` varchar(255) DEFAULT NULL,
            `date_of_birth` date DEFAULT NULL,
            `address` text DEFAULT NULL,
            `phone` varchar(255) DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `user_id` bigint(20) unsigned DEFAULT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `students_tenant_id_index` (`tenant_id`),
            KEY `students_school_id_index` (`school_id`),
            KEY `students_class_id_index` (`class_id`),
            KEY `students_parent_id_index` (`parent_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "classes" => "CREATE TABLE IF NOT EXISTS `classes` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `school_id` bigint(20) unsigned DEFAULT NULL,
            `className` varchar(255) NOT NULL,
            `session` varchar(255) DEFAULT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `user_id` bigint(20) unsigned DEFAULT NULL,
            `created_at" . "` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `classes_tenant_id_index` (`tenant_id`),
            KEY `classes_school_id_index` (`school_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "sections" => "CREATE TABLE IF NOT EXISTS `sections` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `sectionName` varchar(255) NOT NULL,
            `class_id` bigint(20) unsigned DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `sections_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "fees" => "CREATE TABLE IF NOT EXISTS `fees` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `student_id` bigint(20) unsigned DEFAULT NULL,
            `class_id` bigint(20) unsigned DEFAULT NULL,
            `fee_name` varchar(255) DEFAULT NULL,
            `amount` decimal(10,2) DEFAULT '0.00',
            `month` int(11) DEFAULT NULL,
            `year` int(11) DEFAULT NULL,
            `status` varchar(255) DEFAULT 'unpaid',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `fees_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "challans" => "CREATE TABLE IF NOT EXISTS `challans` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `student_id` bigint(20) unsigned DEFAULT NULL,
            `challan_no` varchar(255) DEFAULT NULL,
            `amount` decimal(10,2) DEFAULT '0.00',
            `status` varchar(255) DEFAULT 'unpaid',
            `due_date` date DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `challans_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "attendances" => "CREATE TABLE IF NOT EXISTS `attendances` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `student_id` bigint(20) unsigned DEFAULT NULL,
            `attendance_date` date DEFAULT NULL,
            `status` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `attendances_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "academicyears" => "CREATE TABLE IF NOT EXISTS `academicyears` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "exams" => "CREATE TABLE IF NOT EXISTS `exams` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `school_id` bigint(20) unsigned DEFAULT NULL,
            `exam_name` varchar(255) NOT NULL,
            `exam_type_id` bigint(20) unsigned DEFAULT NULL,
            `class_id` bigint(20) unsigned DEFAULT NULL,
            `subject_id` bigint(20) unsigned DEFAULT NULL,
            `exam_date` date DEFAULT NULL,
            `status` varchar(255) DEFAULT 'draft',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `exams_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "exam_results" => "CREATE TABLE IF NOT EXISTS `exam_results` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id` varchar(255) DEFAULT NULL,
            `exam_id` bigint(20) unsigned NOT NULL,
            `student_id` bigint(20) unsigned NOT NULL,
            `marks_obtained` decimal(8,2) DEFAULT '0.00',
            `status` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `exam_results_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "manual_exam_entries" => "CREATE TABLE IF NOT EXISTS `manual_exam_entries` (
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
            KEY `manual_exam_entries_student_id_index` (`student_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "audit_logs" => "CREATE TABLE IF NOT EXISTS `audit_logs` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) unsigned DEFAULT NULL,
            `event` varchar(255) NOT NULL,
            `details` json DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "timetables" => "CREATE TABLE IF NOT EXISTS `timetables` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tenant_id" . "` varchar(255) DEFAULT NULL,
            `teacher_id` bigint(20) unsigned NOT NULL,
            `day` varchar(255) NOT NULL,
            `period_id` bigint(20) unsigned NOT NULL,
            `class` varchar(255) NOT NULL,
            `subject` varchar(255) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `timetables_tenant_id_index` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "Table '$name' handled.\n";
    }

    echo "All tables handled successfully.";

} catch (\PDOException $e) {
     echo "Error: " . $e->getMessage();
}
