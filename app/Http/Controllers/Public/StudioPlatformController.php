<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\OrderModel;
use App\Models\PlanOrderModel;
use App\Models\PlatformModel;
use App\Models\SubscriptionOrderModel;
use App\Models\UserModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use App\Models\PlatformModel as MainModel;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioPlatformController extends Controller
{
    private $pathViewController     = "studio.pages.platforms";
    private $controllerName         = "public/studio/platforms";
    private $model;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $platforms = PlatformModel::orderBy('created_at','desc')->get();
        return view(
            "{$this->pathViewController}/index",
            [
                'platforms' => $platforms,
            ]
        );
    }
    public function data(Request $request)
    {
        $platform = PlatformModel::find($request->id??'');
        if ($platform) {
            return response()->json([
                'success' => true,
                'settings' => $platform->settings,
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
