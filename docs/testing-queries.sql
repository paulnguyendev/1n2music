-- =====================================================
-- TESTING QUERIES FOR TRACK FILTERING REFACTOR
-- =====================================================

-- =====================================================
-- 0. TRACKS FROM HOME CONTROLLER LOGIC (Exact Match)
-- =====================================================
-- This query mimics exactly what HomeController + TrackFilterService does

SELECT 
    t.id as track_id,
    t.name as track_name,
    t.is_trending,
    t.is_recommend,
    t.is_featured,
    
    -- Contract Info (from listContracts relationship)
    tc.id as track_contract_id,
    cs.id as contract_setting_id,
    c.id as contract_id,
    c.name as contract_name,
    c.stay_on_list,
    
    -- Order Count (from orderItem relationship)
    (SELECT COUNT(*) FROM rrt_order_items oi WHERE oi.track_id = t.id) as total_orders,
    
    -- File Info (from file relationship)
    tf.id as file_id,
    tf.name as file_name,
    tf.type as file_type,
    
    -- File Exists Check (TrackFilterService logic)
    CASE 
        WHEN tf.name IS NOT NULL AND tf.name != '' THEN 
            CONCAT('File: ', tf.name)
        ELSE 'No File'
    END as file_status,
    
    -- Contract Filtering Decision (TrackFilterService logic)
    CASE 
        -- First: File check
        WHEN tf.name IS NULL OR tf.name = '' THEN 'EXCLUDED - No File'
        
        -- No contracts = always visible  
        WHEN tc.id IS NULL THEN 'INCLUDED - No Contracts'
        
        -- stay_on_list = 1 = always visible (Free, Hard Copy)
        WHEN c.stay_on_list = 1 THEN 'INCLUDED - Always On List'
        
        -- stay_on_list = 0 with orders = hidden (Digital, Copyright)
        WHEN c.stay_on_list = 0 AND (SELECT COUNT(*) FROM rrt_order_items oi WHERE oi.track_id = t.id) > 0 
        THEN 'EXCLUDED - Has Orders'
        
        -- stay_on_list = 0 without orders = visible (Digital, Copyright)
        WHEN c.stay_on_list = 0 AND (SELECT COUNT(*) FROM rrt_order_items oi WHERE oi.track_id = t.id) = 0 
        THEN 'INCLUDED - No Orders Yet'
        
        ELSE 'INCLUDED - Default'
    END as filter_decision

FROM rrt_tracks t

-- Join file relationship (TrackModel->file())
LEFT JOIN rrt_tracks_files tf ON t.id = tf.track_id AND tf.type = 'mp3'

-- Join listContracts relationship (TrackModel->listContracts())
LEFT JOIN rrt_contracts_tracks tc ON t.id = tc.track_id AND tc.enabled = 1 AND tc.price > 0
LEFT JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id
LEFT JOIN rrt_contracts c ON cs.contract_id = c.id

WHERE t.status = 'public' 
  AND t.visibility = 'public'
  AND t.is_trending IS NOT NULL  -- trending tracks

ORDER BY t.id DESC
LIMIT 20;

-- =====================================================
-- 1. RECOMMENDED TRACKS (Same Logic)
-- =====================================================

SELECT 
    t.id as track_id,
    t.name as track_name,
    t.is_recommend,
    
    -- Contract Info
    c.id as contract_id,
    c.name as contract_name,
    c.stay_on_list,
    
    -- Order Count
    (SELECT COUNT(*) FROM rrt_order_items oi WHERE oi.track_id = t.id) as total_orders,
    
    -- File Info
    tf.name as file_name,
    
    -- Filter Decision
    CASE 
        WHEN tf.name IS NULL OR tf.name = '' THEN 'EXCLUDED - No File'
        WHEN tc.id IS NULL THEN 'INCLUDED - No Contracts'
        WHEN c.stay_on_list = 1 THEN 'INCLUDED - Always On List'
        WHEN c.stay_on_list = 0 AND (SELECT COUNT(*) FROM rrt_order_items oi WHERE oi.track_id = t.id) > 0 
        THEN 'EXCLUDED - Has Orders'
        WHEN c.stay_on_list = 0 AND (SELECT COUNT(*) FROM rrt_order_items oi WHERE oi.track_id = t.id) = 0 
        THEN 'INCLUDED - No Orders Yet'
        ELSE 'INCLUDED - Default'
    END as filter_decision

FROM rrt_tracks t
LEFT JOIN rrt_tracks_files tf ON t.id = tf.track_id AND tf.type = 'mp3'
LEFT JOIN rrt_contracts_tracks tc ON t.id = tc.track_id AND tc.enabled = 1 AND tc.price > 0
LEFT JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id
LEFT JOIN rrt_contracts c ON cs.contract_id = c.id

WHERE t.status = 'public' 
  AND t.visibility = 'public'
  AND t.is_recommend IS NOT NULL  -- recommended tracks

ORDER BY t.id DESC
LIMIT 20;

-- =====================================================
-- 2. SIMPLE DEBUG QUERIES
-- =====================================================

-- Check trending tracks basic info
SELECT id, name, is_trending, status, visibility 
FROM rrt_tracks 
WHERE status = 'public' AND visibility = 'public' AND is_trending IS NOT NULL
ORDER BY id DESC LIMIT 10;

-- Check files exist
SELECT track_id, name, type 
FROM rrt_tracks_files 
WHERE type = 'mp3' AND name IS NOT NULL AND name != ''
ORDER BY track_id DESC LIMIT 10;

-- Check contracts
SELECT id, name, stay_on_list FROM rrt_contracts ORDER BY id;

-- Check track contracts
SELECT tc.track_id, tc.contact_setting_id, tc.enabled, tc.price, c.name as contract_name
FROM rrt_contracts_tracks tc
JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id
JOIN rrt_contracts c ON cs.contract_id = c.id
WHERE tc.enabled = 1 AND tc.price > 0
ORDER BY tc.track_id DESC LIMIT 10;

-- =====================================================
-- 3. TRACKS CURRENTLY DISPLAYED (Based on New Logic)
-- =====================================================
-- This query shows tracks that SHOULD be visible based on new contract rules

SELECT 
    t.id as track_id,
    t.name as track_name,
    t.status,
    t.visibility,
    t.is_trending,
    t.is_recommend,
    t.is_featured,
    
    -- Contract Info
    c.id as contract_id,
    c.name as contract_name,
    c.category as contract_category,
    c.stay_on_list,
    c.number_order,
    
    -- Order Count
    COALESCE(order_counts.order_count, 0) as total_orders,
    
    -- File Status
    CASE 
        WHEN tf.name IS NOT NULL AND tf.name != '' THEN 'Has File'
        ELSE 'No File'
    END as file_status,
    
    -- Visibility Decision (New Logic)
    CASE 
        -- File check first
        WHEN tf.name IS NULL OR tf.name = '' THEN 'HIDDEN - No File'
        
        -- No contracts = always visible
        WHEN c.id IS NULL THEN 'VISIBLE - No Contracts'
        
        -- stay_on_list = 1 = always visible (Free, Hard Copy)
        WHEN c.stay_on_list = 1 THEN 'VISIBLE - Always On List'
        
        -- stay_on_list = 0 with orders = hidden (Digital, Copyright after purchase)
        WHEN c.stay_on_list = 0 AND COALESCE(order_counts.order_count, 0) > 0 THEN 'HIDDEN - Has Orders'
        
        -- stay_on_list = 0 without orders = visible (Digital, Copyright before purchase)
        WHEN c.stay_on_list = 0 AND COALESCE(order_counts.order_count, 0) = 0 THEN 'VISIBLE - No Orders Yet'
        
        ELSE 'VISIBLE - Default'
    END as visibility_decision

FROM rrt_tracks t

-- Get the first file for each track (FIXED TABLE NAME)
LEFT JOIN (
    SELECT track_id, name, 
           ROW_NUMBER() OVER (PARTITION BY track_id ORDER BY id) as rn
    FROM rrt_tracks_files 
    WHERE type = 'mp3'
) tf ON t.id = tf.track_id AND tf.rn = 1

-- Get contract info through contracts_tracks -> contract_settings -> contracts (FIXED TABLE AND COLUMN NAMES)
LEFT JOIN rrt_contracts_tracks tc ON t.id = tc.track_id
LEFT JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id  
LEFT JOIN rrt_contracts c ON cs.contract_id = c.id

-- Count orders for each track
LEFT JOIN (
    SELECT track_id, COUNT(*) as order_count
    FROM rrt_order_items 
    GROUP BY track_id
) order_counts ON t.id = order_counts.track_id

WHERE t.status = 'public' 
  AND t.visibility = 'public'
  AND (t.is_trending IS NOT NULL OR t.is_recommend IS NOT NULL OR t.is_featured IS NOT NULL)

ORDER BY t.id DESC;

-- =====================================================
-- 4. TRACKS WITH ORDERS (Order History)
-- =====================================================
-- This query shows which tracks have been purchased/ordered

SELECT 
    t.id as track_id,
    t.name as track_name,
    
    -- Contract Info
    c.id as contract_id,
    c.name as contract_name,
    c.category as contract_category,
    c.stay_on_list,
    
    -- Order Details
    oi.id as order_item_id,
    oi.created_at as order_date,
    oi.price as order_price,
    
    -- User Info
    u.id as user_id,
    u.username,
    u.email,
    
    -- Current Visibility Status
    CASE 
        WHEN c.stay_on_list = 1 THEN 'Still Visible (Always On List)'
        WHEN c.stay_on_list = 0 THEN 'Should Be Hidden (Off List After Order)'
        ELSE 'Unknown Contract Status'
    END as current_status

FROM rrt_order_items oi

-- Join to get track info
INNER JOIN rrt_tracks t ON oi.track_id = t.id

-- Join to get contract info (FIXED TABLE AND COLUMN NAMES)
LEFT JOIN rrt_contracts_tracks tc ON t.id = tc.track_id
LEFT JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id
LEFT JOIN rrt_contracts c ON cs.contract_id = c.id

-- Join to get user info
LEFT JOIN rrt_users u ON oi.user_id = u.id

WHERE t.status = 'public'

ORDER BY oi.created_at DESC, t.id;

-- =====================================================
-- 5. SUMMARY STATS FOR TESTING
-- =====================================================

-- Contract Distribution (FIXED TABLE AND COLUMN NAMES)
SELECT 
    c.name as contract_name,
    c.stay_on_list,
    COUNT(DISTINCT tc.track_id) as track_count,
    COUNT(DISTINCT oi.track_id) as tracks_with_orders
FROM rrt_contracts c
LEFT JOIN rrt_contract_settings cs ON c.id = cs.contract_id
LEFT JOIN rrt_contracts_tracks tc ON cs.id = tc.contact_setting_id
LEFT JOIN rrt_order_items oi ON tc.track_id = oi.track_id
GROUP BY c.id, c.name, c.stay_on_list
ORDER BY c.id;

-- Track Visibility Summary
SELECT 
    'Total Public Tracks' as category,
    COUNT(*) as count
FROM rrt_tracks 
WHERE status = 'public' AND visibility = 'public'

UNION ALL

SELECT 
    'Trending Tracks' as category,
    COUNT(*) as count
FROM rrt_tracks 
WHERE status = 'public' AND visibility = 'public' AND is_trending IS NOT NULL

UNION ALL

SELECT 
    'Recommended Tracks' as category,
    COUNT(*) as count
FROM rrt_tracks 
WHERE status = 'public' AND visibility = 'public' AND is_recommend IS NOT NULL

UNION ALL

SELECT 
    'Featured Tracks' as category,
    COUNT(*) as count
FROM rrt_tracks 
WHERE status = 'public' AND visibility = 'public' AND is_featured IS NOT NULL

UNION ALL

SELECT 
    'Tracks with Orders' as category,
    COUNT(DISTINCT track_id) as count
FROM rrt_order_items;

-- =====================================================
-- 6. QUICK TEST QUERIES
-- =====================================================

-- Check specific contract types (FIXED TABLE AND COLUMN NAMES)
SELECT 'Free Contract Tracks' as type, COUNT(*) as count
FROM rrt_tracks t
JOIN rrt_contracts_tracks tc ON t.id = tc.track_id
JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id
JOIN rrt_contracts c ON cs.contract_id = c.id
WHERE c.id = 1 AND t.status = 'public';

-- Check Digital Contract tracks with orders (should be hidden) (FIXED TABLE AND COLUMN NAMES)
SELECT 'Digital Tracks with Orders (Should be Hidden)' as type, COUNT(*) as count
FROM rrt_tracks t
JOIN rrt_contracts_tracks tc ON t.id = tc.track_id
JOIN rrt_contract_settings cs ON tc.contact_setting_id = cs.id
JOIN rrt_contracts c ON cs.contract_id = c.id
JOIN rrt_order_items oi ON t.id = oi.track_id
WHERE c.id = 4 AND t.status = 'public';

-- =====================================================
-- 7. SIMPLE DEBUGGING QUERIES
-- =====================================================

-- Check if stay_on_list column exists and has correct values
SELECT id, name, stay_on_list FROM rrt_contracts ORDER BY id;

-- Check track files table structure
SELECT track_id, name, type FROM rrt_tracks_files LIMIT 5;

-- Check trending/recommended tracks
SELECT id, name, is_trending, is_recommend, is_featured 
FROM rrt_tracks 
WHERE status = 'public' AND visibility = 'public' 
  AND (is_trending IS NOT NULL OR is_recommend IS NOT NULL OR is_featured IS NOT NULL)
LIMIT 10;

-- Check contracts_tracks table structure (FIXED TABLE AND COLUMN NAME)
SELECT track_id, contact_setting_id, price, enabled FROM rrt_contracts_tracks LIMIT 5; 