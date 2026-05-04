<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslationModel extends Model
{
    protected $table = 'rrt_page_translations';
    protected $fillable = [
        'page_id',
        'language',
        'name',
        'content'
    ];
    
    public function page()
    {
        return $this->belongsTo(PageModel::class, 'page_id');
    }
} 