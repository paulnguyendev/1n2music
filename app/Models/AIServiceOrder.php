<?php

namespace App\Models;

use App\Helpers\Template;
use Illuminate\Database\Eloquent\Model;

class AIServiceOrder extends Model
{
    protected $table = 'rrt_ai_service_orders';
    protected $fillable = ['id','user_id','pay_amount','usage_count','download_available','ai_id','payment_method','is_payment','status','note','created_at','updated_at'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes'];
    protected $with = ['aiService','user'];
    public function aiService()
    {
        return $this->belongsTo(AIService::class, 'ai_id', 'id');
    }
    public function user(){
        return $this->belongsTo(UserModel::class,'user_id','id');
    }
    public function logOrder(){
        return $this->hasMany(LogOrderAI::class,'order_id');
    }
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable)
            ->with(['user', 'aiService']); // eager load user và aiService
        if ($options['task'] == 'admin') {
            // Lọc theo điều kiện phân trang
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            // Tìm kiếm
            if (isset($params['search'])) {
                $query = $query->where('name', 'LIKE', "%{$params['search']}%");
            }
            // Loại trừ ID
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            if (isset($params['ai_id'])) {
                $query = $query->where('ai_id',$params['ai_id']);
            }

            // Thực hiện truy vấn
            $result = $query->orderBy('id', 'desc')->get();

            // Xử lý kết quả
            if (isset($params['is_map'])) {
                $controllerName = $params['controllerName'] ?? '';
                $result = $result->map(function ($item) use ($controllerName) {
                    $id = $item->id;
                    $item->userInfo = [
                        'email' => $item->user->email ?? '-',
                        'fullname' => ($item->user->fullname ?? ''),
                        'phone' => $item->user->phone ?? '',
                    ];
                    $item->aiServiceName = $item->aiService->name ?? '-';
                    $status = $item->status ?? 0;
                    $paymentStatus = $item->is_payment ?? 0;

                    // Tạo badge cho trạng thái thanh toán
                    $item->badgePaymentStatus = $paymentStatus == 1
                        ? Template::showStatus('badge', 'complete')
                        : Template::showStatus('badge', 'pending');

                    // Tạo badge cho trạng thái
                    $item->badgeStatus = $status == 1
                        ? Template::showStatus('badge', 'complete')
                        : Template::showStatus('badge', 'pending');
                    $routeUpdate = '#';
                    if ($controllerName){
                        $routeUpdate = rrt_route($controllerName.'/detail',['id'=>$id]);
                    }
                    $item->route_update = $routeUpdate;
                    return $item;
                });
            }

            // Nếu cần đếm số bản ghi
            if (isset($params['count'])) {
                return $result->count();
            }
        }

        return $result;
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
