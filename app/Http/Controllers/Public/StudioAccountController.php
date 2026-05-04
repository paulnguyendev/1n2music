<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Subscription;
use App\Http\Controllers\Controller;
use App\Models\CountryModel;
use App\Models\PlanModel;
use App\Models\SocialMediaModel;
use App\Models\SubscriptionOrderModel;
use App\Models\PlanOrderModel;
use App\Models\SubscriptionModel;
#Model
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

#Helper
class StudioAccountController extends Controller
{
    private $pathViewController     = "studio.pages.account";
    private $controllerName         = "public/studio/account";
    private $model;
    private $planOrderModel;
    private $subscriptionOrderModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new UserModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->subscriptionOrderModel = new SubscriptionOrderModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        // dd(123);
        $userID = rrt_get_user_login('id');
        $userModel = new UserModel();
        $user = $userModel->getItem(['user_id' => $userID], ['task' => 'id']);
        $userPro = $user['pro'] ?? "";
        $userIPI = $user['ipi_cae'] ?? "";
        $dataPro = array(
            "ABRAMUS" => "ABRAMUS",
            "ACAM" => "ACAM",
            "ACDAM" => "ACDAM",
            "ACUM" => "ACUM",
            "ADDAF" => "ADDAF",
            "AEPI" => "AEPI",
            "AGADU" => "AGADU",
            "AKM" => "AKM",
            "ALBAUTOR" => "ALBAUTOR",
            "AMAR" => "AMAR",
            "AMCOS" => "AMCOS",
            "AMRA" => "AMRA",
            "ANACIM" => "ANACIM",
            "APDAYC" => "APDAYC",
            "APRA" => "APRA",
            "ARTISJUS" => "ARTISJUS",
            "ASCAP" => "ASCAP",
            "ASSIM" => "ASSIM",
            "AUSTRO_MECHANA" => "AUSTRO-MECHANA",
            "AUTODIA" => "AUTODIA",
            "BMI" => "BMI",
            "BUMA" => "BUMA",
            "CAPAC" => "CAPAC",
            "CASH" => "CASH",
            "CHA" => "CHA",
            "CMRRA" => "CMRRA",
            "COMPASS" => "COMPASS",
            "COSCAP" => "COSCAP",
            "COTT" => "COTT",
            "EAU" => "EAU",
            "EUCADA" => "EUCADA",
            "FILSCAP" => "FILSCAP",
            "FOX" => "FOX",
            "GEMA" => "GEMA",
            "HDS_ZAMP" => "HDS-ZAMP",
            "ICE" => "ICE",
            "IMRO" => "IMRO",
            "IPRS" => "IPRS",
            "JACAP" => "JACAP",
            "JASRAC" => "JASRAC",
            "KCI" => "KCI",
            "KODA" => "KODA",
            "KOMCA" => "KOMCA",
            "LITA" => "LITA",
            "MACP" => "MACP",
            "MCPS" => "MCPS",
            "MCSC" => "MCSC",
            "MCT" => "MCT",
            "MESAM" => "MESAM",
            "MRS" => "MRS",
            "MSG" => "MSG",
            "MUSICAUTOR" => "MUSICAUTOR",
            "MUST" => "MUST",
            "MusicMark" => "MusicMark",
            "NASCAM" => "NASCAM",
            "NCB" => "NCB",
            "NS" => "NS",
            "OSA" => "OSA",
            "PROCAN" => "PROCAN",
            "PRS" => "PRS",
            "RAO" => "RAO",
            "SABAM" => "SABAM",
            "SABEM" => "SABEM",
            "SACD" => "SACD",
            "SACEM" => "SACEM",
            "SACM" => "SACM",
            "SACVEN" => "SACVEN",
            "SADAIC" => "SADAIC",
            "SADEMBRA" => "SADEMBRA",
            "SAMRO" => "SAMRO",
            "SARRAL" => "SARRAL",
            "SAYCE" => "SAYCE",
            "SAYCO" => "SAYCO",
            "SAZAS" => "SAZAS",
            "SBACEM" => "SBACEM",
            "SBAT" => "SBAT",
            "SCD" => "SCD",
            "SDRM" => "SDRM",
            "SESAC" => "SESAC",
            "SGAE" => "SGAE",
            "SIAE" => "SIAE",
            "SICAM" => "SICAM",
            "SOBODAYC" => "SOBODAYC",
            "SOCAN" => "SOCAN",
            "SOCINPRO" => "SOCINPRO",
            "SODRAC" => "SODRAC",
            "SOKOJ" => "SOKOJ",
            "SOZA" => "SOZA",
            "SPA" => "SPA",
            "SPAC" => "SPAC",
            "SPACEM" => "SPACEM",
            "SQN" => "SQN",
            "STEF" => "STEF",
            "STEMRA" => "STEMRA",
            "STIM" => "STIM",
            "SUISA" => "SUISA",
            "TEOSTO" => "TEOSTO",
            "TONO" => "TONO",
            "UACRR" => "UACRR",
            "UBC" => "UBC",
            "UCMR_ADA" => "UCMR-ADA",
            "WAMI" => "WAMI",
            "ZAIKS" => "ZAIKS",
            "ZAMP" => "ZAMP",
            "VCMPC" => "VCMPC",
        );
            $dataCurrency = config('rrtech.type_currency');
        return view(
            "{$this->pathViewController}/index",
            [
                'user' => $user,
                'dataPro' => $dataPro,
                'userPro' => $userPro,
                'dataCurrency' => $dataCurrency,
            ]
        );
    }
    public function postProfile(Request $request)
    {
        $params = $request->all();

        $response = $this->model->saveItem($params, ['task' => 'edit-item']);
        if($response['status']){
            return response()->json([
                'status' => 200,
                'data' => $params,
                'msg' => $response['message'],
            ]);
        }
        return response()->json([
            'status' => 500,
            'data' => $params,
            'msg' => $response['message'],
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $file = $request->hasFile('file');
        // dd($file);
        if ($file) {
            $file = $request->file('file');

            $originalName = $file->getClientOriginalName();
            $originalName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->clientExtension();
            $name = Str::random(10) . "." . $extension;
            $file->storeAs('users', $name, 'rrt_storage');
            $params['user_id'] = rrt_get_user_login('id') ?? 0;
            $params['thumbnail'] = $name;
            $user =  $this->model->getItem($params, ['task' => 'id']);

            if ($user->thumbnail) {
                Storage::disk('rrt_storage')->delete("users/{$user->thumbnail}");
            }
            $result =     $this->model->saveItem($params, ['task' => 'thumbnail']);

            if ($result) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 500]);
            }
        }
    }
    public function credentials(Request $request)
    {
        $userID = rrt_get_user_login('id');
        $userModel = new UserModel();
        $user = $userModel->getItem(['user_id' => $userID], ['task' => 'id']);
        $countries = CountryModel::all();
        return view(
            "{$this->pathViewController}/credentials",
            [
                'user' => $user,
                'countries' => $countries
            ]
        );
    }
    public function postCredentials(Request $request)
    {
        $params = $request->all();
        $name = $params['name'] ?? "";
        $userID = $params['id'] ?? "";
        $value = $params['value'] ?? "";
        
        $error = 0;
        $msg = "Update successfully";
        
        // Check if this is a unique validation request
        if (strpos($name, 'check_') === 0) {
            $fieldToCheck = str_replace('check_', '', $name);
            
            // Check uniqueness based on field type
            if ($fieldToCheck == 'username') {
                $checkUser = $this->model->where('username', $value)->where('id', '!=', $userID)->first();
                if ($checkUser) {
                    return response()->json([
                        'error' => 1,
                        'msg' => 'Username is already taken'
                    ]);
                }
            } 
            else if ($fieldToCheck == 'email') {
                $checkUser = $this->model->where('email', $value)->where('id', '!=', $userID)->first();
                if ($checkUser) {
                    return response()->json([
                        'error' => 1,
                        'msg' => 'Email is already used by another account'
                    ]);
                }
            }
            else if ($fieldToCheck == 'phone') {
                $checkUser = $this->model->where('phone', $value)->where('id', '!=', $userID)->first();
                if ($checkUser) {
                    return response()->json([
                        'error' => 1,
                        'msg' => 'Phone number is already used by another account'
                    ]);
                }
            }
            
            // If validation passed, return success
            return response()->json([
                'error' => 0,
                'msg' => 'Validation passed'
            ]);
        }
        
        // Handle username change
        if ($name == 'username' && !empty($value)) {
            $checkUser = $this->model->where('username', $value)->where('id', '!=', $userID)->first();
            if ($checkUser) {
                $error = 1;
                $msg = "Username is already taken";
            } else {
                $data = ['username' => $value, 'id' => $userID];
                $result = $this->model->saveItem($data, ['task' => 'edit-item']);
            }
        }
        // Handle email change
        else if ($name == 'email' && !empty($value)) {
            $checkUser = $this->model->where('email', $value)->where('id', '!=', $userID)->first();
            if ($checkUser) {
                $error = 1;
                $msg = "Email is already used by another account";
            } else {
                $data = ['email' => $value, 'id' => $userID];
                $result = $this->model->saveItem($data, ['task' => 'edit-item']);
            }
        }
        // Handle password change
        else if ($name == 'password' && !empty($value)) {
            $encryptedPassword = rrt_encrypt_password($value);
            $data = ['password' => $encryptedPassword, 'id' => $userID];
            $result = $this->model->saveItem($data, ['task' => 'edit-item']);
        }
        // Handle phone change
        else if ($name == 'phone') {
            $checkUser = $this->model->where('phone', $value)->where('id', '!=', $userID)->first();
            if ($checkUser) {
                $error = 1;
                $msg = "Phone number is already used by another account";
            } else {
                $data = ['phone' => $value, 'id' => $userID];
                if(isset($params['country_code'])) {
                    $data['country_code'] = $params['country_code'];
                }
                $result = $this->model->saveItem($data, ['task' => 'edit-item']);
            }
        }
        // Handle other fields
        else {
            // Special handling for discography field
            if ($name == 'discography') {
                try {
                    // Convert JSON to comma-separated text
                    $links = json_decode($value, true);
                    if (is_array($links) && !empty($links)) {
                        $linkTexts = [];
                        foreach ($links as $link) {
                            if (!empty($link['title']) && !empty($link['url'])) {
                                $linkTexts[] = $link['title'] . ' - ' . $link['url'];
                            }
                        }
                        $value = implode(', ', $linkTexts);
                    }
                } catch (\Exception $e) {
                    // If JSON parsing fails, keep the original value
                }
                
                $data = [$name => $value, 'id' => $userID];
                $result = $this->model->saveItem($data, ['task' => 'edit-item']);
            }
            // Handle other non-unique fields without uniqueness check
            else if (in_array($name, ['work_history', 'accomplishments', 'youtube_link'])) {
                $data = [$name => $value, 'id' => $userID];
                $result = $this->model->saveItem($data, ['task' => 'edit-item']);
            }
            // Default handling with uniqueness check
            else {
                $checkUser = $this->model->where($name, $value)->where('id', '!=', $userID)->first();
                if ($checkUser) {
                    $error = 1;
                    $msg = "{$name} is already taken";
                } else {
                    $data = [$name => $value, 'id' => $userID];
                    $result = $this->model->saveItem($data, ['task' => 'edit-item']);
                }
            }
        }

        $params['error'] = $error;
        $params['msg'] = $msg;
        return $params;
    }
    public function social(Request $request)
    {
        $userID = rrt_get_user_login('id');
        $params['id'] = $userID;
        $user = UserModel::where('id', $userID)->with('socialmedia')->first();
        $result = [];
        foreach ($user->socialmedia as $key => $item) {
            $result[$item->name] = $item->link;
        }
        return view(
            "{$this->pathViewController}/social",
            [
                'user' => $user,
                'result' => $result
            ]
        );
        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function postSocial(Request $request)
    {
        $params = $request->all();

        $session_studio = rrt_get_user_login();
        $params['id'] = $session_studio['id'];
        $SocialMediaModel = SocialMediaModel::where('user_id', $session_studio['id'])->get()->pluck('name')->toArray();

        foreach ($params as $key => $value) {
            if (in_array($key, $SocialMediaModel)) {
                SocialMediaModel::where('user_id', $session_studio['id'])->where('name', $key)->update(['link' => $value]);
            } else {
                SocialMediaModel::create([
                    'user_id' =>  $session_studio['id'],
                    'link' => $value,
                    'name' => $key
                ]);
            }
        }


        return redirect()->back();
    }
    public function subscription(Request $request)
    {
        $userId = rrt_get_user_login('id');
        $user = UserModel::where('id', $userId)->first();
        $joinType = $user['join_type'] ?? "";

        $proSellerPlan = PlanModel::find(3) ?? [];
        if($proSellerPlan){
            $proSellerPlan = $proSellerPlan->toArray();
        }
        $subscriptionPlan = SubscriptionModel::orderBy('id', 'DESC')->get();
        if(count($subscriptionPlan)){
            $subscriptionPlan = $subscriptionPlan->toArray();
        }
        $plans = array_merge($subscriptionPlan, [$proSellerPlan]);
        $planOrder = $user->planOrders()->where('status', 'active')->orderBy('id', 'desc')->get();
        $subscriptionOrder = $user->subscriptionOrders()->where('status', 'active')->orderBy('id', 'desc')->get();
        if ($joinType == 'pro_seller' && count($planOrder)) {
            $subscriptionArrayRender = [];
            foreach($plans as $plan){
                if($plan['slug'] == "basic"){
                    continue;
                }
                $planObj = SubscriptionModel::find($plan['id']);
                $is_active = $subscriptionOrder->where('subscription_id', $plan['id'])->first() ?? null;
                if($plan['slug'] == "pro"){
                    $is_active = $planOrder->where('plan_id', $plan['id'])->first();
                    $planObj = PlanModel::find($plan['id']);
                }
                $subscriptionArrayRender[] = (object) [
                    'subscription' => $planObj,
                    'orderItem' => $is_active,
                    'active' => $is_active ? 'active': 'not_active',
                ] ;
            }
        } else {
            $subscriptionArrayRender = [];
            foreach($plans as $subscription){
                $is_active = null;
                $subscriptionObj = PlanModel::find($subscription['id']);
                $is_active = $planOrder->where('plan_id', $subscription['id'])->first() ?? null;
                if ($subscription['slug'] !== "pro") {
                    $is_active = $subscriptionOrder->where('subscription_id', $subscription['id'])->first();
                    $subscriptionObj = SubscriptionModel::find($subscription['id']);
                }
                $subscriptionArrayRender[] = (object) [
                    'subscription' => $subscriptionObj,
                    'orderItem' => $is_active,
                    'active' => $is_active ? 'active': 'not_active',
                ] ;
            }
        }
        return view(
            "{$this->pathViewController}/subscription",
            [
                'subscriptionArrayRender' => $subscriptionArrayRender
            ]
        );
    }
    public function payment(Request $request)
    {
        return view(
            "{$this->pathViewController}/payment",
            []
        );
    }
    public function addresses(Request $request)
    {
        return view(
            "{$this->pathViewController}/addresses",
            []
        );
    }
    public function languages(Request $request)
    {
        return view(
            "{$this->pathViewController}/languages",
            []
        );
    }
}
