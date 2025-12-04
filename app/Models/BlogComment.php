<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'name',
        'email',
        'website',
        'comment',
        'is_approved',
        'parent_id',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get the blog that owns the comment.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get approved replies.
     */
    public function approvedReplies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
}
