<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\BulletinBoardCategoryModel;
use App\Models\BulletinBoardModel as MainModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use App\Models\BulletinBoardTranslationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

#Helper
class BulletinBoardController extends Controller
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
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/bulletinBoard";
        $this->pathViewController = "{$this->prefix}.pages.bulletinboard";
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
        $title = "Create a New Bulletin Board";
        $plans = $this->planModel->listItems([], ['task' => 'list']);
        $currentDate = date('Y-m-d H:i:s');
        $currentDateTime = Carbon::now();
        $expriredDate = Carbon::now()->addYears(1);
        $expriredDate = $expriredDate->format('Y-m-d H:i:s');
        $categories = $this->bulletinBoardCategoryModel->listItems([],['task' => 'all']);
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $expriredDate = $item['plan_order']['expired_date'] ?? "";
            $title = "Update Notice ";
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'plans' => $plans,
                'expriredDate' => $expriredDate,
                'item' => $item,
                'id' => $id,
                'categories' => $categories,
            ]
        );
    }
    public function save(Request $request)
    {
        $params = $request->all();
        $id = $request->id ?? null;
        $task = "add-item";
        
        if ($id) {
            $task = "edit-item";
            $params['id'] = $id;
        }

        $params['code'] = $this->model->randomCode();
        $params['language'] = 'en'; // Default language
        $params['admin_id'] = rrt_get_admin_login('id');

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $originalName = $file->getClientOriginalName();
            $originalName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->clientExtension();
            $name = $originalName . "-" . \Str::random(10) . "." . $extension;
            $file->storeAs('threads', $name, 'rrt_storage');
            $params['thumbnail'] = $name;
        }

        // Remove translations from params before saving to main table
        $translations = $params['translations'] ?? [];
        unset($params['translations']);

        $result = $this->model->saveItem($params, ['task' => $task]);

        // Save translations separately
        if (!empty($translations)) {
            foreach ($translations as $lang => $translation) {
                $translation['bulletin_board_id'] = $id ?? $result;
                BulletinBoardTranslationModel::updateOrCreate(
                    [
                        'bulletin_board_id' => $id ?? $result,
                        'language' => $lang
                    ],
                    $translation
                );
            }
        }

    return $params;
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
        $recordsTotal = $this->model->listItems(['count' => '1','bulletin'=>'1'], ['task' => 'admin']);
        $recordsFiltered = 10;
        $data = [];
        $params = [];
        $params['start'] = $start;
        $params['length'] = $length;
        if ($searchValue) {

            $params['search'] = $searchValue;
            $recordsTotal = $this->model->listItems(['search' => $searchValue, 'count' => '1','bulletin'=>'1'], ['task' => 'admin']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';
        $params['controllerName'] = $this->controllerName;
        $params['bulletin'] = '1';
        $data = $this->model->listItems($params, ['task' => 'admin']);

        $data = $data ? $data->toArray() : [];
        $recordsFiltered = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
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
