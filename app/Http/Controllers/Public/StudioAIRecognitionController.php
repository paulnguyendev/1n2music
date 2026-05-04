<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\LogAIUsage;
use App\Models\RecognitionModel as MainModel;
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

class StudioAIRecognitionController extends Controller
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
    private $recognition_endpoint;
    private $api_key_recognition;
    private $secret_key_mastering;
    private $platforms = [
        'musicbrainz',
        'apple_music',
        'spotify',
        'deezer',
        'napster'
    ];
    public function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->noticeLogModel = new NoticeLogModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
        $this->controllerName = "public/{$this->prefix}/recognition";
        $this->pathViewController = "{$this->prefix}.pages.recognition";
        $this->apiUrl  = env('API_RECOGNITION');
        $this->recognition_endpoint=env('ENDPOINT_API_RECOGNITION');
        $this->api_key_recognition=env('API_KEY_RECOGNITION');
        $this->secret_key_mastering=env('SECRET_KEY_RECOGNITION');
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $all_platforms = [
            'apple_music'   => 'Apple Music',
            'spotify'       => 'Spotify',
            'deezer'        => 'Deezer',
            'napster'       => 'Napster'
        ];
        // ai_id 1 ai mastering
        $textPriceRole =  rrt_get_text_price_role(2);
        $priceRole = rrt_get_price_role(2);
        $usage_count = rrt_get_user_ai_usage(\App\Models\AIService::AIServiceAIRecognition);
        $aiStudio = true;
        return view(
            "{$this->pathViewController}/index",
            [
                'aiStudio'      => $aiStudio,
                "textPriceRole" => $textPriceRole,
                "priceRole" => $priceRole,
                "platforms"     => $all_platforms,
                "usage_count"   => $usage_count,
            ]
        );
    }
    public function form(Request $request)
    {
        $id = $request->id;
        $item = $this->model->find($id);
        $item->platforms = @explode(',',$item->platforms);
        $title = 'View detail #'.$id;
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
                'id' => $id,
            ]
        );
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
    public function aiRecognitionCreateToken(){
        try {
            return $this->api_key_recognition;
        }catch (\Exception $e){
            return '';
        }
    }
    public function upload(Request $request)
    {
        $platforms = $request->input('platforms');
        $platforms = implode(',',$platforms);
        if($platforms == 'all'){
            $platforms = implode(',',$this->platforms);
        }
        // Save to server
        $file       = $request->file('file');
        $fileExtension = $file->clientExtension();
        $fileName   = 'recognition_' . time() . '.'.$fileExtension;
        $tempFilePath = tempnam(sys_get_temp_dir(), 'master');
        $storedFilePath = Storage::disk('rrt_storage')->putFileAs('recognition', $file, $fileName);
        if($storedFilePath){
            $recognition_file_url = 'public/uploads/master/' . $fileName;
            $recognition_data = [
                'fileUrl'           => $recognition_file_url,
                'fileName'          => $fileName,
                'storedFilePath'    => $storedFilePath,
                'platforms'         => $platforms
            ];
            session()->put('recognition_data',$recognition_data);
            return response()->json(['success' => true, 'message' => 'File upload successfully']);
        }else{
            return response()->json(['success' => false, 'message' => 'File upload failed']);
        }
    }

    public function processAi(){
        $token = $this->aiRecognitionCreateToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unable to retrieve token']);
        }
        $recognition_data = session()->get('recognition_data');
        if (!$recognition_data) {
            return response()->json(['success' => false, 'message' => 'Error creating recognize']);
        }
        $client = new Client();
        $platforms = $recognition_data['platforms'];
        try {
            $response = $client->post($this->recognition_endpoint.'/recognize', [
                'multipart' => [
                    [
                        'name'     => 'api_token',
                        'contents' => $token,
                    ],
                    [
                        'name'     => 'file',
                        'contents' => fopen(Storage::disk('rrt_storage')->path($recognition_data['storedFilePath']), 'r'),
                        'filename' => $recognition_data['fileName'],
                    ],
                    [
                        'name'     => 'return',
                        'contents' => $platforms,
                    ],
                ],
            ]);

            session()->forget('recognition_data');

            $res = json_decode($response->getBody(), true);
            if($res['status'] != 'success'){
                return response()->json(['success' => false, 'message' => 'Error creating recognize']);
            }else{
                $userId = rrt_get_user_login('id');
                // Save to database
                $dataRes = $res['result'];
                $saveData = [
                    'artist' => @$dataRes['artist'],
                    'title' => @$dataRes['title'],
                    'album' => @$dataRes['album'],
                    'release_date' => @$dataRes['release_date'],
                    'label' => @$dataRes['label'],
                    'timecode' => @$dataRes['timecode'],
                    'song_link' => @$dataRes['song_link'],
                    'user_id' => $userId,
                    'platforms' => $platforms
                ];
                foreach( $this->platforms as $platform_key ){
                    if( isset($dataRes[$platform_key]) ){
                        $saveData[$platform_key] = json_encode($dataRes[$platform_key]);
                    }
                }
                $recognition = MainModel::create($saveData);
                if($recognition){
                    // Minus ai_usage_count_reconize
                    $user = UserModel::find($userId);
                    $before_usage_count = $user->ai_usage_count_reconize;
                    $amount = rrt_get_price_role(2) > 0 ? 1 : 0;
                    $current_usage_count  = max(0, $before_usage_count - $amount);
                    $log = [
                        'ai_id' => 1,
                        'user_id' => $userId,
                        'before_usage_count' => $before_usage_count,
                        'amount' => $amount,
                        'current_usage_count' => $current_usage_count,
                        'recognition_id' => $recognition->id??'',
                    ];
                    LogAIUsage::create($log);
                    $user->ai_usage_count_reconize = $current_usage_count;
                    $user->save();
                }
                return response()->json(['success' => true, 'message' => 'Recognize successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating recognize: ' . $e->getMessage()]);
        }
    }
}
