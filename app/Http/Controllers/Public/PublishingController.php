<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ProOrganizationModel;
#Model
use App\Models\SubscriptionModel as MainModel;
use App\Models\SubscriptionOrderModel;
use App\Models\TaxModel;
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class PublishingController extends Controller
{
    private $pathViewController     = "public2.pages.publishing";
    private $controllerName         = "public/join/publishing";
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
        $item = $this->model->getItem(['slug' => 'publishing'], ['task' => 'slug']);
        return view($this->pathViewController . "/coming_soon");
//        return view(
//            "{$this->pathViewController}/index",
//            [
//                'item' => $item
//            ]
//        );
    }

    public function register(Request $request)
    {
        $item = $this->model->getItem(['slug' => 'publishing'], ['task' => 'slug']);
        $tax = TaxModel::all();
        // dd($item);
        $id = $item['id'] ?? "";
        $studio_session = rrt_get_user_login();
        $user = $studio_session ?? [];
        $pro_organization = ProOrganizationModel::all();
        if ($studio_session) {
            $studio = UserModel::where('id', $studio_session['id'])->first();
            if (isset($studio->paymentAccount)) {
                foreach ($studio->paymentAccount->paymentmethod as $key => $item) {
                    if ($item->method == 'paypal') {
                        $card_paypal_info =  $item->info->toArray();
                    } elseif ($item->method == 'bank') {
                        $card_bank_info =  $item->info->toArray();
                    }
                }
            }
        }

        return view(
            "{$this->pathViewController}/register",
            [
                'id' => $id,
                'user' => $user,
                'card_bank_info' => $card_bank_info ?? [],
                'card_paypal_info' => $card_paypal_info  ?? [],
                'tax' => $tax,
                'pro_organization' => $pro_organization
            ]
        );
    }

    public function postRegister(Request $request)
    {

        $data = $request->all();
        $studio_session = rrt_get_user_login();
        $user = UserModel::where('id', $studio_session['id'])->first();
        if ($studio_session) {
            $subscription_order =    SubscriptionOrderModel::where('user_id', $user->id)->where('subscription_id', 1)->first();
            if (empty($subscription_order)) {
                SubscriptionOrderModel::create([
                    'subscription_id' => 1,
                    'total' => $data['total'] ?? 0,
                    'status' => 'pending',
                    'user_id' => $user->id,
                ]);
                $redirect  = rrt_route('public/studio/publishing/index');
                return [
                    'status' => 200,
                    'redirect' => $redirect,
                ];
            }
            return [
                'status' => 400,
                'msg' => ['identification' => 'You have subscribed to publishing']
            ];
        }
        return [
            'status' => 400,
            'msg' => ['identification' => 'Error System']
        ];
    }
}
