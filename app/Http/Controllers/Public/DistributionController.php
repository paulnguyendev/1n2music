<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Subscription;
use App\Http\Controllers\Controller;
use App\Models\ProOrganizationModel;
#Model
use App\Models\PurchasedModel;
use App\Models\SubscriptionModel as MainModel;
use App\Models\SubscriptionOrderModel;
use App\Models\TaxModel;
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
#Helper
use Srmklive\PayPal\Services\PayPal as PayPalClient;
class DistributionController extends Controller
{
    private $pathViewController     = "public2.pages.distribution";
    private $controllerName         = "public/join/distribution";
    private $model;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {

        $item = $this->model->getItem(['slug' => 'distribution'], ['task' => 'slug']);
        $distribution = Subscription::getSubscription(2);
        $status =  $distribution['status'] ?? null;
        $redirect = url()->previous();
        if ($redirect == rrt_route('public/join/distribution/index')) {
            $redirect =  rrt_route('public/home/index');
        }
        if ($status == 'pending') {
            $redirect = rrt_route('public/studio/account/subscription');
            $desc = sprintf('You have registered but have not paid yet. <a href="%s"> See more info here!</a>', $redirect);
        }
        if ($status == 'active') {
            $status = 'successfull';
            $desc = sprintf('You have successfully registered. <a href="%s">Go back!</a>', $redirect);
        }
        return view(
            "{$this->pathViewController}/index",
            [
                'item' => $item,
                'status' => $status,
                'desc' => $desc ?? ''
            ]
        );
    }
    public function register(Request $request)
    {

        $item = $this->model->getItem(['slug' => 'publishing'], ['task' => 'slug']);
        $tax = TaxModel::all();
        // dd($item);
        $id = $item['id'] ?? "";
        $studio_session = rrt_get_user_login();
        $user = $studio_session ?? [];
        $pro_organization = ProOrganizationModel::all();
        if ($studio_session) {
            $studio = UserModel::where('id', $studio_session['id'])->first();
            if (isset($studio->paymentAccount->paymentmethod)) {
                if ($studio->paymentAccount->paymentmethod) {
                    foreach ($studio->paymentAccount->paymentmethod as $key => $item) {
                        if ($item->method == 'paypal') {
                            $card_paypal_info =  $item->info->toArray();
                        } elseif ($item->method == 'bank') {
                            $card_bank_info =  $item->info->toArray();
                        }
                    }
                }
            }
        }
        return view(
            "{$this->pathViewController}/register",
            [
                'id' => $id,
                'user' => $user,
                'card_bank_info' => $card_bank_info ?? [],
                'card_paypal_info' => $card_paypal_info  ?? [],
                'tax' => $tax,
                'pro_organization' => $pro_organization
            ]
        );
    }

    public function postRegister(Request $request)
    {

        $data = $request->all();
        $studio_session = rrt_get_user_login();
        $user = UserModel::where('id', $studio_session['id'])->first();
        if ($studio_session) {
            $subscription_order =    SubscriptionOrderModel::where('user_id', $user->id)->where('subscription_id', 2)->first();
            if (empty($subscription_order)) {
                SubscriptionOrderModel::create([
                    'subscription_id' => 2,
                    'total' => $data['total'] ?? 0,
                    'status' => 'pending',
                    'user_id' => $user->id,
                ]);
                $redirect  = rrt_route('public/studio/distribution/index');
                return [
                    'status' => 200,
                    'redirect' => $redirect,
                ];
            }
            return [
                'status' => 400,
                'msg' => ['identification' => 'You have subscribed to distributior']
            ];
        }
        return [
            'status' => 400,
            'msg' => ['identification' => 'Error System']
        ];
    }
    public function checkout(Request $request){
        $subscriptionId = $request->subscription_id??null;
        if (!$subscriptionId){
            return redirect(rrt_route('public/home/index'));
        }
        $userID = rrt_get_user_login('id');
        $model = new UserModel();
        $user = $model::find($userID);
        $subscription = MainModel::find($subscriptionId);
        if (!$user){
            return redirect(rrt_route('public/auth/signIn'));
        }
        return view('public2.pages.payment.checkout',['subscription'=>$subscription]);
    }
    public function payment(Request $request){
        $user_id = $request->user_id ?? '';
        $subscription_id = $request->subscription_id ?? '';
        $email = $request->email ?? '';
        $payment_method = $request->payment_method ?? 'paypal';
        $price = $request->price ?? 0;
        $subscription_order = SubscriptionOrderModel::where('user_id', $user_id)
            ->where('subscription_id', $subscription_id)
            ->where('status', 'active')
            ->first();
        if ($subscription_order) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already subscribed this subscription.',
            ]);
        }
        else{
            $order = SubscriptionOrderModel::create([
                'subscription_id' => $subscription_id,
                'total' => $price,
                'status' => 'pending',
                'user_id' => $user_id,
            ]);
        }
        if ($payment_method == 'paypal'){
            $provider = new PayPalClient;
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => rrt_route('public/join/payment/successPayment', ['order_id' => $order->id ?? '','status'=>'active']),
                    "cancel_url" =>  rrt_route('public/join/payment/cancelPayment'),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $price,
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong.',
                    'redirect' => route('public/join/payment/cancelPayment')
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response['message'] ?? 'Something went wrong.'
                ]);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment method not supported.'
        ]);
    }
    public function successPayment(Request $request){
        $order_id = $request->order_id??null;
        if (!$order_id){
            return redirect()->route('public/home/index');
        }
        $order = SubscriptionOrderModel::find($order_id);
        if (!$order){
            return redirect()->route('public/home/index');
        }
        $status = $request->status??null;
        if (!$status){
            return redirect()->route('public/home/index');
        }
        $purchasedModel = new PurchasedModel();
        if ($status == 'active' && $order->status !=='active'){
            $order->status = $status;
            $order->save();
            $purchasedModel->addData('subscription', [
                'user_id' => $order->user_id??null,
                'order_id' => $order->id??null,
                'name' => $order->subscription->name??null,
                'price' => $order->subscription->price??0,
                'status'=>$order->status??'pending'
            ]);
        }
        if ($order->subscription_id == 2) {
            $route = 'public/join/distribution/index';
        }
        if ($order->subscription_id == 1) {
            $route = 'public/join/publishing/index';
        }
        Session::flash('payment-success', 'Payment Success');
        return redirect(rrt_route($route));

        // return view('public2.pages.payment.successPayment',['order'=>$order]);
    }
    public function cancelPayment(Request $request){
        return view('public2.pages.payment.cancel');
    }
}
