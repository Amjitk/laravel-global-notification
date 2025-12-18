<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use AmjitK\GlobalNotification\Http\Controllers\NotificationConfigController;
use AmjitK\GlobalNotification\Http\Controllers\NotificationLogController;
use AmjitK\GlobalNotification\Http\Controllers\NotificationTemplateController;

Route::group(['middleware' => Config::get('global-notification.admin_middleware'), 'prefix' => Config::get('global-notification.route_prefix')], function () {
    
    // Admin Configurations
    Route::resource('notification-types', NotificationConfigController::class);
    Route::resource('notification-templates', NotificationTemplateController::class)->only(['store', 'update', 'destroy']);
    
    // User Notifications (assuming 'auth' is handled by the app)
    Route::get('my-notifications', [NotificationLogController::class, 'index'])->name('global-notification.user.index');
    Route::post('my-notifications/{id}/read', [NotificationLogController::class, 'markAsRead'])->name('global-notification.user.read');
});
