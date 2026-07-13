<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Supply;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

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
        // This makes data accessible in every single blade view (Sidebar, Header, etc.)
        View::composer('*', function ($view) {
            $unreadCount = 0;
            $availableSupplies = [];

            // Only run this if a user is actually logged in
            if (Auth::check()) {
                // 1. Get count of unread notifications for the red badge
                $unreadCount = Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();

                // 2. Get available supplies for the New Request modal
                $availableSupplies = Supply::where('quantity', '>', 0)->get();
            }

            $view->with([
                'unreadCount' => $unreadCount,
                'availableSupplies' => $availableSupplies
            ]);
        });
    }
}




