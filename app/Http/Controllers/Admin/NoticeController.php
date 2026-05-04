<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\NoticeModel as MainModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\RequestSendMailLogModel;
use App\Models\RequestSendMailModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use App\Models\NoticeModel;
use App\Models\NewsletterSubscribersModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

#Helper
class NoticeController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $noticeLogModel;
    private $userModel;
    private $planModel;
    private $planOrderModel;
    private $newsletterSubscribersModel;
    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->newsletterSubscribersModel = new NewsletterSubscribersModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/notice";
        $this->pathViewController = "{$this->prefix}.pages.notice";
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
        $id             = $request->id;
        $item           = [];

        $title          = "Create a New Notice";
        $plans          = $this->planModel->listItems([], ['task' => 'list']);

        $expriredDate   = Carbon::now()->addYears(1);
        $expriredDate   = $expriredDate->format('Y-m-d H:i:s');

        $mailHistories  = [];

        if ($id) {
            $item           = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $expriredDate   = $item['plan_order']['expired_date'] ?? "";
            $title          = "Update Notice ";

            $username       = $request->input('username');
            $type           = $request->input('type');

            $isSend         = $request->input('is_send');
            $isFail         = $request->input('is_fail');

            $createdAt      = $request->input('created_at');

            $query = RequestSendMailModel::where(['noti_id' => $id])
                ->with(['user' => function ($user) {
                    $user->select('id', 'fullname', 'email');
                }]);

            if ($username) {
                $query->whereHas('user', function ($query) use ($username) {
                    $query->where('fullname', 'like', '%' . $username . '%');
                });

                $query->orWhere('email', $username);
            }

            if ($type) {
                $query->where('type', $type);
            }

            if ($isSend !== null) {
                $query->where('is_send', $isSend);
            }
            if ($isFail !== null) {
                $query->where('is_failed', $isFail);
            }

            if ($createdAt !== null) {
                $query->whereDate('created_at', '=', $createdAt);
            }

            $mailHistories = $query->orderBy('created_at', 'desc')->paginate(30);
        }

        return view(
            "{$this->pathViewController}/form",
            [
                'title'         => $title,
                'plans'         => $plans,
                'expriredDate'  => $expriredDate,
                'item'          => $item,
                'id'            => $id,
                'mailHistories' => $mailHistories
            ]
        );
    }


    public function save(Request $request)
    {

        try{

            DB::beginTransaction();
            $params             = $request->all();

            $paramsPlanOrder    = [];

            $id     = $request->id;
            $item   = [];

            if ($id) {
                $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);

                $params['id'] = $id;
            }

            $status     = null;
            $error      = [];

            $fields     = [
                'name' => [
                    'required'  => 1,
                    'unique'    => 0,
                ],
                'description' => [
                    'required'  => 1,
                    'unique'    => 0,
                ],
                'content' => [
                    'required'  => 1,
                    'unique'    => 0,
                ],
            ];

            // Nếu is_other = other thì emails là required
            if (isset($params['is_other']) && $params['is_other'] == 1) {
                $fields['emails'] = [
                    'required' => 1,
                    'unique'   => 0,
                ];
            }

            $check = [];

            foreach ($fields as $field => $fieldItem) {
                $fieldValue         = $params[$field] ?? "";
                $fieldName          = ucfirst(str_replace("_", " ", $field));

                $fieldIsRequired    = $fieldItem['required'] ?? 0;
                $fieldIsUnique      = $fieldItem['unique'] ?? 0;

                if ($fieldIsRequired == 1 && !$fieldValue) {
                    $error[$field] = "Please enter {$fieldName}";
                } elseif ($fieldIsUnique == 1) {
                    $fieldCurrentValue      = $item[$field] ?? "";
                    $check                  = $this->model->getItem([$field => $fieldValue], ['task' => 'check']);

                    if ($fieldCurrentValue != $fieldValue && $check) {
                        $error[$field] = "{$fieldName} is already exists";
                    }
                }
            }

            $strEmails = '';
            if (isset($params['emails'])) {
                $strEmails = $params['emails'];
            }

            if (empty($error)) {

                $taskName               = $id ? "edit-item" : "add-item";
                $params['created_at']   = date('Y-m-d H:i:s');

                unset($params['emails']);
                $action = $this->model->saveItem($params, ['task' => $taskName]);

                if (!$id) {
                    $id = $action ?? "";
                }

                if (isset($params['is_other']) && $params['is_other'] == 1 && $strEmails != '' && isset($id)) {

                    $notice = NoticeModel::find($id);

                    $emailArray = explode(',', $strEmails);

                    $validEmails = [];

                    foreach ($emailArray as $email) {
                        $email = trim($email);

                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $validEmails[] = $email;
                        }
                    }

                    foreach ($validEmails as $item) {
                        RequestSendMailModel::updateOrCreate(
                            [
                                'email' => $item,
                                'noti_id' => $notice->id,
                            ],
                            [
                                'user_id' => null,
                                'email' => $item,
                                'noti_id' => $notice->id,
                                'data' => [
                                    'name' => $notice->name,
                                    'description' => $notice->description,
                                    'content' => $notice->content,
                                    'user_type' => 'other',
                                    'id' => $notice->id,
                                    'email' => $item
                                ],
                                'type' => 'other',
                                'is_send' => false
                            ]
                        );
                    }
                    RequestSendMailModel::where('noti_id', $notice->id)
                        ->whereNotIn('email', $validEmails)
                        ->delete();

                    $this->noticeLogModel->saveItem(['notice_id' => $notice->id, 'status' => $status, 'msg' => 'OK', 'created_at' => date('Y-m-d H:i:s')], ['task' => 'add-item']);
                }

                if (!$id) {
                    if (isset($params['is_other']) && $params['is_other'] == 1) {
                        $params['redirect'] = rrt_route($this->controllerName . "/other");
                    }else{
                        $params['redirect'] = rrt_route($this->controllerName . "/index");
                    }
                } else {
                    if (isset($params['is_other']) && $params['is_other'] == 1) {
                        $params['redirect'] = rrt_route($this->controllerName . "/other/form", ['id' => $id]);
                    }else{
                        $params['redirect'] = rrt_route($this->controllerName . "/form", ['id' => $id]);
                    }
                }

                $params['id'] = $id;
                DB::commit();
                Session::flash('notice-success',__('Email request has been added to the queue'));
                return $params;
            } else {
                return response()->json(
                    $error,
                    422,
                );
            }
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(
                'Error make notice',
                500,
            );
        }
    }



    public function sendMail(Request $request)
    {
        $params = $request->all();
        $id = $request->id;
        $params['id'] = $id;
        $user_type = $request->user_type;
        $params['user_type'] = $user_type;
        $items = $this->userModel->listItems($params, ['task' => 'send-mail']);
        $params['items'] = $items;
        $status = 200;
        $msg = __('Please wait 10 - 15 minutes for the email to be sent.');
        if ($items) {
            foreach ($items as $item) {
                $email = $item['email'] ?? "";
                if ($email) {
                    $params['email'] = $email;
                    try {
                        unset($params['items']);
                        RequestSendMailModel::create([
                            'user_id' => $item['id'],
                            'email' => $email,
                            'noti_id' => $id,
                            'data' => $params,
                            'type' => $params['user_type'],
                            'is_send' => false
                        ]);
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


    public function reSendMail(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['status' => 400, 'msg' => 'No items selected']);
        }

        $records = RequestSendMailModel::whereIn('id', $ids)->get();

        foreach ($records as $record) {
            $record->is_send = 0;
            $record->is_failed = 0;

            $record->count_resend = $record->count_resend ? $record->count_resend + 1 : 1;

            $record->save();
        }

        return response()->json(['status' => 200, 'msg' => 'Resend mail success']);
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
        $params['is_other'] = $request->type == 'other' ? 1 : 0;

        if ($searchValue) {
            $params['search'] = $searchValue;
            $recordsTotal = $this->model->listItems(['search' => $searchValue, 'count' => '1', 'is_other' => $params['is_other']], ['task' => 'admin']);
        }
        $params['is_map'] = '1';
        $params['with'] = '1';
        $params['controllerName'] = $this->controllerName;

        $data = $this->model->listItems($params, ['task' => 'admin']);
        $data = $data ? $data->toArray() : [];
        $recordsFiltered = count($data);
        foreach ($data as &$item) {
            $totalRequests = RequestSendMailModel::where('noti_id', $item['id'])->count();
            $sentRequests = RequestSendMailModel::where('noti_id', $item['id'])->where('is_send', 1)->count();
            $item['process'] = "{$sentRequests}/{$totalRequests}";
            $failRequests = RequestSendMailModel::where('noti_id', $item['id'])->where('is_failed', 1)->count();
            $item['failed'] = "{$failRequests}/{$totalRequests}";
        }
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
        $id     = $request->id;

        $status = null;
        $msg    = null;

        if (isset($params['status'])) {

            $status = $params['status'] ?? "inactive";

            $this->model->saveItem(['id' => $id, 'status' => $status], ['task' => 'edit-item']);
            $msg = "Status update successful";
        }

        if (isset($params['plan_id'])) {

            $plan_order_id  = $params['plan_order_id'] ?? "";
            $plan_id        = $params['plan_id'] ?? "";

            $user_id        = $params['user_id'] ?? "";
            $plan_status    = $params['plan_status'] ?? "0";

            $msg                    = "Plan type update successful";
            $paramsPlan['user_id']  = $user_id;
            $paramsPlan['plan_id']  = $plan_id;

            $paramsPlan['status']   = 'active';
            $currentDate            = date('Y-m-d H:i:s');
            $currentDateTime        = Carbon::now();
            $expriredDate           = Carbon::now()->addYears(1);

            $expriredDate               = $expriredDate->format('Y-m-d H:i:s');
            $paramsPlan['expired_date'] = $expriredDate;


            if ($plan_order_id) {
                $paramsPlan['id']           = $plan_order_id;
                $paramsPlan['updated_at']   = $currentDate;

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

    public function handleRequestMail()
    {
        // cron sendmail
        $requestMail = RequestSendMailModel::where('is_send', 0)->where('is_failed', 0)->first();

        if ($requestMail) {
            $data = $requestMail->data;

            try {
                Mail::to($data['email'])->send(new SendNoticeMail($data));
                $requestMail->is_send = 1;
                $requestMail->save();
            } catch (\Throwable $th) {
                $status = 500;
                $msg = $th->getMessage();

                RequestSendMailLogModel::create([
                    'notice_id' => $requestMail->noti_id,
                    'request_id' => $requestMail->id,
                    'status' => $status,
                    'message' => $msg
                ]);
                $requestMail->is_failed = 1;
                $requestMail->save();
            }
            sleep(1);
            return redirect(rrt_route('cron.processSendMail'));
        } else {
            return response()->json(['status' => 'success', 'message' => 'All emails processed']);
        }
    }

    /******************************** Other Notice  *****************************************************************/
    public function other()
    {
        return view(
            "{$this->pathViewController}/other/index",
            []
        );
    }

    public function otherForm(Request $request)
    {
        $id             = $request->id;
        $item           = [];

        $title          = "Create a New Notice For Other";
        $plans          = $this->planModel->listItems([], ['task' => 'list']);

        $expriredDate   = Carbon::now()->addYears(1);
        $expriredDate   = $expriredDate->format('Y-m-d H:i:s');

        $mailHistories  = [];

        // Get email from request if present
        $email = $request->input('email');
        if ($email) {
            $item['emails'] = $email;
        }

        if ($id) {
            $item           = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);

            $emails      = RequestSendMailModel::where(['noti_id' => $id])->pluck('email')->toArray();

            $item['emails'] = implode(',', $emails);

            $expriredDate   = $item['plan_order']['expired_date'] ?? "";
            $title          = "Update Notice For Other";

            $username       = $request->input('username');
            $type           = $request->input('type');

            $isSend         = $request->input('is_send');
            $isFail         = $request->input('is_fail');

            $createdAt      = $request->input('created_at');

            $query = RequestSendMailModel::where(['noti_id' => $id]);

            if ($username) {
                $query->where('email', $username);
            }

            if ($type) {
                $query->where('type', $type);
            }

            if ($isSend !== null) {
                $query->where('is_send', $isSend);
            }
            if ($isFail !== null) {
                $query->where('is_failed', $isFail);
            }

            if ($createdAt !== null) {
                $query->whereDate('created_at', '=', $createdAt);
            }

            $mailHistories = $query->orderBy('created_at', 'desc')->paginate(30);
        }

        return view(
            "{$this->pathViewController}/other/form",
            [
                'title'         => $title,
                'plans'         => $plans,
                'expriredDate'  => $expriredDate,
                'item'          => $item,
                'id'            => $id,
                'mailHistories' => $mailHistories
            ]
        );
    }
    public function destroyMulti(Request $request)
    {

        $ids = $request->ids;
        $this->model->deleteItem(['id' => $ids], ['task' => 'delete']);
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }

    /******************************** Subscribers  *****************************************************************/
    public function subscribers(Request $request)
    {
        return view(
            "{$this->pathViewController}/subscribers/index",
            []
        );
    }

    public function subscribersList(Request $request)
    {
        $result = [];
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 0;
        $search = $request->search ? $request->search : [];
        $searchValue = $search['value'] ?? "";
        $recordsTotal = $this->newsletterSubscribersModel->listItems(['count' => '1'], ['task' => 'all']);
        $data = [];
        $params = [];
        $params['start'] = $start;
        $params['length'] = $length;

        if ($searchValue) {
            $params['search'] = $searchValue;
            $recordsTotal = $this->newsletterSubscribersModel->listItems(['search' => $searchValue, 'count' => '1'], ['task' => 'admin']);
        }
        $params['is_map'] = '1';
        $params['controllerName'] = $this->controllerName;

        $data = $this->newsletterSubscribersModel->listItems($params, ['task' => 'admin']);
        $data = $data ? $data->toArray() : [];
        
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ];
        return $result;
    }

    public function subscriberDelete(Request $request)
    {
        $id = $request->id;
        $this->newsletterSubscribersModel->deleteItem(['id' => $id], ['task' => 'delete']);
        return [
            'success' => true,
            'message' => 'Subscriber deleted successfully',
        ];
    }

    public function subscriberDeleteMulti(Request $request)
    {
        $ids = $request->ids;
        $this->newsletterSubscribersModel->deleteItem(['ids' => $ids], ['task' => 'multi-delete']);
        return [
            'success' => true,
            'message' => 'Subscribers deleted successfully',
        ];
    }

    public function sendNoticeToSubscribers(Request $request)
    {
        $params = $request->all();
        $id = $request->id;
        $params['id'] = $id;
        
        $subscribers = $this->newsletterSubscribersModel->listItems([], ['task' => 'all']);
        
        $status = 200;
        $msg = __('Please wait 10 - 15 minutes for the email to be sent to subscribers.');
        
        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $email = $subscriber['email'] ?? "";
                if ($email) {
                    $params['email'] = $email;
                    try {
                        RequestSendMailModel::create([
                            'user_id' => null,
                            'email' => $email,
                            'noti_id' => $id,
                            'data' => $params,
                            'type' => 'subscriber',
                            'is_send' => false
                        ]);
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
}
