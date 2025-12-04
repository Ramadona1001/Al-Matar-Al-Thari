<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'section_id',
        'locale',
        'title',
        'subtitle',
        'content',
    ];
}
