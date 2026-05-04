<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Models\PlanOrderModel;
use App\Models\SubscriptionOrderModel;
use App\Models\UserModel as MainModel;
use App\Models\LogAIUsage;
#Mail
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

#Helper
class ToolController extends Controller
{
    private $prefix;
    private $controllerName;
    private $model;
    private $params = [];
    private $planOrderModel;
    private $subscriptionOrderModel;
    public function __construct()
    {

        $this->model = new MainModel();
        $this->planOrderModel = new PlanOrderModel();
        $this->subscriptionOrderModel = new SubscriptionOrderModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/tool";
    }
    public function usageAi(Request $request)
    {
        $data = $request->all();
        $user = $this->model->find($data['user_id']);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not exist.'
            ], 404);
        }
        $count = (int) $data['ai_count'];
        $ai_selected = (int) $data['ai_select'];
        
        rrt_tool_count_usageai($user, $count, $ai_selected);

        return response()->json([
            'status' => 'success',
            'message' => 'Usage count updated successfully.'
        ], 200);
    }
    public function packageUsage(Request $request)
    {
        $data = $request->all();
        $params = [];

        $ai_selected = $data['ai_select'];
        $count = $data['ai_count'];
        $accountType = $data['account_type'] ??'';
        if ($accountType == 'free-user'){
            $params['account_type'] = ['free-user'];
        }elseif ($accountType == 'seller'){
            $params['account_type'] = ['free-seller','proseller-monthly','proseller-annually'];
        }
        elseif ($accountType == 'distribution'){
            $params['account_type'] = ['distribution-annually'];
        }
        elseif ($accountType == 'publishing'){
            $params['account_type'] = ['publishing-annually'];
        }
        $users = $this->model->listItems($params, ['task' => 'filterRole']);
        foreach ($users as $user) {
            if ($ai_selected == \App\Models\AIService::AIServiceAIMastering) {
                if (($user->ai_usage_count + $count) >= 0) {
                    $before_usage_count = $user->ai_usage_count;
                    $amount = $count;
                    $user->ai_usage_count += $count;
                    $log = [
                        'ai_id' => 1,
                        'user_id' => $user->id,
                        'before_usage_count' => $before_usage_count,
                        'amount' => $amount,
                        'current_usage_count' => $user->ai_usage_count,
                        'mastering_id' => $ai_selected,
                        'note' => 'admin add minus use',
                    ];
                    LogAIUsage::create($log);
                }
            } elseif ($ai_selected == \App\Models\AIService::AIServiceAIRecognition) {
                if (($user->ai_usage_count_reconize + $count) >= 0) {
                    $before_usage_count = $user->ai_usage_count_reconize;
                    $amount = $count;
                    $user->ai_usage_count_reconize += $count;
                    $log = [
                        'ai_id' => 1,
                        'user_id' => $user->id,
                        'before_usage_count' => $before_usage_count,
                        'amount' => $amount,
                        'current_usage_count' => $user->ai_usage_count_reconize,
                        'recognition_id' => $ai_selected,
                        'note' => 'admin add minus use',
                    ];
                    LogAIUsage::create($log);
                }
            }
            $user->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Usage count updated successfully.'
        ], 200);
    }
}
