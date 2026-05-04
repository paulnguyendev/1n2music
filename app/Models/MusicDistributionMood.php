<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class MusicDistributionMood extends Model
{
    protected $table = 'rrt_music_distribution_moods_relation';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'music_distribution_id', 'mood_id'];
    public function info() {
        return $this->belongsTo(MoodsModel::class,'mood_id','id');
    }
}
