<?php

namespace App\Helpers;

use App\Models\TrackModel;
use App\Models\UserModel;
use App\Models\WalletModel;
use App\Models\PayoutMethodModel;

class Balance
{
    private static $walletModel;

    public function __construct()
    {
        $this->walletModel = new WalletModel();
    }

    public static function getBalanceUser($user_id  = null, $type = 'total', $format = 0)
    {

        if (is_null($user_id))
            return false;

        $query = WalletModel::where('user_id', $user_id);

        if ($type != 'total')
            $query = $query->where('type', $type);

        $result = $query->get()->sum('total');

        return $result ?? 0;
    }

    public static function getTotalTransactions($user_id  = null, $type, $format = 0)
    {

        if (is_null($user_id))
            return false;

        $query = WalletModel::where('user_id', $user_id);

        $query = $query->where('type', $type);

        $result = $query->get()->sum('total');

        return $result ?? 0;
    }
}
