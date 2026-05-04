<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\BulletinBoardCategoryModel;
use App\Models\BulletinBoardCommentModel;
use App\Models\BulletinBoardModel as MainModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

#Helper
class FreeBoardController extends Controller
{
    private $pathViewController= "public2.pages.freeboard";
    private $controllerName         = "public/freeboards";
    private $noticeLogModel;
    private $userModel;
    private $commentModel;
    private $bulletinBoardCategoryModel;
    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->commentModel = new BulletinBoardCommentModel();
        $this->bulletinBoardCategoryModel = new BulletinBoardCategoryModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $category_id = $request->category_id??null;
        $items = $this->model->listItems(['paginate'=>1,'category'=>$category_id],['task' => 'free-board']);
        $categories = $this->bulletinBoardCategoryModel->orderBy('id', 'desc')->get();
        return view(
            "{$this->pathViewController}/index",
            [
                'items' => $items,
                'categories'=>$categories,
                'selected_category_id'=>$category_id
            ]
        );
    }
    public function form(Request $request)
    {
        $id = $request->id;
        $item = [];
        $title = "Create a Free Board";
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
        $request->validate([
            'g-recaptcha-response' => 'required|captcha',
        ],[
            'g-recaptcha-response.required' => __('Please complete the captcha to proceed.'),
            'g-recaptcha-response.captcha' => __('Captcha verification failed. Please try again.'),
        ]);
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
                    $error[$field] = "{$fieldName} ".__("is already exits");
                }
            }
        }
        if (empty($error)) {
            $taskName = $id ? "edit-item" : "add-item";
            $params['user_id'] = rrt_get_user_login('id') !==''? rrt_get_user_login('id') : $request->ip() ;
            $params['created_at'] = date('Y-m-d H:i:s');
            $params['type']='free';
            if ($request->hasFile('image')) {
                $file  = $request->file('image');
                $originalName = $file->getClientOriginalName();
                $originalName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->clientExtension();
                $name = $originalName . "-" . Str::random(10) . "." . $extension;
                $file->storeAs('threads', $name, 'rrt_storage');
                $params['thumbnail'] = $name;
            }
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
        $recordsTotal = $this->model->listItems(['count' => '1'], ['task' => 'admin']);
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
    public function detail(Request $request)
    {
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        if (!$item) {
            return redirect(rrt_route('public/home/index'));
        }
        $comments = $item->comments()->get();


        $threadsMostView = $this->model->listItems(['code' => $code], ['task' => 'most-view']);
        // dd($track->listContracts[0]->contractSetting->contract_info->name);
        return view(
            "{$this->pathViewController}/detail",
            [
                'item' => $item,
                'threadsMostView' => $threadsMostView,
                'comments' => $comments,
            ]
        );
    }
    public function reply(Request $request)
    {
        $code = $request->code ?? '';
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $params = $request->all();
        $commentId = $params['comment_id'] ?? null;
        try {
            $commentSaveId = $this->commentModel->saveItem([
                'thread_id' => $item->id ?? '',
                'user_id' => rrt_get_user_login('id'),
                'content' => $params['content'] ?? '',
                'parent_id' => $commentId,
            ], ['task' => 'add-item']);
            $coment = $this->commentModel->getItem(['id' => $commentSaveId],['task' => 'id']);
            $params['xhtml'] = view($this->pathViewController . '.comment_item',['comment' => $coment,'depth' => 1])->render();

            $params['data'] = [
                'id' => $commentSaveId,
                'user_name' => 'test',
                'content' => $params['content'] ?? '',
            ];
            $params['success'] = true;
        } catch (\Throwable $th) {
            $params['success'] = false;
            $params['msg'] =$th->getMessage();
        }



        return $params;
    }
}
