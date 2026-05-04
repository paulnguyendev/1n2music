<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionOrderModel;
#Model
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioPublishingController extends Controller
{
    private $pathViewController     = "studio.pages.publishing";
    private $controllerName         = "public/studio/publishing";
    private $model;
    private $params                 = [];
    function __construct()
    {
        // $this->model = new MainModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $modelUser = new UserModel();
        $studio_session = rrt_get_user_login();
        $user = UserModel::where('id', $studio_session['id'])->first();
        $subscription_order =    SubscriptionOrderModel::where('user_id', $user->id)->where('subscription_id', 1)->first();

        return view(
            "{$this->pathViewController}/index",
            [
                'subscription_order' => $subscription_order ?? ''
            ]
        );
    }
}
