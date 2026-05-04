<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class MusicDistributionPlatform extends Model
{
    protected $table = 'rrt_music_distribution_platform_relation';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'music_distribution_id', 'platform_id'];
    public function info() {
        return $this->belongsTo(PlatformModel::class,'platform_id','id');
    }
}
