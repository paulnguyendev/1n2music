# Track Filtering Refactor Documentation

## Overview
This document outlines all the locations and logic that need to be refactored for the new contract-based track filtering system.

## Business Rules

### Current Contract Types & Behavior
| ID | Name | Category | number_order | stay_on_list | Behavior After Order |
|----|------|----------|--------------|--------------|---------------------|
| 1  | Free | free | -1 | 1 | Always visible (stays on list) |
| 3  | Hard Copy License | regular | -1 | 1 | Always visible (stays on list) |
| 4  | Digital License | regular | -1 | 0 | Hidden after first order (off the list) |
| 5  | Copyright License | regular | 1 | 0 | Hidden after 1 order (off the list) |

### New Filtering Logic
```php
// Priority order:
1. File existence check (unchanged)
2. If no contracts → always show
3. If stay_on_list = 1 → always show (Free, Hard Copy)
4. If stay_on_list = 0 AND has orders → hide (Digital, Copyright)
5. If stay_on_list = 0 AND no orders → show
```

## Database Changes Required

### 1. Add stay_on_list Column
```sql
ALTER TABLE rrt_contracts ADD COLUMN stay_on_list TINYINT(1) DEFAULT 0;
UPDATE rrt_contracts SET stay_on_list = 1 WHERE id IN (1, 3);
UPDATE rrt_contracts SET stay_on_list = 0 WHERE id IN (4, 5);
```

### 2. Update ContractModel Fillable
```php
protected $fillable = [
    'id', 'code', 'name', 'category', 'is_default', 
    'updated_at', 'created_at', 'number_order', 'stay_on_list'
];
```

## Controllers Requiring Changes

### 1. HomeController.php
**File:** `app/Http/Controllers/Public/HomeController.php`

#### Location 1: Trending Tracks (Lines 53-113)
```php
// BEFORE: Lines 53-113
$trendings = $this->trackModel
    ->where('status','public')
    ->where('visibility','public')
    ->whereNotNull('is_trending')
    ->with(['listContracts', 'orderItem'])
    ->orderBy('id','desc')
    ->limit(10)
    ->get()
    ->filter(function($track) {
        // 60+ lines of duplicate filtering logic
    })->take(4);

// AFTER: Replace with
$trendingsRaw = $this->trackModel->trending(10)->get();
$trendings = TrackFilterService::filterByContractRules($trendingsRaw)->take(4);
```

#### Location 2: Recommended Tracks (Lines 115-177)
```php
// BEFORE: Lines 115-177 
$recommendeds = $this->trackModel
    ->where('status','public')
    ->where('visibility','public')
    ->whereNotNull('is_recommend')
    ->with(['listContracts', 'orderItem'])
    ->orderBy('id','desc')
    ->limit(10)
    ->get()
    ->filter(function($track) {
        // 60+ lines of duplicate filtering logic
    })->take(4);

// AFTER: Replace with
$recommendedsRaw = $this->trackModel->recommended(10)->get();
$recommendeds = TrackFilterService::filterByContractRules($recommendedsRaw)->take(4);
```

**Other data in HomeController:** (No changes needed)
- $users, $relate_contents, $bulletins, $banner, $slides, $genres

### 2. MarketController.php  
**File:** `app/Http/Controllers/Public/MarketController.php`

#### Location 1: Main Track List (Lines 101-260)
```php
// BEFORE: Lines 140-260
$trackQuery = $this->trackModel->with(['listContracts','orderItem'])->where('status', 'public');
// ... search/filter logic ...
$tracks = $trackQuery->where('visibility','public')->whereNotNull('user_id')->orderBy('id', 'desc')->paginate(6);

$filteredTracks = $tracks->getCollection()->filter(function ($track) {
    // 120+ lines of duplicate filtering logic with debug info
});
$tracks->setCollection($filteredTracks);

// AFTER: Replace filter section with
$tracks = $trackQuery->withContractData()
                    ->where('visibility','public')
                    ->whereNotNull('user_id')
                    ->orderBy('id', 'desc')
                    ->paginate(6);

$filteredTracks = TrackFilterService::filterByContractRules($tracks->getCollection(), true); // enable debug
$tracks->setCollection($filteredTracks);
```

#### Location 2: Featured Tracks (Lines 275-340)
```php
// BEFORE: Lines 275-340
$featuredTracks = $this->trackModel
    ->where(['status' => 'public', 'is_featured' => 'checked'])
    ->where('visibility','public')
    // ... with relationships ...
    ->get()
    ->filter(function($track) {
        // 40+ lines of duplicate filtering logic
    })->shuffle();

// AFTER: Replace with
$featuredTracksRaw = $this->trackModel->featured()->get();
$featuredTracks = TrackFilterService::filterByContractRules($featuredTracksRaw)->shuffle();
```

**Other data in MarketController:** (No changes needed)
- $genres, $tags, $moods, $users, $producer - these remain unchanged

### 3. TrackModel.php
**File:** `app/Models/TrackModel.php`

#### Location 1: listItems() Method (Lines 82-102)
```php
// BEFORE: Lines 82-102 in ajax task
$result = $query->where('status', 'public')->with('file')->orderBy('id', 'desc')->get();

// AFTER: Replace with
$result = $query->where('status', 'public')
               ->with(['file', 'listContracts.contractSetting.contract', 'orderItem'])
               ->orderBy('id', 'desc')
               ->get();
               
// Apply contract filtering
$result = TrackFilterService::filterByContractRules($result);
```

## New Files to Create

### 1. TrackFilterService
**File:** `app/Services/TrackFilterService.php`
```php
<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class TrackFilterService 
{
    public static function filterByContractRules($tracks, $debug = false)
    {
        return $tracks->filter(function($track) use ($debug) {
            return self::shouldIncludeTrack($track, $debug);
        });
    }
    
    public static function shouldIncludeTrack($track, $debug = false)
    {
        // Implementation detailed in main plan
    }
    
    private static function hasValidFile($track)
    {
        // File existence check logic
    }
    
    private static function passesContractRules($track, &$debugInfo = [])
    {
        // New contract rules logic
    }
}
```

### 2. Track Model Scopes
**Add to:** `app/Models/TrackModel.php`
```php
public function scopeWithContractData($query)
{
    return $query->with(['listContracts.contractSetting.contract', 'orderItem', 'file']);
}

public function scopePublicTracks($query)
{
    return $query->where('status', 'public')->where('visibility', 'public');
}

public function scopeTrending($query, $limit = 10)
{
    return $query->publicTracks()
                 ->whereNotNull('is_trending')
                 ->withContractData()
                 ->orderBy('id', 'desc')
                 ->limit($limit);
}

public function scopeRecommended($query, $limit = 10)
{
    return $query->publicTracks()
                 ->whereNotNull('is_recommend')
                 ->withContractData()
                 ->orderBy('id', 'desc')
                 ->limit($limit);
}

public function scopeFeatured($query, $limit = 10)
{
    return $query->publicTracks()
                 ->whereNotNull('is_featured')
                 ->withContractData()
                 ->orderBy('id', 'desc')
                 ->limit($limit);
}
```

## Verification Checklist

### Before Refactor
- [ ] Document current behavior with sample data
- [ ] Run queries and capture current results
- [ ] Backup database before schema changes

### After Refactor  
- [ ] Verify HomeController trending tracks work correctly
- [ ] Verify HomeController recommended tracks work correctly
- [ ] Verify MarketController main list filtering
- [ ] Verify MarketController featured tracks filtering
- [ ] Verify TrackModel ajax filtering
- [ ] Test with each contract type (Free, Hard Copy, Digital, Copyright)
- [ ] Test with tracks having no contracts
- [ ] Test with tracks having multiple contracts
- [ ] Verify file existence checking still works
- [ ] Verify pagination still works in MarketController

### Test Cases Required

#### Contract Combinations to Test:
1. **Track with Free contract + orders** → Should be visible
2. **Track with Hard Copy contract + orders** → Should be visible  
3. **Track with Digital contract + orders** → Should be hidden
4. **Track with Copyright contract + orders** → Should be hidden
5. **Track with Digital contract + no orders** → Should be visible
6. **Track with Copyright contract + no orders** → Should be visible
7. **Track with no contracts** → Should be visible
8. **Track with missing/invalid files** → Should be hidden
9. **Track with multiple contracts (mixed types)** → Follow priority rules

## Migration Plan

### Phase 1: Database Updates
1. Add `stay_on_list` column to `rrt_contracts`
2. Update existing contract data
3. Update `ContractModel` fillable array

### Phase 2: Create New Services
1. Create `TrackFilterService`
2. Add scopes to `TrackModel`

### Phase 3: Controller Updates  
1. Update `HomeController.php`
2. Update `MarketController.php`
3. Update `TrackModel.php` listItems method

### Phase 4: Testing & Verification
1. Run all test cases
2. Compare before/after results
3. Performance testing
4. Debug any issues

## Rollback Plan
If issues arise:
1. Revert controller changes
2. Keep database schema (no harm in extra column)
3. Remove new service files if needed
4. Restore from git backup

## Performance Considerations
- New logic should be faster (less duplicate queries)
- Eager loading relationships reduces N+1 queries
- Collection filtering is done in memory (acceptable for reasonable dataset sizes)
- Consider adding database indexes if performance degrades

## Additional Notes
- The debug logging in `TrackFilterService` can be enabled/disabled per call
- Consider adding caching for frequently accessed track lists
- Monitor for any edge cases during initial rollout
- Document any deviations from this plan during implementation 