<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackHistoryModel extends Model
{

    protected $table = 'rrt_tracks_history';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = ['user_id','track_id'];
    use HasFactory, SoftDeletes;
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
    public function track()
    {
        return $this->belongsTo(TrackModel::class, 'track_id');
    }
}
