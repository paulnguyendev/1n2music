<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Transactions;
use App\Http\Controllers\Controller;
use App\Models\BannerModel;
use App\Models\BulletinBoardModel;
#Model
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\RelatedContentModel;
use App\Models\RequestPayoutModel;
use App\Models\TrackTrendingModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioActivityController extends Controller
{
    private $pathViewController     = "studio.pages.activity";
    private $controllerName         = "public/studio/activity";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $relateContentModel;
    private $bulletin;
    private $params                 = [];
    function __construct()
    {
        $this->model = new RequestPayoutModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->bulletin = new BulletinBoardModel();
        $this->relateContentModel = new RelatedContentModel();

        View::share('controllerName', $this->controllerName);
    }
    public function index(Request $request)
    {


        return view(
            "{$this->pathViewController}/index",
            []
        );
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
        //   $params['role'] = 'seller';
        $params['controllerName'] = $this->controllerName;
        $session_studio = rrt_get_user_login();

        $params['user_id'] = $session_studio['id'] ?? 1;
        $data = $this->model->listItems($params, ['task' => 'studio']);
        $data = $data ? $data->toArray() : [];
        $recordsFiltered = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,

        ];
        //  dd($result);
        //    $result['route_detail'] = rrt_route($this->controllerName . '/detail', ['id', $id]);
        //  dd($result);
        // return $params;
        return $result;
    }

    public function detail(Request $request)
    {
        $payout = $this->model->where('id', $request->id)
            ->with('users')
            ->with('paymentAccount')
            ->with(['log' => function ($query) {
                $query->orderBy('id', 'DESC'); // Sắp xếp giảm dần theo id log
            }])
            ->first();
        $info_card = $payout->users->paymentAccount;


        $method = $payout->method ? $payout->method : '';
        $methodName = $method ? $method->method : '-';
        $methodInfo = $method ? $method->info : '';

        return view(
            "{$this->pathViewController}/detail",
            [
                'item' => $payout['users'],
                'payout' => $payout,
                'logs' => $payout->log,
                'info_card' => $info_card,
                'method' => $method,
                'methodName' => $methodName,
                'methodInfo' => $methodInfo,
            ]
        );
    }


    public function checkRequestPayout(Request $request)
    {

        $user_id =  rrt_get_user_login('id');
        $check =  Transactions::checkTotalWithdrawlWallet($user_id);
        if (is_array($check) && $check['value'] == 1) {
            $total_affter_peding = Transactions::getTotalWalletByUser($user_id);
            $method = $this->userModel->getMethodAccount($user_id);
            $total = Transactions::getTotalByUser($user_id);
            foreach ($method  as $key => $value) {
                $arr_method[$value->id] = ucfirst($value->method);
            }
            $status = 200;
        } else {
            $total_affter_peding = 0;
            $arr_method = [];
            $status = 500;
            $total = 0;
        }
        $check['msg'] = __($check['msg']);
        return response()->json(
            [
                'status' => $status,
                'method' => $arr_method,
                'total_affter_peding' => $total_affter_peding,
                'total' => $total,
                'check' => $check,
            ]
        );
    }
}
