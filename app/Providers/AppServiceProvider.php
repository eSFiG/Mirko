<?php

namespace App\Providers;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('file-action', function (User $user, File $file)
        {
            return $user->id === $file->user_id;
        });
    }
}
