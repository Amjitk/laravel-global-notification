<?php

namespace AmjitK\GlobalNotification;

use Illuminate\Support\ServiceProvider;

class GlobalNotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        $this->publishes([
            __DIR__.'/../config/global-notification.php' => $app->configPath('global-notification.php'),
        ], 'global-notification-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'global-notification');
        
        $this->publishes([
            __DIR__.'/../resources/views' => $app->resourcePath('views/vendor/global-notification'),
        ], 'global-notification-views');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/global-notification.php', 'global-notification'
        );
    }
}
