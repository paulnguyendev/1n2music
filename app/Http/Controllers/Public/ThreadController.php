<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BulletinBoardCommentModel;
use App\Models\BulletinBoardModel;
use App\Models\DownloadModel;
#Model
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\TrackCommentModel;
use App\Models\TrackFavouritesModel;
use App\Models\BulletinBoardModel as MainModel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
#Helper
class ThreadController extends Controller
{
    private $pathViewController     = "public2.pages.threads";
    private $controllerName         = "public/threads";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $trackCommentModel;
    private $trackFavouritesModel;
    private $bulletionBoardModel;
    private $commentModel;
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
        $this->commentModel = new BulletinBoardCommentModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request) {
        $items = $this->model->listItems([],['task' => 'threads']);

        return view(
            "{$this->pathViewController}/index",
            [
                'items' => $items,

            ]
        );
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
