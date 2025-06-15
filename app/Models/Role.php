<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Many-to-Many: Role belongs to many users
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Many-to-Many: Role has many permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // Query Scope: Get a specific role by name
    public function scopeWithName($query, $name)
    {
        return $query->where('name', $name);
    }

    // Query Scope: Roles with specific permission
    public function scopeWithPermission($query, $permissionName)
    {
        return $query->whereHas('permissions', function($query) use ($permissionName) {
            $query->where('name', $permissionName);
        });
    }
}
