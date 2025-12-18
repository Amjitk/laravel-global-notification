<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use AmjitK\GlobalNotification\Http\Controllers\NotificationConfigController;
use AmjitK\GlobalNotification\Http\Controllers\NotificationComposeController;
use AmjitK\GlobalNotification\Http\Controllers\NotificationLogController;
use AmjitK\GlobalNotification\Http\Controllers\NotificationTemplateController;
use AmjitK\GlobalNotification\Http\Controllers\ScheduledNotificationController;

Route::group(['middleware' => Config::get('global-notification.admin_middleware'), 'prefix' => Config::get('global-notification.route_prefix')], function () {
    
    // Scheduled Notifications
    // Scheduled Notifications
    Route::get('/admin/scheduled', [ScheduledNotificationController::class, 'index'])->name('global-notification.notifications.scheduled');

    // Admin Configurations
    Route::resource('notification-types', NotificationConfigController::class)->names([
        'index' => 'global-notification.notification-types.index',
        'create' => 'global-notification.notification-types.create',
        'store' => 'global-notification.notification-types.store',
        'show' => 'global-notification.notification-types.show',
        'edit' => 'global-notification.notification-types.edit',
        'update' => 'global-notification.notification-types.update',
        'destroy' => 'global-notification.notification-types.destroy',
    ]);
    
    Route::resource('notification-templates', NotificationTemplateController::class)->only(['store', 'update', 'destroy'])->names([
        'index' => 'global-notification.notification-templates.index', // Even if only is set, providing names avoids ambiguity
        'store' => 'global-notification.notification-templates.store',
        'update' => 'global-notification.notification-templates.update',
        'destroy' => 'global-notification.notification-templates.destroy',
    ]);
    
    Route::get('/notification-types/{id}', [NotificationConfigController::class, 'show'])->name('notification-types.show');

    // Manual / Ad-hoc Notifications
    // Manual / Ad-hoc Notifications
    Route::get('/compose', [NotificationComposeController::class, 'create'])->name('global-notification.notifications.compose');
    Route::post('/compose', [NotificationComposeController::class, 'store'])->name('global-notification.notifications.send');

    // User Notifications (assuming 'auth' is handled by the app)
    Route::get('my-notifications', [NotificationLogController::class, 'index'])->name('global-notification.user.index');
    Route::post('my-notifications/{id}/read', [NotificationLogController::class, 'markAsRead'])->name('global-notification.user.read');
});
