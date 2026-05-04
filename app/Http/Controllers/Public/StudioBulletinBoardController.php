<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
#Model

use App\Mail\SendNoticeMail;
use App\Models\BulletinBoardCategoryModel;
use App\Models\BulletinBoardModel as MainModel;
use App\Models\BulletinBoardModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
#Helper
use Illuminate\Support\Str;
class StudioBulletinBoardController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $noticeLogModel;
    private $userModel;
    private $planModel;
    private $planOrderModel;
    private $bulletinBoardCategoryModel;
    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->bulletinBoardCategoryModel = new BulletinBoardCategoryModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
        $this->controllerName = "public/{$this->prefix}/bulletinBoard";
        $this->pathViewController = "{$this->prefix}.pages.bulletinBoard";
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
        $title = "Create a New Thread";
        $categories = $this->bulletinBoardCategoryModel->listItems([],['task' => 'all']);

        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $title = "Update Thread ";
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
                'id' => $id,
                'categories' => $categories,
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
        }
        $status = null;
        $error = [];
        $fields = [
            'name' => [
                'required' => 1,
                'unique' => 0,
            ],
            'desc' => [
                'required' => 1,
                'unique' => 0,
            ],
            'content' => [
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
            $thumbnail = $request->file('thumbnail');
            if($thumbnail) {
                $originalName = $thumbnail->getClientOriginalName();
                $originalName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $thumbnail->clientExtension();
                $name = $originalName . "-" . Str::random(10) . "." . $extension;
                $thumbnail->storeAs('threads', $name, 'rrt_storage');
                $params['thumbnail'] = $name;
            }
            else {
                $params['thumbnail'] = $params['thumbnail_text'] ?? '';
            }

            $params['created_at'] = date('Y-m-d H:i:s');
            $params['user_id'] = rrt_get_user_login('id');
            if(!$id) {
                $params['code'] = $this->model->randomCode();
            }
            $action = $this->model->saveItem($params, ['task' => $taskName]);
            if (!$id) {

                $id = $action->id ?? "";
            }
            $params['id'] = $id;
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
        $params['controllerName'] = $this->controllerName;
        $params['user_id'] = rrt_get_user_login('id');
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
    public function destroyMulti(Request $request)
    {
        $ids = $request->ids;
        if($ids) {
            foreach($ids as $id) {
                $this->model->deleteItem(['id' => $id], ['task' => 'delete']);
            }
        }

        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }
}
