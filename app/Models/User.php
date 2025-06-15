<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // One-to-Many: User has many posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // One-to-Many: User has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Many-to-Many: User has many roles
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Query Scope: Active users only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Query Scope: Users with more than X posts
    public function scopeWithPostsMoreThan($query, $count)
    {
        return $query->withCount('posts')
            ->having('posts_count', '>', $count);
    }

    // Query Scope: Users with specific role
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('roles', function($query) use ($roleName) {
            $query->where('name', $roleName);
        });
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    public function hasPermission($permission)
    {
        return $this->roles->flatMap->permissions->contains('name', $permission);
    }
}
