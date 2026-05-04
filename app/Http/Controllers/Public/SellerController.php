<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
#Model
use App\Models\UserModel;
use App\Models\SubscriptionModel as MainModel;
use App\Models\SubscriptionOrderModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\PurchasedModel;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
#Helper
use App\Helpers\Subscription;
use App\Models\SubscriptionModel;
use Illuminate\Support\Facades\Session;

class SellerController extends Controller
{
    private $pathViewController     = "public2.pages.seller";
    private $controllerName         = "public/join/seller";
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
        $user = rrt_get_user_login();
        $userObj = UserModel::findOrfail($user['id']);
        $proSellerPlan = $this->planModel->find(3) ?? [];
        $subscriptionPlan = $this->model->orderBy('id', 'DESC')
            ->first()
            ->toArray();
        $plans = array_merge([$subscriptionPlan], [$proSellerPlan]);

        $orderPlan = $userObj->planOrders()->first();
        if (!empty($orderPlan)) {
            if ($orderPlan->plan_id == 3 && $orderPlan->status == 'active') {
                // Pro Seller
                return view(
                    "{$this->pathViewController}/index",[
                        'user_id' => $user['id'],
                        'plans' => '',
                    ]
                );
            }
        }
        $orderSub = $userObj->subscriptionOrders()->where('status', 'active')->pluck('subscription_id');
        if (!empty($orderSub)) {
            if ($orderSub->contains(3)) {
                // Basic Seller
                $plans = [$proSellerPlan];
            }
        }
        
        $params = [
            'plans' => $plans,
            'user_id' => $user['id'],
        ];
        return view(
            "{$this->pathViewController}/index",$params
        );
    }

    public function postSelling(Request $request)
    {
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
        $redirect = rrt_route($this->controllerName.'/checkout', ['user_id'=>$userId]);
        return response()->json(['redirect' => $redirect]);
    }

    public function checkout(Request $request){
        $user_id = $request->user_id ?? '';
        $user = UserModel::find($user_id);
        $pendingOrders = SubscriptionOrderModel::where('user_id', $user_id)
            ->where('status', 'pending')
            ->with('subscription')
            ->get();
        $pendingPlans = PlanOrderModel::where('user_id', $user_id)
            ->where('status', 'pending')
            ->with('plan')
            ->get();
        if ($pendingOrders->isEmpty() && $pendingPlans->isEmpty()) {
            return redirect(rrt_route("public/auth/signIn"));
        }
        $allFree = true;
        $purchasedModel = new PurchasedModel();
        foreach ($pendingPlans as $plan) {
            $selectedPlan = $plan->plan;
            $price = ($plan->cycle === 'monthly') ? $selectedPlan->pricing_monthly : $selectedPlan->pricing_annually;

            if ($price == 0) {
                $purchasedModel->addData('plan', [
                    'user_id' => $user_id ?? null,
                    'order_id' => $plan->id ?? null,
                    'name' => $selectedPlan->name ?? null,
                    'price' => $price,
                    'status' => $plan->status ?? 'pending'
                ]);
            } else {
                $allFree = false;
            }
        }
        foreach ($pendingOrders as $order) {
            $subscription = $order->subscription;
            $price = ($order->cycle === 'monthly') ? $subscription->price : $subscription->pricing_annually;
            if ($price == 0) {
                $purchasedModel->addData('subscription', [
                    'user_id' => $user_id ?? null,
                    'order_id' => $order->id ?? null,
                    'name' => $subscription->name ?? null,
                    'price' => $price,
                    'status' => $order->status ?? 'pending'
                ]);
            } else {
                $allFree = false;
            }
        }
        if ($allFree) {
            SubscriptionOrderModel::where('user_id', $user_id)
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->limit(1)
            ->update(['status' => 'active']);
            Session::flash('seller-success', 'Buy Package Success');
            return redirect(rrt_route('public/home/index'));
        }

        return view("{$this->pathViewController}/checkout", [
            'pendingOrders' => $pendingOrders,
            'pendingPlans' => $pendingPlans,
            'user_id' => $user_id,
            'user' => $user,
        ]);
    }

    public function postCheckout(Request $request){
        $user_id = $request->user_id ?? null;
        $subscription_ids = $request->subscription_ids ?? [];
        $plan_ids = $request->plan_ids ?? [];
        $total_price = $request->total_price ?? 0;
        $payment_method = $request->payment_method ?? 'paypal';
        if (empty($subscription_ids) && empty($plan_ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No subscription or plan selected.',
            ]);
        }
        $user = UserModel::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ]);
        }else{
            // Update user info
            $user->phone = $request->phone;
            $user->fullname = $request->fullname;
            $user->save();
        }
        if ($total_price == 0){
            return response()->json([
                'status' => 'success',
                'redirect' => rrt_route($this->controllerName.'/handlePaymentSuccess', ['user_token' => $user->token ?? '','subscription_ids'=>$subscription_ids,'plan_ids'=>$plan_ids])
            ]);
        }

        if ($payment_method == 'paypal') {
            $provider = new PayPalClient();
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => rrt_route($this->controllerName.'/handlePaymentSuccess', ['user_token' => $user->token ?? '','subscription_ids'=>$subscription_ids,'plan_ids'=>$plan_ids]), // handle success và chuyển hướng sang verify
                    "cancel_url" => rrt_route('public/home/index'), // chuyển về home
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $total_price,
                        ]
                    ]
                ]
            ]);
            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {

                        return response()->json([
                            'status' => 'success',
                            'redirect' => $links['href']
                        ]);
                    }
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment method not supported.'
        ]);
    }

    public function handlePaymentSuccess(Request $request){
        $token = $request->user_token??'';
        $user = UserModel::whereToken($token)->first();
        $subscription_ids = $request->subscription_ids??[];
        $plan_ids = $request->plan_ids??[];
        $orders = SubscriptionOrderModel::whereIn('id', $subscription_ids)
            ->where('status', 'pending')
            ->get();
        $pendingPlans = PlanOrderModel::where('id',$plan_ids)
            ->where('status','pending')
            ->with('plan')
            ->get();
        $purchasedModel = new PurchasedModel();
        foreach ($orders as $order) {
            $cycle = $order->cycle ?? 'annually';
            $price = ($cycle === 'monthly') ? ($order->subscription->price ?? 0) : ($order->subscription->pricing_annually ?? 0);
            $order->status = 'active';
            $order->save();
            
            $user->role = 'user';
            $user->join_type = ($order->subscription->slug ?? '');
            $user->save();

            $purchasedModel->addData('subscription', [
                'user_id' => $order->user_id??null,
                'order_id' => $order->id??null,
                'name' => $order->subscription->name??null,
                'price' => $price,
                'cycle'=>$cycle,
                'status'=>$order->status??'pending'
            ]);
        }
        foreach ($pendingPlans as $plans) {
            $cycle = $plans->cycle ?? 'annually';
            $price = ($cycle === 'monthly') ? ($plans->plan->pricing_monthly ?? 0) : ($plans->plan->pricing_annually ?? 0);
            $plans->status = 'active';
            $plans->save();
            if($plans->plan_id == 3){
                $orderPlan = SubscriptionOrderModel::where('user_id', $plans->user_id)
                    ->where('status', 'active')
                    ->where('subscription_id', 3)
                    ->first();
                if ($orderPlan) {
                    $orderPlan->update(['status' => 'cancel']);
                }
                if ($cycle == "annually") {
                    rrt_add_proseller_usage_ai($user->id);
                }

                $user->role = 'seller';
                $user->join_type = 'pro_seller';
                $user->save();
                
            }
            $purchasedModel->addData('plan', [
                'user_id' => $plans->user_id??null,
                'order_id' => $plans->id??null,
                'name' => $plans->plan->name??null,
                'price' => $price,
                'cycle'=>$cycle,
                'status'=>$plans->status??'pending'
            ]);
        }
        Session::flash('seller-success', 'Payment Success');
        return redirect(rrt_route('public/home/index'));
    }
}
