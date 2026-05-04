<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class LogRequestPayoutModel extends Model
{
    use HasFactory;
    protected $table = 'rrt_log_request_payout';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order'];
    protected $fillable = ['id', 'image', 'user_id', 'content', 'type', 'request_payout_id'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];




    public function users()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(AdminModel::class, 'user_id', 'id');
    }

    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccountModel::class, 'user_id', 'user_id');
    }
}
