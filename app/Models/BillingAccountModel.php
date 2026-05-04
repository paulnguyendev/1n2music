<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class BillingAccountModel extends Model
{
    protected $table = 'rrt_billing_accounts';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes'];
    protected $fillable = [
        'phone',  'company_name', 'unit', 'province', 'id', 'first_name', 'date_of_birth', 'user_id', 'created_at', 'last_name', 'email', 'country', 'address_1', 'address_2', 'city', 'postal_code', 'account_type'
    ];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;

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
    }

    public function getItem($params = [], $option = [])
    {
        if ($option['task'] == 'get-first-info-account') {

            $result =  $this->where('user_id', $params['user_info'])->first();
        }
        return $result;
    }






    public function paymentmethod()
    {
        return $this->hasMany(PayoutMethodModel::class, 'payout_account_id', 'id');
    }
}
