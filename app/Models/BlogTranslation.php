<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'blog_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
