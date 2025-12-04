<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosDevice extends Model
{
    protected $fillable = [
        'device_id',
        'company_id',
        'branch_id',
        'name',
        'model',
        'serial_number',
        'api_key',
        'status',
        'last_active_at',
        'last_ip_address',
        'settings',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'settings' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($device) {
            if (!$device->device_id) {
                $device->device_id = 'POS-' . strtoupper(uniqid());
            }
            if (!$device->api_key) {
                $device->api_key = bin2hex(random_bytes(32));
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function posTransactions(): HasMany
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online' && 
               $this->last_active_at && 
               $this->last_active_at->diffInMinutes(now()) < 5;
    }

    public function isOffline(): bool
    {
        return !$this->isOnline();
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online')
                     ->where('last_active_at', '>=', now()->subMinutes(5));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function markAsActive(string $ipAddress = null): void
    {
        $this->update([
            'last_active_at' => now(),
            'last_ip_address' => $ipAddress ?? request()->ip(),
            'status' => 'online',
        ]);
    }

    public function regenerateApiKey(): string
    {
        $newKey = bin2hex(random_bytes(32));
        $this->update(['api_key' => $newKey]);
        return $newKey;
    }
}