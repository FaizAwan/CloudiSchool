<?php
/**
 * Simple Tenancy Diagnostic Script
 * Connects directly to database to check tenant issues
 */

// Load .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("$key=$value");
        }
    }
}

// Database connection
$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_DATABASE') ?: 'cloudischool';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Tenancy Diagnostic Report ===\n\n";
    echo "Database: $database @ $host\n\n";

    // 1. Check for schools without tenant records
    echo "1. Schools with missing tenant records:\n";
    $stmt = $pdo->query("
        SELECT s.id, s.schoolName, s.tenant_id, s.domain
        FROM schools s
        LEFT JOIN tenants t ON s.tenant_id = t.id
        WHERE s.tenant_id IS NOT NULL AND t.id IS NULL
    ");
    $missingTenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($missingTenants)) {
        echo "   ✓ All schools have valid tenant records\n\n";
    } else {
        echo "   ✗ Found " . count($missingTenants) . " schools with missing tenant records:\n\n";
        foreach ($missingTenants as $school) {
            echo "   School: {$school['schoolName']} (ID: {$school['id']})\n";
            echo "   Missing Tenant ID: {$school['tenant_id']}\n";
            echo "   Domain: {$school['domain']}\n";

            $tenantId = $school['tenant_id'];
            $domain = $school['domain'] ? explode('.', $school['domain'])[0] : 'school-' . $school['id'];
            $name = addslashes($school['schoolName']);

            echo "\n   FIX SQL:\n";
            echo "   INSERT INTO `tenants` (`id`, `domain`, `data`, `created_at`, `updated_at`)\n";
            echo "   VALUES ('{$tenantId}', '{$domain}', '{\"name\":\"{$name}\"}', NOW(), NOW());\n\n";

            // Also generate domain insert
            if ($school['domain']) {
                echo "   INSERT INTO `domains` (`domain`, `tenant_id`, `created_at`, `updated_at`)\n";
                echo "   VALUES ('{$school['domain']}', '{$tenantId}', NOW(), NOW());\n\n";
            }
            echo "   " . str_repeat("-", 70) . "\n\n";
        }
    }

    // 2. Check for tenants without domain records
    echo "2. Tenants without domain records:\n";
    $stmt = $pdo->query("
        SELECT t.id, t.domain, t.data
        FROM tenants t
        LEFT JOIN domains d ON t.id = d.tenant_id
        WHERE d.tenant_id IS NULL
    ");
    $tenantsWithoutDomains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tenantsWithoutDomains)) {
        echo "   ✓ All tenants have domain records\n\n";
    } else {
        echo "   ✗ Found " . count($tenantsWithoutDomains) . " tenants without domain records:\n\n";
        foreach ($tenantsWithoutDomains as $tenant) {
            echo "   Tenant ID: {$tenant['id']}, Domain: {$tenant['domain']}\n";

            // Find associated school
            $schoolStmt = $pdo->prepare("SELECT domain FROM schools WHERE tenant_id = ?");
            $schoolStmt->execute([$tenant['id']]);
            $school = $schoolStmt->fetch(PDO::FETCH_ASSOC);
            $domain = $school ? $school['domain'] : ($tenant['domain'] . '.cloudischool.com');

            echo "   FIX SQL:\n";
            echo "   INSERT INTO `domains` (`domain`, `tenant_id`, `created_at`, `updated_at`)\n";
            echo "   VALUES ('{$domain}', '{$tenant['id']}', NOW(), NOW());\n\n";
        }
    }

    // 3. Summary
    echo "3. Current Tenant-School Mapping:\n";
    $stmt = $pdo->query("
        SELECT 
            t.id as tenant_id,
            t.domain as tenant_domain,
            s.id as school_id,
            s.schoolName,
            s.domain as school_domain,
            GROUP_CONCAT(d.domain) as actual_domains
        FROM tenants t
        LEFT JOIN schools s ON t.id = s.tenant_id
        LEFT JOIN domains d ON t.id = d.tenant_id
        GROUP BY t.id, s.id
        ORDER BY t.id
    ");
    $mapping = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mapping as $row) {
        $schoolInfo = $row['schoolName'] ? "{$row['schoolName']} (ID: {$row['school_id']})" : "No school linked";
        echo "   Tenant {$row['tenant_id']}: {$schoolInfo}\n";
        echo "     Domains: " . ($row['actual_domains'] ?: 'NONE') . "\n";
    }

    echo "\n=== End of Report ===\n";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
