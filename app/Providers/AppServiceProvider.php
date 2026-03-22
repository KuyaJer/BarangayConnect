<?php

namespace App\Providers;

use App\Models\Complaint;
use App\Models\ServiceRequest;
use App\Observers\ComplaintObserver;
use App\Observers\ServiceRequestObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        ServiceRequest::observe(ServiceRequestObserver::class);
        Complaint::observe(ComplaintObserver::class);

        // Share unread notification count and avatar with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user        = auth()->user();
                $navNotifications = \App\Models\BrgyNotification::where('user_id', $user->id)
                    ->latest()
                    ->limit(8)
                    ->get();
                $unreadCount = $navNotifications->where('read', false)->count();
                $avatarUrl = $user->profile?->avatar_url;
                $view->with('navNotifications', $navNotifications);
                $view->with('unreadNotificationsCount', $unreadCount);
                $view->with('navAvatarUrl', $avatarUrl);
            }
        });
    }
}
