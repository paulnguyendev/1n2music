<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\ManagerFileModel as MainModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
#Mail
use Illuminate\Support\Facades\View;

#Helper
class ManagerFileCotroller extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $planOrderModel;
    private $title;
    private $params = [];
    public function __construct()
    {

        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/account";
        $this->pathViewController = "{$this->prefix}.pages.account";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function upload(Request $request)
    {
        $params = $request->all();
        $code = $request->code;
        $trackItem = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $trackId = $trackItem['id'] ?? "";
        $user_id = rrt_get_user_login('id');
        $file = $request->file('track_file');
        $originalName = $file->getClientOriginalName();
        $originalName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->clientExtension();
        $name = $originalName . "-" . Str::random(10) . "." . $extension;
        $file->storeAs('tracks', $name, 'rrt_storage');
        $params['user_id'] = $user_id;
        $params['name'] = $name;
        $params['track_id'] = $trackId;
        $params['created_at'] = date('Y-m-d h:i:s');
        $trackFileItem = [];
        $trackFileItem = $this->model->getItem($params, ['task' => 'check']);
        if ($trackFileItem) {
            $params['id'] = $trackFileItem['id'] ?? "";
            Storage::disk('rrt_storage')->delete("tracks/{$trackFileItem['name']}");
        }
        $task = !$trackFileItem ? "add-item" : "edit-item";
        $action = $this->model->saveItem($params, ['task' => $task]);
        if ($task == 'add-item') {
            $params['id'] = $action;
        }
        $result = [
            'files' => $file,
            'params' => $params,
            'url' => url("public/uploads/tracks/{$params['name']}"),
            'name' => $name,
            'originalName' => $originalName,
        ];
        return $result;
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
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'user_id']);

            $title = "Update Account";
        }
        $type = $request->account_type ?? '';

        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,

                'item' => $item,

                'id' => $id,

                'type' => $type,
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
        $params['role'] = 'user';
        $params['controllerName'] = $this->controllerName;
        $params['account_type'] = $request->account_type ?? '';

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
            []
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
            []
        );
    }
}
