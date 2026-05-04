<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSendMailModel extends Model
{
    use HasFactory;

    protected $table = 'rrt_request_sendmail';

    protected $fillable = [
        'user_id','email', 'noti_id', 'data', 'type','is_send','is_failed'
    ];

    protected $casts = [
        'data' => 'array',
        'is_send' => 'boolean',
    ];

    public function user(){
        return $this->hasOne(UserModel::class, 'id', 'user_id');
    }

    public function log(){
        return $this->hasOne(RequestSendMailLogModel::class, 'request_id', 'id');
    }
}
