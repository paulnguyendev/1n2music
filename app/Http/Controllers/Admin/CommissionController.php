<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Mail\SendNoticeMail;
use App\Models\BannerModel as MainModel;
use App\Models\OrderModel;
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\SettingModel;
use App\Models\UserModel;
use App\Models\NoticeLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CategoryBannerModel;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class CommissionController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    public function __construct()
    {
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/commission";
        $this->pathViewController = "{$this->prefix}.pages.commission";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request){
        $settingsModel = new SettingModel();
        $commissions = $settingsModel->getOrCreateCommissions();
        $commissionSeller = $commissions['commission_seller'];
        $commissionPublishing = $commissions['commission_publishing'];
        $commissionDistribute = $commissions['commission_distribute'];
//        $order = OrderModel::find(77);
//        $orderCtrl = new OrderController();
//        $listCommision = $orderCtrl->getListComission($order);
//        dd($listCommision);
        return view ($this->pathViewController.'.index',compact('commissionSeller', 'commissionPublishing','commissionDistribute'));
    }
    public function save(Request $request){
        $settingsModel = new SettingModel();
        $commissions = [
            'commission_seller' => $request->commission_seller??0,
            'commission_subscriber' => $request->commission_subscriber??0,
            'commission_publishing' => $request->commission_publishing??0,
            'commission_distribute' => $request->commission_distribute??0,
        ];
        foreach ($commissions as $key => $value) {
            $settingsModel->saveItem([$key => ($value/100)]);
        }

        return response()->json(['status' => 'success', 'message' => 'Commissions updated successfully!']);
    }
}
