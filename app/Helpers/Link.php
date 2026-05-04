<?php
namespace App\Helpers;
use App\Models\PageModel;
use App\Models\SettingModel;
use App\Models\TrackModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\View;
class Link
{
    private static $userModel;
    private static $trackModel;
    public function __construct()
    {
    }
    public static function producerDetail($trackID)
    {
        $track = TrackModel::find($trackID);
        $user = $track->user()->first();
        $userID = $user->id ?? "";
        $username = $user->username ?? "";
        $route =  rrt_route('public/producer/detail', ['user_id' => $userID, 'username' => $username]);
        return $route;
    }
    public static function trackDetail()
    {
    }
    public static function trackCategory()
    {
    }
    public static function getFooterLink()
    {
        $settings = SettingModel::orderBy('id')->get();
        $columns = $settings->groupBy(function ($item) {
            return explode('_', $item['meta_key'])[2] ?? 'default';
        });
        $footerLinks = $columns->map(function ($items, $colKey) {
            $colTitle = $items->firstWhere('meta_key', "footer_col_{$colKey}_title")['meta_value'] ?? 'Untitled Column';
            $links = [];
            foreach ($items as $item) {
                if (str_contains($item['meta_key'], '_link_') && str_contains($item['meta_key'], '_page_id')) {
                    $index = explode('_', $item['meta_key'])[4];
                    $titleKey = "footer_col_{$colKey}_link_{$index}_title";
                    $anotherKey = "footer_col_{$colKey}_link_{$index}_another";
                    $title = $items->firstWhere('meta_key', $titleKey)['meta_value'] ?? '';
                    $pageId = $item['meta_value'] ?? null;
                    $anotherLink = $items->firstWhere('meta_key', $anotherKey)['meta_value'] ?? null;
                    if ($title) {
                        $links[] = [
                            'title' => $title,
                            'url' => $anotherLink ?: ($pageId ? rrt_route('public/page/showPage', ['id' => $pageId]) : '#'),
                        ];
                    }
                }
            }
            if (empty($links)) {
                return null;
            }
            return [
                'title' => $colTitle,
                'links' => $links,
            ];
        })->filter();
        return $footerLinks;
    }
    public static function getFooterSocial()
    {
        $data = collect([]);
        $title = self::getSetting('title_social', 'Social Media', false);
        $data[] = self::getSetting('instagram', 'Instagram');
        $data[] = self::getSetting('youtube', 'YouTube');
        $data[] = self::getSetting('soundcloud', 'SoundCloud');
        $data = $data->filter(function ($item) {
            return !is_null($item);
        });
        return [
            'title' => $title,
            'data' => $data,
        ];
    }
    protected static function getSetting($meta_key, $default_title, $get_title = true)
    {
        $setting = SettingModel::where("meta_key", $meta_key)->first();
        if ($setting->meta_value) {
            return [
                'title' =>  $get_title == true ? $default_title : $setting->meta_value,
                'url' => $setting->meta_value,
            ];
        }
        return null;
    }
    public static function getPageBySlug($slug = '', $type = null)
    {
        $model = new PageModel();
        $item = $model->getItem(['slug' => $slug], ['task' => 'slug']);
        if (!$item) {
            return;
        }
        if ($type == 'url') {
            $id = $item['id'] ?? null;
            $url = $id ? rrt_route('public/page/showPage', ['id' => $id]) : "#";
            return $url;
        }
        if($type == 'name') {
            $title = $item['name'] ?? 'No title';
        }
        return $item;
    }
}
