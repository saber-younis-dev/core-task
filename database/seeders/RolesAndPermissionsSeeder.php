<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'create_posts', 'description' => 'Create posts'],
            ['name' => 'edit_posts', 'description' => 'Edit own posts'],
            ['name' => 'delete_posts', 'description' => 'Delete posts'],
            ['name' => 'edit_others_posts', 'description' => 'Edit posts by other users'],
            ['name' => 'publish_posts', 'description' => 'Publish posts'],
            ['name' => 'approve_posts', 'description' => 'Approve or reject submitted posts'],
            ['name' => 'view_all_posts', 'description' => 'View all posts including drafts'],
            ['name' => 'assign_roles', 'description' => 'Assign roles to users'],
            ['name' => 'view_users', 'description' => 'View user list'],
            ['name' => 'edit_users', 'description' => 'Edit user details'],
            ['name' => 'delete_users', 'description' => 'Delete users'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator role with all permissions'
        ]);

        $editorRole = Role::create([
            'name' => 'editor',
            'description' => 'Editor role with post management permissions'
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'description' => 'Regular user with basic permissions'
        ]);

        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::all());

        $editorRole->permissions()->attach(
            Permission::whereIn('name', [
                'create_posts',
                'edit_posts',
                'edit_others_posts',
                'delete_posts',
                'publish_posts',
                'approve_posts',
                'view_all_posts'
            ])->get()
        );

        $userRole->permissions()->attach(
            Permission::whereIn('name', [
                'create_posts',
                'edit_posts'
            ])->get()
        );

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->roles()->attach($adminRole);

        // Create editor user
        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
        ]);

        $editor->roles()->attach($editorRole);
    }
}
