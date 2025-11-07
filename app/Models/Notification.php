<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'message',
        'type',
        'category',
        'icon',
        'action_url',
        'action_text',
        'is_read',
        'read_at',
        'user_id',
        'company_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title' => 'array',
        'message' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the notification.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return false;
        }

        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        if (!$this->is_read) {
            return false;
        }

        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return true;
    }

    /**
     * Get title for current locale.
     */
    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->title[$locale] ?? $this->title['en'] ?? '';
    }

    /**
     * Get message for current locale.
     */
    public function getLocalizedMessageAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->message[$locale] ?? $this->message['en'] ?? '';
    }

    /**
     * Get icon class based on type.
     */
    public function getIconClassAttribute(): string
    {
        $icons = [
            'info' => 'fas fa-info-circle text-info',
            'success' => 'fas fa-check-circle text-success',
            'warning' => 'fas fa-exclamation-triangle text-warning',
            'error' => 'fas fa-times-circle text-danger',
            'offer' => 'fas fa-tag text-primary',
            'coupon' => 'fas fa-qrcode text-primary',
            'loyalty' => 'fas fa-star text-warning',
            'affiliate' => 'fas fa-handshake text-info',
            'transaction' => 'fas fa-credit-card text-success',
            'system' => 'fas fa-cog text-secondary',
        ];

        return $icons[$this->type] ?? $icons['info'];
    }

    /**
     * Create a notification for a user.
     */
    public static function createForUser(int $userId, array $data): self
    {
        return self::create(array_merge($data, [
            'user_id' => $userId,
        ]));
    }

    /**
     * Create a notification for a company.
     */
    public static function createForCompany(int $companyId, array $data): self
    {
        return self::create(array_merge($data, [
            'company_id' => $companyId,
        ]));
    }

    /**
     * Create a bulk notification for multiple users.
     */
    public static function createBulkForUsers(array $userIds, array $data): bool
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = array_merge($data, [
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return self::insert($notifications);
    }

    /**
     * Set title as JSON.
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Set message as JSON.
     */
    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get title as array.
     */
    public function getTitleAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get message as array.
     */
    public function getMessageAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include notifications for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include notifications for a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include notifications of a specific type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include notifications of a specific category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsReadForUser(int $userId): int
    {
        return self::forUser($userId)
                   ->unread()
                   ->update([
                       'is_read' => true,
                       'read_at' => now(),
                   ]);
    }

    /**
     * Get unread count for a user.
     */
    public static function getUnreadCountForUser(int $userId): int
    {
        return self::forUser($userId)
                   ->unread()
                   ->count();
    }
}