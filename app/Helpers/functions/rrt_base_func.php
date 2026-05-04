<?php

use App\Models\AIPackageRole;
use App\Models\AIServiceOrder;
use App\Models\Currency;
use App\Models\LogAIUsage;
use App\Models\MusicDistributionModel;
use App\Models\OrderModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\SettingModel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Role;
use App\Models\PlatformModel;
use App\Models\SubscriptionOrderModel;
use App\Models\User;
use App\Models\AIPackage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

function rrt_get_logo()
{
    return asset('public/images/logo.png');
}
function rrt_get_config_core($key, $keyChild = "")
{
    $result = null;
    $config = config("rrtech.core.{$key}");
    $resultKeyChild = $keyChild && isset($config[$keyChild]) ? $config[$keyChild] : "";
    $result = $resultKeyChild ? $resultKeyChild : $config;
    return $result;
}
function rrt_get_config_status()
{
    return config("rrtech.status");
}
function rrt_get_config_category_transaction()
{
    return config("rrtech.category");
}
function rrt_get_config_transaction()
{
    return config("rrtech.transaction");
}
function rrt_get_config_route()
{
    return config("rrtech.route");
}
function rrt_get_config_by($type = 'core', $key = "", $keyChild = "")
{
    $result = null;
    $config = config("rrtech.{$type}.{$key}");
    $resultKeyChild = $keyChild && isset($config[$keyChild]) ? $config[$keyChild] : "";
    $result = $resultKeyChild ? $resultKeyChild : $config;
    return $result;
}
function rrt_show_status($status)
{
    $xhtml = null;
    $status = $status ? $status : 'default';
    $template  = rrt_get_config_status();
    $current = isset($template[$status]) ? $template[$status] : $template['default'];
    $xhtml = sprintf('<span class = "badge %s">%s</span>', $current['class'], $current['name']);
    return $xhtml;
}
function rrt_show_price($price, $prefix = "", $after = "")
{
    $price = (float)$price;
   
    $userID = rrt_get_user_login('id');
    $user = UserModel::find($userID);
    $result = rrt_get_config_core('currency') . " " . number_format($price, 2, ".", ",");
    if ($user && $user->currency) {
        $currencyModel = Currency::where('name', $user->currency)->first();
        if ($currencyModel) {
            $price = convertUsdToCurrency($price, $currencyModel->exchange_rate);
            $result = config("rrtech.type_currency.$user->currency.unit") . " " . number_format($price, 2, ".", ",");
        }
    }


    if ($prefix) {
        $result = $prefix . " " . $result;
    }
    if ($after) {
        $result = $result  . $after;
    }
    return $result;
}
function rrt_show_thumbnail($thumb = "")
{
    $result = $thumb ? $thumb : asset('public/images/no-image.png');
    return $result;
}
function rrt_show_long_time($time)
{
    $timeFormat = rrt_get_config_core('format.long_time');
    $xhtml = ($time) ? date($timeFormat, strtotime($time)) : "Chưa xác định";
    return $xhtml;
}
function rrt_show_short_time($time)
{
    $timeFormat = rrt_get_config_core('format.short_time');
    $xhtml = ($time) ? date($timeFormat, strtotime($time)) : "Chưa xác định";
    return $xhtml;
}
function rrt_convert_format_date($date, $format = "")
{
    $carbonDateTime = Carbon::parse($date);
    $formattedDateTime = $carbonDateTime->format($format);
    return $formattedDateTime;
}
function rrt_get_date_hrd($createdAt)
{
    $created_at = Carbon::parse($createdAt);
    $diffInDays = $created_at->diffInDays(Carbon::now());
    $humanReadableDifference = $created_at->subDays($diffInDays)->diffForHumans();

    return $humanReadableDifference;
}
function rrt_get_expired_at($createdAt,  $key = "day", $number = 30)
{
    $createdAt  = Carbon::parse($createdAt);
    $result = $key == 'day' ? $createdAt->addDays($number) : $createdAt->addYear($number);
    return $result;
}

function rrt_get_youtube_url($url)
{
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
    $youtube_id = "";
    if (preg_match($longUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }
    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }
    return 'https://www.youtube.com/embed/' . $youtube_id;
}
function rrt_convert_vi_to_en($str)
{
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
    $str = preg_replace("/(đ)/", "d", $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
    $str = preg_replace("/(Đ)/", "D", $str);
    //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
    return $str;
}
function get_list_prefix()
{
    $result = [];
    $list = config('rrtech.core.prefix');
    foreach ($list as $key => $item) {
        $result[$key]['session'] = "info_{$item}";
        $result[$key]['redirect'] = "{$item}/home/index";
        $result[$key]['login'] = "{$item}/auth/login";
    }
    return $result;
}
function rrt_get_locale()
{
    $locale = app()->getLocale();
    return $locale;
}
function rrt_get_param_locale()
{
    $locale = app()->getLocale();
    return ['locale' => $locale];
}
function rrt_route($routeName, $params = [])
{
    $locale = app()->getLocale();
    $params['locale'] = $locale;
    return route($routeName, $params);
}

function rrt_encrypt_password($password)
{
    $salt = rrt_get_config_by('core', 'security', 'salt');
    $secret = rrt_get_config_by('core', 'security', 'secret');
    $result = md5($salt . $password . $secret);
    return $result;
}
function rrt_get_username_from_email($email)
{
    $email = explode("@", $email);
    $result = $email ? array_shift($email) : "";
    return $result;
}
function rrt_check_login()
{
    $result = session()->has('info_studio');
    return $result;
}
function rrt_get_user_login($key = "")
{
    $user = session()->get('info_studio');
    $user = $user ? $user->toArray() : [];

    $resultKey = isset($user[$key]) ? $user[$key] : "";
    $result = $key ? $resultKey : $user;
    return $result ?? "";
}
function rrt_get_fullname()
{
    $user = rrt_get_user_login();
    $firstName = $user['first_name'] ?? "";
    $lastName = $user['last_name'] ?? "";
    $fullname = "{$firstName} {$lastName}";
    $username = $user['username'] ?? "";
    $result = $firstName ? $fullname : $username;
    return $result;
}
function rrt_get_fullname_by_user($user)
{

    $firstName = $user['first_name'] ?? "";
    $lastName = $user['last_name'] ?? "";
    $fullname = "{$firstName} {$lastName}";
    $username = $user['username'] ?? "";

    $result = $firstName ? $fullname : $username;
    return $result;
}

function rrt_get_step_name($step = "1")
{
    $content_step = rrt_get_config_by('core', 'content', "step_{$step}");
    return $content_step;
}
function rrt_group_by($array, $key)
{
    $result = [];
    foreach ($array as $val) {
        $val = (is_array($val)) ? $val : (array) $val;
        $valKey = isset($val[$key]) ? $val[$key] : "";
        if ($valKey) {
            $result[$valKey][] = $val;
        }
    }
    return $result;
}
function rrt_get_deliverables_name($type)
{
    $result = null;
    $list = [
        'default' => [
            'name' => 'unknow',
        ],
        'unTaggedMp3' => [
            'name' => 'Untagged Mp3'
        ],
        'taggedMp3' => [
            'name' => 'TAGGED MP3'
        ],
        'mp3AndWav' => [
            'name' => 'MP3 + WAV'
        ],
        'stems' => [
            'name' => 'Stem files, MP3, WAV'
        ],
        'untaggedMp3Wave' => [
            'name' => 'Untagged Mp3/WAV'
        ],
    ];
    $current = isset($list[$type]) ? $list[$type] : $list['default'];
    $result = isset($current['name']) ? $current['name'] : "";
    return $result;
}
function rrt_get_admin_login($key = "")
{
    $user = session()->get('info_admin');
    $user = $user ? array_shift($user) : [];
    $user = $user ? $user->toArray() : [];
    $resultKey = $user[$key] ?? "";
    $result = $resultKey ? $resultKey : $user;
    return $result;
}

function rrt_get_url_image_upload($key, $name)
{
    return url(sprintf('/public/uploads/%s/%s/', $key, $name));
}

function rrt_get_thumb_studio($userID = "")
{
    if (!$userID) {
        $userID = rrt_get_user_login('id') ?? '';
    }

    $userModel = new UserModel();
    $user = $userModel->getItem(['user_id' => $userID], ['task' => 'id']);
    $userThumb = $user->thumbnail ?? "";
    if (!empty($userThumb)) {
        $thumbnail =  url('public/uploads/users/' . $userThumb);
    } else {
        $thumbnail = asset('public/images/no-image.png');
    }
    return $thumbnail;
}
function rrt_random_code($modelName, $column)
{
    $model = null;
    switch ($modelName) {
        case 'order':
            $model = new OrderModel();
            break;

        default:
            # code...
            break;
    }
    do {
        $code = random_int(1000, 9999);
    } while ($model::where($column, "=", $code)->first());
    return $code;
}
function rrt_get_setting($meta_key)
{
    $settingModel = new SettingModel();
    $item = $settingModel->getItem(['meta_key' => $meta_key], ['task' => 'meta_key']);
    $result = $item && isset($item['meta_value']) ? $item['meta_value'] : '';
    return $result;
}

function getLocalIP()
{
    $client = new Client();
    $response = $client->get('https://api.ipify.org/');
    $ip = $response->getBody()->getContents() ?? null;
    return $ip;
}
function rrt_get_locale_by_ip($ip = null)
{
    $clientIP = getLocalIP();
    $ip = $ip ?? $clientIP;
    $endpoint = sprintf('http://ip-api.com/json/%s', $ip);
    $client = new Client();
    try {
        $response = $client->request('GET', $endpoint);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        if (array_key_exists('countryCode', $data)) {
            $locale  = strtolower($data['countryCode']);
        } else {
            $locale = config('app.locale');
        }
    } catch (Exception $e) {
        $locale = config('app.locale');
    }
    $locale = ($locale != 'kr') ? 'en' : $locale;
    return $locale;
}

function rrt_set_locale($locale = 'en')
{

    return app()->setLocale($locale);
}




function rrt_show_upload_url($thumbnail, $folder = "")
{

    $result = $thumbnail ?  url("uploads/" . $folder . "/" . $thumbnail) : asset('public/images/track-thumb.jpg');
    return  $result;
}
function rrt_get_time_of_day()
{
    $currentHour = Carbon::now('Asia/Seoul')->format('H');


    if ($currentHour >= 5 && $currentHour < 12) {
        return __('Good Morning');
    } elseif ($currentHour >= 12 && $currentHour < 17) {
        return __('Good Afternoon');
    } else {
        return __('Good Evening');
    }
}
function rrt_convert_duration($duration)
{
    // Chuyển đổi duration thành integer (số giây)
    $seconds = intval($duration);

    // Tính số phút và số giây còn lại
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;

    // Định dạng phút và giây để luôn có 2 chữ số
    $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    $formattedSeconds = str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);

    // Trả về kết quả dưới định dạng mm:ss
    return "{$formattedMinutes}:{$formattedSeconds}";
}
function rrt_get_user_ai_usage($ai_id = 1)
{
    $userId = rrt_get_user_login('id');
    $user = UserModel::find($userId);
    if ($ai_id == \App\Models\AIService::AIServiceAIMastering) {
        $userAIUsageCount = $user->ai_usage_count ?? 0;
    } else if ($ai_id == \App\Models\AIService::AIServiceAIRecognition) {
        $userAIUsageCount = $user->ai_usage_count_reconize ?? 0;
    }
    return $userAIUsageCount;
}
function rrt_get_package_with_role()
{
    $userId = rrt_get_user_login('id');
    $user = UserModel::find($userId);
    $userRoles = rrt_get_user_role($user); // lấy được role
    // lấy package theo ai_id và role của user;
    $role = Role::whereIn('slug', $userRoles)->first();
    if ($role) {
        $aiPackages = $role->aiPackages;
        return ['role' => $role, 'packages' => $aiPackages];
    }
    return null;
}
function rrt_get_user_role($user)
{
    // Mặc định là free-user
    $role = ['free-user'];

    // Kiểm tra tất cả Subscription Orders của user
    $subscriptionOrders = $user->subscriptionOrders()->get();

    // Kiểm tra publishing và distribution trước
    $hasPublishing = false;
    $hasDistribution = false;

    foreach ($subscriptionOrders as $subscriptionOrder) {
        if ($subscriptionOrder->subscription_id == 1) {
            $hasPublishing = true;
        }
        if ($subscriptionOrder->subscription_id == 2) {
            $hasDistribution = true;
        }
    }

    // Nếu có cả publishing và distribution, trả về cả hai vai trò
    if ($hasPublishing && $hasDistribution) {
        return ['publishing-annually', 'distribution-annually'];
    }

    // Nếu chỉ có publishing, trả về publishing
    if ($hasPublishing) {
        return ['publishing-annually'];
    }

    // Nếu chỉ có distribution, trả về distribution
    if ($hasDistribution) {
        return ['distribution-annually'];
    }

    // Kiểm tra các subscription khác chỉ khi không có publishing hoặc distribution
    foreach ($subscriptionOrders as $subscriptionOrder) {
        if ($subscriptionOrder->subscription_id == 3) {
            // Nếu có subscription id = 3 thì là basic seller
            $role = ['free-seller'];
        }
    }

    // Kiểm tra Plan Orders chỉ khi không có publishing hoặc distribution
    $planOrder = $user->planOrders()->first();
    if ($planOrder && $planOrder->plan_id == 3) {
        if ($planOrder->cycle === null || $planOrder->cycle === 'monthly') {
            // Nếu có plan id = 3 và không có cycle hoặc cycle = monthly thì là proseller monthly
            $role = ['proseller-monthly'];
        } elseif ($planOrder->cycle === 'annually') {
            // Nếu có plan id = 3 và cycle = annually thì là proseller annually
            $role = ['proseller-annually'];
        }
    }

    return $role;
}

function rrt_get_all_user_roles($user = null)
{
    $roles = [];
    $subscriptionOrders = $user->subscriptionOrders()->get();
    $hasPublishing = false;
    $hasDistribution = false;
    foreach ($subscriptionOrders as $subscriptionOrder) {
        if ($subscriptionOrder->subscription_id == 1) {
            $hasPublishing = true;
        }
        if ($subscriptionOrder->subscription_id == 2) {
            $hasDistribution = true;
        }
    }
    if ($hasPublishing) {
        $roles[] = 'publishing-annually';
    }
    if ($hasDistribution) {
        $roles[] = 'distribution-annually';
    }
    foreach ($subscriptionOrders as $subscriptionOrder) {
        if ($subscriptionOrder->subscription_id == 3) {
            // Nếu có subscription id = 3 thì thêm vai trò free-seller
            $roles[] = 'free-seller';
        }
    }
    $planOrder = $user->planOrders()->first();
    if ($planOrder && $planOrder->plan_id == 3) {
        if ($planOrder->cycle === null || $planOrder->cycle === 'monthly') {
            // Nếu có plan id = 3 và không có cycle hoặc cycle = monthly thì thêm proseller monthly
            $roles[] = 'proseller-monthly';
        } elseif ($planOrder->cycle === 'annually') {
            // Nếu có plan id = 3 và cycle = annually thì thêm proseller annually
            $roles[] = 'proseller-annually';
        }
    }
    if (empty($roles)) {
        $roles[] = 'free-user';
    }

    return $roles;
}
function rrt_get_user_joinType($user)
{
    $packages = [];
    $subscriptionOrders = $user->subscriptionOrders()->get();
    if ($subscriptionOrders->isEmpty()) {
        $planOrder = $user->planOrders()->first();
        if ($planOrder) {
            if ($planOrder->plan_id == 3) {
                if ($planOrder->cycle === null || $planOrder->cycle === 'monthly') {
                    $packages[] = 'Pro Seller Monthly';
                } elseif ($planOrder->cycle === 'annually') {
                    $packages[] = 'Pro Seller Annually';
                }
            }
        } else {
            return ['Free User'];
        }
    } else {
        foreach ($subscriptionOrders as $subscriptionOrder) {
            switch ($subscriptionOrder->subscription_id) {
                case 1:
                    $packages[] = 'Publishing Annually';
                    break;
                case 2:
                    $packages[] = 'Distribution Annually';
                    break;
                case 3:
                    $packages[] = 'Basic Seller';
                    break;
            }
        }
    }
    if (empty($packages)) {
        return ['Free User'];
    }

    return $packages;
}

function rrt_get_all_user_joinTypes($user = null)
{
    $packages = [];
    $subscriptionOrders = $user->subscriptionOrders()->get();
    foreach ($subscriptionOrders as $subscriptionOrder) {
        switch ($subscriptionOrder->subscription_id) {
            case 1:
                $packages[] = 'Publishing Annually';
                break;
            case 2:
                $packages[] = 'Distribution Annually';
                break;
            case 3:
                $packages[] = 'Basic Seller';
                break;
        }
    }
    $planOrder = $user->planOrders()->first();
    if ($planOrder) {
        if ($planOrder->plan_id == 3) {
            if ($planOrder->cycle === null || $planOrder->cycle === 'monthly') {
                $packages[] = 'Pro Seller Monthly';
            } elseif ($planOrder->cycle === 'annually') {
                $packages[] = 'Pro Seller Annually';
            }
        }
    }
    if (empty($packages)) {
        $packages[] = 'Free User';
    }
    $packages = array_unique($packages);

    return $packages;
}

// Default usage count when no specific count is set
const DEFAULT_USAGE_COUNT = 0;
const SUBSCRIPTION_IDS = [1, 2];

function rrt_add_subscription_ai_usage_count(int $userId, ?int $numberAdd = null): ?array
{


    $user = UserModel::with(['subscriptionOrders','planOrders'])->find($userId);

    if (!$user) {
        throw new \Exception("User not found");
    }

    $userRoles = rrt_get_user_role($user);
    $role = Role::whereIn('slug', $userRoles)->first();

    if (!$role) {
        throw new \Exception("User role not found");
    }

    // Lấy tất cả các loại AI
    $aiPackages = AIPackage::all();
    if ($aiPackages->isEmpty()) {
        throw new \Exception("No AI packages found");
    }

    // Lấy usage counts cho tất cả AI
    $usageCounts = getUsageCounts($role, $aiPackages);
    $subscriptionOrder = $user->subscriptionOrders ?? null;
    $planOrder = $user->planOrders ?? null;
    $subscriptionOrderId = $subscriptionOrder->id ?? $planOrder->id ?? null;
    // Log thông tin debug
    Log::info("Adding AI usage for user {$userId}", [
        'role' => $role->slug,
        'usage_counts' => $usageCounts,
        'subscription_order_id' =>  $subscriptionOrderId,
    ]);
    
    // Kiểm tra subscription order và thêm usage count

    DB::transaction(function () use ($user, $subscriptionOrderId, $usageCounts) {
        $existingLog = LogAIUsage::where('user_id', $user->id)
            ->where('subscription_order_id', $subscriptionOrderId)
            ->first();
        if (!$existingLog) {
            // Add usage for AIMastering
            addAIUsage(
                $user,
                AIService::Mastering->value,
                $usageCounts[AIService::Mastering->value] ?? DEFAULT_USAGE_COUNT,
                $subscriptionOrderId
            );
            // Add usage for AIRecognition
            addAIUsage(
                $user,
                AIService::Recognition->value,
                $usageCounts[AIService::Recognition->value] ?? DEFAULT_USAGE_COUNT,
                $subscriptionOrderId
            );
        }
        $user->save();
    });

    return $userRoles;
}

/**
 * Lấy usage counts cho tất cả AI
 */
function getUsageCounts(Role $role, Collection $aiPackages): array
{
    $aiPackageRoles = AIPackageRole::whereIn('ai_id', $aiPackages->pluck('ai_id'))
        ->where('role_id', $role->id)
        ->whereIn('package_id', $aiPackages->pluck('id'))
        ->get();

    return $aiPackageRoles->pluck('usage_count', 'ai_id')->toArray();
}

/**
 * Thêm usage count cho một loại AI
 */
function addAIUsage(UserModel $user, int $aiId, int $usageCount,  $subscriptionOrderId): void
{
    $beforeCount = $user->{"ai_usage_count" . ($aiId == AIService::Recognition->value ? "_reconize" : "")} ?? 0;
    $currentCount = max(0, $beforeCount + $usageCount);

    $user->{"ai_usage_count" . ($aiId == AIService::Recognition->value ? "_reconize" : "")} = $currentCount;

    $data = [
        'ai_id' => $aiId,
        'user_id' => $user->id,
        'before_usage_count' => $beforeCount,
        'amount' => $usageCount,
        'current_usage_count' => $currentCount,
        'service_order_id' => null,
        'subscription_order_id' => $subscriptionOrderId,
    ];

    addLogUsageAi($data);
}

/**
 * Enum cho các loại AI service
 */
enum AIService: int
{
    case Mastering = 1;
    case Recognition = 2;
}

function rrt_add_proseller_usage_ai($userId, $numberAdd = 30)
{

    $user = UserModel::find($userId);
    if ($user) {
        // Add 30 usage for AIMastering
        $before_usage_count = $user->ai_usage_count ?? 0;
        $current_usage_count = max(0, $before_usage_count + $numberAdd);
        $user->ai_usage_count = $current_usage_count;
        $data = [
            'ai_id' => \App\Models\AIService::AIServiceAIMastering,
            'user_id' => $userId,
            'before_usage_count' => $before_usage_count,
            'amount' => $numberAdd,
            'current_usage_count' => $current_usage_count,
            'service_order_id' => null,
            'subscription_order_id' => $subscriptionOrder->id ?? null,
        ];
        addLogUsageAi($data);

        // Add 30 usage for AIRecognition
        $before_usage_count = $user->ai_usage_count_reconize ?? 0;
        $current_usage_count = max(0, $before_usage_count + $numberAdd);
        $user->ai_usage_count_reconize = $current_usage_count;
        $data = [
            'ai_id' => \App\Models\AIService::AIServiceAIRecognition,
            'user_id' => $userId,
            'before_usage_count' => $before_usage_count,
            'amount' => $numberAdd,
            'current_usage_count' => $current_usage_count,
            'service_order_id' => null,
            'subscription_order_id' => $subscriptionOrder->id ?? null,
        ];
        addLogUsageAi($data);

        $user->save();
    }
}
function addLogUsageAi($data = [])
{
    if (!empty($data)) {
        LogAIUsage::create($data);
    }
}
function rrt_getListFlatform()
{
    $platforms = PlatformModel::orderBy('id', 'desc')->get();
    return $platforms ?? [];
}
function getTimeDiffHuman($time)
{
    $locale = rrt_get_locale();
    $char = 'en';
    if ($locale === 'kr') {
        $char = 'ko';
    }
    return Carbon::parse($time)->locale($char)->diffForHumans() ?? '';
}

function convertUsdToCurrency($amountInUsd, $exchangeRate)
{
    return $amountInUsd * $exchangeRate;
}
// ai_id 1 ai mastering
// ai_id 2 ai recognition
function rrt_get_text_price_role($ai_id = 1)
{
    $userID = rrt_get_user_login('id');
    $textPriceRole =  __("Free to use");
    $packageRole = rrt_get_package_with_role();
    if ($packageRole && isset($packageRole['role']) && $packageRole['role']) {
        $role = $packageRole['role'];
        $package = $packageRole['packages']->where('pivot.ai_id', $ai_id)->first();

        // ai mastering ai_id = 1;
        $usage_count = rrt_get_user_ai_usage($ai_id);
        if ($usage_count) {
            $textPriceRole =  __("Free to use");
        } else {
            $settingPrice = AIPackageRole::where('ai_id', $ai_id)->where('package_id', $package->id)->where('role_id', $role->id)->first();
            if (!empty($settingPrice) && $settingPrice->price > 0) {
                $textPriceRole =  __(rrt_show_price($settingPrice->price) . " to use");
            } else {
                $textPriceRole =   "Free to use";
            }
        }
    }
    return $textPriceRole;
}
function rrt_get_price_role($ai_id = 1)
{
    $userID = rrt_get_user_login('id');
    $packageRole = rrt_get_package_with_role();
    if ($packageRole && isset($packageRole['role']) && $packageRole['role']) {
        $role = $packageRole['role'];
        $package = $packageRole['packages']->where('pivot.ai_id', $ai_id)->first();
        $settingPrice = AIPackageRole::where('ai_id', $ai_id)->where('package_id', $package->id)->where('role_id', $role->id)->first();
        return !empty($settingPrice) ? $settingPrice->price : 0;
    }
    return 0;
}

function getPresetInfo($preset = 'a')
{
    $presets = config("rrtech.presets");
    if ($preset == 'gereral') {
        return 'General';
    }
    if (isset($presets[$preset])) {
        $info = $presets[$preset];
        $name = $info['name'] ?? 'N/A';
        $description = $info['description'] ?? 'N/A';
        return "{$name} - {$description}";
    }
    return 'N/A';
}

function rrt_role_buy_package()
{
    $user = rrt_get_user_login();
    $userObj = UserModel::findOrfail($user['id']);
    $orderPlan = $userObj->planOrders()->whereStatus('active')->first();
    if (!empty($orderPlan)) {
        if ($orderPlan->plan_id == 3) {
            // Pro Seller
            return false;
        }
    }
    return true;
}

function rrt_tool_count_usageai($user, $count, $ai_selected)
{
    if ($ai_selected == \App\Models\AIService::AIServiceAIMastering) {
        if (($user->ai_usage_count + $count) < 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient AI Mastering usage.'
            ], 400);
        }
        $before_usage_count = $user->ai_usage_count;
        $amount = $count;
        $user->ai_usage_count += $count;
        $log = [
            'ai_id' => 1,
            'user_id' => $user->id,
            'before_usage_count' => $before_usage_count,
            'amount' => $amount,
            'current_usage_count' => $user->ai_usage_count,
            'mastering_id' => $ai_selected,
            'note' => 'admin add minus use',
        ];
        LogAIUsage::create($log);
    } elseif ($ai_selected == \App\Models\AIService::AIServiceAIRecognition) {
        if (($user->ai_usage_count_reconize + $count) < 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient AI Recognition usage.'
            ], 400);
        }
        $before_usage_count = $user->ai_usage_count_reconize;
        $amount = $count;
        $user->ai_usage_count_reconize += $count;
        $log = [
            'ai_id' => 1,
            'user_id' => $user->id,
            'before_usage_count' => $before_usage_count,
            'amount' => $amount,
            'current_usage_count' => $user->ai_usage_count_reconize,
            'recognition_id' => $ai_selected,
            'note' => 'admin add minus use',
        ];
        LogAIUsage::create($log);
    }
    $user->save();

    return true;
}
function rrt_get_order_pending($user_id)
{
    $data = '';
    if (!empty($user_id)) {
        return $data;
    }
    $subOrder = SubscriptionOrderModel::whereUser_id($user_id)->whereStatus('pending')->get();
    if (!empty($subOrder)) {
        $data = $subOrder;
    }
    $planOrder = PlanOrderModel::whereUser_id($user_id)->whereStatus('pending')->get();
    if (!empty($planOrder)) {
        $data = $planOrder;
    }
    return $data;
}
function rrt_limit_upload($count, $type)
{
    $meta_keys = MusicDistributionModel::get_meta_key($type);

    if (empty($meta_keys) || !isset($meta_keys['meta_key'], $meta_keys['limit_upload'])) {
        return false;
    }

    $limit = SettingModel::whereMeta_key($meta_keys['meta_key'])->first();
    if ($limit && is_numeric($limit->meta_value)) {
        return $limit->meta_value == -1 || $count < (int) $limit->meta_value ? true : false;
    }
    return $meta_keys['limit_upload'] == -1 || $count < (int) $meta_keys['limit_upload'];
}
function rrt_icon_coin_checkout($style = '')
{
    if (file_exists(public_path('assets/public/style2/img/icon_coin.svg'))) {
        $img = "<img ";
        if (!empty($style)) {
            $img .= "style='" . $style . "' ";
        }
        $img .= "src='" . asset('public/style2/img/icon_coin.svg') . "' alt=''>";
        return $img;
    } else {
        return "<span style='font-size:18px; color:black'>$</span>";
    }
}

/**
 * Clone image with special characters in filename to a safe name for sharing
 * 
 * @param string $originalPath Original path to image file
 * @param string $type Type of content (threads, track, page)
 * @return string URL to safe image
 */
function rrt_get_safe_image_url($originalPath, $type = 'general') {
    if (empty($originalPath)) {
        return asset('public/style2/img/1N2Logo 2.png');
    }

    // Get filename from path
    $filename = basename($originalPath);
    $fileInfo = pathinfo($filename);
    $filenameWithoutExt = $fileInfo['filename'];
    $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
    
    // Check if filename contains non-ASCII characters
    if (preg_match('/[^\x20-\x7e]/', $filenameWithoutExt)) {
        // Create safe filename - sanitize and add hash to ensure uniqueness
        $safeFilename = \Illuminate\Support\Str::slug($filenameWithoutExt);
        if (empty($safeFilename)) {
            $safeFilename = 'image';
        }
        $safeFilename .= '_' . substr(md5($filenameWithoutExt), 0, 8);
        if (!empty($extension)) {
            $safeFilename .= '.' . $extension;
        }

        // Determine paths
        $safeDir = public_path('uploads/safe_images/' . $type);
        $safePath = $safeDir . '/' . $safeFilename;
        $safeUrl = url('public/uploads/safe_images/' . $type . '/' . $safeFilename);

        // Determine original full path based on type
        $baseUploadPath = public_path('uploads');
        switch ($type) {
            case 'threads':
                $originalFullPath = $baseUploadPath . '/threads/' . $filename;
                break;
            case 'tracks':
                $originalFullPath = $baseUploadPath . '/tracks/' . $filename;
                break;
            case 'page':
                $originalFullPath = $baseUploadPath . '/page/' . $filename;
                break;
            default:
                $originalFullPath = $baseUploadPath . '/' . $filename;
        }
        
        // Create directory if it doesn't exist
        if (!file_exists($safeDir)) {
            mkdir($safeDir, 0777, true);
        }
        
        // If safe file doesn't exist yet, create a copy
        if (!file_exists($safePath) && file_exists($originalFullPath)) {
            copy($originalFullPath, $safePath);
        }
        
        return $safeUrl;
    }

    // If filename doesn't contain special characters, return original URL
    switch ($type) {
        case 'threads':
            return url('public/uploads/threads/' . $filename);
        case 'tracks':
            return url('public/uploads/tracks/' . $filename);
        case 'page':
            return url('public/uploads/page/' . $filename);
        default:
            return url('public/uploads/' . $filename);
    }
}
