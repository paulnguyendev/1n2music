<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

#Model

use App\Models\AdminModel as MainModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\View;

#Helper
class AdminController extends Controller
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
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/admin";
        $this->pathViewController = "{$this->prefix}.pages.admin";
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
    public function form(Request $request)
    {

        $id = $request->id;
        $item = [];
        $title = "Create a New Account";
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
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
            ],
            'phone' => [
                'required' => 1,
            ],
            'username' => [
                'required' => 1,
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
            $params['parent_id'] = "";
            if(isset($params['first_name']) && !empty($params['first_name'] && isset($params['last_name']) && !empty($params['last_name']))){
                $params['fullname'] = ($params['first_name']??"").' '.($params['last_name']??"");
            }
            $currentPassword = $item['password'] ?? null;
            $params['password'] = ($params['password'] != $currentPassword) ? rrt_encrypt_password($params['password']) : $params['password'];
            $params['created_at'] = date('Y-m-d H:i:s');
            
            // Process thumbnail upload if exists
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                if ($thumbnailFile->isValid()) {
                    // Generate unique filename
                    $fileName = time() . '_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();
                    
                    // Create admin directory if it doesn't exist
                    $uploadPath = public_path('uploads/admins');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    // Move the file to the public/uploads/admins directory
                    $thumbnailFile->move($uploadPath, $fileName);
                    
                    // Update params with filename
                    $params['thumbnail'] = $fileName;
                }
            }

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
        $params = [];
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
                    'search' => $searchValue,
                    'count'=>1
                ],
                ['task' => 'admin']
            );
        } else {
            $recordsTotal = $this->model->listItems(['count' => '1'], ['task' => 'admin']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';

        $params['controllerName'] = $this->controllerName;

        $users = $this->model->listItems($params, ['task' => 'admin']);
        $recordsFiltered = count($users);

        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users,
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

    public function destroyMulti(Request $request)
    {

        $ids = $request->ids;
        $this->model->deleteItem(['ids' => $ids], ['task' => 'multi-delete']);
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }
}
