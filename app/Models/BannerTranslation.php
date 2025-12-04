<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'banner_id',
        'locale',
        'title',
        'subtitle',
        'description',
        'button_text',
    ];
}
