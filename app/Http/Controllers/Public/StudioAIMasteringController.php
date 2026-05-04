<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\BulletinBoardCategoryModel;
use App\Models\LogAIUsage;
use App\Models\MasteringModel as MainModel;
use App\Models\BulletinBoardModel;
use App\Models\MasteringAudioModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\Role;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
#Helper
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Mockery\Exception;

class StudioAIMasteringController extends Controller
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
    private $masteringAudioModel;
    private $title;
    private $apiUrl;
    private $params = [];
    //mastering
    private $mastering_endpoint;
    private $api_key_mastering;
    private $secret_key_mastering;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->bulletinBoardCategoryModel = new BulletinBoardCategoryModel();
        $this->masteringAudioModel = new MasteringAudioModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
        $this->controllerName = "public/{$this->prefix}/mastering";
        $this->pathViewController = "{$this->prefix}.pages.mastering";
        $this->apiUrl  = env('API_MASTERING');
        $this->mastering_endpoint=env('ENDPOINT_API_MASTERING');
        $this->api_key_mastering=env('API_KEY_MASTERING');
        $this->secret_key_mastering=env('SECRET_KEY_MASTERING');
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        // ai_id 1 ai mastering
        $textPriceRole = rrt_get_text_price_role(1);
        $priceRole = rrt_get_price_role(1);
        $usage_count = rrt_get_user_ai_usage(\App\Models\AIService::AIServiceAIMastering);
        $aiStudio = true;
        return view(
            "{$this->pathViewController}/index",
            [
                'aiStudio' => $aiStudio,
                "textPriceRole" => $textPriceRole,
                "priceRole" => $priceRole,
                'usage_count' => $usage_count
            ]
        );
    }
    public function form(Request $request)
    {
        $id = $request->id;

        $item = [];
        $title = "Create a New Mastering";
        $categories = $this->bulletinBoardCategoryModel->listItems([], ['task' => 'all']);
        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);
            $title = __("Detail Mastering");
        }
        $inputAudioId = $item->input_audio_id ?? '';
        $audio = $this->masteringAudioModel->getItem(['id' => $inputAudioId] ,['task' => 'id']);
        $mastering = $this->model->getItem(['input_audio_id'=>$audio->id??''],['task'=>'input_audio_id']);
        return view(
            "{$this->pathViewController}/form",
            [
                'mastering'=>$mastering,
                'title' => $title,
                'item' => $item,
                'id' => $id,
                'categories' => $categories,
                'inputAudioId' => $inputAudioId,
                'audio' => $audio,
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
            if ($thumbnail) {
                $originalName = $thumbnail->getClientOriginalName();
                $originalName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $thumbnail->clientExtension();
                $name = $originalName . "-" . Str::random(10) . "." . $extension;
                $thumbnail->storeAs('threads', $name, 'rrt_storage');
                $params['thumbnail'] = $name;
            } else {
                $params['thumbnail'] = $params['thumbnail_text'] ?? '';
            }
            $params['created_at'] = date('Y-m-d H:i:s');
            $params['user_id'] = rrt_get_user_login('id');
            $params['code'] = $this->model->randomCode();
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
        if ($ids) {
            foreach ($ids as $id) {
                $this->model->deleteItem(['id' => $id], ['task' => 'delete']);
            }
        }
        return [
            'success' => true,
            'message' => 'Content moved to trash',
        ];
    }
    public function aiMasteringCreateToken(){
        try {
            $client = new Client();
            $credentials = [
                'grant_type' => 'client_credentials',
                'expires_in' => 86400,
            ];
            $response = $client->post($this->mastering_endpoint.'/v1/auth/token', [
                'auth' => [$this->api_key_mastering, $this->secret_key_mastering],
                'form_params' => $credentials
            ]);
            $body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            return $body['access_token']??'';
        }catch (\Exception $e){
            return '';
        }
    }
    public function aiMasteringRequest($input,$output,$preset='a',$type = 'preview'){
        $token = $this->aiMasteringCreateToken();
        if(!$token){
            return null;
        }
        $client = new Client();
        $path = '/media/master/preview';
        if($type == 'master'){
            $path = '/media/master';
        }
        try {
            $inputs = [
                'source' => $input,
            ];

            if ($type == 'preview') {
                $inputs['segment'] = [
                    "start" => 10,
                    "duration" => 30
                ];
            }
            $previewResponse = $client->post($this->mastering_endpoint . $path, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'inputs' => [$inputs],
                    'outputs' => [
                        [
                            'destination'=>$output,
                            'master'=>[
                                'dynamic_eq'=>[
                                    'preset'=>$preset
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $previewBody = json_decode($previewResponse->getBody(), true);
            return $previewBody['job_id'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
    public function removeSpecialCharacters($string)
    {
        $result = preg_replace('/[^A-Za-z0-9]/', '', $string);
        return $result;
    }
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $filePath = $file->getPathname();
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->clientExtension();
        $token = $this->aiMasteringCreateToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unable to retrieve token']);
        }
        $client = new Client();
        $presignedUrl = null;
        $removeSpecialFileName = $this->removeSpecialCharacters($fileName);
        $input = "dlb://in/{$removeSpecialFileName}";
        $output = "dlb://out/{$removeSpecialFileName}";
        // resgiter pre_signUrl
        try {
            $response = $client->post($this->mastering_endpoint.'/media/input', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'url' => $input
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $presignedUrl = $body['url']??null;
            if(!$presignedUrl){
                return response()->json(['success' => false, 'message' => 'File upload failed']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating presigned URL: ' . $e->getMessage()]);
        }
        $audioId = null;
        // upload file to pre_signUrl
        try {
            $uploadResponse = $client->put($presignedUrl, [
                'body' => fopen($filePath, 'r'),
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ]);

            if ($uploadResponse->getStatusCode() === 200) {
                // lưu
                $fullFileName = $fileName??'';
                $data = [
                    'id'=>time(),
                    'name'=>$fullFileName,
                    'input'=>$input??'',
                    'output'=>$output??'',
                    'presign_url'=>$presignedUrl??'',
                    'status'=>'waiting',
                    'user_id'=>rrt_get_user_login('id'),
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now()
                ];
                $audioId = $this->masteringAudioModel->saveItem($data, ['task' => 'add-item']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error uploading file: ' . $e->getMessage()]);
        }
        catch (GuzzleException $e) {
            return response()->json(['success' => false, 'message' => 'File upload failed']);
        }
        // diagnose
        try {
            $audio = $this->masteringAudioModel->getItem(['id'=>$audioId],['task'=>'id']);
            $response = $client->post($this->mastering_endpoint.'/media/diagnose', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'input' => $input
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $diagnoseJobId = $body['job_id']??'';
            $audio->diagnose_job_id = $diagnoseJobId;
            $audio->save();
            return response()->json(['success' => true, 'data' => $audio]);
        }catch (\Exception $e){
            return response()->json(['success' => false, 'message' => 'File upload failed']);
        }
    }
    public function mastering(Request $request)
    {
        $params = $request->all();
        $audioId = $params['audioId'] ?? '';
        $preset  = $params['preset']??'a';
        $audio = $this->masteringAudioModel->getItem(['id' => $audioId], ['task' => 'id']);
        $input = $audio->input??'';
        $output =$audio->output??'';
        $previewJobId = $this->aiMasteringRequest($input, $output,$preset,'preview');
        if(!$previewJobId){
            return response()->json(['success' => false, 'message' => 'Process failed']);
        }
        $masterJobId = $this->aiMasteringRequest($input, $output,$preset,'master');
        try {
            $userId = rrt_get_user_login('id');
            $data = [
                'id'=>time(),
                'name'=>$audio->name??'',
                'status'=>'waiting',
                'input_audio_id'=>$audioId,
                'preview_job_id'=>$previewJobId??null,
                'master_job_id'=>$masterJobId??null,
                'user_id'=>$userId,
                'preset'=>$preset
            ];
            $result = $this->model->saveItem($data, ['task' => 'add-item']);
            $user = UserModel::find($userId);
            $before_usage_count = $user->ai_usage_count;
            $amount =  rrt_get_price_role(1) > 0 ? 1 : 0;
            $current_usage_count  = max(0, $before_usage_count - $amount);
            $log = [
                'ai_id' => 1,
                'user_id' => $userId,
                'before_usage_count' => $before_usage_count,
                'amount' => $amount,
                'current_usage_count' => $current_usage_count,
                'mastering_id' => $result->id??'',
            ];
            LogAIUsage::create($log);
            $user->ai_usage_count = $current_usage_count;
            $user->save();
            $audio->preview_job_id = $previewJobId??null;
            $user = UserModel::find($userId);
            return response()->json(['success' => true, 'data' => $data]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return $params;
    }
    public function getProcessPreview(Request $request){
        $params['user_id'] = rrt_get_user_login('id');
        $params['is_map'] = 1;
        $data = $this->model->listItems($params, ['task' => 'processing']);
        return $data ?? [];
    }
    public function checkStatusMastering($jobId, $type = 'preview'){
        $token = $this->aiMasteringCreateToken();
        if (!$token) {
            return null;
        }
        $path = ($type === 'master') ? '/media/master' : '/media/master/preview';
        try {
            $client = new Client();
            $response = $client->get($this->mastering_endpoint . $path . '?job_id=' . $jobId, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $status = $body['status'] ?? null;
            $percentComplete = $body['progress'] ?? 0;
            if ($status) {
                $this->updateStatusMastering($jobId, $type, $percentComplete,$body);
            }

            return $percentComplete;
        } catch (\Exception $e) {
            Log::error('Error checking status for job ' . $jobId . ': ' . $e->getMessage());
            return null;
        }
    }



    public function updateStatusMastering($jobId, $type = 'preview', $percentComplete = 0,$data = []){
        $fillToCheck = ($type === 'master') ? 'master_job_id' : 'preview_job_id';
        $fillToUpdate = ($type === 'master') ? 'process_mastering' : 'process_preview';

        $request = $this->model->where($fillToCheck, $jobId)->first();
        if ($request) {
            if ($request->$fillToUpdate != $percentComplete) {
                if ($request->status === 'succeeded') {
                    return;
                }
                $request->$fillToUpdate = $percentComplete;
                $request->save();
                if($type=='preview' && $percentComplete==100){
                    $request->api_version = $data['api_version']??'';
                    $result = $data['result'] ?? [];
                    $media_info = $result['media_info']??[];
                    $audio = $result['audio'] ?? [];

                    $request->container_kind = ($media_info['container']['kind'])??'';
                    $request->container_duration = ($media_info['container']['duration'])??0;
                    $request->container_size = ($media_info['container']['size'])??0;
                    $request->container_bitrate = ($media_info['container']['bitrate'])??0;
                    $request->audio_codec = ($media_info['audio']['codec'])??'';
                    $request->audio_channels = ($media_info['audio']['channels'])??0;
                    $request->audio_sample_rate = ($media_info['audio']['sample_rate'])??0;
                    $request->audio_duration = ($media_info['audio']['duration'])??0;
                    $request->audio_bitrate = ($media_info['audio']['bitrate'])??0;


                    $request->loudness_measured = ($audio['loudness']['loudness'])??0;
                    $request->loudness_range = ($audio['loudness']['range'])??0;
                    $request->loudness_true_peak = ($audio['loudness']['true_peak'])??0;
                    $request->eq_levels = ($audio['eq']['levels'])??[];


                    $request->stereo_low_width = ($audio['spatial']['stereo_image']['width']['low'])??0;
                    $request->stereo_mid_width = ($audio['spatial']['stereo_image']['width']['mid'])??0;
                    $request->stereo_high_width = ($audio['spatial']['stereo_image']['width']['high'])??0;
                    $request->save();
                }
                if ($request->process_mastering == 100 && $request->process_preview == 100){
                    // download file
                    $request->status = 'succeeded';
                    $request->save();
                    $this->downloadMasterFile($request);
                }
            }
        }
    }
    public function downloadMasterFile($request){
        $client = new Client();
        $token = $this->aiMasteringCreateToken();
        $path = '/media/output';
        $inputAudioId = $request->input_audio_id??'';
        $audio = $this->masteringAudioModel->find($inputAudioId);
        if(!$audio){
            return false;
        }
        $output = $audio->output??'';
        if(!$output){
            return false;
        }
        try {
            $response = $client->get($this->mastering_endpoint . $path . '?url=' . $output, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/octet-stream'
                ]
            ]);
            $fileContent = $response->getBody()->getContents();

            $fileName = 'mastered_' . $request->id . '.mp3';
            $tempFilePath = tempnam(sys_get_temp_dir(), 'master');
            file_put_contents($tempFilePath, $fileContent);
            $storedFilePath = Storage::disk('rrt_storage')->putFileAs('master', $tempFilePath, $fileName);
            $request->mastered_file_url = 'public/uploads/master/' . $fileName;
            $request->save();
            try {
                $userId = $request->user_id ??'';
                $user = UserModel::find($userId);
                if($user){
                    $userRoles = rrt_get_user_role($user)??[];
                    $role = Role::whereIn('slug', $userRoles)->first();
                    $downloadAvaiable = 0;
                    if ($role) {
                        $aiPackage = $role->aiPackages->where('ai_id',1)->first();
                        $downloadAvaiable = $aiPackage->pivot->download_available ?? 7;
                    }
                    $request->expire_download_at = Carbon::now()->addDays($downloadAvaiable);
                    $request->save();
                }
            }catch (\Exception $exception){

            }
            $audio->status = 'prepared';
            $audio->save();
            unlink($tempFilePath);
            return true;

        }catch (\Exception $exception){
            return false;
        }
    }
    public function processAiMastering(){
        $processingRequest = $this->model->listItems([],['task'=>'processing']);
        $requests = $processingRequest['data']??[];
        $processingCount = $processingRequest['processing_count'];
        $updatedJobs = [];
        if (!empty($requests)) {
            foreach ($requests as $r) {
                $previewJobId = $r->preview_job_id ?? '';
                $masterJobId = $r->master_job_id ?? '';
                if($r->status!=='succeeded'){
                    $audio = $this->masteringAudioModel->getItem(['id'=>$r->input_audio_id??""],['task'=>'id']);
                    if($audio){
                        $diagnoseJobId = $audio->diagnose_job_id??'';
                        $diagnose = $this->updateDiagnose($diagnoseJobId, $audio);
                    }
                    $previewPercent = $this->checkStatusMastering($previewJobId); // preview
                    $masterPercent = $this->checkStatusMastering($masterJobId, 'master'); // master

                    $updatedJobs[] = [
                        'previewJobId' => $previewJobId,
                        'previewPercent' => $previewPercent,
                        'masterJobId' => $masterJobId,
                        'masterPercent' => $masterPercent,
                        'diagnose'=>$diagnose ?? null,
                    ];
                }
            }
        }
        if ($processingCount === 0) {
            return response()->json([
                'status'=>true,
                'message'=>'All tasks are done!'
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Processing jobs',
            'updatedJobs' => $updatedJobs
        ]);
    }
    public function updateDiagnose($jobId, $audio = null){
        $token = $this->aiMasteringCreateToken();
        if (!$token) {
            return null;
        }
        $path = '/media/diagnose';
        try {
            $client = new Client();
            $response = $client->get($this->mastering_endpoint . $path . '?job_id=' . $jobId, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $status = $body['status'] ?? null;
            $progress = $body['progress'] ?? 0;
            if ($status === 'Success' && $progress === 100 && $audio) {
                $result = $body['result']??[];
                $media_info=$result['media_info']??[];
                $audio_info = $result['audio']??[];
                $audio->container_kind = ($media_info['container']['kind'])??'';
                $audio->container_duration = ($media_info['container']['duration'])??0;
                $audio->container_size = ($media_info['container']['size'])??0;
                $audio->container_bitrate = ($media_info['container']['bitrate'])??0;
                $audio->audio_codec = ($media_info['audio']['codec'])??'';
                $audio->audio_channels = ($media_info['audio']['channels'])??0;
                $audio->audio_sample_rate = ($media_info['audio']['sample_rate'])??0;
                $audio->audio_duration = ($media_info['audio']['duration'])??0;
                $audio->audio_bitrate = ($media_info['audio']['bitrate'])??0;
                $audio->loudness_measured = ($audio_info['loudness']['measured'])??0;
                $audio->loudness_true_peak = ($audio_info['loudness']['true_peak'])??0;
                $audio->loudness_range = ($audio_info['loudness']['range'])??0;
                $audio->save();
                return $audio;
            }
        } catch (\Exception $e) {
            Log::error('Error checking status for job ' . $jobId . ': ' . $e->getMessage());
            return null;
        }
    }




    public function cronMastering(Request $request)
    {
        $items = $this->model->listItems(['status' => 'waiting'], ['task' => 'admin']);
        $client = new Client();
        foreach ($items as $item) {
            try {
                // Gửi yêu cầu GET đến API với ID của item
                $response = $client->request('GET', $this->apiUrl . "/mastering/{$item->id}", [
                    'verify' => false // Bỏ qua xác minh SSL
                ]);
                $responseBody = json_decode($response->getBody(), true);
                // Kiểm tra status của response
                if (isset($responseBody['status']) && $responseBody['status'] == 'succeeded') {
                    // Cập nhật lại status của item
                    $item->status = 'succeeded';
                    $item->limiting_error_spectrogram_image_url = $responseBody['limiting_error_spectrogram_image_url'] ?? '';
                    $item->save();
                }
            } catch (\Exception $e) {
                // Xử lý lỗi nếu có
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        return $items;
    }
    public function cronAudio(Request $request)
    {
        $items = $this->masteringAudioModel->listItems(['status' => 'prepared'], ['task' => 'admin']);

        $client = new Client();
        foreach ($items as $key => $item) {
            try {
                // Gửi yêu cầu GET đến API với ID của item
                $response = $client->request('GET', $this->apiUrl . "/audio/{$item->id}", [
                    'verify' => false // Bỏ qua xác minh SSL
                ]);
                $responseBody = json_decode($response->getBody(), true);

                // Kiểm tra status của response

                if (isset($responseBody['status']) && $responseBody['status'] != 'waiting') {
                    // Cập nhật lại status của item

                    $item->status = $responseBody['status'];
                    if ($responseBody['status'] == 'failed') {
                        $item->failure_reason = $responseBody['failure_reason'] ?? 'unknown';
                    }
                    if ($responseBody['status'] == 'prepared') {
                        $fillable = $item->getFillable();
                        foreach ($responseBody as $key => $value) {
                            $item->$key = $value;
                        }
                    }
                    $item->save();
                }
            } catch (\Exception $e) {
                // Xử lý lỗi nếu có
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        return $items;
    }
    public function analysis(Request $request) {
        $id = $request->id;
        $client = new Client();
        try {
            $response = $client->request('GET', $this->apiUrl . "/audio/{$id}/analysis", [

                'verify' => false  // Bỏ qua xác minh SSL
            ]);
            $responseBody = json_decode($response->getBody(), true);
            return response()->json($responseBody);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return $id;
    }

    public function getAnalysisData(Request $request)
    {
        $id = $request->id;
        $mastering = $this->model->find($id);
        if (!$mastering) {
            return response()->json([
                'status' => 'error',
                'message' => 'Audio not found'
            ], 404);
        }
        $eqLevels = $mastering->eq_levels?? [];
        $stereoWidth = [
            'low' => $mastering->stereo_low_width ?? 0,
            'mid' => $mastering->stereo_mid_width ?? 0,
            'high' => $mastering->stereo_high_width ?? 0
        ];
        $loudness = [
            'measured' => $mastering->loudness_measured ?? 0,
            'range' => $mastering->loudness_range ?? 0,
            'true_peak' => $mastering->loudness_true_peak ?? 0
        ];
        return response()->json([
            'status' => 'success',
            'data' => [
                'eq_levels' => $eqLevels,
                'stereo_width' => $stereoWidth,
                'loudness' => $loudness
            ]
        ]);
    }
    public function getLinkDownload(Request $request)
    {
        $id = $request->id??'';
        $mastering = $this->model->find($id);

        if (!$mastering || empty($mastering->mastered_file_url)) {
            return response()->json([
                'success' => false,
                'message' => 'Mastered file not found.'
            ]);
        }
        $expireDate = $mastering->expire_download_at ?? null;
        if (!$expireDate || now()->greaterThan(Carbon::parse($expireDate))) {
            return response()->json([
                'success' => false,
                'message' => 'The download link has expired.'
            ]);
        }
        $tokenExpireAt = now()->addMinutes(1);
        $tokenData = [
            'mastering_id' => $mastering->id,
            'expire_at' => $tokenExpireAt->timestamp,
        ];
        $token = encrypt(json_encode($tokenData));
        return response()->json([
            'success' => true,
            'downloadUrl' => rrt_route('public/studio/mastering/downloadMasteredFile',['token' => $token])
        ]);
    }
    public function downloadMasteredFile(Request $request){
        try {
            $token = $request->token??'';
            if(!$token){
                return response()->json([
                    'status'=>false,
                    'message'=>"Invalid Token"
                ]);
            }
            $tokenData = json_decode(decrypt($token), true);

            if (now()->timestamp > $tokenData['expire_at']) {
                return response()->json([
                    'success' => false,
                    'message' => 'The download link has expired.'
                ]);
            }
            $masteringId = $tokenData['mastering_id']?? '';
            $mastering = $this->model->find($masteringId);
            $masteredFileUrl = $mastering->mastered_file_url??null;
            if(!$mastering || !$masteredFileUrl){
                return response()->json([
                    'status'=>false,
                    'message'=>"File not found"
                ]);
            }
            if (!file_exists($masteredFileUrl)) {
                return response()->json([
                    'status' => false,
                    'message' => "File does not exist on the server"
                ]);
            }
            return response()->download($masteredFileUrl);
        }
        catch (Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.'
            ]);
        }
    }
}
