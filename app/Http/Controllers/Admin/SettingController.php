<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\SettingModel as MainModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use App\Models\PageModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

#Helper
class SettingController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $noticeLogModel;
    private $userModel;
    private $planModel;
    private $planOrderModel;
    private $pageModel;
    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->pageModel = new PageModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/setting";
        $this->pathViewController = "{$this->prefix}.pages.setting";
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
        $title = "Setting Footer";
        $pages = $this->pageModel->listItems([], ['task' => 'list']);


        $result = $this->model->get()->pluck('meta_value', 'meta_key')->toArray();

        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $result,
                'id' => $id,
                'pages' => $pages,
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
            // $params['redirect'] = rrt_route($this->controllerName . "/index");
        } else {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $params['id'] = $id;
        }
        $status = null;
        $error = [];
        $fields = [
            'youtube' => [
                'required' => 0,
                'unique' => 0,
            ],
            'instagram' => [
                'required' => 0,
                'unique' => 0,
            ],
            'soundcloud' => [
                'required' => 0,
                'unique' => 0,
            ],
            'company_name' => [
                'required' => 1,
                'unique' => 0,
            ],
            'founder' => [
                'required' => 1,
                'unique' => 0,
            ],
            'address' => [
                'required' => 1,
                'unique' => 0,
            ],
            'business' => [
                'required' => 1,
                'unique' => 0,
            ],
            'digital' => [
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
            if ($params['delete-setting'] === "true") {
                $params['producer_setting_image'] = '';
            }
            unset($params['delete-setting']);
            if ($params['delete-logo-footer']  === "true") {
                $params['setting_logo_footer'] = '';
            }
            unset($params['delete-logo-footer']);
            if ($params['delete-logo-header']  === "true") {
                $params['setting_logo_header'] = '';
            }
            unset($params['delete-logo-header']);

            if ($request->hasFile('setting_image') && $request->file('setting_image')->isValid()) {
                $image = $request->file('setting_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/banner'), $imageName);
                $params['producer_setting_image'] = $imageName;
            }

            if ($request->hasFile('logo_footer') && $request->file('logo_footer')->isValid()) {
                $imageLogo = $request->file('logo_footer');
                $logoName = time() . '.' . $imageLogo->getClientOriginalExtension();
                $imageLogo->move(public_path('uploads/logo'), $logoName);
                $params['setting_logo_footer'] = $logoName;
            }

            if ($request->hasFile('logo_header') && $request->file('logo_header')->isValid()) {
                $logoHeader = $request->file('logo_header');
                $logoHeaderName = time() . '.' . $logoHeader->getClientOriginalExtension();
                $logoHeader->move(public_path('uploads/logo'), $logoHeaderName);
                $params['setting_logo_header'] = $logoHeaderName;
            }

            //  $params['created_at'] = date('Y-m-d H:i:s');
            $action = $this->model->saveItem($params);
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
}
