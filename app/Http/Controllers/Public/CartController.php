<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Template;
use App\Helpers\Transactions;
use App\Http\Controllers\Admin\OrderController;
use App\Models\PlanOrderModel;
use App\Models\PurchasedModel;
use App\Models\SubscriptionOrderModel;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Controllers\Controller;
use App\Models\DownloadModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
#Model
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\OrderPaymentModel;
use App\Models\TrackCommentModel;
use App\Models\TrackFavouritesModel;
use App\Models\TrackContractModel;
use App\Models\LogOrderModel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\CountryModel;
use App\Models\SettingModel;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
#Helper
class CartController extends Controller
{
    private $pathViewController     = "public2.pages.cart";
    private $controllerName         = "public/cart";
    private $model;
    private $trackModel;
    private $trackContractModel;
    private $genreModel;
    private $userModel;
    private $trackCommentModel;
    private $trackFavouritesModel;
    private $orderPaymentModel;
    private $orderModel;
    private $orderItemModel;
    private $paymentModel;
    private $adminOrderController;
    private $logOrderModel;
    private $params                 = [];
    function __construct()
    {
        // $this->model = new MainModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->trackFavouritesModel = new TrackFavouritesModel();
        $this->trackCommentModel = new TrackCommentModel();
        $this->trackContractModel = new TrackContractModel();
        $this->orderPaymentModel = new OrderPaymentModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel = new OrderPaymentModel();
        $this->adminOrderController = new OrderController();
        $this->logOrderModel = new LogOrderModel();
        View::share('controllerName', $this->controllerName);
    }


    public function postAddCart(Request $request)
    {
        $params = $request->all();
        $userID = rrt_get_user_login('id');
        $createdAt = date('Y-m-d H:i:s');
        $trackID = $params['track_id'] ?? "";
        $type = $params['type'] ?? "";
        $trackInfo = $this->trackModel->getItem(['id' => $trackID], ['task' => 'id']);
        $trackCode = $trackInfo['code'] ?? "";
        $trackUserID = $trackInfo['user_id'] ?? "";
        $trackFiles = $trackInfo->file()->get();

        $contractID = $params['contract_id'] ?? "";
        $contractName = $params['contract_name'] ?? "";
        $contractDeliverables = $params['contract_deliverables'] ?? "";
        $contractInfo = $this->trackContractModel->getItem(['id' => $contractID], ['task' => 'id']);
        $price = $contractInfo && isset($contractInfo['price']) ? $contractInfo['price'] : 0;
        $existingItem = Cart::search(function ($cartItem, $rowId) use ($trackID) {
            return $cartItem->id === $trackID;
        });
        if ($existingItem->isNotEmpty()) {
            $rowId = $existingItem->first()->rowId;
            Cart::remove($rowId);
        }
        Cart::add(
            [
                'id' => $trackID,
                'name' => $trackInfo['name'] ?? "",
                'qty' => 1,
                'price' => $price,
                'options' => [
                    'contract_id' => $contractID,
                    'user_id' => $trackUserID,
                    'contract_name' => $contractName,
                    'contract_deliverables' => $contractDeliverables,
                    'thumbnail' => Template::getTrackThumbnailUrl($trackFiles) ?? asset('public/images/no-image.png'),
                    'code' => $trackCode,
                ]
            ]
        );
        if (!empty($type)) {
            $params['redirect'] = rrt_route('public/cart/index');
        }
        $params['user_id'] = $userID;
        $params['created_at'] = $createdAt;
        $params['existingItem'] = $existingItem;
        $params['count'] = Cart::count();
        return $params;
    }
    public function index(Request $request)
    {

        $countries = CountryModel::orderBy('name', 'asc')->get();
        $payments = $this->paymentModel->listItems(['status' => 'public'], ['task' => 'list']);
        $policyPage = \App\Models\PageModel::where([
            'type' => 'shop-policy',
            'slug' => 'refundreturn-policy'
        ])->first();

        return view(
            "{$this->pathViewController}/index",
            [
                'carts' => Cart::content(),
                'countries' => $countries,
                'payments' => $payments,
                'policyPage' => $policyPage
            ]
        );
    }
    public function checkout(Request $request)
    {
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
        $countries = CountryModel::all();
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
            $user = UserModel::find($user_id);
            if ($user && $user->status == 'pending') {
                foreach ($pendingPlans as $plan) {
                    $plan->status = 'active';
                    $plan->save();
                }

                foreach ($pendingOrders as $order) {
                    $order->status = 'active';
                    $order->save();
                }
                return redirect(rrt_route('public/auth/verifyCode', ['token' => $user->token]));
            } else {
                return redirect(rrt_route('public/home/index'));
            }
        }

        return view("{$this->pathViewController}/checkout", [
            'pendingOrders' => $pendingOrders,
            'pendingPlans' => $pendingPlans,
            'user_id' => $user_id,
            'user' => $user,
            'countries' => $countries,
        ]);
    }
    public function postCheckout(Request $request)
    {

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
        } else {
            // Update user info
            $user->phone = $request->phone;
            $user->fullname = $request->fullname;
            $user->save();
        }
        if ($total_price == 0) {
            return response()->json([
                'status' => 'success',
                'redirect' => rrt_route('public/checkout/handlePaymentSuccess', ['user_token' => $user->token ?? '', 'subscription_ids' => $subscription_ids, 'plan_ids' => $plan_ids])
            ]);
        }

        if ($payment_method == 'paypal') {

            $provider = new PayPalClient();
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => rrt_route('public/checkout/handlePaymentSuccess', ['user_token' => $user->token ?? '', 'subscription_ids' => $subscription_ids, 'plan_ids' => $plan_ids]), // handle success và chuyển hướng sang verify
                    "cancel_url" => rrt_route('public/auth/signIn'), // chuyển về login
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
    public function handlePaymentSuccess(Request $request)
    {
        $token = $request->user_token ?? '';
        $subscription_ids = $request->subscription_ids ?? [];
        $plan_ids = $request->plan_ids ?? [];
        $orders = SubscriptionOrderModel::whereIn('id', $subscription_ids)
            ->where('status', 'pending')
            ->get();
        $pendingPlans = PlanOrderModel::where('id', $plan_ids)
            ->where('status', 'pending')
            ->with('plan')
            ->get();
        $purchasedModel = new PurchasedModel();
        foreach ($orders as $order) {
            $cycle = $order->cycle ?? 'annually';
            $price = ($cycle === 'monthly') ? ($order->subscription->price ?? 0) : ($order->subscription->pricing_annually ?? 0);
            $order->status = 'active';
            $order->save();
            $purchasedModel->addData('subscription', [
                'user_id' => $order->user_id ?? null,
                'order_id' => $order->id ?? null,
                'name' => $order->subscription->name ?? null,
                'price' => $price,
                'cycle' => $cycle,
                'status' => $order->status ?? 'pending'
            ]);
        }
        foreach ($pendingPlans as $plans) {
            $cycle = $plans->cycle ?? 'annually';
            $price = ($cycle === 'monthly') ? ($plans->plan->pricing_monthly ?? 0) : ($plans->plan->pricing_annually ?? 0);
            $plans->status = 'active';
            $plans->save();
            $purchasedModel->addData('plan', [
                'user_id' => $plans->user_id ?? null,
                'order_id' => $plans->id ?? null,
                'name' => $plans->plan->name ?? null,
                'price' => $price,
                'cycle' => $cycle,
                'status' => $plans->status ?? 'pending'
            ]);
        }
        if (Cookie::get('first_payment_reminder')) {
            Cookie::queue(Cookie::forget('first_payment_reminder'));
        }
        return redirect(rrt_route('public/auth/verifyCode', ['token' => $token]));
    }
    public function remove(Request $request)
    {
        $id = $request->id;
        if (!$id) {
            Cart::destroy();
        } else {
            Cart::remove($id);
            return "Remove Cart Success";
        }
    }
    public function postOrder(Request $request)
    {
        $params = $request->all();

        $code = rrt_random_code('order', 'code');
        $params['code'] = $code;

        // Use raw_total if available, otherwise fix the Cart::total() formatting
        $total = 0;
        if (isset($params['raw_total'])) {
            $params['total'] = (float) $params['raw_total'];
        } else {
            $total = Cart::total();
            $params['total'] = (float) str_replace(',', '', $total);
        }

        $params['user_id'] = rrt_get_user_login('id');
        $carts = Cart::content();
        $orderID = null;
        $orderID = $this->orderModel->saveItem($params, ['task' => 'add-item']);
        if ($orderID) {
            foreach ($carts as $cart) {
                $cartOption = $cart->options ?? [];
                $this->orderItemModel->saveItem([
                    'order_id' => $orderID,
                    'track_id' => $cart->id ?? "",
                    'contract_track_id' => $cartOption['contract_id'],
                    'user_id' => $cartOption['user_id'],
                    'price' => $cart->price ?? 0,
                ], ['task' => 'add-item']);
            }
        }
        Cart::destroy();
        $paymentID = $params['payment_id'] ?? "";
        $payment = $this->paymentModel->getItem(['id' => $paymentID], ['task' => 'id']);
        $urlRedirect = $payment['url_redirect'] ?? "";
        if (!filter_var($urlRedirect, FILTER_VALIDATE_URL) && !empty($urlRedirect)) {
            $urlRedirect = Route::has($urlRedirect) ? rrt_route($urlRedirect) : "empty";
        }
        $paymentName = $payment->name ?? '';
        $params['order_id'] = $orderID;
        $params['carts'] = $carts;
        $params['payment'] = $payment;
        if ($paymentName == 'Paypal') {

            return $this->handlePaymentPaypal($params);
        }
        if (empty($urlRedirect)) {
            $urlRedirect = rrt_route('public/studio/order/detail', ['id' => $orderID]);
        }

        $params['redirect'] = $urlRedirect;

        return $params;
    }
    public function handlePaymentPaypal($params)
    {
        $provider = new PayPalClient();
        $paypalToken = $provider->getAccessToken();
        $total = $params['total'] ?? 0;
        $orderID = $params['order_id'] ?? '';
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => rrt_route($this->controllerName . '/paymentPaypalOrderSuccess', ['id' => $orderID]),
                "cancel_url" => rrt_route('public/auth/signIn'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $total,
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    $params['redirect'] = $links['href'] ?? route('public/home/index');
                    return $params;
                }
            }
        }
    }
    public function handlePaymentOrderSuccess(Request $request)
    {
        $params = $request->all();
        $orderID = $params['id'] ?? '';

        $order = OrderModel::find($orderID);
        if (!$order) {
            return redirect(rrt_route('public/studio/order/detail', ['id' => $orderID]));
        }
        $order->status = 'deliver';

        $description = "From <span class = 'badge badge-warning'>Order</span> to  <span class = 'badge badge-success'>Delievered</span> ";
        if ($order->save()) {
            $params['status'] = 'deliver';
            $handleCommission = $this->handleAddComssion($order, $params);
            $this->logOrderModel->saveItem(['name' => 'Update status', 'description' => $description, 'order_id' => $orderID], ['task' => 'add-item']);
        }
        return redirect(rrt_route('public/studio/order/detail', ['id' => $orderID]));
    }
    public function handleAddComssion($item, $params = [])
    {
        $id = $item['id'] ?? "";
        $orderItems = $item ? $item->orderItems()->get() : null;
        $listUserId = [];
        $result = [];
        $totalCommissions = [];
        $listUser = [];
        $listTransaction = [];
        $status = 400;
        $orderStatus = $params['status'] ?? "order";
        
        $listComission = $this->getListComission($item);
        if ($orderStatus == 'deliver') {
            $status = 200;
            foreach ($listComission as $comissionItem) {
                $userID = $comissionItem['user_id'] ?? "";
                $total = $comissionItem['total'] ?? 0;
                $transactionID = Transactions::addTransaction([
                    'user_id' => $userID,
                    'total' => $total,
                    'category' => 'commission',
                    'status' => 'active',
                ]);
                $this->handleAddLog($item, $comissionItem, 'comission');
                $listTransaction[] = $transactionID;
            }
            try {
                $this->handleSendMail($id);
            } catch (\Exception $e) {
            }
        }
        return [
            'totalCommissions' => $totalCommissions,
            'status' => $status,
            'orderStatus' => $orderStatus,
            'listUser' => $listUser,
            'listTransaction' => $listTransaction,
        ];
    }
    public function getListComission($item)
    {
        $orderItems = $item ? $item->orderItems()->get() : null;
        $totalCommissions = [];
        $result = [];
        $listTransaction = [];
        $comissionDefault = rrt_get_setting('commision');
        foreach ($orderItems as $key => $orderItem) {
            $user = $orderItem->user;
            $userID = $user->id ?? "";
            $userEmail = $user->email ?? "";
            $role = $user->role ?? "";
            $currentOrder = null;

            $paymentAccount = $user->paymentAccount()->first();
            $accountType = $user->getTypeUser();
            if ($accountType == 'publishing') {
                $commissionMetaKey = 'commission_publishing';
            } elseif ($accountType == 'distribute') {
                $commissionMetaKey = 'commission_distribute';
            } elseif ($accountType == 'seller') {
                $commissionMetaKey = 'commission_seller';
            } else {
                $commissionMetaKey = 'commission';
            }
            $commissionSetting = SettingModel::where('meta_key', $commissionMetaKey)->first();
            if ($commissionSetting) {
                $commission = $commissionSetting->meta_value;
            }
            $commission = !empty($commission) && $commission != 0 ? $commission : $comissionDefault;
            $price = $orderItem->price ? $orderItem->price : null;
            $total = $price * $commission;
            $result[$key]['price'] = $price;
            $result[$key]['commission'] = $commission * 100 . "%";
            $result[$key]['total'] = $total;
            $result[$key]['user_id'] = $userID;
            $result[$key]['email'] = $userEmail;
            $result[$key]['fullname'] = rrt_get_fullname_by_user($user);
        }
        return $result;
    }
    public function handleAddLog($item, $params = [], $key = 'status')
    {
        $oldStatus = $item['status'] ?? "order";
        $xhtmlOldStatus = rrt_show_status($oldStatus);
        $newStatus = $params['status'] ?? "order";
        $xhtmlNewStatus = rrt_show_status($newStatus);
        $name = null;
        $orderID = $item['id'] ?? "";
        $description = null;
        if ($key == 'status' && $oldStatus != $newStatus) {
            $name = "Update status";
            $description = "From {$xhtmlOldStatus} to  {$xhtmlNewStatus} ";
        }
        if ($key == 'comission' && isset($params['fullname']) && isset($params['total']) && isset($params['price'])) {
            $fullname = $params['fullname'] ?? "";
            $total = $params['total'] ?? 0;
            $total = rrt_show_price($total);
            $price = $params['price'] ?? 0;
            $price = rrt_show_price($price);
            $name = "Add commission successfully";
            $description = "Add <b>{$total}</b> to <b>{$fullname}</b>'s account with an order price of <b>{$price}</b>.";
        }
        if ($key == 'send_mail') {
            $email = $params['email'] ?? "";
            $contentMail = $params['content_mail'] ?? [];
            $name = "Send mail download beat";
            $description = $contentMail;
        }
        $logID = null;
        if ($name && $description) {
            $logID = $this->logOrderModel->saveItem(['name' => $name, 'description' => $description, 'order_id' => $orderID], ['task' => 'add-item']);
        }
        $result = [
            'name' => $name,
            'description' => $description,
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
            'logID' => $logID,
        ];
        return $result;
    }
    public function orderDetail(Request $request)
    {
        $code = $request->code;
        $item = $this->orderModel->getItem(['code' => $code], ['task' => 'code']);
        $paymentAccount = $item->paymentAccount()->first();

        $paymentID = $item['payment_id'] ?? "";
        $paymentInfo = $this->orderPaymentModel->getItem(['id' => $paymentID], ['task' => 'id']);
        $orders = $item->orderItems()->get();
        return view(
            "{$this->pathViewController}/orderDetail",
            [
                'code' => $code,
                'orders' => $orders,
                'item' => $item,
                'paymentInfo' => $paymentInfo,
                'paymentAccount' => $paymentAccount,

            ]
        );
    }
    public function paymentAccount(Request $request)
    {
        $params = $request->all();
        $paymentID = $params['payment_id'] ?? "";
        $payment = $this->orderPaymentModel->getItem(['id' => $paymentID], ['task' => 'id']);
        $hasChild = $payment['has_child'] ?? 0;
        $accounts = $hasChild ? $payment->accounts()->get() : [];
        $xhtml = view("{$this->pathViewController}/payment_account")->with(
            [
                'accounts' => $accounts,
            ]
        )->render();
        $params['payment'] = $payment;
        $params['xhtml'] = $xhtml;
        $params['accounts'] = $accounts;
        return $params;
    }
    public function paypal(Request $request)
    {
        $code = $request->code ?? '';
        $total = $request->total ?? 0;
        $order = OrderModel::where('code', $code)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.',
            ]);
        }
        $provider = new PayPalClient();
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => rrt_route('public/cart/paymentPaypalSuccess', ['order_code' => $order->code]),
                "cancel_url" => rrt_route('public/cart/orderDetail', ['code' => $order->code]),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $total,
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
    public function paypalSuccess(Request $request)
    {
        $code = $request->order_code ?? '';
        $order = OrderModel::where('code', $code)->first();
        $item = [];
        if ($order) {
            $item = $this->orderModel->getItem(['id' => $order->id, 'with' => '1'], ['task' => 'id']);
            $params['status'] = 'deliver';
            $handleCommission = $this->adminOrderController->handleAddComssion($item, $params);
            $handleAddLog = $this->adminOrderController->handleAddLog($item, $params);
            $order->status = 'deliver';
            $order->deliveried_at = Carbon::now();
            $order->payment_confirmed_at = Carbon::now();
            $order->save();
        }
        Session::flash('payment-success', 'Payment Success');
        return redirect(rrt_route('public/studio/order/detail', ['id' => $order->id]));
        // return redirect(rrt_route('public/cart/orderDetail',['code'=>$order->code]));
    }
    public function cancel(Request $request)
    {
        try {
            DB::beginTransaction();
            $user_id = $request->user_id;
            $user = UserModel::find($user_id);
            $pendingOrders = SubscriptionOrderModel::where('user_id', $user_id ?? '')
                ->where('status', 'pending')
                ->with('subscription')
                ->get();
            $pendingPlans = PlanOrderModel::where('user_id', $user_id ?? '')
                ->where('status', 'pending')
                ->with('plan')
                ->get();
            if ($pendingOrders->isEmpty() && $pendingPlans->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'msg' => __('Order not found.')
                ]);
            }
            if (!$pendingOrders->isEmpty()) {
                foreach ($pendingOrders as $key => $order) {
                    $order->status = 'cancel';
                    $order->save();
                }
            }
            if (!$pendingPlans->isEmpty()) {
                foreach ($pendingPlans as $key => $order) {
                    $order->status = 'cancel';
                    $order->save();
                }
            }
            #_ Cancel cookie
            Cookie::queue(Cookie::forget('first_payment_reminder'));
            DB::commit();
            return response()->json([
                'status' => 200,
                'msg' => __('Cancel order success'),
                'redirect' => rrt_route('public/auth/verifyCode', ['token' => $user->token ?? ''])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel order error: ' . $e->getMessage(), ['user_id' => $user_id]);
            return response()->json([
                'status' => 400,
                'msg' => __('Cancel order failed. Please try again later!')
            ]);
        }
    }
}
