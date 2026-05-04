<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\TrackModel as MainModel;
use App\Models\UserModel;
use App\Models\TrackFileModel;
use App\Models\TrackTagModel;
use App\Models\GenresModel;
use App\Models\MoodsModel;
use App\Models\InvsModel;
use App\Models\TrackGenresModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioSoundKitController extends Controller
{
    private $pathViewController     = "studio.pages.content";
    private $controllerName         = "public/studio/soundKit";
    private $model;
    private $userModel;
    private $trackFileModel;
    private $genresModel;
    private $moodModel;
    private $invModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->trackFileModel = new TrackFileModel();
        $this->genresModel = new GenresModel();
        $this->moodModel = new MoodsModel();
        $this->invModel = new InvsModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $user_id  = rrt_get_user_login('id');
        $items = $this->userModel::find($user_id)->tracks()->with('file')->orderBy('id', 'desc')->paginate(20);
        $code = $this->model->randomCode();
        return view(
            "{$this->pathViewController}/index",
            [
                'code' => $code,
                'items' => $items,
            ]
        );
    }
    public function filter(Request $request)
    {
        $params = $request->all();
        $status = isset($params['status']) ? $params['status'] : "";
        $visibility = isset($params['visibility']) ? $params['visibility'] : "";
        $user_id  = rrt_get_user_login('id');
        $items = $this->userModel::find($user_id)->tracks()->with('file');
        if ($status) {
            $items = $items->where('status', $status);
        }
        if ($visibility) {
            $items = $items->where('visibility', $visibility);
        }
        $items = $items->orderBy('id', 'desc')->paginate(20);
        $xhtml = view($this->pathViewController . "/template/list_track")->with('items', $items)->render();
        $params['items'] = $items;
        $params['xhtml'] = $xhtml;
        return $params;
    }
    public function delete(Request $request)
    {
        $params = $request->all();
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $this->model->deleteItem($item, ['task' => 'delete']);
        $user_id  = rrt_get_user_login('id');
        $items =  $this->userModel::find($user_id)->tracks()->with('file')->orderBy('id', 'desc')->paginate(20);
        $xhtml = view($this->pathViewController . "/template/list_track")->with('items', $items)->render();
        $params['code'] = $code;
        $params['item'] = $item;
        $params['xhtml'] = $xhtml;
        return $params;
    }
    public function detail(Request $request)
    {
        $params = $request->all();
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $contracts = $item->listContracts()->get();
        $contractsTotal = $contracts->count();
        $params['name'] = $item['name'] ?? "";
        $params['release_date'] = $item['release_date'] ?? "";
        $params['bpm_number'] = $item['bpm_number'] ?? 0;
        $params['description'] = $item['description'] ?? "";
        $params['visibility_text'] = 'Public - Anyone can view and purchase';
        $trackTypeId = $item['track_type_id'] ? $item['track_type_id'] :  'BEAT';
        $params['track_type_id'] = $trackTypeId;
        $trackKeyId  = $item['track_key_id'] ? $item['track_key_id'] : "";
        $params['track_key_id'] = $trackKeyId;
        $tags = $item->listTags()->pluck('name')->toArray();
        $tags = $tags ? implode(",", $tags) : "";
        $params['tags'] = $tags;
        $genres = $item->listGenres()->pluck('genre_id')->toArray();
        $params['genres[]'] =  $genres ?  $genres : [];
        $moods = $item->listMoods()->pluck('mood_id')->toArray();
        $params['moods[]'] = $moods ? $moods : [];
        $invs = $item->listInvs()->pluck('invs_id')->toArray();
        $params['invs[]'] = $invs ? $invs : [];
        if ($contractsTotal > 0) {
            foreach ($contracts as $contract) {
                $contactSettingId = $contract['contact_setting_id'];
                $contractEnabled = $contract['enabled'] ?? 0;
                $contractPrice = $contract['price'] ?? 0;
                $params["contracts_tracks[{$contactSettingId}][enabled]"] = $contractEnabled;
                $params["contracts_tracks[{$contactSettingId}][price]"] = $contractPrice;
            }
        }
        $params['contracts'] = $contracts;
        $params['contractsTotal'] = $contractsTotal;
        $files = $item->file()->get();
        $params['thumbnail_url'] = $this->model->getFileInfo($files, 'thumbnail');
        $params['unTaggedMp3'] = $this->model->getFileInfo($files, 'unTaggedMp3', 'name');
        $params['stems'] = $this->model->getFileInfo($files, 'stems', 'name');
        $params['taggedMp3'] = $this->model->getFileInfo($files, 'taggedMp3', 'name');
        return $params;
    }
    public function save(Request $request)
    {

        $params = $request->all();
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $id = $item['id'] ?? "";
        $params['code'] = $code;
        $releaseDate = $params['release_date'] ?? "";
        $params['release_date'] = $params['release_date'] ? date("Y-m-d H:i:s", strtotime($params['release_date'] ?? "")) : NULL;
        $tags = $params['tags'] ? $params['tags'] : "";
        $tags = $tags ? explode(",", $tags) : [];
        $params['id'] = $id;
        $params['updated_at'] = date('Y-m-d H:i:s');
        $status = $params['status'] ?? "draft";
        $curretnStatus = $item['status'] ?? "";
        $params['status'] = $curretnStatus == 'public' ? 'public' : $status;
        $this->model->saveItem($params, ['task' => 'edit-item']);
        $params['tags'] = $tags;
        $params['tags_save'] = $this->saveAnotherTable("tags", $id, $tags);
        $genres = $params['genres'] ?? [];
        $params['genres_save'] = $this->saveAnotherTable("genres", $id, $genres);
        $moods = $params['moods'] ?? [];
        $params['moods_save'] =  $this->saveAnotherTable("moods", $id, $moods);
        $invs = $params['invs'] ?? [];
        $params['invs_save'] = $this->saveAnotherTable("invs", $id, $invs);
        $contractsTracks = $params['contracts_tracks'] ?? [];
        $params['contracts_save'] = $this->saveAnotherTable("contract", $id, $contractsTracks);
        return $params;
    }
    public function saveAnotherTable($table, $track_id, $data)
    {
        $model = null;
        $params = [];
        $track = $this->model::find($track_id);
        $relation = null;
        switch ($table) {
            case 'tags':
                $model = new TrackTagModel();
                foreach ($data as $key => $item) {
                    $params[$key]['name'] = $item;
                    $params[$key]['track_id'] = $track_id;
                }
                $relation = $track->tags();
                break;
            case 'genres':
                $relation = $track->genres();
                break;
            case 'moods':
                $relation = $track->moods();
                break;
            case 'invs':
                $relation = $track->invs();
                break;
            case 'contract':
                foreach ($data as $key => $item) {
                    $params[$key]['contact_setting_id'] = $key;
                    $params[$key]['track_id'] = $track_id;
                    $price = $item['price'] ?? 0;
                    $price = preg_replace('/[^0-9.]/', '', $price);
                    $params[$key]['price'] = $price;
                    $params[$key]['enabled'] = $item['enabled'] ?? 0;
                }
                $track->contracts()->sync($params);
                break;
            default:
                break;
        }
        if ($relation) {
            $relation->sync($data);
        }
        // if ($relation && $params) {
        //     foreach ($params as $item) {
        //         $relation->attach($track_id);
        //     }
        //     // $track->trackTag()->sync($params);
        // }
        return $params;
    }
    public function form(Request $request)
    {
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        return view(
            "{$this->pathViewController}/form",
            [
                'item' => $item,
            ]
        );
    }
    public function files(Request $request)
    {
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        if (!$item) {
            $this->insert($code);
        }
        return view(
            "{$this->pathViewController}/form-files",
            [
                'code' => $code,
                'item' => $item
            ]
        );
    }
    public function basicInfo(Request $request)
    {
        $code = $request->code;
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        return view(
            "{$this->pathViewController}/basic-info",
            [
                'code' => $code,
                'item' => $item,
            ]
        );
    }
    public function metadata(Request $request)
    {
        $code = $request->code;
        $genres = $this->genresModel->listItems([], ['task' => 'list']);
        $moods = $this->moodModel->listItems([], ['task' => 'list']);
        $invs = $this->invModel->listItems([], ['task' => 'list']);
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        return view(
            "{$this->pathViewController}/metadata",
            [
                'code' => $code,
                'genres' => $genres,
                'moods' => $moods,
                'invs' => $invs,
                'item' => $item,
            ]
        );
    }
    public function collaborators(Request $request)
    {
        $code = $request->code;
        return view(
            "{$this->pathViewController}/collaborators",
            [
                'code' => $code
            ]
        );
    }
    public function pricing(Request $request)
    {
        $code = $request->code;
        $contractsResult = $this->contracts($code);
        $contracts = $contractsResult['contracts'] ?? [];
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $itemId = $contractsResult['itemId'];
        return view(
            "{$this->pathViewController}/pricing",
            [
                'code' => $code,
                'contracts' => $contracts,
                'itemId' => $itemId,
                'item' => $item,
            ]
        );
    }
    public function review(Request $request)
    {
        $code = $request->code;
        $contractsResult = $this->contracts($code);
        $contracts = $contractsResult['contracts'] ?? [];
        $itemId = $contractsResult['itemId'];
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        return view(
            "{$this->pathViewController}/review",
            [
                'code' => $code,
                'contracts' => $contracts,
                'itemId' => $itemId,
                'item' => $item,
            ]
        );
    }
    public function contracts($code)
    {
        $item = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $itemId = $item['id'] ?? "";
        $user_id = rrt_get_user_login('id');
        $user = $this->userModel::findOrFail($user_id);
        $contracts = $user->contracts()->with('contract_info')->get()->toArray();
        $contracts = rrt_group_by($contracts, 'category');
        $result = [];
        $result['contracts'] = $contracts;
        $result['itemId'] = $itemId;
        return $result;
    }
    public function insert($code)
    {
        $params = [];
        $params['code'] = $code;
        $params['name'] = 'New Track';
        $params['status'] = 'draft';
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['user_id'] = rrt_get_user_login('id');
        $this->model->saveItem($params, ['task' => 'add-item']);
    }
    public function uploadTrack(Request $request)
    {
        $params = $request->all();
        $code = $request->code;
        $trackItem = $this->model->getItem(['code' => $code], ['task' => 'code']);
        $trackId = $trackItem['id'] ?? "";
        $user_id = rrt_get_user_login('id');
        $file = $request->file('track_file');
        $originalName = $file->getClientOriginalName();
        $originalName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->clientExtension();
        $name = $originalName . "-" . Str::random(10) . "." . $extension;
        $file->storeAs('tracks', $name, 'rrt_storage');
        $params['user_id'] = $user_id;
        $params['name'] = $name;
        $params['track_id'] = $trackId;
        $params['created_at'] = date('Y-m-d h:i:s');
        $trackFileItem = [];
        $trackFileItem = $this->trackFileModel->getItem($params, ['task' => 'check']);
        if ($trackFileItem) {
            $params['id'] = $trackFileItem['id'] ?? "";
            Storage::disk('rrt_storage')->delete("tracks/{$trackFileItem['name']}");
        }
        $task = !$trackFileItem ? "add-item" : "edit-item";
        $action = $this->trackFileModel->saveItem($params, ['task' => $task]);
        if ($task == 'add-item') {
            $params['id'] = $action;
        }
        $result = [
            'files' => $file,
            'params' => $params,
            'url' => url("public/uploads/tracks/{$params['name']}"),
            'name' => $name,
            'originalName' => $originalName,
        ];
        return $result;
    }
}
