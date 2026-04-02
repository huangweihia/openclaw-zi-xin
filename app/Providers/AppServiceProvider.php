<?php

namespace App\Providers;

use App\Models\AdSlot;
use App\Models\Announcement;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);

        View::composer('layouts.app', function ($view): void {
            $marqueeAnnouncements = collect();
            $adSlot = null;

            if (Schema::hasTable('announcements')) {
                $marqueeAnnouncements = Announcement::query()
                    ->active()
                    ->orderBy('sort_order')
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->get();
            }

            if (Schema::hasTable('ad_slots')) {
                $adSlot = AdSlot::query()->orderBy('id')->first();
            }

            $view->with([
                'marqueeAnnouncements' => $marqueeAnnouncements,
                'adSlot' => $adSlot,
            ]);
        });
    }
}
