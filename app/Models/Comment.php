<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // One-to-Many (Inverse): Comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // One-to-Many (Inverse): Comment belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Query Scope: Approved comments only
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Query Scope: Comments from a specific user
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Query Scope: Recent comments
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
