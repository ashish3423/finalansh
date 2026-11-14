<?php

namespace App\Providers;

use App\Models\User;
use App\Services\VehicleExpiryReminderService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        Paginator::useBootstrapFive();
        Gate::define('manage-records', fn (User $user): bool => $user->isSuperAdmin());
        Gate::define('manage-users', fn (User $user): bool => $user->isSuperAdmin());
        View::composer('layouts.newapp', function ($view): void {
            $userId = Auth::id();
            $reminders = $userId
                ? app(VehicleExpiryReminderService::class)->forUser((int) $userId)
                : collect();

            $view->with('vehicleExpiryReminders', $reminders);
        });
    }
}
