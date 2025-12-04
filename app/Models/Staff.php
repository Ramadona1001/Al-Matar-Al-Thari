<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'club_id',
        'name',
        'email',
        'phone',
        'role',
        'status',
        'is_verified',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(\App\Models\Club::class);
    }
}
