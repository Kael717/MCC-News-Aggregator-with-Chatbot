<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\News;
use App\Observers\AnnouncementObserver;
use App\Observers\EventObserver;
use App\Observers\NewsObserver;

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
        // Register model observers
        Announcement::observe(AnnouncementObserver::class);
        Event::observe(EventObserver::class);
        News::observe(NewsObserver::class);
    }
}
