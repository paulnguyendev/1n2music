<?php

namespace App\Helpers;

use App\Models\TrackModel;
use App\Models\UserModel;
use App\Models\WalletModel;
use App\Models\PayoutMethodModel;

class Payout
{
    private static $payoutModel;

    public function __construct()
    {
        $this->payoutModel = new PayoutMethodModel();
    }

    public static function getMethodByUser()
    {
        $user_id = rrt_get_user_login('id');
        
    }
}
