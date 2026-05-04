<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIPackageRole extends Model
{
    protected $table = 'rrt_ai_package_roles';
    protected $fillable = ['ai_id', 'role_id', 'package_id', 'usage_count', 'download_available', 'price'];
    protected $with = ['aiService','role','aiPackage'];
    public function aiService()
    {
        return $this->belongsTo(AIService::class, 'ai_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function aiPackage()
    {
        return $this->belongsTo(AIPackage::class, 'package_id');
    }
}
