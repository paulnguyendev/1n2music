<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'rrt_languages';
    protected $fillable = ['id','name','code','status','created_at','updated_at'];
    use HasFactory;

    public static function getActiveLanguageCodes()
    {
        return self::where('status', 1)->pluck('code')->toArray();
    }

}
