<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\TrackHistoryModel as MainModel;
use App\Models\TrackHistoryModel;
use App\Models\UserModel;
use App\Models\TrackModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class StudioHistoryController extends Controller
{
    private $pathViewController     = "public2.pages.history";
    private $controllerName         = "public/studio/history";
    private $trackControllerName         = "public/studio/content";
    private $model;
    private $userModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        View::share('controllerName', $this->controllerName);
        View::share('trackControllerName', $this->trackControllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $user_id  = rrt_get_user_login('id');
        $items = [];
        if ($user_id) {
            $items = $this->model::whereUser_id($user_id)->orderBy('id', 'desc')->paginate(10);
        }
        return view(
            "{$this->pathViewController}/index",
            [
                'items' => $items,
            ]
        );
    }
    public function save(Request $request)
    {
        $user_id  = rrt_get_user_login('id');
        $track_id = $request->track_id;
        
        $item = new TrackHistoryModel();
        $item->user_id = $user_id;
        $item->track_id = $track_id;
        $item->save();
        return response()->json([
            'success' => true,
            'message' => __('Save History Success')
        ]);
    }
}
