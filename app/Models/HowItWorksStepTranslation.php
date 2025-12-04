<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HowItWorksStepTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'how_it_works_step_id',
        'locale',
        'title',
        'description',
    ];
}
