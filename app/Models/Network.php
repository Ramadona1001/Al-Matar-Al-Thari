<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Network extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'manager_network',
            'network_id',
            'manager_user_id'
        )->withTimestamps();
    }
}
