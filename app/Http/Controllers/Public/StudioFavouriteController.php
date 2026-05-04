<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\UserModel;
use App\Models\TrackModel as MainModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioFavouriteController extends Controller
{
    private $pathViewController     = "public2.pages.favourite";
    private $controllerName         = "public/studio/favourite";
    private $trackControllerName         = "public/studio/content";
    private $model;
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
        $code = $this->model->randomCode();
        $user_id  = rrt_get_user_login('id');
        $user = $this->userModel::find($user_id);
        $items = $user->favorite()->with('track')->paginate(10);

       
        return view(
            "{$this->pathViewController}/index",
            [
                'code' => $code,
                'items' => $items,
            ]
        );
    }
}
