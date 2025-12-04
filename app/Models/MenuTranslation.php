<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'menu_id',
        'locale',
        'label',
    ];
}
