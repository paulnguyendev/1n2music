<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'rrt_roles';
    protected $fillable = ['name','slug'];
    protected $with = ['aiPackages'];
    public function aiPackages()
    {
        return $this->belongsToMany(AIPackage::class, 'rrt_ai_package_roles', 'role_id', 'package_id')
            ->withPivot('ai_id', 'usage_count', 'download_available', 'price');
    }
    public function getAiPackageRole()
    {
        return $this->aiPackages()->get();
    }
}
