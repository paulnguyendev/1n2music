<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

#Model
use App\Models\PlanModel;
use App\Models\PlanOrderModel;

use Carbon\Carbon;
use App\Models\TrackModel as MainModel;;

use App\Models\TrackTrendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
#Mail
use Illuminate\Support\Facades\View;

#Helper
class MerchandiseController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $planOrderModel;
    private $title;
    private $params = [];
    private $track;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/merchandise";
        $this->pathViewController = "{$this->prefix}.pages.merchandise";
        //$this->track = new TrackModel();
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

        $item = [];
        $title = "Create a Merchandise";

        $merchandises = $this->track->where('status', 'public')->paginate(10);
        $list = $this->model->select('track_id')->get();
        $item = $list->toArray();
        $arr_id = array_column($item, 'track_id');

        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'merchandises' => $merchandises,
                'list' => $arr_id,

            ]
        );
    }
    // public function save(Request $request)
    // {
    //     $params = $request->all();

    //     $paramsPlanOrder = [];
    //     $id = $request->id;
    //     $item = [];
    //     if (!$id) {
    //         $params['redirect'] = rrt_route($this->controllerName . "/index");
    //     } else {
    //         $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'user_id']);
    //         $params['id'] = $id;
    //     }
    //     $status = null;
    //     $currentPassword = null;
    //     $error = [];
    //     $fields = [
    //         'first_name' => [
    //             'required' => 1,
    //             'unique' => 0,
    //         ],
    //         'last_name' => [
    //             'required' => 1,
    //             'unique' => 0,
    //         ],
    //         'email' => [
    //             'required' => 1,
    //             'unique' => 1,
    //         ],
    //         'phone' => [
    //             'required' => 1,
    //             'unique' => 1,
    //         ],
    //         'username' => [
    //             'required' => 1,
    //             'unique' => 1,
    //         ],
    //         'password' => [
    //             'required' => 1,
    //             'unique' => 0,
    //         ],
    //     ];
    //     $check = [];
    //     foreach ($fields as $field => $fieldItem) {
    //         $fieldValue = $params[$field] ?? "";
    //         $fieldName = ucfirst(str_replace("_", " ", $field));
    //         $fieldIsRequired = $fieldItem['required'] ?? 0;
    //         $fieldIsUnique = $fieldItem['unique'] ?? 0;
    //         if ($fieldIsRequired == 1 && !$fieldValue) {
    //             $error[$field] = "Please enter {$fieldName}";
    //         } elseif ($fieldIsUnique == 1) {
    //             $fieldCurrentValue = $item[$field] ?? "";
    //             $check = $this->model->getItem([$field => $fieldValue], ['task' => 'check']);
    //             if ($fieldCurrentValue != $fieldValue && $check) {
    //                 $error[$field] = "{$fieldName} is already exits";
    //             }
    //         }
    //     }
    //     if (empty($error)) {
    //         $taskName = $id ? "edit-item" : "add-item";
    //         $params['parent_id'] = "";
    //         $currentPassword = $item['password'] ?? "";
    //         $params['password'] = $currentPassword && $params['password'] != $currentPassword ? rrt_encrypt_password($params['password']) : $params['password'];
    //         $params['created_at'] = date('Y-m-d H:i:s');
    //         $action = $this->model->saveItem($params, ['task' => $taskName]);
    //         if (!$id) {
    //             $id = $action->id ?? "";
    //         }
    //         $params['id'] = $id;
    //         $action = $id ? "Update" : "Add";
    //         $params['message'] = "{$action} successfully";
    //         return $params;
    //     } else {
    //         return response()->json(
    //             $error,
    //             422,
    //         );
    //     }
    // }

    // public function save(Request $request)
    // {
    //     $data = $request->all();

    //     $this->model::truncate();
    //     foreach ($data['merchandise'] as $value) {
    //         $this->model->create(['track_id' => $value, 'created_at' => date('Y-m-d H:i:s')]);
    //     }
    //     $params['message'] = "Add merchandise successfully";
    //     return $params;
    // }

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

        // $recordsFiltered = count($data);
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

        if (isset($params['is_trending'])) {

            if ($params['is_trending'] == 1) {
                $params['is_trending'] = 'checked';
            } else {
                $params['is_trending'] = '';
            }
            $this->model->saveItem(['id' => $id, 'is_trending' => $params['is_trending']], ['task' => 'edit-item']);
            $msg = "Trending update successful";
        }
        if (isset($params['is_recommend'])) {
            if ($params['is_recommend'] == 1) {
                $params['is_recommend'] = 'checked';
            } else {
                $params['is_recommend'] = '';
            }
            $this->model->saveItem(['id' => $id, 'is_recommend' => $params['is_recommend']], ['task' => 'edit-item']);
            $msg = "Recommend update successful";
        }
        if (isset($params['is_featured'])) {
            if ($params['is_featured'] == 1) {
                $params['is_featured'] = 'checked';
            } else {
                $params['is_featured'] = '';
            }
            $this->model->saveItem(['id' => $id, 'is_featured' => $params['is_featured']], ['task' => 'edit-item']);
            $msg = "Featured update successful";
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
}
