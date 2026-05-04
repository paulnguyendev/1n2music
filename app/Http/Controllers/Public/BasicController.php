<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Subscription;
use App\Http\Controllers\Controller;
#Model
use App\Models\UserModel as MainModel;
use App\Models\PlanOrderModel;
use App\Models\PlanModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterUserMail;
use App\Mail\SendForgotMail;
use App\Models\SubscriptionModel;
use App\Models\SubscriptionOrderModel;
use App\Models\User;
use App\Models\UserModel;

#Helper
class BasicController extends Controller
{
    private $pathViewController     = "public.pages.join.basic";
    private $controllerName         = "public/join/basic";
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
    public function signup(Request $request)
    {
        $email = $request->session()->get('email', '');
        return view(
            "{$this->pathViewController}/signup",
            [
                'email' => $email,
            ]
        );
    }
    public function signin(Request $request)
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
        $email = $request->session()->get('email', '');
        return view(
            "{$this->pathViewController}/verifyCode",
            [
                'email' => $email,
            ]
        );
    }
    public function postSignin(Request $request)
    {
        $params = $request->all();
        $email = $params['email'] ?? "";
        $password = $params['password'] ?? "";
        $password = rrt_encrypt_password($password);
        $user = $this->model->getItem(['email' => $email], ['task' => 'email']);
        $error = [];
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
                $error['status'] = "Your account is not verified";
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
        $email = $params['email'] ?? "";
        $password = $params['password'] ?? "";
        $joinType = $params['join_type'] ?? "";
        $password = rrt_encrypt_password($password);
        $user = $this->model->getItem(['email' => $email], ['task' => 'email']);
        $error = [];
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
            #_Save Db
            $params['password'] = $password;
            $params['username'] = rrt_get_username_from_email($email);
            $params['parent_id'] = "";
            $params['status'] = "pending";
            $token = md5($email . time());
            $params['token'] = $token;
            $params['role'] = $joinType == 'pro_seller' ? 'seller' : 'user';
            $params['created_at'] = date('Y-m-d H:i:s');
            $validateCode = $this->model->randomCode();
            $params['validate_code'] = $validateCode;
            $params['basic_signup'] = 1;
            $userID = $this->model->saveItem($params, ['task' => 'add-item']);
            #_Send mail
            Mail::to($email)->send(new RegisterUserMail($params));
            #_Add order
            $params['user_id'] = $userID;
            $orderItem = $joinType == 'pro_seller' ?   $this->planModel->getItem([], ['task' => 'default_signup']) : $this->subscriptionModel->getItem(['slug' => $joinType], ['task' => 'slug']);
            $orderID = $orderItem['id'] ?? "";
            $params['item_id'] = $orderID;
            $params['orderItem'] = $orderItem;
            $orderPricePlan = $orderItem['pricing_annually'] ?? 0;
            $orderPriceSup = $orderItem['price'] ?? 0;
            $params['price'] = $joinType == 'pro_seller' ? $orderPricePlan : $orderPriceSup;
            $orderInfo = Subscription::addOrder($params);
            $params['orderInfo'] = $orderInfo;
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
            'user' => $user,
            'redirect' => $redirect,
        ];
    }
    public function postVerifyCode(Request $request)
    {
        $params = $request->all();
        $paramsUpdate = [];
        $email = $request->session()->get('email', '');
        $error = [];
        $status = null;
        $msg = null;
        $validateCode = null;
        $user = $this->model->getItem(['email' => $email], ['task' => 'email']);
        $validateCodeOfUser = $user['validate_code'] ?? "";
        $redirect = null;
        foreach ($params as $item) {
            $validateCode .= $item;
        }
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
            $redirect = rrt_route("public/studio/home/index");
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
        return redirect(rrt_route($this->controllerName . "/index"));
    }

    public function forgot(Request $request)
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
                return response()->json(['status' => 200]);
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
            "{$this->pathViewController}/forgot-password",
            [
                'email' => $token,
            ]
        );
    }


    public function postForgot(Request $request)
    {
        // dd($request->all());
        $result = $this->model->forgotPassword($request->token, $request->password);
        if ($result) {
            $status = 200;
            $msg = 'Password has change successfully';
            $redirect = rrt_route($this->controllerName . '/signin');
        } else {
            $status = 500;
            $msg = 'Password change failed';
            $redirect = '';
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'redirect' => $redirect]);
    }
}
