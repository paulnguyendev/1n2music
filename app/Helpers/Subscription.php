<?php

namespace App\Helpers;

use App\Models\SubscriptionOrderModel;
use App\Models\PlanOrderModel;
use App\Models\UserModel;

class Subscription
{
    public function __construct()
    {
    }
    public static function addOrder($data = [])
    {
        $planOrderModel = new PlanOrderModel();
        $subscriptionOrderModel = new SubscriptionOrderModel();
        $userID = $data['user_id'] ?? "";
        $status = $data['status'] ?? "pending";
        $itemID = $data['item_id'] ?? "";
        $cycle = $data['cycle'] ?? "";
        if (empty($userID)) {
            return;
        }
        if (empty($itemID)) {
            return;
        }
        $joinType = $data['join_type'] ?? "pro_seller";
        $total = $data['price'] ?? 0;
        $currentDate = date('Y-m-d H:i:s');
        $orderID = null;
        $params = [
            'user_id' => $userID,
            'status' => $status,
            'total' => $total,
            'created_at' => $currentDate,
            'cycle'=>$cycle
        ];
        if ($joinType == 'pro_seller') {
            $params['plan_id'] = $itemID;
            $orderID = $planOrderModel->saveItem($params, ['task' => 'add-item']);
        } else {
            $params['subscription_id'] = $itemID;
            $orderID = $subscriptionOrderModel->saveItem($params, ['task' => 'add-item']);
        }
        $params['order_id'] = $orderID;
        return $params;
    }
    public static function getCurrent($userID)
    {
        $model = new UserModel();
        $user = $model::find($userID);
        $role = $user['role'] ?? "user";
        $result = [
            'role' => $role,
        ];
        return $result;
    }


    public static function getSubscription($subscription_id)
    {
        $userID = rrt_get_user_login('id');
        $model = new UserModel();
        $user = $model::find($userID);
        // 2 là Distribute
        $result = $user ?  $user->subscriptionOrders()->where('subscription_id', $subscription_id)->orderBy('id', 'desc')->first() : collect();

        return $result;
    }


    public static function checkSubscription(array $subscription_ids)
    {
        $userType = self::checkUserType();
        $redirect = null;
        $validSubscriptionFound = false;
        foreach ($subscription_ids as $subscription_id) {
            $subscription = Subscription::getSubscription($subscription_id);
            if ($subscription && $subscription->status !== 'pending') {
                $validSubscriptionFound = true;
                break;
            }
        }
        if ($validSubscriptionFound) {
            return null;
        }
        if (!$subscription) {
            $redirect = rrt_route('public/join/distribution/index');
        } else {
            $redirect = rrt_route('public/studio/account/subscription');
        }
        return $redirect;
    }
    public static function checkUserType() {
        $userID = rrt_get_user_login('id');
        $user = UserModel::find($userID);
        $planOrderModel = new PlanOrderModel();
        $subscriptionOrderModel = new SubscriptionOrderModel();
        $userTypePlan = $user->planOrders->info->type ?? '';
        $userTypeSub = $user->subscriptionOrders->info->type ?? '';
        $userType = $userTypePlan ? $userTypePlan : $userTypeSub;
        if($userTypePlan && $userTypeSub) {
            $userType = 'all';
        }
        return $userType;



    }
    public static function checkUserRole(){
        $userID = rrt_get_user_login('id');
        $user = UserModel::find($userID);
        $userRole = $user->role ?? 'user';

        // Get active subscriptions with cycle information
        $subscriptionOrders = SubscriptionOrderModel::with('info')
            ->where('user_id', $userID)
            ->where('status','active')
            ->get();

        $subscriptions = [];
        foreach ($subscriptionOrders as $order) {
            $slug = $order->info->slug ?? '';
            $cycle = $order->cycle ?? 'annually';

            // Debug: Log what we're getting
            \Log::info('Subscription Debug', [
                'user_id' => $userID,
                'slug' => $slug,
                'cycle' => $cycle,
                'order_id' => $order->id
            ]);

            // Map subscription slug to role name based on cycle
            // For annually: append '-annually' suffix
            // For monthly: use slug as-is
            if ($cycle === 'annually') {
                $subscriptions[] = $slug . '-annually';
            } else {
                $subscriptions[] = $slug;
            }

            // Also add the base slug for backward compatibility
            $subscriptions[] = $slug;
        }

        $plans = PlanOrderModel::with('info')
            ->where('user_id', $userID)
            ->where('status','active')
            ->get()
            ->pluck('info.type')->toArray();

        $userRolesAndSubsAndPlans = array_merge([$userRole], $subscriptions, $plans);

        // Debug: Log final result
        \Log::info('Final Roles', [
            'user_id' => $userID,
            'roles' => $userRolesAndSubsAndPlans
        ]);

        return $userRolesAndSubsAndPlans;
    }
}
