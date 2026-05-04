<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Models\DownloadModel;
#Model
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\NoticeModel;
use App\Models\TrackCommentModel;
use App\Models\TrackFavouritesModel;
use App\Models\TrackContractModel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
#Helper
class TrackController extends Controller
{
    private $pathViewController     = "public.pages.track";
    private $controllerName         = "public/track";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $trackCommentModel;
    private $trackFavouritesModel;
    private $trackContractModel;
    private $noticeModel;
    private $downloadModel;
    private $params                 = [];
    function __construct()
    {
        // $this->model = new MainModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->trackFavouritesModel = new TrackFavouritesModel();
        $this->trackCommentModel = new TrackCommentModel();
        $this->trackContractModel = new TrackContractModel();
        $this->noticeModel = new NoticeModel();
        $this->downloadModel = new DownloadModel();
        View::share('controllerName', $this->controllerName);
    }
    public function detail(Request $request)
    {
        $code = $request->code;
        $track = $this->trackModel->where('code', $code)
            ->with('user')
            ->with('like')
            ->with('download')
            ->with('genres')
            ->with('listTags')
            ->with('listContracts.contractSetting.contract')
            ->with('invs')
            ->with('file')
            ->first();
        if (!$track) {
            abort(404);
        }

        // Replace listContracts with sorted contracts
        $track->list_contracts = $track->getSortedContracts();
        
       
        $totalFavourite = $this->trackFavouritesModel->listItems(['track_id' => $track['id'] ?? ""], ['task' => 'count']);
        $checkFavourite =  $this->trackFavouritesModel->getItem([
            'user_id' => rrt_get_user_login('id'),
            'track_id' =>  $track['id'] ?? "",
        ], ['task' => 'check']);
        // dd($track->listContracts[0]->contractSetting->contract_info->name);
        return view(
            "{$this->pathViewController}/detail",
            [
                'track' => $track,
                'totalFavourite' => $totalFavourite,
                'checkFavourite' => $checkFavourite,
            ]
        );
    }
    public function getAudio(Request $request)
    {
        $code = $request->code;
        $track = TrackModel::where('code', $code)->first();
        $file = null;
        foreach ($track->file as $key => $item) {
            //dd($item);
            if ($item->type == 'taggedMp3' || $item->type == 'unTaggedMp3') {
                $file  = $item;
            }
        }
        return view("{$this->pathViewController}/audio", [
            'track' => $track,
            'file' => $file
        ]);
    }
    public function download(Request $request)
    {
        $data = $request->all();
        if (isset($data['code'])) {
            $track = TrackModel::where('code', $data['code'])->first();
            if (!$track) {
                //  $errors[] = '';
                return response()->json(['error' => 'Track Not Found']);
            }
            $session = rrt_get_config_by("session", 'studio', 'session');
            $session_user_info = $request->session()->get($session);
            DownloadModel::create(['user_id' => $session_user_info->id, 'track_id' => $track->id]);;
            foreach ($track->file as $key => $item) {
                if ($item->type == 'taggedMp3' || $item->type == 'unTaggedMp3') {
                    $file  = $item;
                }
            }
            $url_file = url('public/uploads/tracks/' . $file->name);
            return response()->json(['status' => 200, 'url' => $url_file, 'fileName' => $file->name]);
        }
    }
    public function downloadTrack(Request $request)
    {
        $token = $request->token;
        $download = $this->downloadModel->where('token', $token)->first();
        if (!$download) {
            return redirect()->route('public/home/index', ['locale' => rrt_get_locale()]);
        }
        $track = $download->track()->first();
        if (!$track) {
            return redirect()->route('public/home/index', ['locale' => rrt_get_locale()]);
        }
        $trackType = $download['track_type'] ?? "";
        $trackFile = $track->fileWithType($trackType)->first();
        if (!$trackFile) {
            return redirect()->route('public/home/index', ['locale' => rrt_get_locale()]);
        }
        $trackFileName = $trackFile['name'] ?? "";
        $trackFilePath = public_path('uploads/tracks/' . $trackFileName);
        return response()->download($trackFilePath, $trackFileName, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $trackFileName . '"',
        ]);
    }
    public function postFavourite(Request $request)
    {
        $params = $request->all();
        $trackID = $params['track_id'] ?? '';
        $checkActive = $params['check_active'];
        $userID = rrt_get_user_login('id');
        $createdAt = date('Y-m-d H:i:s');
        $check = [];
        $params['track_id'] = $trackID;
        $params['user_id'] = $userID;
        $check =  $this->trackFavouritesModel->getItem($params, ['task' => 'check']);
        $favouriteID = null;
        $favourite = 0;
        $params['created_at'] = $createdAt;
        if (!$check) {
            $favouriteID =  $this->trackFavouritesModel->saveItem($params, ['task' => 'add-item']);
            if ($favouriteID) {
                $createdAt = date('Y-m-d H:i:s');
                $params['create_at'] = $createdAt;
                $this->noticeModel->saveItem($params, ['task' => 'favourite']);
            }
            $favourite = 1;
        } elseif ($checkActive == 1) {
            $params['id'] = $check['id'] ?? "";
            $favouriteID =  $check->id ?? '';
            $favourite = 0;
            $this->trackFavouritesModel->deleteItem($params, ['task' => 'delete']);

        }
        $total = $this->trackFavouritesModel->listItems($params, ['task' => 'count']);
        $params['check'] = $check;
        $params['favourite'] = $favourite;
        $params['favourite_id'] = $favouriteID;
        $params['total'] = $total;
        return $params;
    }
    public function postComment(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        $params['track_id'] = $data['track_id'] ?? 0;
        $params['content'] = $data['comment'] ?? '';
        $params['parent_id'] = $data['parent'] ?? 0;
        $params['user_id'] =    rrt_get_user_login('id');
        $check_user_allow_cmt  = $this->userModel->checkAllow();
        if ($check_user_allow_cmt == 0) {
            return  response()->json(['status' => 403]);
        }
        $result =   $this->trackCommentModel->saveItem($params, ['task' => 'add-item']);
        if ($result) {
            $status = 200;
        } else {
            $status = 500;
        }
        return  response()->json(['status' => $status, 'data' => $result]);
    }
    public function getCommentToTrack(Request $request)
    {
        $params['limit'] = $data['limit'] ?? 5;
        $params['track_id'] = $request->track_id  ?? 0;
        $resutl  =  $this->trackCommentModel->getListToTrack($params);
        if ($resutl) {
            $status = 200;
        } else {
            $status = 500;
        }
        return response()->json(['status' => $status, 'data' => $resutl]);
    }
    public function seeMoreComment(Request $request)
    {
        $data = $request->all();
        $params = $data;
        $comment = $this->trackCommentModel->listItems($params, ['task' => 'ajax']);
        if (count($comment)) {
            $status = 200;
        } else {
            $status = 500;
        }
        return response()->json([
            'status' => $status,
            'data' => $comment,
        ]);
    }
    public function listContracts(Request $request) {
        $params = $request->all();
        $contractIds = $params['contract_ids'] ?? '';
        $contractIdsArr = $contractIds ? explode(",",$contractIds) : [];
        $contracts = $this->trackContractModel::whereIn('id', $contractIdsArr)
                    ->with('contractSetting.contract')
                    ->get()
                    ->sortBy(function($contract) {
                        return $contract->contractSetting->contract->order;
                    })
                    ->values();
        $params['contracts'] = $contracts;
        return $params;
    }
}
