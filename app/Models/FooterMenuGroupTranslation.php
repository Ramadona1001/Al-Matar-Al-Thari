<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterMenuGroupTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'footer_menu_group_translations';

    protected $fillable = [
        'footer_menu_group_id',
        'locale',
        'name',
    ];
}
