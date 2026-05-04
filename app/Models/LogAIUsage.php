<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAIUsage extends Model
{
    protected $table = 'rrt_log_ai_usage';
    protected $fillable = ['ai_id','user_id','before_usage_count','amount','current_usage_count','service_order_id','subscription_order_id', 'created_at','updated_at','recognition_id','mastering_id','note'];

    public function aiPackages()
    {
        return $this->hasMany(AIPackage::class, 'ai_id');
    }
    public function aiPackageRoles()
    {
        return $this->hasMany(AIPackageRole::class, 'ai_id');
    }
}
