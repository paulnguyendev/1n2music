-- ============================================================================
-- Fix AI Limits Bugs (2026-05-04)
-- ============================================================================
-- This script fixes bugs #3, #4, and #5 related to AI package role limits

-- Bug #3: Pro Seller Music Recognition Limits
-- Problem: Pro Seller has 10 Music Recognition uses, should be 5
-- Fix: Update both role_id=3 (monthly) and role_id=4 (annually)

UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = 3 AND `package_id` = 2;

UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = 4 AND `package_id` = 2;

-- Bug #4: Publishing AI Limits
-- Problem: Publishing has 10/10, should be 20/40
-- Fix: Update role_id=6 for both AI Mastering and Music Recognition

UPDATE `rrt_ai_package_roles`
SET `usage_count` = 20, `updated_at` = NOW()
WHERE `ai_id` = 1 AND `role_id` = 6 AND `package_id` = 2;

UPDATE `rrt_ai_package_roles`
SET `usage_count` = 40, `updated_at` = NOW()
WHERE `ai_id` = 2 AND `role_id` = 6 AND `package_id` = 4;

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Verify Pro Seller Music Recognition limits (should be 5)
SELECT 'Pro Seller Monthly Music Recognition' AS check_name, usage_count
FROM `rrt_ai_package_roles`
WHERE `ai_id` = 2 AND `role_id` = 3 AND `package_id` = 2;

SELECT 'Pro Seller Annually Music Recognition' AS check_name, usage_count
FROM `rrt_ai_package_roles`
WHERE `ai_id` = 2 AND `role_id` = 4 AND `package_id` = 2;

-- Verify Publishing AI limits (should be 20 for Mastering, 40 for Recognition)
SELECT 'Publishing AI Mastering' AS check_name, usage_count
FROM `rrt_ai_package_roles`
WHERE `ai_id` = 1 AND `role_id` = 6 AND `package_id` = 2;

SELECT 'Publishing Music Recognition' AS check_name, usage_count
FROM `rrt_ai_package_roles`
WHERE `ai_id` = 2 AND `role_id` = 6 AND `package_id` = 4;
