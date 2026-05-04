<?php
namespace App\Http\Controllers\Admin;
use App\Helpers\Subscription;
use App\Helpers\Transactions;
use App\Http\Controllers\Controller;
#Model
use App\Models\AIServiceOrder as MainModel;
use App\Models\LogAIUsage;
use App\Models\LogOrderAI;
use App\Models\UserModel;
use App\Models\AIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
#Mail
use App\Mail\SendTrackToUserMail;
use Illuminate\Support\Facades\View;
#Helper
class OrderAIController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/orderAI";
        $this->pathViewController = "{$this->prefix}.pages.aiOrder";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request, $ai_id = 0)
    {
        $ai_id = $request->ai_id ? $request->ai_id : \App\Models\AIService::AIServiceAIMastering;
        $ai_service = AIService::find($ai_id);
        session()->put('ai_id',$ai_id);
        
        return view(
            "{$this->pathViewController}/index",
            [
                'title' => 'Order '.$ai_service->name ?? 'Order AI Mastering',
                'ai_id' => $ai_id
            ]
        );
    }
    public function list(Request $request)
    {
        $ai_id = session()->get('ai_id',\App\Models\AIService::AIServiceAIMastering);
        $result = [];
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 10; // Đặt mặc định cho length
        $search = $request->search ? $request->search : [];
        $searchValue = $search['value'] ?? "";

        // Lấy tổng số bản ghi
        $recordsTotal = $this->model->listItems([
            'count' => '1',
            'ai_id' => $ai_id,
        ], ['task' => 'admin']);

        // Thiết lập tham số cho truy vấn
        $params = [
            'start' => $start,
            'length' => $length,
            'is_map' => '1',
            'ai_id' => $ai_id,
            'controllerName' => $this->controllerName,
        ];

        // Thêm tìm kiếm vào tham số nếu có
        if ($searchValue) {
            $params['search'] = $searchValue;
        }

        // Lấy dữ liệu đã lọc và phân trang
        $data = $this->model->listItems($params, ['task' => 'admin']);
        $data = $data ? $data->toArray() : [];

        // Đếm số bản ghi đã lọc
        $recordsFiltered = count($data);

        // Trả về kết quả
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
        return $result;
    }
    public function detail(Request $request){
        $id = $request->id;
        $item = [];
        $title = "Create a New Order";
        $listLog = null;
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $code = $item['code'] ?? "";
            $title = "Update Order #{$code}";
            $listLog = $item->logOrder()->get()->toArray();
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
                'id' => $id,
                'listLog'=>$listLog
            ]
        );
    }
    public function save(Request $request)
    {
        $params = $request->all();
        $id = $request->id;
        $item = $id ? $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']) : new AIServiceOrder();

        // Thiết lập redirect
        $params['redirect'] = $id ? rrt_route($this->controllerName . "/detail", ['id' => $id]) : rrt_route($this->controllerName . "/index");

        // Kiểm tra lỗi
        $error = [];
        $fields = [
            'is_payment' => ['required' => 0, 'unique' => 0],
        ];

        foreach ($fields as $field => $fieldItem) {
            $fieldValue = $params[$field] ?? "";
            $fieldName = ucfirst(str_replace("_", " ", $field));
            $fieldIsRequired = $fieldItem['required'] ?? 0;

            if ($fieldIsRequired == 1 && !$fieldValue) {
                $error[$field] = "Please enter {$fieldName}";
            }
        }

        if (empty($error)) {
            $is_payment = $params['is_payment'] ?? 0;
            if ($item->is_payment == 1) {
                return $params;
            }
            if ($item && $is_payment != $item->is_payment) {
                if ($is_payment == 1) {
                    $this->addUsageCount($item,['is_payment'=>$is_payment]);
                } elseif ($is_payment == 0) {
                    $this->revokeUsageCount($item,['is_payment'=>$is_payment]);
                }
                $log = $this->logChangeStatus($item, ['status' => $is_payment]);
            }
            $item->is_payment = $is_payment;
            $item->save();

            return $params;
        } else {
            return response()->json($error, 422);
        }
    }

    public function logChangeStatus($item, $params = [], $key = 'status')
    {
        $isPayment = $item->is_payment ?? 0;
        $oldStatus = ($isPayment == 1) ? 'complete' : 'pending';
        $xhtmlOldStatus = rrt_show_status($oldStatus);

        $isPaymentNew = $params['status'] ?? 0;
        $newStatus = ($isPaymentNew == 1) ? 'complete' : 'pending';
        $xhtmlNewStatus = rrt_show_status($newStatus);

        if ($oldStatus !== $newStatus) {
            $name = "Update status";
            $description = "Changed from {$xhtmlOldStatus} to {$xhtmlNewStatus}";

            // Lưu log
            $logID = LogOrderAI::create([
                'name' => $name,
                'description' => $description,
                'order_id' => $item->id ?? ""
            ]);

            return [
                'name' => $name,
                'description' => $description,
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'logID' => $logID,
            ];
        }

        return null;
    }
    public function addUsageCount($item,$params = [], $numberAdd = 1){
        $isPayment = $params['is_payment'] ?? 1;
        if ($isPayment == 1 && $isPayment != $item->is_payment) {
            $userId = $item->user_id ?? '';
            $user = UserModel::find($userId);
            if (!$user) {
                return null;
            }

            $ai_id = $item->ai_id ?? '';
            $before_usage_count = $user->ai_usage_count ?? 0;
            $amount = $numberAdd;
            $current_usage_count = max(0, $before_usage_count + $amount);
            $data = [
                'ai_id' => $ai_id,
                'user_id' => $userId,
                'before_usage_count' => $before_usage_count,
                'amount' => $amount,
                'current_usage_count' => $current_usage_count,
                'service_order_id' => $item->id ?? ''
            ];

            $this->addLogUsageAi($data);
            $user->ai_usage_count = $current_usage_count;
            $user->save();
        }
    }
    public function revokeUsageCount($item, $params = [], $numberRevoke = 1)
    {
        $isPayment = $params['is_payment'] ?? 0;

        if ($isPayment == 0 && $isPayment != $item->is_payment) {
            $userId = $item->user_id ?? '';
            $user = UserModel::find($userId);
            if (!$user) {
                return null;
            }

            $ai_id = $item->ai_id ?? '';
            $before_usage_count = $user->ai_usage_count ?? 0;
            $amount = $numberRevoke;
            $current_usage_count = max(0, $before_usage_count - $amount);
            $data = [
                'ai_id' => $ai_id,
                'user_id' => $userId,
                'before_usage_count' => $before_usage_count,
                'amount' => $amount,
                'current_usage_count' => $current_usage_count,
                'service_order_id' => $item->id ?? ''
            ];

            $this->addLogUsageAi($data);
            $user->ai_usage_count = $current_usage_count;
            $user->save();
        }
    }

    public function addLogUsageAi($data = [])
    {
        if (!empty($data)) {
            LogAIUsage::create($data);
        }
    }
}
