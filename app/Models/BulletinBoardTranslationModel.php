<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinBoardTranslationModel extends Model
{
    protected $table = 'rrt_bulletin_board_translations';
    protected $fillable = [
        'bulletin_board_id',
        'language',
        'name',
        'content'
    ];
} 