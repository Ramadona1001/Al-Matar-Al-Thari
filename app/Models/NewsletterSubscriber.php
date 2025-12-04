<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscriber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'name',
        'subscribed_at',
        'unsubscribed_at',
        'is_active',
        'source',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSubscribed($query)
    {
        return $query->whereNotNull('subscribed_at')
            ->whereNull('unsubscribed_at')
            ->where('is_active', true);
    }

    public function unsubscribe()
    {
        $this->update([
            'unsubscribed_at' => now(),
            'is_active' => false,
        ]);
    }

    public function resubscribe()
    {
        $this->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'is_active' => true,
        ]);
    }
}
