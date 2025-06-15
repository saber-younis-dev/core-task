<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'status',
    ];

    // Define status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';

    // One-to-Many (Inverse): Post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // One-to-Many: Post has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Many-to-Many: Post belongs to many categories
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Query Scope: Posts with at least X comments
    public function scopeWithCommentsAtLeast($query, $count)
    {
        return $query->withCount('comments')
            ->having('comments_count', '>=', $count);
    }

    // Query Scope: Published posts only
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Query Scope: Posts in a specific category
    public function scopeInCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        });
    }

    // Scope for pending posts
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Scope for rejected posts
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
