<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\BannerModel as MainModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CategoryBannerModel;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

#Helper
class BannerController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $noticeLogModel;
    private $userModel;
    private $planModel;
    private $planOrderModel;
    private $title;
    private $catBannerModel;
    private $params = [];
    public function __construct()
    {
        $this->catBannerModel = new CategoryBannerModel();
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/banner";
        $this->pathViewController = "{$this->prefix}.pages.banner";
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
        $title = "Create a New Banner";
        $plans = $this->planModel->listItems([], ['task' => 'list']);
        $currentDate = date('Y-m-d H:i:s');
        $currentDateTime = Carbon::now();
        $expriredDate = Carbon::now()->addYears(1);
        $expriredDate = $expriredDate->format('Y-m-d H:i:s');
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);

            $title = "Update Banner ";
        } else {
            $item['category_banner_id']  = 0;
        }
        $cat_banner = $this->catBannerModel->listItems([], ['task' => 'all']);

        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'plans' => $plans,
                'expriredDate' => $expriredDate,
                'item' => $item,
                'id' => $id,
                'cat_banner' => $cat_banner
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
            'link' => [
                'required' => 0,
                'unique' => 0,
            ],
            'name' => [
                'required' => 1,
                'unique' => 0,
            ],
            'description' => [
                'required' => 0,
                'unique' => 0,
            ],
            'content' => [
                'required' => 0,
                'unique' => 0,
            ],
            'category_banner_id' => [
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
            $params['created_at'] = date('Y-m-d H:i:s');
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/banner'), $imageName);
                // You can also save $imageName to your database if needed.
                $params['image'] = $imageName;
            }
            if ($request->hasFile('new_image')) {
                $image = $request->file('new_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/banner'), $imageName);
                // You can also save $imageName to your database if needed.
                $params['image'] = $imageName;
                unset($params['new_image']);
            }
            if ($request->category_banner_id == 2){
                $params['is_banner_homepage'] = 1;
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
    public function sendMail(Request $request)
    {
        $params = $request->all();
        $id = $request->id;
        $params['id'] = $id;
        $items = $this->userModel->listItems([], ['task' => 'send-mail']);
        $params['items'] = $items;
        $status = 200;
        $msg = "Send mail success";
        if ($items) {
            foreach ($items as $item) {
                $email = $item['email'] ?? "";
                if ($email) {
                    $params['email'] = $email;
                    try {
                        Mail::to($email)->send(new SendNoticeMail($params));
                    } catch (\Throwable $th) {
                        $status = 400;
                        $msg = $th->getMessage();
                    }
                }
            }
        }
        $this->noticeLogModel->saveItem(['notice_id' => $id, 'status' => $status, 'msg' => $msg, 'created_at' => date('Y-m-d H:i:s')], ['task' => 'add-item']);
        $params['msg'] = $msg;
        $params['status'] = $status;
        return $params;
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
    // public function update(Request $request)
    // {
    //     $params = $request->all();

    //     $result =     $this->model->saveItem($params, ['task' => 'edit-item']);
    //     if ($result) {
    //         $msg = 'Update successfull';
    //     } else {
    //         $msg = null;
    //     }
    //     $params['msg'] = $msg;
    //     return $params;
    // }
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
