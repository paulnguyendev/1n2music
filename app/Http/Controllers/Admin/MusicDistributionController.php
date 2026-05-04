<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
#Model
use App\Models\GenresModel;
use App\Models\LogStreamCountModel;
use App\Models\MoodsModel;
use App\Models\MusicDistributionShopModel;
use App\Models\MusicDistributionTrackModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\PlatformModel;
use App\Models\User;
use App\Models\UserModel;
use Carbon\Carbon;
use App\Models\TrackModel;
use App\Models\MusicDistributionModel as MainModel;
use App\Models\TrackTrendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
#Mail
use Illuminate\Support\Facades\View;
#Helper
class MusicDistributionController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $planOrderModel;
    private $genresModel;
    private $musicDistributionShopModel;
    private $musicDistributionTrackModel;
    private $musicDistributionPlatformModel;
    private $title;
    private $params = [];
    private $track;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->planModel = new PlanModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/music-distribution";
        $this->pathViewController = "{$this->prefix}.pages.musicdistribution";
        $this->genresModel = new GenresModel();
        $this->musicDistributionShopModel = new MusicDistributionShopModel();
        $this->musicDistributionTrackModel = new MusicDistributionTrackModel();
        $this->musicDistributionPlatformModel = new PlatformModel();
        //$this->track = new TrackModel();
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        return view(
            "{$this->pathViewController}/index",
            [
                'type'=>$request->type ??'',
                'platform'=>$request->platform??''
            ]
        );
    }
    public function save(Request $request)
    {
        $params = $request->all();
//        dd($params);
        $code = $request->code;
        $type = $request->type;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $id = $item['id'] ?? "";
        $releaseDate = $params['releaseDate'] ?? "";
        if ($releaseDate) {
            $params['release_date'] = $releaseDate;
        }
        $shopIds = $params['shopIds'] ?? [];
        $genreIds = $params['genreIds'] ?? [];
        $moods = $params['moods'] ?? [];
        $producers = $params['producers'] ?? [];
        $composers = $params['composers'] ?? [];
        $lyricists = $params['lyricists'] ?? [];
        $params['id'] = $id;
        $params['updated_at'] = date('Y-m-d H:i:s');
        $this->model->saveItem($params, ['task' => 'edit-item']);
        if ($shopIds) {
            $params['shopes_save'] = $this->saveAnotherTable("shopes", $id, $shopIds);
        }
        if ($genreIds) {
            $params['genreIds_save'] = $this->saveAnotherTable("genres", $id, $genreIds);
        }
        if ($moods) {
            $params['moods_save'] = $this->saveAnotherTable("moods", $id, $moods);
        }
        return $params;
    }
    public function saveAnotherTable($table, $id, $data)
    {
        $model = null;
        $params = [];
        $item = $this->model::find($id);
        $relation = null;
        switch ($table) {
            case 'shopes':
                $relation = $item->shopes();
                break;
            case 'genres':
                $relation = $item->genres();
                break;
            case 'moods':
                $relation = $item->moods();
                break;
            default:
                break;
        }
        if ($relation) {
            $relation->sync($data);
        }
        return $params;
    }
    function list(Request $request)
    {
        $result = [];
        $type = $request->type??'';
        $platform = $request->platform??'';
        $draw = $request->draw ?? 1;
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $search = $request->search ?? [];
        $searchValue = $search['value'] ?? "";
        $params = [
            'start' => $start,
            'type' => $type,
            'platform'=>$platform,
            'length' => $length,
            'is_map' => '1',
            'with' => '1',
            'controllerName' => $this->controllerName,
            'area' => 'admin'
        ];
        $data = $this->model->listItems($params, ['task' => 'list']);
        $data = $data->map(function ($item) {
            $id = $item->id;
            $item->route_update = rrt_route( $this->controllerName . "/update", ['id' => $id]);
            return $item;
        })->filter(function ($item) {
            return $item->totalTrack > 0;
        })->values();
        
        $dataAll = $this->model->listItems(['type' => $type], ['task' => 'admin']);
        if($platform){
            $dataAll = $this->model->listItems(['platform'=>$platform],['task'=>'list']);
        }
        $dataAll = $dataAll->filter(function ($item) {
            return $item->totalTrack > 0;
        })->values();
        
        $recordsFiltered = count($dataAll);
        $recordsTotal = count($data);
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
            $status = $params['status'] ?? "denied";
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
    public function delivery(Request $request)
    {
        $type = $request->type;
        $code = $request->code;
        $params['type'] = $type;
        $params['code'] = $code;
        $genres = $this->genresModel->listItems([], ['task' => 'list']);
        $shopes = $this->musicDistributionPlatformModel->orderBy('id','desc')->get();
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $itemGenres = [];
        $itemShopes = [];
        $itemPlatforms=[];
        if ($item) {
            $itemGenres = $item->listGenres()->pluck('genre_id')->toArray();
            $itemShopes = $item->listPlatform()->pluck('platform_id')->toArray();
            $itemPlatforms = PlatformModel::whereIn('id',$itemShopes??[])->get();
        }
        $moods = MoodsModel::orderBy('id','desc')->get();
        $platforms = PlatformModel::latest('id')->get();
        $tracks = $item->listTracks()->orderBy('id', 'desc')->get();
        $user = UserModel::find($item->user_id);
        $itemGenre = $item->genre_id??null;
        $subGenres = collect();
        if($itemGenres){
            $subGenres = GenresModel::where('parent_id',$itemGenre)->get();
        }
        return view(
            "{$this->pathViewController}/delivery",
            [
                'user'=>$user,
                'code' => $code,
                'type' => $type,
                'genres' => $genres,
                'shopes' => $shopes,
                'params' => $params,
                'item' => $item,
                'itemGenres' => $itemGenres,
                'itemShopes' => $itemShopes,
                'platforms'=>$platforms,
                'tracks'=>$tracks,
                'itemPlatforms'=>$itemPlatforms,
                'moods'=>$moods,
                'subGenres'=>$subGenres
            ]
        );
    }
    public function getSubGenre(Request $request)
    {
        $genreId = $request->input('genre_id');
        $subgenres = GenresModel::where('parent_id', $genreId)->orderBy('order_number', 'asc')->get();
        return response()->json([
            'success' => true,
            'subgenres' => $subgenres
        ]);
    }
    public function updateStreamCount(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'music_distribution_id' => 'required|integer|exists:rrt_music_distribution,id',
                'platform_id' => 'required|integer|exists:rrt_platforms,id',
                'stream_count' => 'required|integer|min:0',
                'update_time' => 'required|date',
            ], [
                'music_distribution_id.required' => 'The music distribution ID is required.',
                'music_distribution_id.integer' => 'The music distribution ID must be an integer.',
                'music_distribution_id.exists' => 'The selected music distribution ID does not exist.',
                'platform_id.required' => 'The platform ID is required.',
                'platform_id.integer' => 'The platform ID must be an integer.',
                'platform_id.exists' => 'The selected platform ID does not exist.',
                'stream_count.required' => 'The stream count is required.',
                'stream_count.integer' => 'The stream count must be a number.',
                'stream_count.min' => 'The stream count must be at least 0.',
                'update_time.required'=>'Update time is required',
                'update_time.date'=>'Update time invalid format',
            ]);
            $user_id = $request->user_id??'';
            $platform = PlatformModel::find($validatedData['platform_id']);
            $streamCount = $validatedData['stream_count'] ?? 0;
            $revenue = 0;
            if ($platform){
                $setting = $platform->settings??[];
                $settingStreamCount = $setting['stream_count']??null;
                $settingRevenue = $setting['revenue']??null;
                if ($settingStreamCount && $settingRevenue){
                    $revenue = ($streamCount * $settingRevenue)/$settingStreamCount;
                }
            }
            LogStreamCountModel::create([
                'music_distribution_id' => $validatedData['music_distribution_id'] ?? '',
                'platform_id' => $validatedData['platform_id'] ?? '',
                'user_id'=>$user_id,
                'stream_count' => $streamCount,
                'revenue'=>$revenue,
                'created_at' => $validatedData['update_time'],
                'updated_at' => $validatedData['update_time'],
            ]);
            return response()->json(['message' => 'Stream count added successfully.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->first()[0];
            return response()->json(['message' => $firstError], 422);
        }
    }
    public function renderStreamCountChart(Request $request)
    {
        $musicDistributionId = $request->input('music_distribution_id');
        $logs = LogStreamCountModel::with('platforms')
            ->where('music_distribution_id', $musicDistributionId)
            ->selectRaw('platform_id, MONTH(created_at) as month, SUM(stream_count) as total_stream_count')
            ->groupBy('platform_id', 'month')
            ->orderBy('month')
            ->get();
        $monthlyData = [];
        $platformNames = [];
        foreach ($logs as $log) {
            $monthName = Carbon::create()->month($log->month)->format('F');
            $platformName = $log->platforms->name ?? 'Unknown Platform';
            $monthlyData[$monthName][$platformName] = $log->total_stream_count;
            if (!in_array($platformName, $platformNames)) {
                $platformNames[] = $platformName;
            }
        }
        return response()->json([
            'monthlyData' => $monthlyData,
            'platformNames' => $platformNames
        ]);
    }
    public function getRevenueChart(Request $request)
    {
        $musicDistributionId = $request->input('music_distribution_id');
        $logs = LogStreamCountModel::with('platforms')
            ->where('music_distribution_id', $musicDistributionId)
            ->selectRaw('platform_id, MONTH(created_at) as month, SUM(revenue) as total_revenue')
            ->groupBy('platform_id', 'month')
            ->orderBy('month')
            ->get();
        $revenues = [];
        $platformNames = [];
        foreach ($logs as $log) {
            $monthName = Carbon::create()->month($log->month)->format('F');
            $platformName = $log->platforms->name ?? 'Unknown Platform';
            $revenues[$monthName][$platformName] = $log->total_revenue;
            if (!in_array($platformName, $platformNames)) {
                $platformNames[] = $platformName;
            }
        }
        return response()->json([
            'revenues' => $revenues,
            'platformNames' => $platformNames
        ]);
    }
    public function getLogStream(Request $request){
        $music_distribution_id = $request->music_distribution_id??'';
        $draw = $request->draw ?? 1;
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $logs = LogStreamCountModel::with('platforms')
            ->where('music_distribution_id',$music_distribution_id)
            ->offset($start)
            ->limit($length)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalRecords = LogStreamCountModel::count();
        $data = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'code' => $log->platform_id??'-',
                'platform' => $log->platforms->name ?? 'Unknown',
                'stream_count' => $log->stream_count??0,
                'revenue' => $log->revenue??0,
                'created_at' => $log->created_at??'-',
            ];
        });
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }
    public function deleteLogStream(Request $request){
        $id = $request->id??"";
        $log = LogStreamCountModel::find($id);
        if ($log){
            $log->delete();
        }
        return response()->json(['message' => 'Deleted successfully']);
    }
    public function export(Request $request) {
        $result = [];
        $type = $request->type??'';
        $platform = $request->platform??'';
        $draw = $request->draw ?? 1;
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $search = $request->search ?? [];
        $searchValue = $search['value'] ?? "";
        $params = [
            'start' => $start,
            'type' => $type,
            'platform'=>$platform,
            'length' => $length,
            'is_map' => '1',
            'with' => '1',
            'controllerName' => $this->controllerName,
            'area' => 'admin'
        ];
        $data = $this->model->listItems($params, ['task' => 'list']);
        $data = $data->map(function ($item) {
            $id = $item->id;
            $item->route_update = rrt_route( $this->controllerName . "/update", ['id' => $id]);
            return $item;
        });
       

        $filename = 'distributions_data_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        $file = fopen($tempFile, 'w');
        fputcsv($file, [
            'Code',
            'Date',
            'Status',
            'Release name',
            'Genre',
            'Shops',
            'Release date',
            'Total Tracks',
        ]);
        foreach ($data as $item) {
            fputcsv($file, [
                $item->code,
                $item->created_at,
                $item->status,
                $item->name ?? '-',
                $item->generes ?? '-',
                $item->shopes ?? '-',
                $item->release_date,
                $item->totalTrack
            ]);
        }
        fclose($file);

        return response()->download($tempFile, $filename, $headers)->deleteFileAfterSend(true);
    }
} 
