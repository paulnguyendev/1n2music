# MarketController Filtering Logic Analysis

## File Location
`app/Http/Controllers/Public/MarketController.php`

## Filtering Locations Identified

### Location 1: Main Track List (Around Lines 140-260)
- **Purpose:** Main track listing with search/filter functionality
- **Current Logic:** Uses duplicate contract filtering with debug logging
- **Query Pattern:** 
  ```php
  $trackQuery = $this->trackModel->with(['listContracts','orderItem'])->where('status', 'public');
  // ... search and filter logic ...
  $tracks = $trackQuery->where('visibility','public')->whereNotNull('user_id')->orderBy('id', 'desc')->paginate(6);
  
  $filteredTracks = $tracks->getCollection()->filter(function ($track) {
      // Duplicate filtering logic with debug info
  });
  ```

### Location 2: Featured Tracks (Around Lines 275-340)  
- **Purpose:** Featured tracks section
- **Current Logic:** Uses same duplicate contract filtering
- **Query Pattern:**
  ```php
  $featuredTracks = $this->trackModel
      ->where(['status' => 'public', 'is_featured' => 'checked'])
      ->where('visibility','public')
      ->with(['listContracts', 'orderItem'])
      ->get()
      ->filter(function($track) {
          // Duplicate filtering logic
      })->shuffle();
  ```

## Key Differences from HomeController
1. **Debug Logging:** MarketController includes debug information in filtering
2. **Pagination:** Main track list uses pagination, needs special handling
3. **Search/Filter:** Additional search and filter logic before applying contract rules
4. **Featured Tracks:** Uses shuffle() after filtering

## Debug Information Pattern
Based on grep results, MarketController includes debug reasons like:
- Line 224: Debug reason for including/excluding tracks
- Tracks filtering decisions for debugging purposes

## Refactor Considerations
1. **Preserve Debug Functionality:** New service should support debug mode
2. **Pagination Compatibility:** Ensure filtering works with Laravel pagination
3. **Search Integration:** Contract filtering should work with existing search logic
4. **Featured Track Shuffle:** Maintain shuffle functionality after filtering

## Dependencies
- Uses same relationships: `listContracts`, `orderItem`
- Same file existence checking logic
- Same contract number_order logic (-1 vs counting)
- Same orderCount >= numberOrder comparison 