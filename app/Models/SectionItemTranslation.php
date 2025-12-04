<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionItemTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'section_item_id',
        'locale',
        'title',
        'subtitle',
        'content',
        'link_text',
    ];
}
