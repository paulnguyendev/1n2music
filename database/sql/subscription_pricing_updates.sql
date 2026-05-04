-- ============================================================================
-- Subscription Pricing Updates and Enterprise Page - Database Changes
-- Date: 2026-05-04
-- ============================================================================

-- Task 1.2: Create rrt_enterprise_inquiries table
CREATE TABLE IF NOT EXISTS `rrt_enterprise_inquiries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `number_of_artists` int(11) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rrt_enterprise_inquiries_status_index` (`status`),
  KEY `rrt_enterprise_inquiries_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Task 2.1: Insert new role for Distribution Basic Annually
INSERT INTO `rrt_roles` (`name`, `slug`, `created_at`, `updated_at`)
VALUES ('Distribution Basic Annually', 'distribution-basic-annually', NOW(), NOW());

-- Get the new role_id (will be used in subsequent queries)
SET @distribution_basic_role_id = LAST_INSERT_ID();

-- Task 2.3: Insert new Distribution Basic subscription
INSERT INTO `rrt_subscriptions` (`name`, `slug`, `price`, `pricing_annually`, `max_track`, `commission`, `heading`, `content`, `description`, `background`, `created_at`, `updated_at`)
VALUES (
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
);

-- Task 2.4: Update Publishing subscription pricing from $1000 to $200
UPDATE `rrt_subscriptions`
SET `pricing_annually` = 200,
    `updated_at` = NOW()
WHERE `slug` = 'publishing';

-- Task 3.1: Update Pro Seller Monthly AI Mastering usage from 20 to 5
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5
WHERE `role_id` = 3 AND `ai_id` = 1 AND `package_id` = 2;

-- Task 3.2: Update Pro Seller Annually AI Mastering usage from 20 to 5
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 5
WHERE `role_id` = 4 AND `ai_id` = 1 AND `package_id` = 2;

-- Task 3.3: Update Distribution Pro AI Mastering usage from 20 to 10
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 10
WHERE `role_id` = 5 AND `ai_id` = 1 AND `package_id` = 2;

-- Task 3.4: Update Distribution Pro Music Recognition usage from 10 to 20
UPDATE `rrt_ai_package_roles`
SET `usage_count` = 20
WHERE `role_id` = 5 AND `ai_id` = 2 AND `package_id` = 4;

-- Task 3.5: Insert Distribution Basic AI Mastering package role
INSERT INTO `rrt_ai_package_roles` (`ai_id`, `role_id`, `package_id`, `usage_count`, `download_available`, `price`, `created_at`, `updated_at`)
VALUES (1, @distribution_basic_role_id, 2, 5, 10, 1, NOW(), NOW());

-- Task 3.6: Insert Distribution Basic Music Recognition package role
INSERT INTO `rrt_ai_package_roles` (`ai_id`, `role_id`, `package_id`, `usage_count`, `download_available`, `price`, `created_at`, `updated_at`)
VALUES (2, @distribution_basic_role_id, 4, 5, 10, 1, NOW(), NOW());

-- ============================================================================
-- Verification Queries (run these to verify changes)
-- ============================================================================

-- Verify enterprise inquiries table exists
-- SELECT * FROM information_schema.tables WHERE table_name = 'rrt_enterprise_inquiries';

-- Verify Distribution Basic role was created
-- SELECT * FROM rrt_roles WHERE slug = 'distribution-basic-annually';

-- Verify Distribution Basic subscription was created
-- SELECT * FROM rrt_subscriptions WHERE slug = 'distribution-basic';

-- Verify Publishing pricing was updated
-- SELECT name, slug, pricing_annually FROM rrt_subscriptions WHERE slug = 'publishing';

-- Verify AI package role updates
-- SELECT r.name, r.slug, ai.name as ai_service, apr.usage_count, apr.download_available
-- FROM rrt_ai_package_roles apr
-- JOIN rrt_roles r ON apr.role_id = r.id
-- JOIN rrt_ai_services ai ON apr.ai_id = ai.id
-- WHERE r.slug IN ('proseller-monthly', 'proseller-annually', 'distribution-annually', 'distribution-basic-annually')
-- ORDER BY r.id, ai.id;
