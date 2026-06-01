<?php
$host = '127.0.0.1';
$db   = 'u296329189_cloudischoool';
$user = 'u296329189_cloudischoool';
$pass = 'Cloudischool@2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ATTR_ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::ATTR_FETCH_MODE_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connected to database successfully.\n";

     // Check if tenant_id exists
     $check = $pdo->query("SHOW COLUMNS FROM `feetypes` LIKE 'tenant_id'");
     if (!$check->fetch()) {
         echo "Adding tenant_id column to feetypes table...\n";
         $pdo->exec("ALTER TABLE `feetypes` ADD `tenant_id` BIGINT(20) UNSIGNED NULL AFTER `id` text");
         // Wait, the error message in the screenshot showed tenant_id = 2.
         // Let's set a default or just leave it NULL for now.
         echo "tenant_id column added successfully.\n";
     } else {
         echo "tenant_id column already exists.\n";
     }

     // Check if school_id exists
     $check = $pdo->query("SHOW COLUMNS FROM `feetypes` LIKE 'school_id'");
     if (!$check->fetch()) {
         echo "Adding school_id column to feetypes table...\n";
         $pdo->exec("ALTER TABLE `feetypes` ADD `school_id` BIGINT(20) UNSIGNED NULL AFTER `tenant_id` text");
         echo "school_id column added successfully.\n";
     } else {
         echo "school_id column already exists.\n";
     }

     echo "Database fix completed.\n";

} catch (\PDOException $e) {
     echo "Database error: " . $e->getMessage() . "\n";
}
