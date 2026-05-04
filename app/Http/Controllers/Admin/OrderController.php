<?php
namespace App\Http\Controllers\Admin;
use App\Helpers\Subscription;
use App\Helpers\Transactions;
use App\Http\Controllers\Controller;
#Model
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\OrderPaymentModel;
use App\Models\OrderPaymentAccountModel;
use App\Models\LogOrderModel;
use App\Models\SettingModel;
use App\Models\TrackModel;
use App\Models\DownloadModel;
use App\Models\OrderModel as MainModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
#Mail
use App\Mail\SendTrackToUserMail;
use Illuminate\Support\Facades\View;
#Helper
class OrderController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $planOrderModel;
    private $orderPaymentModel;
    private $orderPaymentAccountModel;
    private $logOrderModel;
    private $trackModel;
    private $downloadModel;
    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->orderPaymentModel = new OrderPaymentModel();
        $this->orderPaymentAccountModel = new OrderPaymentAccountModel();
        $this->logOrderModel = new LogOrderModel();
        $this->trackModel = new TrackModel();
        $this->downloadModel = new DownloadModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/order";
        $this->pathViewController = "{$this->prefix}.pages.order";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
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
        $id = $request->id;
        $item = $this->model::findOrFail($id);
        $orderItems = $item->orderItems()->get();
        return view(
            "{$this->pathViewController}/detail",
            [
                'id' => $id,
            ]
        );
    }
    public function form(Request $request)
    {
        $id = $request->id;
        $item = [];
        $title = "Create a New Order";
        $code = null;
        $payments =  $this->orderPaymentModel->listItems([], ['task' => 'list']);
        $paymentAccounts = $this->orderPaymentAccountModel->listItems([], ['task' => 'list']);
        $paymentConfirmedAt = null;
        $paymentConfirmedAtFormated = null;
        $listComission = null;
        $listLog = null;
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $code = $item['code'] ?? "";
            $title = "Update Order #{$code}";
            $paymentID = $item['payment_id'] ?? "";
            if ($paymentID) {
                $paymentInfo = $this->orderPaymentModel->findOrFail($paymentID);
            }
            $paymentAccounts =   $paymentInfo->accounts()->get();
            $paymentConfirmedAt = $item['payment_confirmed_at'] ?? "";
            $paymentConfirmedAtFormated = rrt_convert_format_date($paymentConfirmedAt, 'Y-m-d\TH:i');
            $listComission = $this->getListComission($item);
            $listLog = $item->orderLogs()->get()->toArray();
        }

        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
                'id' => $id,
                'payments' => $payments,
                'paymentAccounts' => $paymentAccounts,
                'paymentConfirmedAtFormated' => $paymentConfirmedAtFormated,
                'listComission' => $listComission,
                'listLog' => $listLog,
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
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $params['id'] = $id;
            $params['redirect'] = rrt_route($this->controllerName . "/form", ['id' => $id]);
        }
        $status = null;
        $currentPassword = null;
        $error = [];
        $fields = [
            'fullname' => [
                'required' => 1,
                'unique' => 0,
            ],
            'email' => [
                'required' => 1,
                'unique' => 0,
            ],
            'phone' => [
                'required' => 1,
                'unique' => 0,
            ],
            'payment_id' => [
                'required' => 1,
                'unique' => 0,
            ],
            'status' => [
                'required' => 0,
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
            $handleCommission = $this->handleAddComssion($item, $params);
            $params['is_refund'] = isset($params['total_refund']) && !empty($params['total_refund']) ? 1 : 0;
            $action = $this->model->saveItem($params, ['task' => $taskName]);
            if (!$id) {
                $id = $action->id ?? "";
            }
            $params['id'] = $id;
            $action = $id ? "Update" : "Add";
            $params['message'] = "{$action} successfully";
            $handleAddLog = $this->handleAddLog($item, $params);
            return $params;
        } else {
            return response()->json(
                $error,
                422,
            );
        }
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
            }
            elseif ($accountType == 'distribute') {
                $commissionMetaKey = 'commission_distribute';
            }
            elseif ($accountType == 'seller') {
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
            }
            catch (\Exception $e){

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
    public function listAccount(Request $request)
    {
        $paymentID = $request->payment_id ?? "";
        $paymentInfo = $this->orderPaymentModel->findOrFail($paymentID);
        $accounts =   $paymentInfo->accounts()->get();
        return $accounts;
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
    function listItem(Request $request)
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
        $id = $request->id;
        $item = $this->model::findOrFail($id);
        $data = $item->orderItems()->get();
        $data = $data->map(function ($item) use ($params) {
            $price = $item['contract_track']['price'] ?? 0;
            $price = rrt_show_price($price);
            $item['show_price'] = $price;
            return $item;
        });
        $data = $data ? $data->toArray() : [];
        $recordsFiltered = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
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
        if (isset($params['trending'])) {
            $trending = $params['trending'] ? 1 :  0;
            $this->model->saveItem(['id' => $id, 'trending' => $trending], ['task' => 'edit-item']);
            $msg = "Trending update successful";
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
    public function sendmail(Request $request)
    {
        $params = $request->all();
        $id = $request->id;
        $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
        $itemStatus = $item['status'] ?? "order";
        $itemCode = $item['code'] ?? "";
        $orderItems = $item ? $item->orderItems()->get() : null;
        $user = $item->user()->first();
        $email = $user->email ?? "";
        $userID = $user->id ?? "";
        $status = 200;
        $msg = null;
        if (!$email) {
            $status = 400;
            $msg = "Email is empty";
        }
        if (!$orderItems) {
            $status = 400;
            $msg = "No data";
        }
        if ($itemStatus != 'deliver') {
            $status = 400;
            $msg = "Status is not Delievered";
        }
        if($status == 200) {
            try {
                $this->handleSendMail($id);
                $msg = "Send mail successfully";
            } catch (\Throwable $e) {
                $status = 400;
                $msg = "Send mail failed";
            }
        }
        $params['status'] = $status;
        $params['msg'] = $msg;
        $params['orderItems'] = $orderItems;
        $params['itemStatus'] = $itemStatus;
        return $params;
    }
    public function handleSendMail($orderID)
    {
        $item = $this->model->getItem(['id' => $orderID, 'with' => '1'], ['task' => 'id']);
        $itemStatus = $item['status'] ?? "order";
        $itemCode = $item['code'] ?? "";
        $orderItems = $item ? $item->orderItems()->get() : null;
        $user = $item->user()->first();
        $email = $user->email ?? "";
        $userID = $user->id ?? "";
        $status = 200;
        $msg = null;
        if (!$email) {
            $status = 400;
            $msg = "Email is empty";
        }
        if (!$orderItems) {
            $status = 400;
            $msg = "No data";
        }
        if ($itemStatus != 'deliver') {
            $status = 400;
            $msg = "Status is not Delievered";
        }
        $trackFiles = [];
        $downloadUrls = [];
        foreach ($orderItems as $key => $orderItem) {
            $trackID = $orderItem['track_id'] ?? "";
            $track = $trackID ? $this->trackModel->getItem(['id' => $trackID], ['task' => 'id']) : null;
            $trackCode = $track ? $track['code'] : "";
            $trackName = $track ? $track['name'] : "";
            $trackType = $orderItem->contract_track->contractSetting->deliverables ?? "";
            $trackFile = $track ? $track->fileWithType($trackType)->first() : [];
            #_Add download
            $token = md5($email . time());
            $this->downloadModel->saveItem([
                'user_id' => $userID,
                'track_id' => $trackID,
                'track_code' => $trackCode,
                'track_type' => $trackType,
                'token' => $token,
            ], ['task' => 'add-item']);
            $downloadUrls[$key]['url'] = $trackFile ? rrt_route('public/track/downloadTrack', ['token' => $token]) : '#';
            $downloadUrls[$key]['name'] = $trackName;
        }
        Mail::to($email)->send(new SendTrackToUserMail([
            'email' => $email,
            'downloadUrls' => $downloadUrls,
            'itemCode' => $itemCode,
        ]));
        $contentMail = "Here is your download link; click to initiate the download.<br>        ";
        if ($downloadUrls) {
            foreach ($downloadUrls as $downloadUrlItem) {
                $downloadName = $downloadUrlItem['name'] ?? "No found name";
                $downloadUrl = $downloadUrlItem['url'] ?? "#";
                $contentMail .= "<a href = '{$downloadUrl}'>{$downloadName}</a> <br>";
            }
        }
        $this->handleAddLog($item, ['email' => $email, 'content_mail' => $contentMail], 'send_mail');
    }
    public function export(Request $request) {
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
        if ($searchValue) {
            $params['search'] = $searchValue;
            $recordsTotal = $this->model->listItems(['search' => $searchValue, 'count' => '1'], ['task' => 'admin']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';
        $params['controllerName'] = $this->controllerName;
        $data = $this->model->listItems($params, ['task' => 'admin']);
      
      
        $filename = 'orders_data_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        $file = fopen($tempFile, 'w');
        fputcsv($file, [
            'Code',
            'Created At',
            'Full Name',
            'Email',
            'Phone',
            'Status',
            'Payment method',
            'Total Item	',
            'Total',
        
        ]);
        foreach ($data as $item) {
            fputcsv($file, [
                $item->code,
                $item->created_at,
                $item->fullname,
                $item->email,
                $item->phone,
                $item->status,
                $item->payment_name,
                $item->order_items_count,
                $item->show_total,
           
            ]);
        }
        fclose($file);
        return response()->download($tempFile, $filename, $headers)->deleteFileAfterSend(true);
    }
}
