<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'rrt_currency';
    protected $fillable = ['id','name','exchange_rate'];
    protected $crudNotAccepted = ['data_attributes', '_token', 'confirm_password', 'is_agree', 'visibility_text', 'track_type_id_text', 'unTaggedMp3', 'stems', 'taggedMp3', 'thumbnail_url', 'tags', 'genres', 'track_key_id_text', 'moods', 'invs', 'genres', 'contracts_tracks', 'genres_text', 'invs_text', 'moods_text', 'contracts', 'contractsTotal', 'thumbnail_url', 'unTaggedMp3', 'stems', 'taggedMp3'];
    use HasFactory;
}
