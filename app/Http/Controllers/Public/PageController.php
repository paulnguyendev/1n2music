<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BulletinBoardModel;
use App\Models\DownloadModel;
#Model
use App\Models\PageModel;
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\TrackCommentModel;
use App\Models\TrackFavouritesModel;
use App\Models\PageModel as MainModel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
#Helper
class PageController extends Controller
{
    private $pathViewController     = "public.pages.page";
    private $controllerName         = "public/page";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $trackCommentModel;
    private $trackFavouritesModel;
    private $bulletionBoardModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->trackFavouritesModel = new TrackFavouritesModel();
        $this->trackCommentModel = new TrackCommentModel();
        $this->bulletionBoardModel = new BulletinBoardModel();
        View::share('controllerName', $this->controllerName);
    }
    public function detail(Request $request)
    {
        $id = $request->id;

        $item = $this->model->getItem(['id' => $id], ['task' => 'id']);
        if (!$item) {
            return redirect(rrt_route('public/home/index'));
        }
        
        // Get translation if available
        $translation = null;
        $currentLocale = app()->getLocale();
        if ($item) {
            $translation = \App\Models\PageTranslationModel::where('page_id', $id)
                ->where('language', $currentLocale)
                ->first();
        }
        
        // Use translation if available
        if ($translation) {
            if (!empty($translation->name)) {
                $item->name = $translation->name;
            }
            
            if (!empty($translation->content)) {
                $item->content = $translation->content;
            }
        }
        
        // dd($track->listContracts[0]->contractSetting->contract_info->name);
        return view(
            "{$this->pathViewController}/detail",
            [
                'item' => $item,
                'translation' => $translation
            ]
        );
    }
    public function getAudio(Request $request)
    {
        $code = $request->code;
        $track = TrackModel::where('code', $code)->first();
        $file = [];
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
    public function postFavourite(Request $request)
    {
        $params = $request->all();
        $trackID = $request->track_id;
        $checkActive = $params['check_active'];
        $userID = rrt_get_user_login('id');
        $createdAt = date('Y-m-d H:i:s');
        $check = [];
        $params['track_id'] = $trackID;
        $params['user_id'] = $userID;
        $check =  $this->trackFavouritesModel->getItem($params, ['task' => 'check']);
        $favouriteID = null;
        $params['created_at'] = $createdAt;
        if (!$check) {
            $favouriteID =  $this->trackFavouritesModel->saveItem($params, ['task' => 'add-item']);
        } elseif ($checkActive == 1) {
            $params['id'] = $check['id'] ?? "";
            $favouriteID =  $this->trackFavouritesModel->deleteItem($params, ['task' => 'delete']);
        }
        $total = $this->trackFavouritesModel->listItems($params, ['task' => 'count']);
        $params['check'] = $check;
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
        $check_user_allow_cmt  = rrt_get_user_login('is_comment');
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


    public function detailBulletionBoard(Request $request)
    {
        $id = $request->id;
        $params['id'] = $request->id;
        $bulletion_board =   $this->bulletionBoardModel->getItem($params, ['task' => 'id']);

        if ($bulletion_board) {
            return view(
                "{$this->pathViewController}/detail",
                [
                    'item' => $bulletion_board,
                ]
            );
        } else {
            return redirect(rrt_route('public/home/index'));
        }
    }
    public function showPage(Request $request){
        $id = $request->id??'';
        $page = PageModel::find($id);
        if (!$page){
            abort(404,'Page not found');
        }
        return view('public2.pages.page.detail',['page'=>$page]);
    }
}
