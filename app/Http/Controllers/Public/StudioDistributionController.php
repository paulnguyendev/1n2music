<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\SubscriptionModel as MainModel;
use App\Models\SubscriptionOrderModel;
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class StudioDistributionController extends Controller
{
    private $pathViewController     = "studio.pages.distribution";
    private $controllerName         = "public/studio/distribution";
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

        $studio_session = rrt_get_user_login();
        $user = UserModel::where('id', $studio_session['id'])->first();
        $subscription_order =    SubscriptionOrderModel::where('user_id', $user->id)->where('subscription_id', 2)->first();

        return view(
            "{$this->pathViewController}/index",
            [
                'subscription_order' => $subscription_order
            ]
        );
    }
}
