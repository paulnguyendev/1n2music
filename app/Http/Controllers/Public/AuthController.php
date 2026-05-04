<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Subscription;
use App\Http\Controllers\Controller;
#Model
use App\Models\UserModel as MainModel;
use App\Models\PlanOrderModel;
use App\Models\PlanModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterUserMail;
use App\Mail\SendForgotMail;
use App\Models\CountryModel;
use App\Models\SubscriptionModel;
use App\Models\SubscriptionOrderModel;
use App\Models\User;
use App\Models\UserModel;
use Illuminate\Support\Facades\Session as FacadesSession;
use Laravel\Socialite\Facades\Socialite;

#Helper
class AuthController extends Controller
{
    private $pathViewController     = "public2.pages.auth";
    private $controllerName         = "public/auth";
    private $model;
    private $planModel;
    private $subscriptionModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        $this->planModel = new PlanModel();
        $this->subscriptionModel = new SubscriptionModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        return view(
            "{$this->pathViewController}/index",
            []
        );
    }
    public function checkEmail(Request $request)
    {
        $params = $request->all();
        $account = $params['account'] ?? "";
        $user = $this->model->getItem(['account' => $account], ['task' => 'account']);
        $error = [];
        $redirect = $user ? rrt_route($this->controllerName . "/signin") : rrt_route($this->controllerName . "/signup");
        $email = $user && isset($user['email']) ? $user['email'] : $account;
        $request->session()->put('email', $email);
        $result = [
            'status' => 200,
            'user' => $user,
            'redirect' => $redirect,
        ];
        return $result;
    }
    public function signUp(Request $request)
    {
        $email = $request->session()->get('email', '');
        $startSelling = $request->start_selling??false;
        return view(
            "{$this->pathViewController}/signup",
            [
                'email' => $email,
                'startSelling'=>$startSelling
            ]
        );
    }
    public function google(Request $request){
        return Socialite::driver('google')->redirect();
    }
    public function googleCallback(Request $request){
        $user = Socialite::driver('google')->stateless()->user();
        $email = $user->getEmail()??null;
        $info = $user->user??[];
        $firstName = $info['family_name']??'';
        $lastName = $info['given_name']??'';
        if(!$email){
            return redirect(rrt_route('public/auth/signIn'));
        }

        $existingUser = $this->model->getItem(['email' => $email], ['task' => 'email']);
        if($existingUser){
            $userStatus = $existingUser['status'] ?? "pending";
            if ($userStatus == 'pending') {
                return redirect(rrt_route('public/auth/verifyCode',['token'=>$existingUser->token??'']));
            }
            if ($userStatus == 'suspend') {
                return redirect(rrt_route('public/auth/signIn'));
            }
            $pendingOrders = SubscriptionOrderModel::where('user_id', $existingUser->id??'')
                ->where('status', 'pending')
                ->with('subscription')
                ->get();
            $pendingPlans = PlanOrderModel::where('user_id',$existingUser->id??'')
                ->where('status','pending')
                ->with('plan')
                ->get();
            if (!$pendingOrders->isEmpty()  && !$pendingPlans->isEmpty()) {
                return redirect(rrt_route( "public/checkout/index",['user_id'=>$existingUser->id??'']));
            }
            $request->session()->put('info_studio', $existingUser);
            return redirect(rrt_route("public/studio/home/index"));
        }
        $validateCode = $this->model->randomCode();
        $token = md5($email . time());
        $params = [
            'email'=>$email,
            'parent_id'=>"",
            'first_name'=>$firstName,
            'last_name'=>$lastName,
            'username'=>rrt_get_username_from_email($email),
            'status'=>'active',
            'token'=>$token,
            'created_at'=>date('Y-m-d H:i:s'),
            'validate_code'=>$validateCode,
            'basic_signup'=>1,
            'role'=>'user',
            'password'=>rrt_encrypt_password(time()),
        ];
        $userID = $this->model->saveItem($params, ['task' => 'add-item']);
        return redirect(rrt_route('public/auth/ssoStartSelling',['token'=>$token]));
    }
    public function ssoStartSelling(Request $request){
        $token = $request->token??'';
        if (!$token){
            return redirect(rrt_route('public/auth/login'));
        }
        $user = $this->model->getItem(['token' => $token], ['task' => 'token']);
        return view("{$this->pathViewController}/ssoStartSelling",[
            'user'=>$user,
            'token'=>$token
        ]);
    }
    public function loginToken(Request $request){
        $token = $request->token??'';
        if (!$token){
            return redirect(rrt_route('public/auth/signIn'));
        }
        $user = $this->model->getItem(['token' => $token], ['task' => 'token']);
        if(!$user){
            return  redirect(rrt_route('public/auth/signIn'));
        }
        $isStartSelling = $request->start_selling??0;
        if ($isStartSelling == 1){
            return redirect(rrt_route('public/auth/startSelling', ['user_id' => $user->id]));
        }
        $userRole = rrt_get_user_role($user);
        $request->session()->put('info_studio', $user);
        if (in_array('free-user',$userRole)){
            $request->session()->put('info_studio', $user);
            return redirect(rrt_route("public/studio/home/index"));
        }
        return redirect(rrt_route("public/auth/updateInfo",['token'=>$token]));
    }
    public function signIn(Request $request)
    {
        $email = $request->session()->get('email', '');
        return view(
            "{$this->pathViewController}/signin",
            [
                'email' => $email,
            ]
        );
    }
    public function verifyCode(Request $request)
    {
        $token = $request->token ?? '';
        $user = $this->model->getItem(['token' => $token], ['task' => 'token']);
        if (!$user) {
            return redirect()->route($this->controllerName . "/signUp", ['locale' => rrt_get_locale()]);
        }
        $addSubScriptionAiCount = rrt_add_subscription_ai_usage_count($user->id);
        $email = $user->email ?? '';
        $userStatus = $user->status??'pending';
        if($userStatus == 'active'){ // login sso

            $userRole = rrt_get_user_role($user);
            if (in_array('free-user',$userRole)){
                $request->session()->put('info_studio', $user);
                return redirect(rrt_route("public/studio/home/index"));
            }

            return redirect(rrt_route("public/auth/updateInfo",['token'=>$token]));
        }
        return view(
            "{$this->pathViewController}/verifyCode",
            [
                'email' => $email,
                'token' => $token,
            ]
        );
    }
    public function postSignIn(Request $request)
    {
        $params = $request->all();
        
        
        $error = [];
        $email = $params['email'] ?? "";
        $password = $params['password'] ?? "";
        $password = rrt_encrypt_password($password);
        $user = $this->model->getItem(['email' => $email], ['task' => 'email']);

        $status = null;
        $msg = null;
        $userPass = null;
        $userStatus = null;
        $redirect = null;
        if (!$user) {
            $error['account'] = "Email does not exist";
        } else {
            $userPass = $user['password'] ?? "";
            if ($password != $userPass) {
                $error['password'] = "Incorrect password";
            }
            $userStatus = $user['status'] ?? "pending";
            if ($userStatus == 'pending') {
                $error['status'] = "Your account is not activate";
                $redirect = rrt_route('public/auth/verifyCode',['token'=>$user->token??'']);
            }
            if ($userStatus == 'suspend') {
                $error['status'] = "Your account is suspended";
            }
        }
        if (empty($error)) {
            $status = 200;
            $msg = "Logged in successfully";
            #_Assign session login
            $request->session()->put('info_studio', $user);
            #_Redirect to studio board
            $redirect = rrt_route("public/studio/home/index");
        } else {
            $status = 400;
            $msg = $error;
        }

        #_ isset pending order is first payment. redirect checkout
        $pendingOrders = SubscriptionOrderModel::where('user_id', $user->id??'')
            ->where('status', 'pending')
            ->with('subscription')
            ->get();
        $pendingPlans = PlanOrderModel::where('user_id',$user->id??'')
            ->where('status','pending')
            ->with('plan')
            ->get();
        $cookiReminder = Cookie::get('first_payment_reminder');
        if ((!$pendingPlans->isEmpty() || !$pendingOrders->isEmpty()) && isset($cookiReminder)) {
            #_ Create again cookie first payment
            Cookie::queue('first_payment_reminder', "true", 60 * 24 * 30);
            $status = 200;
            $redirect = rrt_route( "public/checkout/index",['user_id'=>$user->id??'']);
            $msg = __("You has order pending. Please complete!");
            $order_old = isset($pendingOrder) && !empty($pendingOrder) ? true : false;
            FacadesSession::flash('order_old',$order_old);
        }
        return [
            'params' => $params,
            'status' => $status,
            'msg' => $msg,
            'redirect' => $redirect,
        ];
    }
    public function postSignup(Request $request)
    {
        $params = $request->all();
        $recaptchaResponse = $request->input('recaptchaResponse');
        $secretKey = config('services.recaptcha.secret_key');
        
        // $client = new Client();
        // $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
        //     'form_params' => [
        //         'secret' => $secretKey,
        //         'response' => $recaptchaResponse,
        //     ],
        // ]);
        $error = [];
        // $responseBody = json_decode((string) $response->getBody());
        // if (!$responseBody->success) {
        //     $error['recaptcha'] = 'Captcha verification failed';
        // }
        $email = $params['email'] ?? "";
        $password = $params['password'] ?? "";
        $joinTypes = $params['joinTypes'] ?? [];
        $startSelling = $params['startSelling'] ?? 0;
        $password = rrt_encrypt_password($password);
        $user = $this->model->getItem(['email' => $email], ['task' => 'email']);
        
        $status = null;
        $msg = null;
        $userID = null;
        $validateCode = null;
        $redirect = null;
        if ($user) {
            $error['account'] = "Email already exists";
        }

        if (empty($error)) {
            $status = 200;
            $msg = "Successful account registration";

            #_Save User to Db
            $params['password'] = $password;
            $params['username'] = rrt_get_username_from_email($email);
            $params['parent_id'] = "";
            $params['status'] = "pending";
            $token = md5($email . time());
            $params['token'] = $token;
            $params['created_at'] = date('Y-m-d H:i:s');
            $validateCode = $this->model->randomCode();
            $params['validate_code'] = $validateCode;
            $params['basic_signup'] = 1;
            $params['role'] = in_array("pro_seller", $joinTypes) ? 'seller' : 'user';
            $userID = $this->model->saveItem($params, ['task' => 'add-item']);
            #_Add order if joinTypes exists
//            if ($joinTypes) {
//                foreach ($joinTypes as $joinType) {
//                    $orderInfo = Subscription::addOrder([
//                        'join_type' => $joinType,
//                        'item_id' => $joinType == 'pro_seller' ? $this->planModel->getItem([], ['task' => 'default_signup'])['id'] : $this->subscriptionModel->getItem(['slug' => $joinType], ['task' => 'slug'])['id'],
//                        'user_id' => $userID,
//                        'price' => $joinType == 'pro_seller' ? $this->planModel->getItem([], ['task' => 'default_signup'])['pricing_annually'] : $this->subscriptionModel->getItem(['slug' => $joinType], ['task' => 'slug'])['price'],
//                    ]);
//                    $redirect = rrt_route('public/checkout/index', ['user_id'=>$userID]);
//                }
//            } else
            if ($startSelling == "1") {
                $redirect = rrt_route('public/auth/startSelling', ['user_id' => $userID]);
            }
            else {
                #_Redirect to verify
                $redirect = rrt_route($this->controllerName . "/verifyCode", ['token' => $token]);
            }

            #_Send mail
            try {
                $params['mail'] = 'Sended';
                Mail::to($email)->send(new RegisterUserMail($params));
            } catch (\Throwable $th) {
                $params['mail'] = 'Not send';
            }
        } else {
            $status = 400;
            $msg = $error;
        }

        return [
            'params' => $params,
            'status' => $status,
            'msg' => $msg,
            'user' => $user,
            'redirect' => $redirect,
        ];
    }
    public function resendEmail(Request $request)
    {
        $token = $request->auth_token??"";
        $user = UserModel::where('token', $token)->first();
        if(!$user){
            return response()->json([
                'status' => 400,
                'msg' => __('Invalid token or user not found.'),
            ]);
        }
        $validateCode = $this->model->randomCode();
        $user->validate_code = $validateCode;
        $user->save();
        $params = [];
        $params['validate_code'] = $validateCode;
        $email = $user->email??"";
        if (!$email) {
            return response()->json([
                'status' => 400,
                'msg' => __('Invalid email or user not found.'),
            ]);
        }
        try {
            $params['mail'] = 'Sended';
            Mail::to($email)->send(new RegisterUserMail($params));
            return response()->json([
                'status' => 200,
                'msg' => __('Verification email has been resent successfully.'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'msg' => __('Failed to resend verification email. Please try again later.'),
                'error' => $th->getMessage(),
            ]);
        }
    }
    public function startSelling(Request $request){
        $userId = $request->user_id ?? '';
        if (!$userId){
            return redirect(rrt_route('public/home/index'));
        }
        $proSellerPlan = $this->planModel->find(3) ?? [];
        $subscriptionPlan = $this->subscriptionModel->orderBy('id', 'ASC')
            ->get()
            ->toArray();
        $plans = array_merge([$proSellerPlan], $subscriptionPlan);
        $sortPlans = array_replace([1 => $plans[3], 2 => $plans[0], 3 => $plans[2], 4 => $plans[1]]);
        $user = UserModel::find($userId);
        return view(
            "{$this->pathViewController}/startSelling",
            [
                'user_id' => $userId,
                'user'=>$user,
                'plans'=>$sortPlans
            ]
        );
    }
    public function postStartSelling(Request $request){
        $userId = $request->input('user_id') ?? "";
        $plans = $request->input('plans') ?? [];
        if (empty($userId) || empty($plans)) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }
        $data = [];
        $existingOrder = PlanOrderModel::where('user_id', $userId)
            ->where('status', 'pending')
            ->delete();
        $existingSubscriptionOrder = SubscriptionOrderModel::where('user_id', $userId)
        ->where('status', 'pending')
        ->delete();
        foreach ($plans as $planData) {
            $planSlug = $planData['plan'] ?? "";
            $cycle = $planData['cycle'] ?? "annually";
            if ($planSlug == 'pro') {
                $planDetails = $this->planModel->where('default_signup', 1)->first();
                if ($planDetails) {
                    $price = ($cycle === 'monthly') ? ($planDetails->pricing_monthly ?? 0) : ($planDetails->pricing_annually ?? 0);
                    $orderInfo = Subscription::addOrder([
                        'join_type' => 'pro_seller',
                        'item_id' => $planDetails->id,
                        'user_id' => $userId,
                        'price' => $price,
                        'cycle'=>$cycle
                    ]);
                }
            } else {
                $subscriptionDetails = $this->subscriptionModel->where('slug', $planSlug)->first();
                if ($subscriptionDetails) {
                    $price = ($cycle === 'monthly') ? ($subscriptionDetails->price ?? 0) : ($subscriptionDetails->pricing_annually ??0);
                    $orderInfo = Subscription::addOrder([
                        'join_type' => $planSlug,
                        'item_id' => $subscriptionDetails->id,
                        'user_id' => $userId,
                        'price' => $price,
                        'cycle'=>$cycle
                    ]);
                }
            }
        }
        #_ Create cookie first payment
        Cookie::queue('first_payment_reminder', 'false', 60 * 24 * 30);
        $redirect = rrt_route('public/checkout/index', ['user_id'=>$userId]);
        return response()->json(['redirect' => $redirect]);
    }
    public function postVerifyCode(Request $request)
    {
        $params = $request->all();
        $paramsUpdate = [];
        $token = $request->token ?? '';
        $email = $request->session()->get('email', '');
        $error = [];
        $status = null;
        $msg = null;
        $validateCode = null;
        $user = $this->model->getItem(['token' => $token], ['task' => 'token']);
        $validateCodeOfUser = $user['validate_code'] ?? "";
        $redirect = null;
        $validateCode = $params['validate_code'] ?? '';
        if (!$validateCode) {
            $error['validate'] = "Please enter your validate code";
        } else {
            if ($validateCode != $validateCodeOfUser) {
                $error['validate'] = "The verification  code is not correct";
            }
        }
        if (empty($error)) {
            $status = 200;
            $msg = "Successful verify";
            #_Update status
            $id = $user['id'];
            $paramsUpdate['id'] = $id;
            $paramsUpdate['status'] = "active";
            $paramsUpdate['updated_at'] = date('Y-m-d H:i:s');
            $this->model->saveItem($paramsUpdate, ['task' => 'edit-item']);
            #_Assign session login
            $request->session()->put('info_studio', $user);
            #_Redirect to studio board
            $userRole = rrt_get_user_role($user);

            if (in_array('free-user',$userRole)){
                $redirect = rrt_route("public/studio/home/index");
            }
            else{
                $redirect = rrt_route("public/auth/updateInfo",['token'=>$token]);
            }
        } else {
            $status = 400;
            $msg = $error;
        }
        return [
            'params' => $params,
            'status' => $status,
            'msg' => $msg,
            'redirect' => $redirect,
            'validateCode' => $validateCode,
            'email' => $email,
            'validateCodeOfUser' => $validateCodeOfUser,
        ];
    }
    public function updateInfo(Request $request){
        $token = $request->token ?? '';
        $user = $this->model->getItem(['token' => $token], ['task' => 'token']);
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
            "ZAMP" => "ZAMP"
        );
        $countries = CountryModel::all();
        if (!$user) {
            return redirect()->route($this->controllerName . "/signUp", ['locale' => rrt_get_locale()]);
        }
        return view(
            $this->pathViewController.'/updateInfo',
            [
                'user'=>$user,
                'token'=>$token,
                'dataPro'=>$dataPro,
                'countries' => $countries,
            ]
        );
    }
    public function postUpdateInfo(Request $request){
        $rules = [
            'email' => 'required|email|exists:rrt_users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:15',
            'tax_type' => 'required|in:1,2',
            'payment_method' => 'required|in:paypal,bank',
        ];
        if ($request->payment_method === 'bank') {
            $rules = array_merge($rules, [
                'bank_name' => 'required|string|max:255',
                'bank_owner' => 'required|string|max:255',
                'bank_number' => 'required|string|max:50',
            ]);
        }
        $messages = [
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.exists' => __('The provided email does not exist in our records.'),
            'first_name.required' => __('The first name field is required.'),
            'first_name.string' => __('The first name must be a string.'),
            'first_name.max' => __('The first name may not be greater than 255 characters.'),
            'last_name.required' => __('The last name field is required.'),
            'last_name.string' => __('The last name must be a string.'),
            'last_name.max' => __('The last name may not be greater than 255 characters.'),
            'dob.required' => __('The date of birth field is required.'),
            'dob.date' => __('The date of birth must be a valid date.'),
            'country_code.required' => __('The country code field is required.'),
            'country_code.max' => __('The country code may not be greater than 5 characters.'),
            'phone.required' => __('The phone number field is required.'),
            'phone.string' => __('The phone number must be a string.'),
            'phone.max' => __('The phone number may not be greater than 15 characters.'),
            'tax_type.required' => __('The tax type field is required.'),
            'tax_type.in' => __('The selected tax type is invalid.'),
            'payment_method.required' => __('The payment method field is required.'),
            'payment_method.in' => __('The selected payment method is invalid.'),
            'bank_name.required' => __('The bank name field is required.'),
            'bank_name.string' => __('The bank name must be a string.'),
            'bank_name.max' => __('The bank name may not be greater than 255 characters.'),
            'bank_owner.required' => __('The bank owner field is required.'),
            'bank_owner.string' => __('The bank owner must be a string.'),
            'bank_owner.max' => __('The bank owner may not be greater than 255 characters.'),
            'bank_number.required' => __('The bank number field is required.'),
            'bank_number.string' => __('The bank number must be a string.'),
            'bank_number.max' => __('The bank number may not be greater than 50 characters.'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'msg' => $validator->errors(),
            ]);
        }
        $param = $request->all();
        $firstName = $param['first_name'] ?? '';
        $lastName = $param['last_name'] ?? '';
        $DOB = $param['dob'] ?? null;
        $country_code = $param['country_code'] ??'';
        if(isset($param['phone'])){
            $phone = $param['phone'] ??'';
            $phone = preg_replace('/\D/', '', $phone);
        }
        $address = $param['address'] ?? '';
        $zip_code = $param['zip_code'] ?? '';
        $city = $param['city'] ?? '';
        $country = $param['country'] ?? '';
        $pro = $param['pro'] ??'';
        $tax_type = $param['tax_type'] ?? 1;
        $paymentMethod = $param['payment_method'] ?? "paypal";
        $bankName = $param['bank_name'] ?? '';
        $bankOwner = $param['bank_owner'] ?? '';
        $bankNumber = $param['bank_number'] ?? '';
        $token = $param['token'] ?? '';
        DB::beginTransaction();
        try {
            $redirect = rrt_route("public/studio/home/index");
            $user = $this->model->getItem(['token' => $token], ['task' => 'token']);
            if(!$user){
                $redirect = route($this->controllerName . "/signUp", ['locale' => rrt_get_locale()]);
                return response()->json([
                    'status' => 200,
                    'msg' => "Invalid Token",
                    'redirect' => $redirect,
                ]);
            }
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->fullname = $firstName.' '.$lastName;
            $user->country_code = $country_code;
            $user->phone = $phone;
            $user->address = $address;
            $user->zip_code = $zip_code;
            $user->city = $city;
            $user->country = $country;
            $user->date_of_birth = $DOB;
            $user->main_payment_method = $paymentMethod;
            $user->pro = $pro;
            $user->tax_type = $tax_type;
            if ($paymentMethod === 'bank') {
                $user->bank_name = $bankName;
                $user->bank_owner = $bankOwner;
                $user->bank_number =$bankNumber;
            }
            $user->save();
            $request->session()->put('info_studio', $user);
            DB::commit();
            return response()->json([
                'status' => 200,
                'msg' => __("Successfully updated user information."),
                'redirect' => $redirect,
            ]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 400,
                'msg' => __('Failed to update user information') .": ". $e->getMessage(),
            ]);
        }
    }
    public function postRegister(Request $request)
    {
        $params = $request->all();
        $page = $params['page'] ?? "";
        $email = $params['email'] ?? "";
        $phone = $params['phone'] ?? "";
        $identification = $params['identification'] ?? "";
        $password = $params['password'] ?? "";
        $password = rrt_encrypt_password($password);
        $checkEmail = $this->model->getItem(['email' => $email], ['task' => 'email']);
        $checkPhone = $this->model->getItem(['phone' => $phone], ['task' => 'phone']);
        $checkIdentification = $this->model->getItem(['identification' => $identification], ['task' => 'identification']);
        $error = [];
        $status = null;
        $msg = null;
        $redirect = null;
        $addUser = [];
        $userId = null;
        $paramsPlanOrder = null;
        $planOrderModel = null;
        $planModel = null;
        $planId = null;
        $totalPlanOrder = 0;
        $planPrice = 0;
        $planPriceMonthly = 0;
        $planPriceAnnually = 0;
        $planCycle = null;
        $paramsSupcriptionOrder = [];
        $subscriptionModel = null;
        $subscriptionPrice = 0;
        $subscriptionOrderModel = null;
        if ($checkEmail) {
            $error['account'] = "Email already exists";
        } elseif ($checkPhone) {
            $error['phone'] = "Phone number already exists";
        } elseif ($checkIdentification) {
            $error['identification'] = "Identification already exists";
        }
        if (empty($error)) {
            $status = 200;
            $msg = "Successful verify";
            $params['password'] = $password;
            $params['username'] = rrt_get_username_from_email($email);
            $params['parent_id'] = "";
            $params['status'] = "pending";
            $token = md5($email . time());
            $params['token'] = $token;
            if ($page == 'sellBeats') {
                $params['role'] = 'seller';
            } else {
                $params['role'] = 'user';
            }
            $params['created_at'] = date('Y-m-d H:i:s');
            $validateCode = $this->model->randomCode();
            $params['validate_code'] = $validateCode;
            $request->session()->put('email', $email);
            $addUser = $this->model->saveItem($params, ['task' => 'add-item']);
            $userId = $addUser['id'] ?? "";
            if ($userId) {
                if ($page == 'sellBeats') {
                    $planOrderModel = new PlanOrderModel();
                    $planModel = new PlanModel();
                    $paramsPlanOrder = $params['plan_order'] ?? [];
                    $planId = $paramsPlanOrder['plan_id'] ?? "";
                    $planCycle = $paramsPlanOrder['cycle'] ?? "anually";
                    $itemPlan = $planModel->getItem(['id' => $planId], ['task' => 'id']);
                    $planPriceMonthly = $itemPlan['pricing_monthly'] ?? 0;
                    $planPriceAnnually = $itemPlan['pricing_annually'] ?? 0;
                    $planPrice = $planCycle == 'monthly' ? $planPriceMonthly : $planPriceAnnually;
                    $totalPlanOrder = $planCycle == 'monthly' ? $planPrice : $planPrice * 12;
                    $paramsPlanOrder['status'] = 'pending';
                    $paramsPlanOrder['user_id'] = $userId;
                    $paramsPlanOrder['created_at'] = date('Y-m-d H:i:s');
                    $paramsPlanOrder['total'] = $totalPlanOrder;
                    $planOrderModel->saveItem($paramsPlanOrder, ['task' => 'add-item']);
                } elseif ($page == 'subscription') {
                    $subscriptionModel = new SubscriptionModel();
                    $subscriptionOrderModel = new SubscriptionOrderModel();
                    $paramsSupcriptionOrder = $params['subscription_order'] ?? [];
                    $subscriptionId = $paramsSupcriptionOrder['id'] ?? "";
                    $itemSubscription = $subscriptionModel->getItem(['id' => $subscriptionId], ['task' => 'id']);
                    $subscriptionPrice = $itemSubscription['price'] ?? 0;
                    $paramsSupcriptionOrder['status'] = 'pending';
                    $paramsSupcriptionOrder['user_id'] = $userId;
                    $paramsSupcriptionOrder['created_at'] = date('Y-m-d H:i:s');
                    $paramsSupcriptionOrder['total'] = $subscriptionPrice;
                    $subscriptionOrderModel->saveItem($paramsSupcriptionOrder, ['task' => 'add-item']);
                }
            }
            #_Send mail
            Mail::to($email)->send(new RegisterUserMail($params));
            #_Redirect to verify
            $redirect = rrt_route($this->controllerName . "/verifyCode");
        } else {
            $status = 400;
            $msg = $error;
        }
        return [
            'params' => $params,
            'status' => $status,
            'msg' => $msg,
            'addUser' => $addUser,
            'redirect' => $redirect,
        ];
    }
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect(rrt_route($this->controllerName . "/signIn"));
    }
    public function forgotPassword(Request $request) {
        return view(
            "{$this->pathViewController}/forgot-password",
           
        );
    }
    public function postForgotPassword(Request $request) 
    {
        // dd($request->all());
        $user = UserModel::where('email', $request->email)->first();
        if (empty($user)) {
            return response()->json(['status' => 500]);
        } else {
            //   dd($user->token);
            if (!$user->token) {
                return response()->json(['status' => 500]);
            }
            $params['token'] = $user->token;
            //  $user->update(['validate_code' => $params['code']]);
            $url_forgot = rrt_route($this->controllerName . '/verifiedForgot', ['token' =>  $params['token']]);
            $params['subject'] = 'Forgot Password';
            $params['email'] = $user->email;
            $params['url'] = $url_forgot;
            $sendmail =   Mail::to($user->email)->send(new SendForgotMail($params));
            if ($sendmail) {
                return response()->json(['status' => 200,'redirect' => rrt_route($this->controllerName . '/signIn')]);
            } else
                return response()->json(['status' => 500]);
        }
    }
    public function verifiedForgot(Request $request)
    {
        $token = $request->token  ?? '';
        $params['token'] =  $token;
        $options['task'] = 'token';
        $result =  $this->model->getItem($params, $options);
        if (!$result) {
            return abort(404);
        }
        return view(
            "{$this->pathViewController}/reset-password",
            [
                'email' => $token,
            ]
        );
    }
    public function postNewPassword(Request $request)
    {
        // dd($request->all());
        $result = $this->model->forgotPassword($request->token, $request->password);
        if ($result) {
            $status = 200;
            $msg = 'Password has change successfully';
            $redirect = rrt_route($this->controllerName . '/signIn');
        } else {
            $status = 500;
            $msg = 'Password change failed';
            $redirect = '';
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'redirect' => $redirect]);
    }

}
