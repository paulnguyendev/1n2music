# Implementation Checklist

## Pre-Implementation
- [x] **Backup Database** - Create full backup before schema changes
- [x] **Document Current Behavior** - Test and record current track visibility for each contract type
- [x] **Create Feature Branch** - `git checkout -b feature/track-filtering-refactor`

## Phase 1: Database Schema Updates ✅ COMPLETED

### 1.1 Add stay_on_list Column
- [x] **Create Migration File**
  ```bash
  php artisan make:migration add_stay_on_list_to_contracts_table
  ```

- [x] **Migration Content**
  ```php
  public function up()
  {
      Schema::table('rrt_contracts', function (Blueprint $table) {
          $table->tinyInteger('stay_on_list')->default(0)->after('number_order');
      });
  }
  ```

- [x] **Run Migration**
  ```bash
  php artisan migrate
  ```

### 1.2 Update Contract Data
- [x] **Update Existing Records**
  ```sql
  UPDATE rrt_contracts SET stay_on_list = 1 WHERE id IN (1, 3); -- Free, Hard Copy
  UPDATE rrt_contracts SET stay_on_list = 0 WHERE id IN (4, 5); -- Digital, Copyright
  ```

### 1.3 Update ContractModel
- [x] **File:** `app/Models/ContractModel.php`
- [x] **Add to fillable array:**
  ```php
  protected $fillable = [
      'id', 'code', 'name', 'category', 'is_default', 
      'updated_at', 'created_at', 'number_order', 'stay_on_list'
  ];
  ```

## Phase 2: Create Service Classes ✅ COMPLETED

### 2.1 Create TrackFilterService
- [x] **Create Directory:** `app/Services/` (created automatically)
- [x] **Create File:** `app/Services/TrackFilterService.php`
- [x] **Implement Methods:**
  - [x] `filterByContractRules($tracks, $debug = false)`
  - [x] `shouldIncludeTrack($track, $debug = false)`
  - [x] `hasValidFile($track)`
  - [x] `passesContractRules($track, &$debugInfo = [])`

### 2.2 Add TrackModel Scopes
- [x] **File:** `app/Models/TrackModel.php`
- [x] **Add Scopes:**
  - [x] `scopeWithContractData($query)`
  - [x] `scopePublicTracks($query)`
  - [x] `scopeTrending($query, $limit = 10)`
  - [x] `scopeRecommended($query, $limit = 10)`
  - [x] `scopeFeatured($query, $limit = 10)`

## Phase 3: Controller Updates 🚧 IN PROGRESS

### 3.1 HomeController.php ✅ COMPLETED
- [x] **File:** `app/Http/Controllers/Public/HomeController.php`
- [x] **Add Service Import:** `use App\Services\TrackFilterService;`
- [x] **Update Trending Tracks (Lines 53-113):**
  - [x] Replace current filtering logic with service call
  - [x] Test trending tracks display
- [x] **Update Recommended Tracks (Lines 115-177):**
  - [x] Replace current filtering logic with service call
  - [x] Test recommended tracks display

### 3.2 MarketController.php
- [x] **File:** `app/Http/Controllers/Public/MarketController.php`
- [x] **Add Service Import:** `use App\Services\TrackFilterService;`
- [x] **Update Main Track List (Lines ~140-260):**
  - [x] Replace filtering logic with service call (debug enabled)
  - [x] Ensure pagination compatibility
  - [x] Test search functionality
- [x] **Update Featured Tracks (Lines ~275-340):**
  - [x] Replace filtering logic with service call
  - [x] Maintain shuffle functionality
  - [x] Test featured tracks display

### 3.3 TrackModel.php
- [ ] **File:** `app/Models/TrackModel.php`
- [ ] **Add Service Import:** `use App\Services\TrackFilterService;`
- [ ] **Update listItems() Method (Lines ~82-102):**
  - [ ] Add contract relationship loading
  - [ ] Apply service filtering
  - [ ] Test AJAX functionality

## Phase 4: Testing & Verification

### 4.1 Contract Type Testing
- [ ] **Free Contract (ID: 1) with Orders:** Should be visible ✓
- [ ] **Hard Copy Contract (ID: 3) with Orders:** Should be visible ✓  
- [ ] **Digital Contract (ID: 4) with Orders:** Should be hidden ✓
- [ ] **Copyright Contract (ID: 5) with Orders:** Should be hidden ✓
- [ ] **Digital Contract (ID: 4) without Orders:** Should be visible ✓
- [ ] **Copyright Contract (ID: 5) without Orders:** Should be visible ✓

### 4.2 Edge Case Testing
- [ ] **Tracks with No Contracts:** Should be visible ✓
- [ ] **Tracks with Invalid/Missing Files:** Should be hidden ✓
- [ ] **Tracks with Multiple Contracts:** Follow priority rules ✓
- [ ] **AJAX Calls:** Consistent with main page filtering ✓

### 4.3 Controller-Specific Testing
- [ ] **HomeController:**
  - [ ] Trending tracks display correctly
  - [ ] Recommended tracks display correctly
  - [ ] Page loads without errors
- [ ] **MarketController:**
  - [ ] Main track list filters correctly
  - [ ] Search functionality works
  - [ ] Pagination works
  - [ ] Featured tracks display correctly
  - [ ] Debug information logs properly
- [ ] **TrackModel AJAX:**
  - [ ] Dynamic loading works
  - [ ] Consistent filtering applied

### 4.4 Performance Testing
- [ ] **Page Load Times:** Compare before/after performance
- [ ] **Database Queries:** Check for N+1 query issues
- [ ] **Memory Usage:** Monitor for increased memory consumption

## Phase 5: Final Verification

### 5.1 Code Quality
- [ ] **Remove Duplicate Code:** Verify old filtering logic is removed
- [ ] **Code Review:** Review all changes for consistency
- [ ] **Documentation:** Update inline comments

### 5.2 Business Logic Verification
- [ ] **Contract Rules:** All business rules properly implemented
- [ ] **Debug Logging:** Debug information available when needed
- [ ] **Error Handling:** Graceful handling of edge cases

### 5.3 Deployment Preparation
- [ ] **Environment Testing:** Test on staging environment
- [ ] **Database Migration:** Verify migration works on production schema
- [ ] **Rollback Plan:** Document rollback procedures

## Phase 6: Deployment

### 6.1 Production Deployment
- [ ] **Deploy Database Changes:** Run migration on production
- [ ] **Deploy Code Changes:** Deploy new service and controller updates
- [ ] **Monitor Performance:** Watch for any performance issues
- [ ] **Verify Functionality:** Test key contract scenarios

### 6.2 Post-Deployment
- [ ] **Monitor Logs:** Check for any error patterns
- [ ] **User Feedback:** Monitor for any user-reported issues
- [ ] **Performance Metrics:** Compare performance metrics

## Rollback Procedures

### If Issues Arise:
1. **Code Rollback:**
   - [ ] Revert controller changes: `git revert <commit-hash>`
   - [ ] Remove service files if causing issues
   - [ ] Keep database schema (safe to leave)

2. **Database Rollback (if necessary):**
   - [ ] Remove stay_on_list column if causing issues
   - [ ] Restore from backup if critical

3. **Verification:**
   - [ ] Test original functionality restored
   - [ ] Monitor for stability

## Notes
- **Low Risk Changes:** Adding column, creating service (can be easily reverted)
- **Medium Risk Changes:** Controller modifications (revertible via git)
- **Testing Environment:** Test thoroughly on staging before production
- **Performance:** Monitor query performance, especially with eager loading

---

## Phase 2 Implementation Summary ✅

### Files Created/Modified:
1. **`app/Services/TrackFilterService.php`** - NEW FILE
   - Complete service with all filtering logic
   - Debug logging support
   - New contract rules using `stay_on_list` flag
   - File existence checking

2. **`app/Models/TrackModel.php`** - MODIFIED
   - Added 5 new scopes for easier querying
   - `withContractData()` - eager loads relationships
   - `publicTracks()` - filters public tracks
   - `trending()`, `recommended()`, `featured()` - specific track types

### Key Features Implemented:
- **New Business Logic:** Uses `stay_on_list` flag instead of complex `number_order` logic
- **Debug Support:** Optional debug logging for MarketController
- **Performance Optimized:** Eager loading to prevent N+1 queries
- **Reusable:** Service can be used across all controllers consistently

### Ready for Phase 3:
All foundation code is in place. Controllers can now use:
```php
use App\Services\TrackFilterService;

// Simple usage
$tracks = TrackFilterService::filterByContractRules($tracksCollection);

// With debug (for MarketController)
$tracks = TrackFilterService::filterByContractRules($tracksCollection, true);

// Using new scopes
$trendings = $this->trackModel->trending(10)->get();
``` 