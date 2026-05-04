<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIService extends Model
{
    protected $table = 'rrt_ai_services';
    protected $fillable = ['name'];

    const AIServiceAIMastering = 1;
    const AIServiceAIRecognition = 2;

    public function aiPackages()
    {
        return $this->hasMany(AIPackage::class, 'ai_id');
    }
    public function aiPackageRoles()
    {
        return $this->hasMany(AIPackageRole::class, 'ai_id');
    }
}
