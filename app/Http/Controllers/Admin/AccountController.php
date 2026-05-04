<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAIUsage;
use App\Models\PaymentAccountModel;
use App\Models\PayoutMethodInfoModel;
#Model
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\ProOrganizationModel;
use App\Models\TaxModel;
use App\Models\UserModel as MainModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
#Mail
use Illuminate\Support\Facades\View;

#Helper
class AccountController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $paymentAccountModel;
    private $PayoutMethodInfoModel;
    private $planOrderModel;
    private $title;
    private $params = [];
    public function __construct()
    {

        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->paymentAccountModel = new PaymentAccountModel();
        $this->PayoutMethodInfoModel = new PayoutMethodInfoModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/account";
        $this->pathViewController = "{$this->prefix}.pages.account";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {

        return view(
            "{$this->pathViewController}/generalMembers",
            []
        );
    }
    public function form(Request $request)
    {

        $id = $request->id;
        $item = [];
        $title = "Create a New Account";
        $planOrder = null;
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'user_id']);
            $title = "Update Account";
            // $user_payment = $item->paymentAccount->paymentmethod;
            // dd($user_payment);
            $planOrder = PlanOrderModel::where('user_id',$id)->latest()->first();
        }

        $type = $request->account_type ?? '';

        $taxs = TaxModel::all();
        $pro_organizations = ProOrganizationModel::all();
        if (!is_array($item)){
            $memberSubscription = implode(',',rrt_get_user_joinType($item));
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
                'id' => $id,
                'type' => $type,
                'taxs' => $taxs,
                'planOrder'=>$planOrder,
                'memberSubscription'=>$memberSubscription??"",
                'pro_organizations' => $pro_organizations
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
        $planType = $request->plan_type??"";
        $cycle = $request->cycle ?? "";
        $params['planType'] = $planType;
        $params['cycle'] = $cycle;
        if(($params['account_type'] ?? '') == 'seller'){
            $params['role']='seller';
        }else{
            $params['role']='user';
        }
        if (empty($error)) {
            $taskName = $id ? "edit-item" : "add-item";
            $params['parent_id'] = "";
            $currentPassword = $item['password'] ?? "";
            $params['password'] = $currentPassword && $params['password'] != $currentPassword ? rrt_encrypt_password($params['password']) : $params['password'];
            $params['created_at'] = date('Y-m-d H:i:s');

            $action = $this->model->saveUserAdmin($params, ['task' => $taskName]);
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
    public function saveList(Request $request)
    {

        $params = $request->all();
        $paramsPaymentAccounts = $params['payment_accounts'] ?? [];
        $paramsPayoutMethodInfo = $params['payout_method_info'] ?? [];
        if($paramsPaymentAccounts) {
            if(isset($paramsPaymentAccounts['commision'])) {
                $paramsPaymentAccounts['commision'] = $paramsPaymentAccounts['commision'] / 100;
            }

            $this->paymentAccountModel->saveItem($paramsPaymentAccounts,['task' => 'edit-item']);
        }
        if($paramsPayoutMethodInfo) {
            foreach ($paramsPayoutMethodInfo as $itemMethodInfoID => $itemMethodInfo) {
                $itemMethodInfo['id'] = $itemMethodInfoID;
                $this->PayoutMethodInfoModel->saveItem($itemMethodInfo,['task' => 'edit-item']);
            }
        }

        return $params;
    }
    function list(Request $request)
    {
        $params = [];
        $accountType = $request->account_type ??'';
        if ($accountType == 'free-user'){
            $params['account_type'] = ['free-user'];
        }elseif ($accountType == 'seller'){
            $params['account_type'] = ['free-seller','proseller-monthly','proseller-annually'];
        }
        elseif ($accountType == 'distribution'){
            $params['account_type'] = ['distribution-annually'];
        }
        elseif ($accountType == 'publishing'){
            $params['account_type'] = ['publishing-annually'];
        }
        $result = [];
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 0;
        $search = $request->search ? $request->search : [];
        $searchValue = $search['value'] ?? "";
        $recordsFiltered = 10;
        $data = [];

        $params['start'] = $start;
        $params['length'] = $length;
        if ($searchValue) {
            $params['search'] = $searchValue;
            $recordsTotal = $this->model->listItems(
                [
                    'account_type' => $params['account_type'],
                    'type' => $accountType,
                    'search' => $searchValue,
                ],
                ['task' => 'filterRole']
            );
        } else {
            $recordsTotal = $this->model->listItems(['count' => '1', 'account_type' => $params['account_type'], 'type' => $accountType], ['task' => 'filterRole']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';

        $params['controllerName'] = $this->controllerName;
        $params['type']= $accountType;

        $users = $this->model->listItems($params, ['task' => 'filterRole']);
        $data = $users ? $users->map(function ($user) {
            $data = $user->toArray();
//            $data['join_type'] = implode(', ', rrt_get_user_joinType($user));
            $data['join_type'] = implode(', ', rrt_get_all_user_joinTypes($user));
            return $data;
        })->toArray() : [];
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
    public function delete(Request $request)
    {
        $id = $request->id;
        $this->model->deleteItem(['id' => $id], ['task' => 'delete']);
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }
    public function generalMembers(Request $request)
    {

        return view(
            "{$this->pathViewController}/generalMembers",
            ['account_type' => 'general']
        );
    }

    public function distributionMembers(Request $request)
    {

        return view(
            "{$this->pathViewController}/distributionMembers",
            [
                'account_type' => 'distribution'
            ]
        );
    }
    public function publishingMembers(Request $request)
    {
        return view(
            "{$this->pathViewController}/publishingMembers",
            ['account_type' => 'publishing']
        );
    }
    public function basicMembers(Request $request)
    {
        return view(
            "{$this->pathViewController}/basicMembers",
            ['account_type' => 'basic']
        );
    }

    public function updateIsHomepage(Request $request)
    {
        $data = $request->all();

        $account = $this->model->find($data['id']);
        if ($account) {
            $account->update([
                'is_homepage' => $data['is_homepage']
            ]);

            return response()->json(['status' => true, 'message' => 'Update Successful']);
        }

        return response()->json(['status' => false, 'message' => 'Update Falsed']);
    }

    public function listPayment(Request $request)
    {
        $id = $request->id;
        //$id = $request->id ?? [];
        //dd($request->session()->all());
        $user_payment = PaymentAccountModel::where('user_id', $id)->first();
        // dd($user_payment);
        $previous = url()->previous();
        return view(
            "{$this->pathViewController}/listpayment",
            [
                'user_payment' => $user_payment,
                'previous' => $previous,
                'id' => $id
            ]
        );
    }
    public function getListPaymentToUser(Request $request)
    {
        $id = $request->id ?? [];
        $user_payment = PaymentAccountModel::where('user_id', $id)->first();
        // dd($user_payment->paymentmethod[0]->info);
    }

    public function destroyMulti(Request $request)
    {

        $ids = $request->ids;
        $this->model->deleteItem(['ids' => $ids], ['task' => 'multi-delete']);
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }
    public function export(Request $request) {
        $params = [];
        $type = $request->type ?? 'basic';
        if ($type == 'free-user'){
            $params['account_type'] = ['free-user'];
        }elseif ($type == 'seller'){
            $params['account_type'] = ['free-seller','proseller-monthly','proseller-annually'];
        }
        elseif ($type == 'distribution'){
            $params['account_type'] = ['distribution-annually'];
        }
        elseif ($type == 'publishing'){
            $params['account_type'] = ['publishing-annually'];
        }
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 0;
        $search = $request->search ? $request->search : [];
        $searchValue = $search['value'] ?? "";
        $recordsFiltered = 10;
        $data = [];

       
        
        if ($searchValue) {
            $params['search'] = $searchValue;
            $recordsTotal = $this->model->listItems(
                [
                    'account_type' =>$type,
                    'type' => $type,
                    'search' => $searchValue,
                ],
                ['task' => 'filterRole']
            );
        } 
        $params['is_map'] = '1';
        $params['with'] = '1';

        $params['controllerName'] = $this->controllerName;
        $params['type']= $type;

        $users = $this->model->listItems($params, ['task' => 'filterRole']);
        $data = $users ? $users->map(function ($user) {
            $data = $user->toArray();
            $data['join_type'] = implode(', ', rrt_get_all_user_joinTypes($user));
            return $data;
        })->toArray() : [];
     
        $filename = 'users_data_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        $file = fopen($tempFile, 'w');
        fputcsv($file, [
            'ID',
            'Username',
            'Full Name',
            'Email',
            'Phone',
            'Status',
            'AI Mastering',
            'AI Recognition',
            'Join Type',
            'Created At'
        ]);
        foreach ($users as $user) {
            fputcsv($file, [
                $user->id,
                $user->username,
                $user->fullname,
                $user->email,
                $user->phone,
                $user->status,
                $user->ai_usage_count,
                $user->ai_usage_count_reconize,
                $user->join_type,
                $user->created_at
            ]);
        }
        fclose($file);
       
   
        return response()->download($tempFile, $filename, $headers)->deleteFileAfterSend(true);
    }
}
