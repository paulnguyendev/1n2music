<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Subscription;
use App\Http\Controllers\Controller;
#Model
use App\Models\MoodsModel;
use App\Models\PlatformModel;
use App\Models\UserModel;
use App\Models\MusicDistributionModel as MainModel;
use App\Models\GenresModel;
use App\Models\MusicDistributionShopModel;
use App\Models\MusicDistributionTrackModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioReleaseController extends Controller
{
    private $pathViewController     = "studio.pages.release";
    private $controllerName         = "public/studio/release";
    private $model;
    private $genresModel;
    private $musicDistributionShopModel;
    private $musicDistributionTrackModel;
    private $params                 = [];
    private $platform;
    function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->genresModel = new GenresModel();
        $this->musicDistributionShopModel = new MusicDistributionShopModel();
        $this->musicDistributionTrackModel = new MusicDistributionTrackModel();
        $this->platform = new PlatformModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $code = $this->model->randomCode();
        $user_id  = rrt_get_user_login('id');
        $items = $this->userModel::find($user_id)->tracks()->with('file')->where('status', 'draft')->orderBy('id', 'desc')->skip(0)->take(3)->get();
        $type = $request->type ?? "single";
        return view(
            "{$this->pathViewController}/index",
            [
                'code' => $code,
                'items' => $items,
                'type' => $type,
            ]
        );
    }
    public function form(Request $request)
    {
        $code = $request->code;
        $type = $request->type;
        $userId = rrt_get_user_login('id');

        // Check distribution limits based on subscription tier
        $user = UserModel::find($userId);
        if ($user) {
            $currentYear = date('Y');
            $yearStart = $currentYear . '-01-01 00:00:00';

            // Check which distribution subscription user has
            $hasDistributionBasic = $user->subscriptionOrders()
                ->where('subscription_id', 4)
                ->exists();

            $hasDistributionPro = $user->subscriptionOrders()
                ->where('subscription_id', 2)
                ->exists();

            $hasPublishing = $user->subscriptionOrders()
                ->where('subscription_id', 1)
                ->exists();

            // Define limits based on subscription tier
            $limits = null;
            if ($hasDistributionBasic) {
                $limits = ['single' => 2, 'album' => 2, 'plan' => 'Distribution Basic'];
            } elseif ($hasDistributionPro) {
                $limits = ['single' => 4, 'album' => 2, 'plan' => 'Distribution Pro'];
            } elseif ($hasPublishing) {
                $limits = ['single' => 4, 'album' => 2, 'plan' => 'Publishing'];
            }

            // Enforce limits if applicable
            if ($limits) {
                // Count singles and albums created this year
                $singlesThisYear = $this->model
                    ->where('user_id', $userId)
                    ->where('type', 'single')
                    ->where('created_at', '>=', $yearStart)
                    ->count();

                $albumsThisYear = $this->model
                    ->where('user_id', $userId)
                    ->where('type', 'album')
                    ->where('created_at', '>=', $yearStart)
                    ->count();

                // Check if user has reached the limit for the type they're trying to create
                if ($type === 'single' && $singlesThisYear >= $limits['single']) {
                    return redirect()->back()->with('error', "You have reached the limit of {$limits['single']} singles per year for the {$limits['plan']} plan");
                }

                if ($type === 'album' && $albumsThisYear >= $limits['album']) {
                    return redirect()->back()->with('error', "You have reached the limit of {$limits['album']} albums per year for the {$limits['plan']} plan");
                }
            }
        }

        $this->model->saveItem(['code' => $code, 'user_id' => $userId, 'type' => $type], ['task' => 'add-item']);
        return redirect()->route($this->controllerName . "/delivery", ['code' => $code, 'type' => $type, 'locale' => app()->getLocale()]);
        $type = $request->type;
        $title = ucfirst($type) . " Release";
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title
            ]
        );
    }
    public function delivery(Request $request)
    {
        $type = $request->type;
        $code = $request->code;
        $params['type'] = $type;
        $params['code'] = $code;
        $genres = $this->genresModel->listItems([], ['task' => 'list']);
        $platforms = $this->platform->orderBy('id','desc')->get();
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $itemGenres = [];
        $itemShopes = [];
       
        if ($item) {
            $itemGenres = $item->listGenres()->pluck('genre_id')->toArray();
            $itemShopes = $item->listPlatform()->pluck('platform_id')->toArray();
        }
        return view(
            "{$this->pathViewController}/delivery",
            [
                'code' => $code,
                'type' => $type,
                'genres' => $genres,
                'shopes' => $platforms,
                'params' => $params,
                'item' => $item,
                'itemGenres' => $itemGenres,
                'itemShopes' => $itemShopes,
            ]
        );
    }
    public function release(Request $request)
    {
        $type = $request->type;
        $code = $request->code;
        $params['type'] = $type;
        $params['code'] = $code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $genres = $this->genresModel->listItems([], ['task' => 'list']);

        $itemGenres = $item->genre_id??null;
        $subGenres = collect();
        if($itemGenres){
            $subGenres = GenresModel::where('parent_id',$itemGenres)->get();
        }
        $itemMoods = $item->moods->pluck('id')->toArray();
        $moods = MoodsModel::orderBy('id','desc')->get();
        return view(
            "{$this->pathViewController}/release ",
            [
                'code' => $code,
                'type' => $type,
                'params' => $params,
                'item' => $item,
                'genres' => $genres,
                'itemMoods' => $itemMoods,
                'subGenres'=>$subGenres,
                'moods'=>$moods
            ]
        );
    }
    public function tracks(Request $request)
    {
        $type = $request->type;
        $code = $request->code;
        $genres = $this->genresModel->listItems([], ['task' => 'list']);
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $params['type'] = $type;
        $params['code'] = $code;
        $tracks = $item->listTracks()->orderBy('id', 'desc')->get();
        $upload_limit_reached = rrt_limit_upload(count($tracks), $type);
        return view(
            "{$this->pathViewController}/track",
            [
                'code' => $code,
                'type' => $type,
                'genres' => $genres,
                'params' => $params,
                'item' => $item,
                'tracks' => $tracks,
                'upload_limit_reached' => $upload_limit_reached,
            ]
        );
    }
    public function list(Request $request)
    {
        $result = [];
        $type = $request->type;
        $draw = $request->draw ?? 1;
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $search = $request->search ?? [];
        $searchValue = $search['value'] ?? "";
        $user_id = rrt_get_user_login('id');
        $params = [
            'start' => $start,
            'type' => $type,
            'length' => $length,
            'is_map' => '1',
            'with' => '1',
            'user_id' => $user_id,
            'controllerName' => $this->controllerName,
            'search' => $searchValue,
        ];
        $data = $this->model->listItems($params, ['task' => 'list']);
        $dataAll = $this->model->listItems(['type' => $type,'user_id' => $user_id,'search' => $searchValue], ['task' => 'list']);
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
    public function save(Request $request)
    {
        $params = $request->all();
//        dd($params);
        $code = $request->code;
        $type = $request->type;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $id = $item['id'] ?? "";
        $releaseDate = $params['releaseDate'] ?? "";
        $secondReleaseDate = $params['secondReleaseDate'] ?? "";
        if ($releaseDate) {
            $params['release_date'] = $releaseDate;
        }
        if ($secondReleaseDate) {
            $params['2nd_release_date'] = $secondReleaseDate;
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
    public function getSubGenre(Request $request)
    {
        $genreId = $request->input('genre_id');
        $subgenres = GenresModel::where('parent_id', $genreId)->orderBy('order_number', 'asc')->get();

        return response()->json([
            'success' => true,
            'subgenres' => $subgenres
        ]);
    }
    public function saveAnotherTable($table, $id, $data)
    {
        $model = null;
        $params = [];
        $item = $this->model::find($id);
        $relation = null;
        switch ($table) {
            case 'shopes':
                $relation = $item->platform();
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
    public function upload(Request $request)
    {
        $params = $request->all();
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $id = $item['id'] ?? "";
        $user_id = rrt_get_user_login('id');
        $file = $request->file('track_file');
        $originalName = $file->getClientOriginalName();
        $originalName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->clientExtension();
        $thumbnail = $originalName . "-" . Str::random(10) . "." . $extension;
        $file->storeAs('release', $thumbnail, 'rrt_storage');
        $params['id'] = $id;
        $params['thumbnail'] = $thumbnail;
        unset($params['type']);
        $action = $this->model->saveItem($params, ['task' => 'edit-item']);
        $result = [
            'files' => $file,
            'params' => $params,
            'url' => url("public/uploads/release/{$thumbnail}"),
            'name' => $thumbnail,
            'originalName' => $originalName,
        ];
        return $result;
    }
    public function uploadTrack(Request $request)
    {

        $params = $request->all();
        $code = $request->code;
        $params['music_distribution_id'] = $request->music_distribution_id;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);

        $id = $item['id'] ?? "";
        $user_id = rrt_get_user_login('id');
        $file = $request->file('track_file');
        $check_sub =   Subscription::getSubscription(2);
        $maxFileSizeInMB = 10;
        if ($check_sub) {
            $maxFileSizeInMB = 30;
        }
        $fileSize = $file->getSize();

        $tracks = $item->listTracks;

        $maxFileSizeInBytes = $maxFileSizeInMB * 1024 * 1024;
        $errors = [];
        if ($fileSize > $maxFileSizeInBytes) {
            $errors['errors'] = 'Uploaded file exceeds ' . $maxFileSizeInMB . 'MB size';
            return $errors;
        }
        // $max_size_album = 300;
        // $max_size_all_track = 0;
        // foreach ($tracks as $key => $track) {
        //     $max_size_all_track += $track->file_size;
        // }

        // if ($max_size_album < $max_size_all_track) {
        //     $errors['errors'] = 'Uploaded file exceeds ' . $max_size_album . 'MB size';
        //     return $errors;
        // }


        $originalName = $file->getClientOriginalName();
        $originalName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->clientExtension();
        $fileName =  Str::random(10) . "." . $extension;
        $file->storeAs('release', $fileName, 'rrt_storage');
        $params['music_distribution_id'] = $id;
        $params['user_id'] = $user_id;
        $params['file'] = $fileName;
        $code = $this->musicDistributionTrackModel->randomCode();
        $params['code'] = $code;
        $params['size'] = $fileSize;
        $result = [
            'files' => $file,
            'params' => $params,
            'url' => url("public/uploads/release/{$fileName}"),
            'name' => $fileName,
            'originalName' => $originalName,

        ];
        return $result;
    }


    public function checkSizeAlbum(Request $request)
    {
        $params = $request->all();
        $code = $request->code;

        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $tracks = $item->listTracks;
        $max_size_album = 300;
        $max_size_all_track = 0;
        foreach ($tracks as $key => $track) {
            $max_size_all_track += $track->file_size;
        }

        if ($max_size_album < $max_size_all_track) {
            $errors['errors'] = 'The album has exceeded  ' . $max_size_album . 'MB size';
            $errors['status'] = 400;
            return $errors;
        }
        return response()->json(['status' => 200]);
    }


    public function saveTrack(Request $request)
    {
        $params = $request->all();

        $user_id = rrt_get_user_login('id');

        $params['user_id'] = $user_id;
        $id = $params['id'] ?? "";
        if (!$id) {
            $code = $this->musicDistributionTrackModel->randomCode();
            $params['code'] = $code;
        }
        $task = $id ? "edit-item" : "add-item";
        $this->musicDistributionTrackModel->saveItem($params, ['task' => $task]);
        return $params;
    }
    public function deleteTrack(Request $request)
    {
        $id = $request->track_id;
        $params = $request->all();
        $item =   $this->musicDistributionTrackModel::find($id);
        if ($item) {
            $this->musicDistributionTrackModel->deleteItem(['id' => $id], ['task' => 'delete']);
        }
        return $params;
    }
}
