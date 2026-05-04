<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper

class RequestSendMailLogModel extends Model
{
    protected $table = 'rrt_request_sendmail_logs';
    protected $primaryKey = 'id';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes'];
    protected $fillable = ['id', 'notice_id','request_id', 'status', 'message', 'created_at','updated_at'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;
    public function notice(){
        return $this->belongsTo(NoticeLogModel::class,'notice_id');
    }
    public function requestSendMail(){
        return $this->belongsTo(RequestSendMailModel::class,'request_id');
    }
}
