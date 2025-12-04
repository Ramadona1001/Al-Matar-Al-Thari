<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionSettingTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'section_setting_id',
        'locale',
        'title',
        'subtitle',
    ];
}

