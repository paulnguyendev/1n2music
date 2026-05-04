<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\NoticeModel as MainModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use App\Models\OrderModel;
use App\Models\RequestPayoutModel;
use App\Models\TrackModel;
use App\Models\MusicDistributionModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

#Helper
class DashboardController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $noticeLogModel;
    private $userModel;
    private $planModel;
    private $planOrderModel;
    private $orderModel;
    private $title;
    private $requestPayoutModel;
    private $musicDitributionModel;
    private $trackModel;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->orderModel = new OrderModel();
        $this->requestPayoutModel = new RequestPayoutModel();
        $this->musicDitributionModel = new MusicDistributionModel();
        $this->trackModel  = new TrackModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/dashboard";
        $this->pathViewController = "{$this->prefix}.pages.dashboard";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        return view('admin.pages.dashboard.index');
    }
    
    public function accessDenied(Request $request)
    {
        return view('admin.pages.dashboard.access_denied');
    }
    public function ajaxGetStatusDayofWeek(Request $request)
    {
        $total_success = [];
        $total_suppend = [];
        for ($i = 7; $i > 0; $i--) {
            $date = Carbon::now()->subDays($i - 1);
            $next_date = Carbon::now()->subDays($i);
            $params['next_date'] = $next_date;
            $params['date'] = $date;
            $params['status'] = 'success';
            $total_order_success =   $this->orderModel->listItems($params, ['task' => 'get-total-order-status']);
            $params['status'] = 'suppend';
            $total_order_suppend =   $this->orderModel->listItems($params, ['task' => 'get-total-order-status']);
            $total_success[] =  $total_order_success ?? 0;
            $total_suppend[] =  $total_order_suppend ?? 0;
        }
        return response()->json(['total_success' => $total_success, 'total_suppend' => $total_suppend]);
    }




    function list(Request $request)
    {
        $result = [];
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 0;
        $search = $request->search ? $request->search : [];
        $searchValue = $search['value'] ?? "";
        $recordsTotal = $this->model->listItems(['count' => '1'], ['task' => 'all']);
        $recordsFiltered = 10;
        $data = [];
        $params = [];
        $params['start'] = $start;
        $params['length'] = $length;
        if ($searchValue) {
            $params['search'] = $searchValue;
            $recordsTotal = $this->model->listItems(['search' => $searchValue, 'count' => '1'], ['task' => 'admin']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';
        $params['controllerName'] = $this->controllerName;
        $data = $this->model->listItems($params, ['task' => 'admin']);
        $data = $data ? $data->toArray() : [];
        $recordsFiltered = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ];
        return $result;
    }
    function saveType(Request $request){
        $params = $request->all();
        $code = $request->code ?? 0;
        if ($code) {
            $item = $this->musicDitributionModel->whereCode($code)->first();
            if($item){
                $item->update(['status'=> $params['type']]);
                return response()->json([
                    'status' => true, 
                    'msg' => __('Status update successful'), 
                ]);
            }else{
                return response()->json([
                    'status' => false, 
                    'msg' => __('Does not exist music ditribution'), 
                ]);
            }
        }else{
            return response()->json([
                'status' => false, 
                'msg' => __('Does not exist code'), 
            ]);
        }
    }
}
