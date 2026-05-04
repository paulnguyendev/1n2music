<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Transactions;
use App\Helpers\User;
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
class StudioTransactionController extends Controller
{
    private $pathViewController     = "studio.pages.transaction";
    private $controllerName         = "public/studio/transaction";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $relateContentModel;
    private $bulletin;
    private $reuqestPayoutModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new TransactionsModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->bulletin = new BulletinBoardModel();
        $this->relateContentModel = new RelatedContentModel();
        $this->reuqestPayoutModel = new RequestPayoutModel();

        View::share('controllerName', $this->controllerName);
    }
    public function index(Request $request)
    {
        $user_methods = User::getMethodByUser();

        return view(
            "{$this->pathViewController}/index",
            [
                'user_methods' => $user_methods
            ]
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

        $total = Transactions::getTotalByUser($user_id);
        return response()->json([
            'total_format' => rrt_show_price($total),
            'total' => $total
        ]);
    }

    public function getWithdrawBalance()
    {
        $user_id = rrt_get_user_login('id');
        $total = Transactions::getTotalWalletByUser($user_id);
        return response()->json([
            'total_format' => rrt_show_price($total),
            'total' => $total
        ]);
    }

    public function postRequestWithdrawBalance(Request $request)
    {
        $total = $request->total_widthdraw ?? 0;
        $user_id = rrt_get_user_login('id');

        $payment = $request->payment ?? 0;
        $code = $this->model->randomCode();
        $check =  Transactions::checkTotalWidthdrawByUser($user_id, $total);

        if (is_array($check) && $check['value'] == 1) {
            $params['owner'] = rrt_get_fullname();
            $params['code'] = $code;
            $params['total'] = $total;
            $params['user_id'] = $user_id;
            $params['type'] = 'out';
            $params['category'] = 'withdrawal';
            $params['status'] = 'pending';
            $params['payment_method_id'] = $payment;
            $params['payment_method_item_id'] = $payment;
            $transaction_id =    $this->model->saveItem($params, ['task' => 'add-item']);
            $params['user_id'] = $user_id;
            $params['seller'] = 'seller';
            $params['manager'] = 'manager';
            $params['method_payment'] = $payment;
            $params['withdrawal_method'] = $payment;
            $params['tax_type'] = rrt_get_user_login('tax_type');
            $params['amount_request'] = $total;
            $params['transaction_id'] = $transaction_id;
            $params['amount_tax'] = Transactions::getTotalTaxType($total, ['task' => 'tax']);
            $params['amount_supply'] = Transactions::getTotalTaxType($total, ['task' => 'supply-price']);
            $params['vat'] = Transactions::getTotalTaxType($total, ['task' => 'vat']);
            $params['amount_payment'] = Transactions::getTotalAmountPayment($total, $params);
            $params['amount_report'] = Transactions::getTotalAmountPayment($total, $params);
            $this->reuqestPayoutModel->addItemFromTransaction($params);


            $status = 200;
        } else {
            $status = 500;
        }

        return response()->json(['status' => $status, 'check' => $check]);
    }
}
