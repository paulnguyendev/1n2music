-- ============================================================================
-- PRODUCTION DEPLOYMENT SCRIPT
-- Date: 2026-05-04
-- Description: Fix Distribution Basic bugs and AI limits
-- ============================================================================
--
-- IMPORTANT: Run this script on production database after deploying code changes
--
-- This script:
-- 1. Creates Distribution Basic role (if not exists)
-- 2. Creates Distribution Basic subscription (if not exists)
-- 3. Fixes AI usage limits for Pro Seller, Publishing, and Distribution Basic
-- 4. Updates Publishing subscription pricing
--
-- Estimated execution time: < 5 seconds
-- Rollback: See ROLLBACK section at the end
-- ============================================================================

-- Start transaction for safety
START TRANSACTION;

-- ============================================================================
-- STEP 1: Create Distribution Basic Role (if not exists)
-- ============================================================================

INSERT INTO `rrt_roles` (`name`, `slug`, `created_at`, `updated_at`)
SELECT 'Distribution Basic Annually', 'distribution-basic-annually', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `rrt_roles` WHERE `slug` = 'distribution-basic-annually'
);

-- Get the role_id for Distribution Basic (will be used later)
SET @distribution_basic_role_id = (SELECT id FROM `rrt_roles` WHERE `slug` = 'distribution-basic-annually');

SELECT CONCAT('✓ Distribution Basic role ID: ', @distribution_basic_role_id) as 'Step 1';

-- ============================================================================
-- STEP 2: Create Distribution Basic Subscription (if not exists)
-- ============================================================================

INSERT INTO `rrt_subscriptions` (`name`, `slug`, `price`, `pricing_annually`, `max_track`, `commission`, `heading`, `content`, `description`, `background`, `created_at`, `updated_at`)
SELECT
    'Basic Distribution',
    'distribution-basic',
    0,
    60,
    NULL,
    NULL,
    'Start Your Distribution Journey',
    '<ul class="list-unstyled">
        <li><i class="fas fa-check text-success"></i> Distribute to Melon, Genie Music, Spotify, Apple Music, TikTok</li>
        <li><i class="fas fa-check text-success"></i> 2 Singles or 2 Albums Per Year</li>
        <li><i class="fas fa-check text-success"></i> Keep 100% of your royalties</li>
        <li><i class="fas fa-check text-success"></i> Basic analytics dashboard</li>
        <li><i class="fas fa-check text-success"></i> Email support</li>
    </ul>',
    '2 Singles or 2 Albums Per Year',
    NULL,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `rrt_subscriptions` WHERE `slug` = 'distribution-basic'
);

SELECT '✓ Distribution Basic subscription created/verified' as 'Step 2';

-- ============================================================================
-- STEP 3: Update Publishing Subscription Pricing
-- ============================================================================

UPDATE `rrt_subscriptions`
SET `pricing_annually` = 200,
    `updated_at` = NOW()
WHERE `slug` = 'publishing' AND `pricing_annually` != 200;

SELECT '✓ Publishing pricing updated to $200/year' as 'Step 3';

-- ============================================================================
-- STEP 4: Fix AI Limits - Pro Seller Music Recognition (10 → 5)
-- ============================================================================

-- Pro Seller Monthly (role_id=3)
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = 3 AND `package_id` = 4 AND `usage_count` != 5;

-- Pro Seller Annually (role_id=4)
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = 4 AND `package_id` = 4 AND `usage_count` != 5;

SELECT '✓ Pro Seller Music Recognition updated to 5 uses' as 'Step 4';

-- ============================================================================
-- STEP 5: Fix AI Limits - Publishing (10/10 → 20/40)
-- ============================================================================

-- Publishing AI Mastering (10 → 20)
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 20, `updated_at` = NOW()
WHERE `ai_id` = 1 AND `role_id` = 6 AND `package_id` = 2 AND `usage_count` != 20;

-- Publishing Music Recognition (10 → 40)
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 40, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = 6 AND `package_id` = 4 AND `usage_count` != 40;

SELECT '✓ Publishing AI limits updated to 20/40' as 'Step 5';

-- ============================================================================
-- STEP 6: Create Distribution Basic AI Package Roles (if not exists)
-- ============================================================================

-- AI Mastering for Distribution Basic (5 uses)
INSERT INTO `rrt_ai_package_roles` (`ai_id`, `role_id`, `package_id`, `usage_count`, `download_available`, `price`, `created_at`, `updated_at`)
SELECT 1, @distribution_basic_role_id, 2, 5, 10, 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `rrt_ai_package_roles`
    WHERE `ai_id` = 1 AND `role_id` = @distribution_basic_role_id AND `package_id` = 2
);

-- Music Recognition for Distribution Basic (5 uses)
INSERT INTO `rrt_ai_package_roles` (`ai_id`, `role_id`, `package_id`, `usage_count`, `download_available`, `price`, `created_at`, `updated_at`)
SELECT 2, @distribution_basic_role_id, 4, 5, 10, 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `rrt_ai_package_roles`
    WHERE `ai_id` = 2 AND `role_id` = @distribution_basic_role_id AND `package_id` = 4
);

-- Fix if already exists but has wrong usage_count
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = @distribution_basic_role_id AND `package_id` = 4 AND `usage_count` != 5;

SELECT '✓ Distribution Basic AI limits set to 5/5' as 'Step 6';

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

SELECT '========================================' as '';
SELECT 'VERIFICATION RESULTS' as '';
SELECT '========================================' as '';

-- Verify Distribution Basic role
SELECT 'Distribution Basic Role:' as 'Check', id, name, slug
FROM `rrt_roles`
WHERE `slug` = 'distribution-basic-annually';

-- Verify Distribution Basic subscription
SELECT 'Distribution Basic Subscription:' as 'Check', id, name, slug, pricing_annually
FROM `rrt_subscriptions`
WHERE `slug` = 'distribution-basic';

-- Verify Publishing pricing
SELECT 'Publishing Pricing:' as 'Check', id, name, pricing_annually
FROM `rrt_subscriptions`
WHERE `slug` = 'publishing';

-- Verify AI limits for all roles
SELECT 'AI Limits Summary:' as 'Check';
SELECT
    r.name as role_name,
    CASE
        WHEN apr.ai_id = 1 THEN 'AI Mastering'
        WHEN apr.ai_id = 2 THEN 'Music Recognition'
    END as ai_service,
    apr.usage_count as uses_per_period
FROM `rrt_ai_package_roles` apr
JOIN `rrt_roles` r ON apr.role_id = r.id
WHERE r.slug IN ('proseller-monthly', 'proseller-annually', 'distribution-annually', 'distribution-basic-annually', 'publishing-annually')
ORDER BY r.id, apr.ai_id;

-- Expected results:
-- proseller-monthly: 5 Mastering, 5 Recognition
-- proseller-annually: 5 Mastering, 5 Recognition
-- distribution-annually: 10 Mastering, 20 Recognition
-- distribution-basic-annually: 5 Mastering, 5 Recognition
-- publishing-annually: 20 Mastering, 40 Recognition

SELECT '========================================' as '';
SELECT 'If all values above are correct, run: COMMIT;' as 'Next Step';
SELECT 'If something is wrong, run: ROLLBACK;' as 'Alternative';
SELECT '========================================' as '';

-- ============================================================================
-- COMMIT OR ROLLBACK
-- ============================================================================
--
-- Review the verification results above.
-- If everything looks correct, run:
--   COMMIT;
--
-- If something is wrong, run:
--   ROLLBACK;
--
-- ============================================================================

-- Uncomment ONE of these lines after reviewing:
-- COMMIT;
-- ROLLBACK;

-- ============================================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================================
--
-- If you need to rollback these changes, run this script:
--
-- START TRANSACTION;
--
-- -- Revert Pro Seller Music Recognition to 10
-- UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 2 AND role_id = 3 AND package_id = 4;
-- UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 2 AND role_id = 4 AND package_id = 4;
--
-- -- Revert Publishing AI limits to 10/10
-- UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 1 AND role_id = 6 AND package_id = 2;
-- UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 2 AND role_id = 6 AND package_id = 4;
--
-- -- Revert Publishing pricing to $1000
-- UPDATE rrt_subscriptions SET pricing_annually = 1000 WHERE slug = 'publishing';
--
-- -- Delete Distribution Basic AI package roles
-- DELETE FROM rrt_ai_package_roles WHERE role_id = (SELECT id FROM rrt_roles WHERE slug = 'distribution-basic-annually');
--
-- -- Delete Distribution Basic subscription
-- DELETE FROM rrt_subscriptions WHERE slug = 'distribution-basic';
--
-- -- Delete Distribution Basic role
-- DELETE FROM rrt_roles WHERE slug = 'distribution-basic-annually';
--
-- COMMIT;
--
-- ============================================================================
