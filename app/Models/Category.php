<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    // Many-to-Many: Category has many posts
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    // Query Scope: Categories with a minimum number of posts
    public function scopeWithPostsMin($query, $count)
    {
        return $query->withCount('posts')
            ->having('posts_count', '>=', $count);
    }

    // Query Scope: Order by post count
    public function scopeOrderByPostCount($query, $direction = 'desc')
    {
        return $query->withCount('posts')
            ->orderBy('posts_count', $direction);
    }
}
