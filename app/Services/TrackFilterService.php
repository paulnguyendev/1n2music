<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TrackFilterService 
{
    /**
     * Filter tracks based on contract rules
     *
     * @param \Illuminate\Support\Collection $tracks
     * @param bool $debug Enable debug logging
     * @return \Illuminate\Support\Collection
     */
    public static function filterByContractRules($tracks, $debug = false)
    {
        return $tracks->filter(function($track) use ($debug) {
            return self::shouldIncludeTrack($track, $debug);
        });
    }
    
    /**
     * Determine if a track should be included based on contract rules
     *
     * @param \App\Models\TrackModel $track
     * @param bool $debug
     * @return bool
     */
    public static function shouldIncludeTrack($track, $debug = false)
    {
        $debugInfo = [];
        
        // First check if file exists
        if (!self::hasValidFile($track)) {
            if ($debug) {
                Log::info("Track {$track->id} excluded: Invalid/missing file");
            }
            return false;
        }
        
        // Check contract rules
        $passesContractRules = self::passesContractRules($track, $debugInfo);
        
        if ($debug) {
            $action = $passesContractRules ? 'included' : 'excluded';
            $reason = implode(', ', $debugInfo);
            Log::info("Track {$track->id} {$action}: {$reason}");
        }
        
        return $passesContractRules;
    }
    
    /**
     * Check if track has valid file
     *
     * @param \App\Models\TrackModel $track
     * @return bool
     */
    private static function hasValidFile($track)
    {
        $file = $track->file[0] ?? null;
        if (!($file && !empty($file->name))) {
            return false;
        }

        $filePath = public_path('uploads/tracks/' . $file->name);
        return file_exists($filePath);
    }
    
    /**
     * Check if track passes contract rules
     *
     * @param \App\Models\TrackModel $track
     * @param array $debugInfo
     * @return bool
     */
    private static function passesContractRules($track, &$debugInfo = [])
    {
        $contracts = $track->listContracts ?? [];
        
        // If no contracts, include the track
        if (empty($contracts)) {
            $debugInfo[] = 'No contracts - always visible';
            return true;
        }
        
        foreach ($contracts as $contract) {
            // Get the contract_setting and contract info
            $contractSetting = $contract->contractSetting ?? null;
            if (!$contractSetting) {
                continue;
            }
            
            $contractInfo = $contractSetting->contract ?? null;
            if (!$contractInfo) {
                continue;
            }
            
            // NEW LOGIC: Check stay_on_list flag first
            $stayOnList = $contractInfo->stay_on_list ?? 0;
            
            if ($stayOnList == 1) {
                // Always visible contracts (Free, Hard Copy)
                $debugInfo[] = "Contract {$contractInfo->name} (stay_on_list=1) - always visible";
                return true;
            }
            
            // For stay_on_list = 0 contracts (Digital, Copyright)
            // Check if track has orders
            $orderItems = $track->orderItem ?? [];
            $orderCount = count($orderItems);
            
            if ($orderCount > 0) {
                // Has orders and stay_on_list = 0 → hide
                $debugInfo[] = "Contract {$contractInfo->name} (stay_on_list=0) with {$orderCount} orders - hidden";
                return false;
            } else {
                // No orders and stay_on_list = 0 → show
                $debugInfo[] = "Contract {$contractInfo->name} (stay_on_list=0) with no orders - visible";
                return true;
            }
        }
        
        // Default: include the track
        $debugInfo[] = 'Default behavior - visible';
        return true;
    }
    
    /**
     * Get all tracks with sold status for producer page
     * Unlike filterByContractRules, this shows all tracks but marks sold ones
     *
     * @param \Illuminate\Support\Collection $tracks
     * @param bool $debug Enable debug logging
     * @return \Illuminate\Support\Collection
     */
    public static function getTracksWithSoldStatus($tracks, $debug = false)
    {
        return $tracks->map(function($track) use ($debug) {
            // Check if file exists
            if (!self::hasValidFile($track)) {
                $track->is_hidden = true;
                $track->is_sold = false;
                return $track;
            }
            
            $track->is_hidden = false;
            $track->is_sold = self::isTrackSold($track, $debug);
            
            return $track;
        });
    }
    
    /**
     * Check if track is sold (should show sold badge)
     *
     * @param \App\Models\TrackModel $track
     * @param bool $debug
     * @return bool
     */
    public static function isTrackSold($track, $debug = false)
    {
        $contracts = $track->listContracts ?? [];
        
        // If no contracts, not sold
        if (empty($contracts)) {
            return false;
        }
        
        foreach ($contracts as $contract) {
            $contractSetting = $contract->contractSetting ?? null;
            if (!$contractSetting) {
                continue;
            }
            
            $contractInfo = $contractSetting->contract ?? null;
            if (!$contractInfo) {
                continue;
            }
            
            $stayOnList = $contractInfo->stay_on_list ?? 0;
            
            // For stay_on_list = 0 contracts (Digital, Copyright)
            if ($stayOnList == 0) {
                $orderItems = $track->orderItem ?? [];
                $orderCount = count($orderItems);
                
                if ($orderCount > 0) {
                    if ($debug) {
                        Log::info("Track {$track->id} marked as sold: Contract {$contractInfo->name} with {$orderCount} orders");
                    }
                    return true;
                }
            }
        }
        
        return false;
    }
} 