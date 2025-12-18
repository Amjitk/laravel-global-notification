<?php

namespace AmjitK\GlobalNotification\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use AmjitK\GlobalNotification\GlobalNotificationServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            GlobalNotificationServiceProvider::class,
        ];
    }
    
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
