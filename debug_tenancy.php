<?php
/**
 * Tenancy Diagnostic Script
 * Run this to identify and fix tenant-related issues
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Tenancy Diagnostic Report ===\n\n";

// 1. Check for schools without tenant records
echo "1. Checking for schools with missing tenant records...\n";
$missingTenants = DB::select("
    SELECT s.id, s.schoolName, s.tenant_id, s.domain
    FROM schools s
    LEFT JOIN tenants t ON s.tenant_id = t.id
    WHERE s.tenant_id IS NOT NULL AND t.id IS NULL
");

if (empty($missingTenants)) {
    echo "   ✓ All schools have valid tenant records\n\n";
} else {
    echo "   ✗ Found " . count($missingTenants) . " schools with missing tenant records:\n";
    foreach ($missingTenants as $school) {
        echo "     - School ID: {$school->id}, Name: {$school->schoolName}, Missing Tenant ID: {$school->tenant_id}\n";

        // Generate SQL to fix
        $tenantId = $school->tenant_id;
        $domain = $school->domain ? explode('.', $school->domain)[0] : 'school-' . $school->id;
        $name = addslashes($school->schoolName);

        echo "       FIX SQL:\n";
        echo "       INSERT INTO `tenants` (`id`, `domain`, `data`, `created_at`, `updated_at`)\n";
        echo "       VALUES ('{$tenantId}', '{$domain}', '{\"name\":\"{$name}\"}', NOW(), NOW());\n\n";
    }
}

// 2. Check for tenants without domain records
echo "2. Checking for tenants without domain records...\n";
$tenantsWithoutDomains = DB::select("
    SELECT t.id, t.domain, t.data
    FROM tenants t
    LEFT JOIN domains d ON t.id = d.tenant_id
    WHERE d.tenant_id IS NULL
");

if (empty($tenantsWithoutDomains)) {
    echo "   ✓ All tenants have domain records\n\n";
} else {
    echo "   ✗ Found " . count($tenantsWithoutDomains) . " tenants without domain records:\n";
    foreach ($tenantsWithoutDomains as $tenant) {
        echo "     - Tenant ID: {$tenant->id}, Domain: {$tenant->domain}\n";

        // Generate SQL to fix
        $school = DB::table('schools')->where('tenant_id', $tenant->id)->first();
        $domain = $school ? $school->domain : ($tenant->domain . '.cloudischool.com');

        echo "       FIX SQL:\n";
        echo "       INSERT INTO `domains` (`domain`, `tenant_id`, `created_at`, `updated_at`)\n";
        echo "       VALUES ('{$domain}', '{$tenant->id}', NOW(), NOW());\n\n";
    }
}

// 3. Check for users with invalid tenant_id references
echo "3. Checking for users with invalid tenant_id (should reference schools.id)...\n";
$usersWithInvalidTenant = DB::select("
    SELECT u.id, u.name, u.email, u.tenant_id, u.school_id
    FROM users u
    LEFT JOIN schools s ON u.tenant_id = s.id
    WHERE u.tenant_id IS NOT NULL AND s.id IS NULL
");

if (empty($usersWithInvalidTenant)) {
    echo "   ✓ All users have valid tenant_id references\n\n";
} else {
    echo "   ✗ Found " . count($usersWithInvalidTenant) . " users with invalid tenant_id:\n";
    foreach ($usersWithInvalidTenant as $user) {
        echo "     - User ID: {$user->id}, Name: {$user->name}, Invalid tenant_id: {$user->tenant_id}\n";
    }
    echo "\n";
}

// 4. Summary of all tenants and schools
echo "4. Current Tenant-School Mapping:\n";
$mapping = DB::select("
    SELECT 
        t.id as tenant_id,
        t.domain as tenant_domain,
        s.id as school_id,
        s.schoolName,
        s.domain as school_domain,
        d.domain as actual_domain
    FROM tenants t
    LEFT JOIN schools s ON t.id = s.tenant_id
    LEFT JOIN domains d ON t.id = d.tenant_id
    ORDER BY t.id
");

foreach ($mapping as $row) {
    echo "   Tenant ID: {$row->tenant_id} → School: " . ($row->schoolName ?? 'N/A') . " ({$row->school_id})\n";
    echo "     Domains: {$row->actual_domain}\n";
}

echo "\n=== End of Report ===\n";
