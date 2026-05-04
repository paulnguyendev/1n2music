<?php

namespace App\Helpers;

use App\Mail\SendChangeStatusTransactionMail;
use App\Mail\SendUserBuy;
use App\Mail\SendUserSeller;
use App\Models\TaxModel;
use App\Models\TrackModel;
use App\Models\UserModel;
use App\Models\TransactionsModel;
use Illuminate\Support\Facades\Mail;

class Transactions
{

    public function __construct()
    {
    }

    public static function checkRequestPayout($user_id)
    {
        $check =  Transactions::checkTotalWithdrawlWallet($user_id);
        return $check;
    }
    public static function checkTotalWithdrawlWallet($user_id)
    {
        $total =   Transactions::getTotalWalletByUser($user_id);
        $check = Transactions::checkTotalWidthdrawByUser($user_id, $total);
        return $check;
    }
    public static function  getTotalByType($user_id = null, $type = null, $status = null, $format = false)
    {
        $result = 0;
        $TransactionsModel = new TransactionsModel();
        $params['user_id'] = $user_id;
        $params['type'] = $type;
        $params['status'] = $status;
        $total =   $TransactionsModel->sumItem($params, ['task' => 'sum-by-type']);

        $result =  Transactions::formatBalance($total, $format);

        return $result;
    }
    public static function updateBanlanceToUser($id, $type)
    {
        $result =   TransactionsModel::where('id', $id)->update(['status' => 'active']);

        return $result;
    }
    public static function formatBalance($total, $format = true)
    {
        $result         =    ($format == true)  ? rrt_show_price($total) : $total;
        return $result;
    }
    public static function  getTotalByUser($user_id, $format = false)
    {
        $result         = 0;
        $total_in       =  Transactions::getTotalByType($user_id, 'in', 'active');
        $total_out      =  Transactions::getTotalByType($user_id, 'out', 'active');
        $total          =  $total_in - $total_out;
        if ($total < 0) {
            return $result;
        }
        $result =  Transactions::formatBalance($total, $format);
        return $result;
    }
    public static function  getTotalWalletByUser($user_id, $format = false)
    {
        $result         = 0;
        $total          =  Transactions::getTotalByUser($user_id);
        $total_pending  = Transactions::getTotalByType($user_id, 'out', 'pending');

        $total_affter_pending = $total - $total_pending;
        $result =  Transactions::formatBalance($total_affter_pending, $format);
        return $result;
    }
    public static function getMinPriceWidthDraw()
    {
        return 50;
    }
    public static function checkAccountPaymentByUser($user_id)
    {
        $userModel  = new UserModel();
        $result  =   $userModel->checkUserAccountPayment($user_id);

        return $result;
    }
    public static function checkTotalWidthdrawByUser($user_id, $total)
    {

        $total_wallet_user = Transactions::getTotalWalletByUser($user_id);
        $min = Transactions::getMinPriceWidthDraw();
        $status =   rrt_get_config_transaction();
        $check_account_payment_user = Transactions::checkAccountPaymentByUser($user_id);
        if ($total < $min) {
            return $status['min'];
        } elseif ($total > $total_wallet_user) {
            return $status['max'];
        } elseif ($check_account_payment_user == 0) {
            return $status['account'];
        }
        return $status['allow'];
    }

    public static function actionUserBuy($to, $params)
    {
        Mail::to($to)->send(new SendUserBuy($params));
    }
    public static function actionUserSeller($user, $params)
    {
        // Transactions::updateBanlanceToUser($params->id, 'in');
        $params['total'] = $params->total;
        $params['code'] = $params->code;
        $params['name'] = 'Notice of adding money';
        $params['email'] = $user->email;
        Mail::to($params['email'])->send(new SendUserSeller($params));
    }

    public static function actionChangeStatusTransaction($to, $params)
    {
        // Transactions::actionUserBuy($to, $params);
        Transactions::actionUserSeller($to, $params);
    }

    public static function getTaxTypeByUser()
    {
        $user_id = rrt_get_user_login('id');

        $user = UserModel::where('id', $user_id)->first();

        $user_payment =  $user->paymentAccount;
        if ($user_payment) {
            return $user_payment->taxs ? $user_payment->taxs->id : 0;
        }
        return 0;
    }
    public static function calculatePercentage($amount, $percentage, $decimalPlaces = 2)
    {
        return ($percentage / 100) * $amount;
    }
    public  static function getTotalTaxBusiness($total, $tax_id)
    {
        $tax = TaxModel::where('id', $tax_id)->first();
        if ($tax) {
            return   Transactions::calculatePercentage($total, $tax->percen);
        }
        return 0;
    }
    public static function getTotalTaxType($total, $options)
    {
        if ($options['task'] == 'supply-price') {
            $type =  Transactions::getTaxTypeByUser();
            if ($type == 1) {
                $tax =   Transactions::getTotalTaxBusiness($total, $type);
            }
            $result = 0;
        }

        if ($options['task'] == 'vat') {
            $result = 0;
        }
        if ($options['task'] == 'tax') {
            $result = 0;
        }
        return $result ?? 0;
    }

    public static function getTotalAmountPayment($total, $params)
    {
        $result = $total;

        if (isset($params['vat'])) {
            $result = $result - $params['vat'];
        }
        if (isset($params['tax'])) {
            $result = $result - $params['vat'];
        }
        return $result;
    }
    public static function addCommission() {

    }
    public static function addTransaction($data = []) {
        $userID = $data['user_id'] ?? "";
        $type = $data['type'] ?? "in";
        $status = $data['status'] ?? "pending";
        $total = $data['total'] ?? 0;
        $category = $data['category'] ?? "add-transaction";
        $model = new TransactionsModel();
        $code = $model->randomCode();
        $transactionID = $model->saveItem(['user_id' => $userID,'type' => $type,'total' => $total,'category' => $category,'status' => $status,'code' => $code],['task' => 'add-item']);
        return $transactionID;
    }
}
