# TrackModel Filtering Logic Analysis

## File Location
`app/Models/TrackModel.php`

## Filtering Location Identified

### Location 1: listItems() Method (Ajax Task)
- **Purpose:** AJAX endpoint for loading tracks dynamically
- **Current Logic:** Basic filtering without contract rules
- **Line Range:** Approximately 82-102 (based on previous analysis)

## Current Implementation Pattern
```php
public function listItems($params = null, $options = null)
{
    // ... other tasks ...
    
    if($options['task'] == 'ajax') {
        $result = $query->where('status', 'public')
                       ->with('file')
                       ->orderBy('id', 'desc')
                       ->get();
        return $result;
    }
}
```

## Missing Contract Logic
Unlike HomeController and MarketController, TrackModel's listItems() method:
1. **No Contract Filtering:** Currently doesn't apply contract-based filtering
2. **No Order Item Relationships:** Missing `orderItem` relationship loading
3. **No Contract Relationships:** Missing `listContracts` relationship loading
4. **Inconsistent Behavior:** AJAX calls show tracks that main pages would hide

## Required Changes
1. **Add Relationship Loading:**
   ```php
   ->with(['file', 'listContracts.contractSetting.contract', 'orderItem'])
   ```

2. **Apply Contract Filtering:**
   ```php
   $result = TrackFilterService::filterByContractRules($result);
   ```

## Dependencies for TrackModel
- Same file existence checking needed
- Same contract relationship structure
- Same orderItem counting logic
- Consistent with controller filtering

## Ajax Usage Context
Based on HomeController usage:
```php
public function tracks(Request $request)
{
    $params = $request->all();
    $type = $params['type'] ?? "trending";
    $items = $this->trackModel->listItems(['type' => $type], ['task' => 'ajax']);
    // Returns tracks for AJAX calls
}
```

## Impact of Missing Filtering
- AJAX calls currently return unfiltered tracks
- May show tracks that should be hidden based on contract rules
- Inconsistent user experience between page load and AJAX load
- Potential business rule violations

## Refactor Priority
**HIGH** - This affects dynamic loading and creates inconsistent behavior 