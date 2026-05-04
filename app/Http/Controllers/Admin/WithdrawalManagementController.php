<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Transactions;
use App\Http\Controllers\Controller;
use App\Models\LogRequestPayoutModel;
#Model
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\RequestPayoutModel as MainModel;
use App\Models\RequestPayoutModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\TransactionsModel;
use App\Models\UserModel;
#Mail
use Illuminate\Support\Facades\View;

#Helper
class WithdrawalManagementController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $planOrderModel;
    private $title;
    private $params = [];
    private $transactionsModel;
    private $userModel;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/withdrawalManagement";
        $this->pathViewController = "{$this->prefix}.pages.withdrawalManagement";
        $this->transactionsModel = new TransactionsModel();
        $this->userModel = new UserModel();
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $data = $this->model->listItems([], ['task' => 'admin']);
        $seller = 1;
        $Marketer  = 0;
        return view(
            "{$this->pathViewController}/index",
            [
                'data' => $data
            ]
        );
    }
    public function form(Request $request)
    {
        $id = $request->id;
        $item = [];
        $title = "Create a New Seller";

        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'user_id']);

            $title = "Update Account";
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,

                'item' => $item,
                'id' => $id,
            ]
        );
    }
    public function save(Request $request)
    {
        $params = $request->all();
        $paramsPlanOrder = [];
        $id = $request->id;
        $item = [];
        if (!$id) {
            $params['redirect'] = rrt_route($this->controllerName . "/index");
        } else {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'user_id']);
            $params['id'] = $id;
        }
        $status = null;
        $currentPassword = null;
        $error = [];
        $fields = [
            'first_name' => [
                'required' => 1,
                'unique' => 0,
            ],
            'last_name' => [
                'required' => 1,
                'unique' => 0,
            ],
            'email' => [
                'required' => 1,
                'unique' => 1,
            ],
            'phone' => [
                'required' => 1,
                'unique' => 1,
            ],
            'username' => [
                'required' => 1,
                'unique' => 1,
            ],
            'password' => [
                'required' => 1,
                'unique' => 0,
            ],
        ];
        $check = [];
        foreach ($fields as $field => $fieldItem) {
            $fieldValue = $params[$field] ?? "";
            $fieldName = ucfirst(str_replace("_", " ", $field));
            $fieldIsRequired = $fieldItem['required'] ?? 0;
            $fieldIsUnique = $fieldItem['unique'] ?? 0;
            if ($fieldIsRequired == 1 && !$fieldValue) {
                $error[$field] = "Please enter {$fieldName}";
            } elseif ($fieldIsUnique == 1) {
                $fieldCurrentValue = $item[$field] ?? "";
                $check = $this->model->getItem([$field => $fieldValue], ['task' => 'check']);
                if ($fieldCurrentValue != $fieldValue && $check) {
                    $error[$field] = "{$fieldName} is already exits";
                }
            }
        }
        if (empty($error)) {
            $taskName = $id ? "edit-item" : "add-item";
            $params['role'] = "seller";
            $params['parent_id'] = "";
            $currentPassword = $item['password'] ?? "";
            $params['password'] = $currentPassword && $params['password'] != $currentPassword ? rrt_encrypt_password($params['password']) : $params['password'];
            $params['created_at'] = date('Y-m-d H:i:s');
            $action = $this->model->saveItem($params, ['task' => $taskName]);
            if (!$id) {
                $id = $action->id ?? "";
            }
            $params['id'] = $id;
            $action = $id ? "Update" : "Add";
            $params['message'] = "{$action} successfully";
            return $params;
        } else {
            return response()->json(
                $error,
                422,
            );
        }
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

    public function update(Request $request)
    {
        $params = $request->all();
        $id = $request->id;
        $status = null;
        $msg = null;
        if (isset($params['status'])) {
            $status = $params['status'] ?? "inactive";
            $this->model->saveItem(['id' => $id, 'status' => $status], ['task' => 'edit-item']);
            $msg = "Status update successful";
        }
        if (isset($params['plan_id'])) {
            $plan_order_id = $params['plan_order_id'] ?? "";
            $plan_id = $params['plan_id'] ?? "";
            $user_id = $params['user_id'] ?? "";
            $plan_status = $params['plan_status'] ?? "0";
            $msg = "Plan type update successful";
            $paramsPlan['user_id'] = $user_id;
            $paramsPlan['plan_id'] = $plan_id;
            $paramsPlan['status'] = 'active';
            $currentDate = date('Y-m-d H:i:s');
            $currentDateTime = Carbon::now();
            $expriredDate = Carbon::now()->addYears(1);
            $expriredDate = $expriredDate->format('Y-m-d H:i:s');
            $paramsPlan['expired_date'] = $expriredDate;
            if ($plan_order_id) {
                $paramsPlan['id'] = $plan_order_id;
                $paramsPlan['updated_at'] = $currentDate;
                $this->planOrderModel->saveItem($paramsPlan, ['task' => 'edit-item']);
            } else {
                $paramsPlan['created_at'] = $currentDate;
                $this->planOrderModel->saveItem($paramsPlan, ['task' => 'add-item']);
            }
        }
        $params['msg'] = $msg;
        return $params;
    }

    public function payout(Request $request)
    {

        $id = $request->id;
        $payout = $this->model->where('id', $id)->with('users')->first()->toArray();

        if (!$payout) {
            $error = 'Not found';
        }
        if ($payout['status'] == 'success') {
            $error = 'Erorr System';
        }
        if (!empty($error)) {
            return response()->json(
                $error,
                422,
            );
        }

        $get_balance = rrt_get_balance_for_user($payout);

        $get_tax_type = $payout['users']['tax_type'] ?? 'personal';

        if ($get_tax_type == 'personal') {
            $platformFee = $get_balance * 0.15;  // 15%
            $stateTax = $get_balance * 0.003;   // 0.3%
            $totalDeduction = $platformFee + $stateTax;
        } elseif ($get_tax_type == 'corporate') {
            $corporateTax = $get_balance * 0.05;  // 5%
            $totalDeduction = $corporateTax;
        }
        $netPayout      = $get_balance - $totalDeduction;
        $msg            = "Payment Process Successfull";
        $payout         = $this->model->where('id', $id)->update(['status' => 'success']);
        $params['msg']  = $netPayout;
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $this->model->deleteItem(['id' => $id], ['task' => 'delete']);
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
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

    public function addLog(Request $request)
    {
        $request_payout_id = $request->id;
        $content = $request->content;
        $admin_session  = rrt_get_admin_login();
        $data = [
            'user_id' => $admin_session['id'],
            'content' => $content,
            'type' => 'note',
            'request_payout_id' => $request_payout_id,

        ];
        if ($request->hasFile('image')) {

            $file  = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $originalName = \Str::slug($originalName);
            $extension = $file->clientExtension();
            $file_name = $originalName . "-" . \Str::random(10) . "." . $extension;
            $file->storeAs('logss', $file_name, 'rrt_storage');
            $data['image'] = $file_name;
        }
        $log =    LogRequestPayoutModel::create($data);

        $time =   \Carbon\Carbon::parse($log->created_at)->diffForHumans();

        $log_new = [
            'time' => $time,
            'fullname' => $log->admin->fullname,
            'content' => $content,

        ];
        if ($log->image) {
            $log_new['file_name'] = rrt_get_url_image_upload('logss', $file_name);
        }
        return response()->json(
            [
                'status' => 200,
                'msg' => 'Add note success',
                'log_new' => $log_new
            ]
        );
    }

    public function postApprove(Request $request)
    {

        $payout_id = $request->id ?? 0;
        $admin_session  = rrt_get_admin_login();

        $payout = RequestPayoutModel::where('id', $payout_id)->first();

        if ($payout->status == 'pending') {
            $payout->update([
                'status' => 'accepted'
            ]);

            LogRequestPayoutModel::create([
                'user_id' => $admin_session['id'],
                'content' => 'status:  pending => accepted successful',
                'type' => 'log',
                'request_payout_id' => $payout->id,
            ]);
        } else {
            LogRequestPayoutModel::create([
                'user_id' => $admin_session['id'],
                'content' => 'status:  pending => accepted false',
                'type' => 'log',
                'request_payout_id' => $payout->id,
            ]);
        }
        $params['id'] = $payout->transaction_id ?? 0;
        $transaction =   $this->transactionsModel->getItem($params, ['task' => 'id']);
        $result = Transactions::updateBanlanceToUser($params['id'], 'out');
        if ($result) {
            $transaction =   $this->transactionsModel->getItem($params, ['task' => 'id']);
            $user = $this->userModel->getItem(['user_id' => $transaction->user_id], ['task' => 'id']);
            Transactions::actionChangeStatusTransaction($user, $transaction);
            return response()->json(['status' => 200, 'msg' => 'Successfull']);
        } else {
            return response()->json(['status' => 500, 'msg' => 'System Error']);
        }
    }

    public function postCancel(Request $request)
    {
        $id = $request->id ?? 1;

        $admin_id = rrt_get_admin_login('id');
        $cancel_request =   $this->model->cancelRequest($id);
        $request_payout = $this->model->getItem(['id' => $id], ['task' => 'id_record']);

        if ($cancel_request) {
            $this->transactionsModel->updateStatus($request_payout->transaction_id);
            LogRequestPayoutModel::create([
                'user_id' => $admin_id,
                'content' => 'status:  pending => cancel',
                'type' => 'log',
                'request_payout_id' => $request_payout->id,
            ]);

            return response()->json(['status' => 200, 'msg' => 'Update successfull']);
        }
        return response()->json(['status' => 500, 'msg' => 'System Error']);
    }
}
