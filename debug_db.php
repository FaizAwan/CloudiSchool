<?php
// Mock Laravel environment enough to run Eloquent if possible, 
// but since I don't have vendor/autoload.php visible, this might fail.
// Let's try to just use basic PDO to check the database.

$env = parse_ini_file('.env');
$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD']);

    echo "TENANTS:\n";
    $stmt = $pdo->query("SELECT * FROM tenants LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }

    echo "\nSCHOOLS:\n";
    $stmt = $pdo->query("SELECT id, tenant_id, schoolName FROM schools LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }

    echo "\nUSERS (first 5):\n";
    $stmt = $pdo->query("SELECT id, name, tenant_id, school_id FROM users LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
