<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-user', function (User $user, User $model) {
            if($user->role->slug === 'admin'){
                return true;
            }
            return $user->id === $model->creator_id;
        });

        Gate::define('update-user', function (User $user, User $model) {
            if($user->role->slug === 'admin'){
                return true;
            }
            return $user->id === $model->creator_id;
        });


        Gate::define('delete-user', function (User $user) {
            return $user->role->slug === 'admin';
        });


        Gate::define('delete-self', function (User $user, User $model) {
            return $user->id !== $model->id;
        });
    }
}
