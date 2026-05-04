<?php

namespace App\Helpers;

use App\Models\TrackModel;
use Carbon\Carbon;

class Template
{
    public static function showPercent($percent)
    {
        return $percent . '%';
    }
    public static function showStatus($class = "badge", $status = "")
    {
        $xhtml = null;
        $config = rrt_get_config_status();
        $currentStatus = isset($config[$status]) ? $config[$status] : $config['default'];
        $currentName = $currentStatus['name'] ?? "";
        $currentClass = $currentStatus['class'] ?? "";
        $xhtml =  "<span class='{$class} {$currentClass}'>{$currentName}</span>";
        return $xhtml;
    }

    public static function showCategoryTrasaction($class = "text", $category = "")
    {

        $xhtml = null;
        $config = rrt_get_config_category_transaction();
        $currentCategoryTransaction = isset($config[$category]) ? $config[$category] : $config['default'];
        $currentName = $currentCategoryTransaction['name'] ?? "";
        $currentClass = $currentCategoryTransaction['class'] ?? "";

        $xhtml =  "<span class='{$currentClass}'>{$currentName}</span>";
        return $xhtml;
    }
    public static function showTrackThumbnail($data)
    {
        $result = null;
        $thumbnail = null;
        $data = is_array($data) ? $data : $data->toArray();
        if ($data) {
            $data = array_filter($data, function ($item) {
                $type = $item['type'] ?? "";
                if ($type == 'thumbnail') {
                    return $item;
                }
            });
            $data = count($data) > 0 ? array_shift($data) : [];
        }
        $thumbnailName = $data['name'] ?? "";
        $thumbnail =  $thumbnailName ? url('public/uploads/tracks/' . $thumbnailName) : "";
        $thumbnailUrl =  rrt_show_thumbnail($thumbnail);
        $result = " <img src='{$thumbnailUrl}'>";
        return $result;
    }
    public static function  showTrackPrice($contracts = [])
    {
        $contracts = is_array($contracts) ? $contracts : [];
        $minPrice = PHP_INT_MAX;
        foreach ($contracts as $contract) {
            if (isset($contract['price']) && $contract['enabled'] == 1) {
                $minPrice = min($minPrice, $contract['price']);
            }
        }
        $minPrice = $minPrice === PHP_INT_MAX ? 0 : $minPrice;
        $result = $minPrice > 0 ? rrt_show_price($minPrice) : __("Contact");
        return $result;
    }
    public static function getTrackThumbnailUrl($data)
    {
        $result = null;
        $thumbnail = null;
        $data = is_array($data) ? $data : $data->toArray();
        if ($data) {
            $data = array_filter($data, function ($item) {
                $type = $item['type'] ?? "";
                if ($type == 'thumbnail') {
                    return $item;
                }
            });
            $data = count($data) > 0 ? array_shift($data) : [];
        }
        $thumbnailName = $data['name'] ?? "";
        $thumbnail =  $thumbnailName ? url('public/uploads/tracks/' . $thumbnailName) : "";
        $thumbnailUrl =  rrt_show_thumbnail($thumbnail);
        return $thumbnailUrl;
    }
    public static function showFreeDownload($trackID)
    {
        $model = new TrackModel();
        $track = $model->findOrFail($trackID, ['id']);
        $hasFreeContract = $track->listContracts()
            ->whereHas('contractSetting', function ($query) {
                $query->where('category', 'free');
            })
            ->exists();
        return $hasFreeContract ? 1 : 0;
    }
    public static function showTimeDiff($createdAt)
    {
        return  Carbon::now()->diffForHumans($createdAt);
    }
    public static function showListStatus($category = "")
    {
        $xhtml = null;
        $config = rrt_get_config_status();
        $result = array_filter($config, function ($item) use ($category) {
            $itemCategory = $item['category'] ?? "";
            if ($category == $itemCategory) {
                return $item;
            }
        });
        return $result;
    }
    public static function showFooterItemLink($column = "2", $setting = [])
    {

        $keyFooter = "footer_col_{$column}";
        $keyLink = "footer_col_{$column}_link";
        $keyTitle = "{$keyLink}_title";
        $filteredItems = [];
        foreach ($setting as $key => $value) {
            if (strpos($key, $keyLink) === 0) {
                $filteredItems[$key] = $value;
            }
        }
        $result = [];
        foreach ($filteredItems as $key => $value) {
            if (strpos($key, 'title') !== false && $value !== NULL) {
                $result[$key] = $value;
            }
        }
        $final = [];
        $index = 0;
        foreach ($result as $key => $item) {
            $index++;
            $final[$index]['title'] = $item;
            $linkAnother = $setting[$keyFooter . '_link_'  . $index . '_another'] ?? "";
            $linkPage = $setting[$keyFooter . '_link_'  . $index . '_page_id'] ?? "#";
            $final[$index]['link'] = $linkAnother ? $linkAnother : rrt_route('public/page/detail', ['id' => $linkPage]);
        }

        //   dd($final);
        return $final;
    }
    public static function showContentType($key = "")
    {
        $xhtml = null;
        $config = rrt_get_config_core('type');

        $currentItem = isset($config[$key]) ? $config[$key] : $config['default'];
        $currentName = $currentItem['name'] ?? "";
        $xhtml =  $currentName;

        return $xhtml;
    }
    public static function checkForFreeContract($contracts = []) {
        foreach ($contracts as $contract) {
            if (isset($contract['contract_setting']['category']) && $contract['contract_setting']['category'] === 'free') {
                return 1;
            }
        }
        return 0;
    }
    public static function getContractsIds($contracts)
    {
        $ids = [];

        foreach ($contracts as $contract) {
            if (isset($contract['price']) && $contract['price'] > 0) {
                $ids[] = $contract['id'];
            }
        }

        return implode(',', $ids); // Return IDs as comma-separated string
    }
}
