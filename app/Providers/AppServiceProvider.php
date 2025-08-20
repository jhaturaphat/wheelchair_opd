<?php

namespace App\Providers;

use App\Notifications\MorpromtChanel;
use Illuminate\Support\ServiceProvider;
use App\Notifications\TelegramChannel;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Notification::extend('telegram', function ($app) {
            return new TelegramChannel();
        });

        
        Notification::extend('morpromt', function ($app) {
            return new MorpromtChanel();
        });
    }
}
