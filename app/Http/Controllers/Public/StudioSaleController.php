<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\OrderModel;
use App\Models\SocialMediaModel;
use App\Models\SubscriptionOrderModel;
#Model
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioSaleController extends Controller
{
    private $pathViewController     = "studio.pages.sale";
    private $controllerName         = "public/studio/sale";
    private $model;
    private $orderModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new UserModel();
        $this->orderModel = new OrderModel();
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
    public function detail(Request $request)
    {
        $id = $request->order_id;
        $item = $this->orderModel->findOrFail($id);
        $code = $item->code ?? "";
        $items = $item->orderItems()->get();
        return view(
            "{$this->pathViewController}/detail",
            [
                'id' => $id,
                'code' => $code,
            ]
        );
    }
    public function list(Request $request)
    {
        $result = [];
        $draw = $request->draw ?? 1;
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $search = $request->search ?? [];
        $searchValue = $search['value'] ?? "";
        $user_id = rrt_get_user_login('id');
        $params = [
            'start' => $start,
            'length' => $length,
            'is_map' => '1',
            'with' => '1',
            'controllerName' => $this->controllerName,
        ];
        $user =  $this->model->find($user_id);
        if ($user) {
            $data = $user->getOrderItemsWithAdditionalInfo($params) ?? [];
            $dataAll = $user->getOrderItemsWithAdditionalInfo() ?? [];
            $recordsFiltered = count($dataAll);
            $recordsTotal = count($data);
            $result = [
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ];
        }
        else {
            $result = [
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ];
        }
        return $result;
       
    }
    public function listOrderItem(Request $request)
    {
        $result = [];
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 0;
        $search = $request->search ? $request->search : [];
        $params['start'] = $start;
        $params['length'] = $length;
        $searchValue = $search['value'] ?? "";
        $recordsTotal = 0;
        $data = [];
        $id = $request->order_id;
        $item = $this->orderModel->findOrFail($id);
        $data =  $item ? $item->orderItems()->get()->toArray() : [];
        $user_id  = rrt_get_user_login('id');
        $user =  $this->model->find($user_id);
        $items =  $user ? $user->getOrderItemsWithAdditionalInfo() : [];
        $item = array_filter($items, function ($item) use ($id) {
            if ($item['order_id'] == $id) {
                return $item;
            }
        });
        $item = $item ? array_shift($item) : [];
        $data = $item ? $item['data']->toArray() : [];
        $data = array_map(function ($item) {
            $item['price'] = rrt_show_price($item['price'] ?? 0);
            return $item;
        }, $data);
        $params = [];

        $params['controllerName'] = $this->controllerName;
        $recordsFiltered = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsFiltered,
            'recordsFiltered' => 10,
            'data' => $data,
        ];
        return $result;
    }
}
