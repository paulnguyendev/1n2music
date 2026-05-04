<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\PageModel as MainModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

#Helper
class PageController extends Controller
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
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/page";
        $this->pathViewController = "{$this->prefix}.pages.page";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $params = $request->all();
        $type = $params['type'] ?? 'page';
        return view(
            "{$this->pathViewController}/index",
            [
                'type' => $type,
            ]
        );
    }
    public function form(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $item = [];
        $title = "Create a New Page";
        $plans = $this->planModel->listItems([], ['task' => 'list']);
        $currentDate = date('Y-m-d H:i:s');
        $currentDateTime = Carbon::now();
        $expriredDate = Carbon::now()->addYears(1);
        $expriredDate = $expriredDate->format('Y-m-d H:i:s');
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $expriredDate = $item['plan_order']['expired_date'] ?? "";
            $title = "Update Page";
            
            // Load translations
            if ($item) {
                $item->translations = \App\Models\PageTranslationModel::where('page_id', $id)->get();
            }
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'plans' => $plans,
                'expriredDate' => $expriredDate,
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
        $type = $params['type'] ?? 'page';
        $item = [];
        
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $params['id'] = $id; // Ensure id is in the params array for edit-item
        }
        
        // Validation rules
        $status = null;
        $error = [];
        $fields = [
            'name' => [
                'required' => 1,
                'unique' => 0,
            ],
            'description' => [
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
            if ($request->hasFile('image')) {
                $file  = $request->file('image');
                $originalName = $file->getClientOriginalName();
                $originalName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->clientExtension();
                $name = $originalName . "-" . \Illuminate\Support\Str::random(10) . "." . $extension;
                $file->storeAs('page', $name, 'rrt_storage');
                $params['image'] = $name;
            }
            else{
                unset($params['image']);
            }
            $taskName = $id ? "edit-item" : "add-item";
            $params['created_at'] = date('Y-m-d H:i:s');
            $slug = \Illuminate\Support\Str::slug($params['name'] ?? "");
            $params['slug'] = $id ? $item['slug'] : $this->model->makeUniqueSlug($slug) ?? "";
            
            // Ensure the 'id' key is set for edit-item
            if ($id && $taskName === 'edit-item' && !isset($params['id'])) {
                $params['id'] = $id;
            }
            
            $action = $this->model->saveItem($params, ['task' => $taskName]);
            
            if (!$id) {
                // For add-item, action should be the new ID
                if (is_object($action) && isset($action->id)) {
                    $id = $action->id;
                } else {
                    $id = $action;
                }
                $params['id'] = $id;
            }
            
            // Save translations
            if (isset($params['translations']) && is_array($params['translations']) && $id) {
                foreach ($params['translations'] as $langCode => $translation) {
                    if (!empty($translation['name']) || !empty($translation['content'])) {
                        // Find existing translation or create new one
                        $exists = \App\Models\PageTranslationModel::where('page_id', $id)
                            ->where('language', $langCode)
                            ->first();
                            
                        if ($exists) {
                            $exists->update([
                                'name' => $translation['name'] ?? '',
                                'content' => $translation['content'] ?? '',
                            ]);
                        } else {
                            \App\Models\PageTranslationModel::create([
                                'page_id' => $id,
                                'language' => $langCode,
                                'name' => $translation['name'] ?? '',
                                'content' => $translation['content'] ?? '',
                            ]);
                        }
                    }
                }
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
        $this->noticeLogModel->saveItem([ 'notice_id' => $id, 'status' => $status,'msg' => $msg ,'created_at' => date('Y-m-d H:i:s')],['task' => 'add-item']);
        $params['msg'] = $msg;
        $params['status'] = $status;
        return $params;
    }
    function list(Request $request) {
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
        $params['type'] = $request->type;
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
        $this->model->deleteItem(['ids' => $ids], ['task' => 'multi-delete']);
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }
}
