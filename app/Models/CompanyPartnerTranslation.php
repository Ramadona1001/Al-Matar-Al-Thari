<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPartnerTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'company_partner_id',
        'locale',
        'name',
    ];
}
