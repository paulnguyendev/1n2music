<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AIPackage;
use App\Models\AIPackageRole;
use App\Models\AIServiceOrder;
use App\Models\AIServiceOrder as MainModel;
use App\Models\LogAIUsage;
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
class AIOrderController extends Controller
{
    private $pathViewController     = "public2.pages.orderAI";
    private $controllerName         = "public/studio/orderAi";
    private $model;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);

    }
    public function checkout(Request $request){
        $package_id = $request->package_id??'';
        $role_id = $request->role_id??'';
        if (!$package_id || !$role_id){
            abort(404);
        }
        $aiPackage = AIPackageRole::where('package_id',$package_id)->where('role_id',$role_id)->first();
        if (!$aiPackage){
            return redirect(rrt_route('public/studio/home/index'));
        }
        return view($this->pathViewController.'/checkout',[
            'aiPackage'=>$aiPackage
        ]);
    }
    public function payment(Request $request){
        $user_id = $request->user_id??"";
        $ai_id = $request->ai_id??"";
        $pay_amount = $request->pay_amount??0;
        $pay_amount = round($pay_amount, 1);
        $usage_count = $request->usage_count??0;
        $download_available = $request->download_available??'';
        $payment_method = $request->payment_method ?? 'paypal';
        $order = AIServiceOrder::create([
            'user_id'=>$user_id,
            'pay_amount'=>$pay_amount,
            'usage_count'=>$usage_count,
            'download_available'=>$download_available,
            'ai_id'=>$ai_id,
            'payment_method'=>$payment_method,
            'is_payment'=>0,
            'status'=>0,// đã download chưa
            'note'=>null,
        ]);
        if($pay_amount == 0){
            return response()->json([
                'status' => 'success',
                'redirect' => rrt_route('public/studio/orderAi/successPayment', ['order_id' => $order->id ?? '','is_payment'=>1])
            ]);
        }
        if ($payment_method == 'paypal'){
            $provider = new PayPalClient;
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => rrt_route('public/studio/orderAi/successPayment', ['order_id' => $order->id ?? '','is_payment'=>1]),
                    "cancel_url" =>  rrt_route('public/studio/orderAi/cancelPayment'),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $pay_amount,
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
                    'redirect' => rrt_route('public/studio/orderAi/cancelPayment')
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response['message'] ?? 'Something went wrong.'
                ]);
            }
        }
    }
    public function successPayment(Request $request){
        $order_id = $request->order_id??null;
        if (!$order_id){
            return redirect()->route('public/studio/home/index');
        }
        $order = AIServiceOrder::find($order_id);
        if (!$order){
            return redirect()->route('public/studio/home/index');
        }
        $is_payment = $request->is_payment??null;
        $userId = $order->user_id??'';
        $user = UserModel::find($userId);
        if (!$user){
            return redirect()->route('public/studio/home/index');
        }
        if ($order->ai_id == \App\Models\AIService::AIServiceAIMastering) {
            $beforeAiUsageCount = $user->ai_usage_count ?? 0;
            $route_ai_package = 'public/studio/mastering/index';
        }
        if ($order->ai_id == \App\Models\AIService::AIServiceAIRecognition) {
            $beforeAiUsageCount = $user->ai_usage_count_reconize ?? 0;
            $route_ai_package = 'public/studio/recognition/index';
        }
        $amount = $order->usage_count??0;
        $currentAiUsageCount = $beforeAiUsageCount + $amount;
        if ($is_payment == 1 && $order->is_payment!== 1){
            $order->is_payment = 1;
            $order->save();
            // check ai package
            if ($order->ai_id == \App\Models\AIService::AIServiceAIMastering) {
                $user->ai_usage_count = $currentAiUsageCount;
            }
            if ($order->ai_id == \App\Models\AIService::AIServiceAIRecognition) {
                $user->ai_usage_count_reconize = $currentAiUsageCount;
            }
            $user->save();
            // add log
            LogAIUsage::create([
                'ai_id'=>$order->ai_id??'',
                'user_id'=>$order->user_id??"",
                'before_usage_count'=>$beforeAiUsageCount,
                'amount'=>$amount,
                'current_usage_count'=>$currentAiUsageCount,
                'service_order_id'=>$order->id??''
            ]);
        }
        Session::flash('payment-success', 'Payment Success');
        return redirect(rrt_route($route_ai_package));
        // return view('public2.pages.orderAI.successPayment',['order'=>$order]);
    }
    public function cancelPayment(Request $request){
        return view('public2.pages.orderAI.cancel');
    }
}
