-- ============================================================================
-- COMPLETE TENANT IDENTIFICATION FIX
-- ============================================================================
-- This SQL script fixes the TenantCouldNotBeIdentifiedByPathException error
-- by ensuring all schools have corresponding tenant records
-- ============================================================================

-- Step 1: Add missing tenant record for Demo School (ID 5)
-- This is the PRIMARY FIX for the reported issue
INSERT INTO `tenants` (`id`, `domain`, `data`, `created_at`, `updated_at`)
VALUES ('5', 'demo', '{"name":"Demo School"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `data` = '{"name":"Demo School"}',
    `updated_at` = NOW();

-- Step 2: Add domain record for Demo School
INSERT INTO `domains` (`domain`, `tenant_id`, `created_at`, `updated_at`)
VALUES ('demo.cloudischool.com', '5', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `updated_at` = NOW();

-- ============================================================================
-- DIAGNOSTIC QUERIES
-- Run these to verify the fix and identify any other issues
-- ============================================================================

-- Query 1: Check for schools with missing tenant records
SELECT 
    s.id AS school_id,
    s.schoolName,
    s.tenant_id,
    s.domain,
    CASE 
        WHEN t.id IS NULL THEN '❌ MISSING TENANT'
        ELSE '✓ OK'
    END AS status
FROM schools s
LEFT JOIN tenants t ON s.tenant_id = t.id
WHERE s.tenant_id IS NOT NULL
ORDER BY s.id;

-- Query 2: Check for tenants without domain records
SELECT 
    t.id AS tenant_id,
    t.domain AS tenant_domain,
    COUNT(d.domain) AS domain_count,
    GROUP_CONCAT(d.domain) AS domains,
    CASE 
        WHEN COUNT(d.domain) = 0 THEN '❌ NO DOMAINS'
        ELSE '✓ OK'
    END AS status
FROM tenants t
LEFT JOIN domains d ON t.id = d.tenant_id
GROUP BY t.id
ORDER BY t.id;

-- Query 3: Complete tenant-school mapping overview
SELECT 
    t.id AS tenant_id,
    t.domain AS tenant_domain,
    s.id AS school_id,
    s.schoolName,
    s.domain AS school_domain,
    GROUP_CONCAT(d.domain) AS actual_domains,
    CASE 
        WHEN s.id IS NULL THEN '⚠️ No School'
        WHEN COUNT(d.domain) = 0 THEN '⚠️ No Domains'
        ELSE '✓ Complete'
    END AS status
FROM tenants t
LEFT JOIN schools s ON t.id = s.tenant_id
LEFT JOIN domains d ON t.id = d.tenant_id
GROUP BY t.id, s.id
ORDER BY t.id;

-- Query 4: Check users table for correct tenant_id references
-- Note: users.tenant_id should reference schools.id, NOT tenants.id
SELECT 
    u.id AS user_id,
    u.name,
    u.email,
    u.tenant_id,
    u.school_id,
    s.schoolName,
    CASE 
        WHEN u.tenant_id IS NOT NULL AND s.id IS NULL THEN '❌ INVALID tenant_id'
        WHEN u.tenant_id IS NULL THEN '⚠️ NULL tenant_id'
        ELSE '✓ OK'
    END AS status
FROM users u
LEFT JOIN schools s ON u.tenant_id = s.id
WHERE u.role != 'superadmin'
ORDER BY u.id;

-- ============================================================================
-- ADDITIONAL FIXES (if needed based on diagnostic queries)
-- ============================================================================

-- If you find other schools with missing tenants, use this template:
-- Replace {TENANT_ID}, {DOMAIN_SLUG}, {SCHOOL_NAME}, and {FULL_DOMAIN} with actual values

/*
INSERT INTO `tenants` (`id`, `domain`, `data`, `created_at`, `updated_at`)
VALUES ('{TENANT_ID}', '{DOMAIN_SLUG}', '{"name":"{SCHOOL_NAME}"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `data` = '{"name":"{SCHOOL_NAME}"}',
    `updated_at` = NOW();

INSERT INTO `domains` (`domain`, `tenant_id`, `created_at`, `updated_at`)
VALUES ('{FULL_DOMAIN}', '{TENANT_ID}', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `updated_at` = NOW();
*/

-- ============================================================================
-- VERIFICATION
-- ============================================================================

-- After running the fixes, verify tenant 5 is properly configured:
SELECT 
    'Tenant Record' AS check_type,
    CASE WHEN EXISTS(SELECT 1 FROM tenants WHERE id = '5') THEN '✓ EXISTS' ELSE '❌ MISSING' END AS status
UNION ALL
SELECT 
    'Domain Record' AS check_type,
    CASE WHEN EXISTS(SELECT 1 FROM domains WHERE tenant_id = '5') THEN '✓ EXISTS' ELSE '❌ MISSING' END AS status
UNION ALL
SELECT 
    'School Link' AS check_type,
    CASE WHEN EXISTS(SELECT 1 FROM schools WHERE tenant_id = '5') THEN '✓ EXISTS' ELSE '❌ MISSING' END AS status;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
