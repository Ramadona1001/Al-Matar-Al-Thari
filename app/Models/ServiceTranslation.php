<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'service_id',
        'locale',
        'title',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
