<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class PayoutMethodInfoModel extends Model
{
    protected $table = 'rrt_payout_method_info';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes', 'method', 'is_active', 'first_name', 'last_name', 'email', 'company_name', 'phone', 'country', 'address_1', 'commision', 'parent_id'];
    protected $fillable = ['paypal_id', 'id', 'payout_method_id', 'swift', 'address', 'city', 'province', 'number', 'name_holder', 'currency'];
    use HasFactory;


    public function payoutMethod()
    {

        return $this->belongsTo(PayoutMethodModel::class, 'payout_method_id', 'id');
    }

    public function updatePaypal($data)
    {

        PayoutMethodInfoModel::where('id', $data['payout_method_info_id'])->update([
            'swift' => $data['paypal_swift_bic'] ?? '',
            'address' => $data['paypal_paymemt_address'] ?? '',
            'province' => $data['paypal_paymemt_province'] ?? '',
            'city' => $data['paypal_paymemt_city'] ?? '',
            'number' => $data['paypal_paymemt_number'] ?? '',
            'name_holder' => $data['paypal_name_holder_card'] ?? '',
            'currency' => $data['paypal_currency'] ?? '',
            'paypal_id' => $data['paypal_id'] ?? '',

        ]);

        PayoutMethodModel::where('id', $data['payout_method_id'])->update(['is_active' => $data['is_active'],  'is_selected' => 1]);
    }

    public function createPaypal($data, $id)
    {
        PayoutMethodInfoModel::create([
            'payout_method_id' => $id,
            'swift' => $data['paypal_swift_bic'] ?? '',
            'address' => $data['paypal_paymemt_address'] ?? '',
            'province' => $data['paypal_paymemt_province'] ?? '',
            'city' => $data['paypal_paymemt_city'] ?? '',
            'number' => $data['paypal_paymemt_number'] ?? '',
            'name_holder' => $data['paypal_name_holder_card'] ?? '',
            'currency' => $data['paypal_currency'] ?? '',
            'paypal_id' => $data['paypal_id'] ?? '',
        ]);

        PayoutMethodModel::where('id', $data['payout_method_id'])->update(['is_active' => 1]);
    }

    public function updateBank($data)
    {

        PayoutMethodInfoModel::where('id', $data['payout_method_info_id'])->update([
            'swift' => $data['bank_swift_bic'] ?? '',
            'address' => $data['bank_paymemt_address'] ?? '',
            'province' => $data['bank_paymemt_province'] ?? '',
            'city' => $data['bank_paymemt_city'] ?? '',
            'number' => $data['bank_paymemt_number'] ?? '',
            'name_holder' => $data['bank_name_holder_card'] ?? '',
            'currency' => $data['bank_currency'] ?? '',
        ]);

        PayoutMethodModel::where('id', $data['payout_method_id'])
            ->update(['is_active' => $data['is_active'], 'is_selected' => 1]);
    }
    public function createBank($data, $id)
    {
        PayoutMethodInfoModel::create([
            'payout_method_id' => $id,
            'swift' => $data['bank_swift_bic'] ?? '',
            'address' => $data['bank_paymemt_address'] ?? '',
            'province' => $data['bank_paymemt_province'] ?? '',
            'city' => $data['bank_paymemt_city'] ?? '',
            'number' => $data['bank_paymemt_number'] ?? '',
            'name_holder' => $data['bank_name_holder_card'] ?? '',
            'currency' => $data['bank_currency'] ?? '',
        ]);
        PayoutMethodModel::where('id', $data['payout_method_id'])->update(['is_active' => 1]);
    }
    public function saveItem($params = [], $option = [])
    {
        if ($option['task'] == 'add-item') {
            $paramsInsert = array_diff_key($params, array_flip($this->crudNotAccepted));
            $dataInsert = self::create($paramsInsert);
            $result =  $dataInsert->id;
            return $result;
        }
        if ($option['task'] == 'edit-item') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('id', $params['id'])->update($paramsUpdate);
        }
        if ($option['task'] == 'active-by-token') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('token', $params['token'])->update($paramsUpdate);
        }

        if ($option['task'] == 'save-setting') {
            dd($params);
        }
    }
}
