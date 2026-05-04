<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\OrderModel;
use App\Models\PlanOrderModel;
use App\Models\SubscriptionOrderModel;
use App\Models\UserModel;
use App\Models\OrderModel as MainModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioOrderController extends Controller
{
    private $pathViewController     = "studio.pages.order";
    private $controllerName         = "public/studio/order";
    private $trackControllerName         = "public/studio/content";
    private $model;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        View::share('controllerName', $this->controllerName);
        View::share('trackControllerName', $this->trackControllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
//        $code = $this->model->randomCode();
//        $user_id  = rrt_get_user_login('id');
//        $items = $this->userModel::find($user_id)->tracks()->with('file')->where('status', 'draft')->orderBy('id', 'desc')->skip(0)->take(3)->get();
        return view(
            "{$this->pathViewController}/index",
            [
//                'code' => $code,
//                'items' => $items,
            ]
        );
    }
    public function list(Request $request)
    {
        $result = [];
        $search = $request->search ?? [];
        $searchValue = $search['value'] ?? "";
        $user_id = rrt_get_user_login('id');

        $orders = OrderModel::where('user_id', $user_id)->where('status','deliver')->get()->map(function ($order) {
            return [
                'type' => 'Cart order',
                'code' => $order->code,
                'created_at' => $order->created_at,
                'status' => rrt_show_status($order->status),
                'paymentName' => $order->payment->name ?? '',
                'orderBuyerInfo' => "{$order->fullname}<br><small>{$order->phone}</small><br><small>{$order->email}</small>",
                'count' => $order->orderItems->count(),
                'total' => rrt_show_price($order->total),
                'routeDetail' => rrt_route($this->controllerName . '/detail', ['id' => $order->id])
            ];
        });
        $planOrders = PlanOrderModel::where('user_id', $user_id)->where('status','active')->get()->map(function ($planOrder) {
            $planName = $planOrder->plan->name ?? 'Unknown Plan';
            $cycle = ucfirst($planOrder->cycle ?? 'Annually');
            return [
                'type' => "Plan Order - {$planName} <br><span style='display: block; text-align: center;'>{$cycle}</span>",
                'code' => $planOrder->id,
                'created_at' => $planOrder->created_at,
                'status' =>rrt_show_status($planOrder->status),
                'paymentName' => $planOrder->payment_method??'Paypal',
                'orderBuyerInfo' => $planOrder->user ? "{$planOrder->user->first_name} {$planOrder->user->last_name}<br><small>{$planOrder->user->phone}</small><br><small>{$planOrder->user->email}</small>" : '-',
                'count' => 1,
                'total' => rrt_show_price($planOrder->total),
                'routeDetail' => rrt_route($this->controllerName . '/detail', ['id' => $planOrder->id])
            ];
        });
        $subscriptionOrders = SubscriptionOrderModel::where('user_id', $user_id)->where('status','active')->get()->map(function ($subscriptionOrder) {
            $subscriptionName = $subscriptionOrder->subscription->name ?? 'Unknown Subscription';
            $cycle = ucfirst($subscriptionOrder->cycle ?? 'Annually');
            return [
                'type' => "Subscription Order - {$subscriptionName} <br><span style='display: block; text-align: center;'>{$cycle}</span>",
                'code' => $subscriptionOrder->id,
                'created_at' => $subscriptionOrder->created_at,
                'status' => rrt_show_status($subscriptionOrder->status),
                'paymentName' => $subscriptionOrder->payment_method??'Paypal',
                'orderBuyerInfo' => $subscriptionOrder->user ? "{$subscriptionOrder->user->first_name} {$subscriptionOrder->user->last_name}<br><small>{$subscriptionOrder->user->phone}</small><br><small>{$subscriptionOrder->user->email}</small>" : '-',
                'count' => 1,
                'total' => rrt_show_price($subscriptionOrder->total),
                'routeDetail' => rrt_route($this->controllerName . '/detail', ['id' => $subscriptionOrder->id])
            ];
        });
        $orders = collect($orders);
        $planOrders = collect($planOrders);
        $subscriptionOrders = collect($subscriptionOrders);
        $allOrders = $orders->merge($planOrders)->merge($subscriptionOrders);
        $sortedOrders = $allOrders->sortByDesc('created_at')->values();
        $perPage = $request->length ?? 10;
        $currentPage = ($request->start / $perPage) + 1;
        $totalItems = $sortedOrders->count();

        $paginatedOrders = new LengthAwarePaginator(
            $sortedOrders->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $totalItems,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Prepare the result for DataTables
        return [
            'draw' => $request->draw ?? 1,
            'recordsTotal' => $totalItems,
            'recordsFiltered' => $totalItems,
            'data' => $paginatedOrders->items(),
        ];
        return $result;

    }

    public function detail(Request $request)
    {
        $id = $request->id;
        $user_id = rrt_get_user_login('id');
        
        $order = OrderModel::where('id', $id)
                ->where('user_id', $user_id)
                ->first();
        
        if ($order) {
            $orderItems = $order->orderItems()->get();
           
            return view(
                "studio/pages/order/detail",
                [
                    'order' => $order,
                    'orderItems' => $orderItems
                ]
            );
        }
        
        $planOrder = PlanOrderModel::where('id', $id)
                ->where('user_id', $user_id)
                ->first();
        if ($planOrder) {
            return view(
                "studio/pages/order/detail",
                [
                    'planOrder' => $planOrder
                ]
            );
        }
        
        $subscriptionOrder = SubscriptionOrderModel::where('id', $id)
                ->where('user_id', $user_id)
                ->first();
        if ($subscriptionOrder) {
            return view(
                "studio/pages/order/detail",
                [
                    'subscriptionOrder' => $subscriptionOrder
                ]
            );
        }
        
        return redirect()->back()->with('error', 'Order not found');
    }
}
