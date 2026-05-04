<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
class LogStreamCountModel extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'rrt_log_stream_count';
    public $timestamps = false;
    protected $fillable = ['id', 'music_distribution_id','user_id', 'platform_id', 'stream_count','revenue','created_at','updated_at','deleted_at'];
    public function musicDistribution(){
        return $this->belongsTo(MusicDistributionModel::class,'music_distribution_id','id');
    }
    public function platforms(){
        return $this->belongsTo(PlatformModel::class,'platform_id');
    }
}
