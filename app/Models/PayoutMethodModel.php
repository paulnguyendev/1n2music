<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class PayoutMethodModel extends Model
{
    protected $table = 'rrt_payout_method';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes'];
    protected $fillable = [ 'id', 'payout_account_id', 'created_at', 'method', 'is_selected', 'is_active'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;



    public function info()
    {
        return $this->hasOne(PayoutMethodInfoModel::class, 'payout_method_id', 'id');
    }

    // public function createMethod($params)
    // {
    //     $this->create([
    //         'payout_account_id' => $params['id'],
    //         'method' => $params['method'],
    //         'is_selected' => 1,
    //         'is_active' => 1,
    //         'tax_id' => $params['tax_id']
    //     ]);
    // }

    public function createMethodPayPal($id)
    {
        $result =   PayoutMethodModel::create([
            'payout_account_id' => $id,
            'method' => 'paypal',
            'is_selected' => 1,
            'is_active' => 1
        ]);

        return $result;
    }
    public function createMethodBank($id, $method = null)
    {
        $result =   PayoutMethodModel::create([
            'payout_account_id' => $id,
            'method' => 'bank',
            'is_selected' => 1,
            'is_active' => 1,
            'method' => $method
        ]);

        return $result;
    }

    public function activeMethod($params)
    {
        //  dd($params);
        if ($params['id']) {
            $item =  $this->where('id', $params['id'])
                ->where('method', $params['method'])
                ->first();
            if ($item) {
                $result =  $item->update(['is_selected' => $params['selected']]);
                //  dd($params['user_id']);
                $method =   ($params['method'] == 'paypal') ? 'bank' : 'paypal';
                $this->where('method', $method)->where('payout_account_id', $item->payout_account_id)->update(['is_selected' => 0]);
                return $result;
            }
        } else {

            $method =    $this->create(
                [
                    'payout_account_id' => $params['payout_account_id'],
                    'is_selected' => 1,
                    'method' => $params['method'],
                    'is_active' => 1
                ]
            );

            return $method ?? 0;
        }
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
    public function getItem($params = [], $options = [])
    {
        if ($options['task'] == 'account') {
            $query = $this->select($this->checkEmail);
            $result = $query->where('username', $params['account'])->orWhere('email', $params['account'])->first();
        }
        $query = $this->select($this->fillable);
        if ($options['task'] == 'login') {
            $result = $query->where('email', $params['email'])->where('password', $params['password'])->first();
        }
        if ($options['task'] == 'email') {
            $result = $query->where('email', $params['email'])->first();
        }
        if ($options['task'] == 'phone') {
            $result = $query->where('phone', $params['phone'])->first();
        }
        if ($options['task'] == 'username') {
            $result = $query->where('username', $params['username'])->first();
        }
        if ($options['task'] == 'id') {
            $result = $query->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'code') {
            $result = $query->where('code', $params['code'])->first();
        }
        if ($options['task'] == 'token') {
            $result = $query->where('token', $params['token'])->first();
        }
        if ($options['task'] == 'identification') {
            $result = $query->where('identification', $params['identification'])->first();
        }
        if ($options['task'] == 'method' && isset($params['payout_account_id'])) {
            $result = $query->where('payout_account_id',$params['payout_account_id'])->where('method', $params['method'])->first();
        }
        if ($options['task'] == 'check') {
            if (isset($params['email'])) {
                $query = $query->where('email', $params['email']);
            }
            if (isset($params['phone'])) {
                $query = $query->where('phone', $params['phone']);
            }
            if (isset($params['username'])) {
                $query = $query->where('username', $params['username']);
            }
            $result = $query->first();
        }
        return $result;
    }
}
