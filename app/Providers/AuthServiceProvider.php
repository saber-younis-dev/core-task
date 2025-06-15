<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register Gates for permissions
        Gate::before(function (User $user, string $ability) {
            // Super admin can do anything
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Define gates for user permissions
        Gate::define('view-users', function (User $user) {
            return $user->hasPermission('view_users');
        });

        Gate::define('edit-users', function (User $user) {
            return $user->hasPermission('edit_users');
        });

        Gate::define('delete-users', function (User $user) {
            return $user->hasPermission('delete_users');
        });

        // Define gates for post permissions
        Gate::define('create-posts', function (User $user) {
            return $user->hasPermission('create_posts');
        });

        Gate::define('edit-any-post', function (User $user) {
            return $user->hasPermission('edit_others_posts');
        });

        Gate::define('publish-posts', function (User $user) {
            return $user->hasPermission('publish_posts');
        });
    }
}
