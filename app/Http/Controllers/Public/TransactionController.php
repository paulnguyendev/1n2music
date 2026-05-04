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
use App\Models\TransactionsModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class TransactionController extends Controller
{
    private $pathViewController     = "studio.pages.transaction";
    private $controllerName         = "public/studio/transaction";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $relateContentModel;
    private $bulletin;

    private $params                 = [];
    function __construct()
    {
        $this->model = new TransactionsModel();
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
        $session_studio = rrt_get_user_login();
        $user_id  = $session_studio['id'] ?? 0;
        $recordsTotal = $this->model->listItems(['count' => '1', 'user_id' => $user_id], ['task' => 'studio']);
        $recordsFiltered = 10;
        $data = [];
        $params = [];
        $params['start'] = $start;
        $params['length'] = $length;
        if ($searchValue) {
            $params['search'] = $searchValue;

            $recordsTotal = $this->model->listItems(['search' => $searchValue, 'count' => '1', 'user_id' => $user_id], ['task' => 'studio']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';
        //   $params['role'] = 'seller';
        $params['controllerName'] = $this->controllerName;


        $params['user_id'] =   $user_id;
        $data = $this->model->listItems($params, ['task' => 'studio']);
        $data = $data ? $data->toArray() : [];
        $recordsFiltered = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ];

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

        return view(
            "{$this->pathViewController}/detail",
            [
                'item' => $payout['users'],
                'payout' => $payout,
                'logs' => $payout->log,
                'info_card' => $payout->paymentAccount->paymentmethod->where('is_active', 1)->first()->info
            ]
        );
    }

    public function getBalanceTotal()
    {
        $user_id = rrt_get_user_login('id');

        $balance_total = Transactions::getTotalByUser($user_id);

        return response()->json(['total' => $balance_total]);
    }
}
