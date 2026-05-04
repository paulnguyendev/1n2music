<?php

namespace App\Helpers;

use App\Models\TrackModel;
use App\Models\UserModel;
use App\Models\WalletModel;
use App\Models\PayoutMethodModel;

class User
{
    private static $UserModel;

    public function __construct()
    {
        $this->UserModel = new UserModel();
    }

    public static function getMethodByUser()
    {
        $user_id = rrt_get_user_login('id');

        $user  =  UserModel::where('id', $user_id)->with('paymentAccount')->first();
        if ($user->paymentAccount) {
            $result = $user->paymentAccount->paymentmethod()->where('is_active', 1)->where('is_selected', 1)->get();
            return $result ?? [];
        } else {
            return false;
        }
    }
}
