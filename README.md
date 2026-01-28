<p align="center">
  <img src="resources/img/logo.png" width="200" alt="Global Notification Logo">
</p>

# Laravel Global Notification

A comprehensive notification system for Laravel applications. This package allows you to manage notification types and templates via a CMS and automatically trigger them based on system events.

## Features

- **CMS Interface**: Manage types, define variables, and test templates directly from the admin panel.
- **Multi-Channel Support**: Configure different templates for different channels for the same event.
- **User Preference Center**: Users can toggle specific notification channels on/off.
- **Auto-Triggering**: Automatically fire notifications on Eloquent model events (created, updated, etc.).
- **In-App Toast Widget**: Real-time polling widget to show notifications instantly.
- **User Notification Center**: Built-in UI for users to view and manage their in-app notifications.
- **Dynamic Content**: Use placeholders like `{{name}}` in your templates.

## Installation

1.  Install the package via Composer:

    ```bash
    composer require amjitk/laravel-global-notification
    ```

    _Note: If testing locally without Packagist, configure your `composer.json` repositories to point to the local path._

2.  Run Migrations:

    ```bash
    php artisan migrate
    ```

3.  (Optional) Publish Configuration and Assets:

    ```bash
    php artisan vendor:publish --tag=global-notification-config
    php artisan vendor:publish --tag=global-notification-views
    ```

## Configuration

The configuration file `config/global-notification.php` allows you to customize:

- **Channels**: Enabled channels (default: `mail`, `database`).
- **Route Prefix**: URL prefix for the package routes (default: `global-notification`).
- **Middleware**: Middleware for admin routes. Configurable via `.env`:
  ```env
  GLOBAL_NOTIFICATION_AUTH_ENABLED=true # Set to false to disable authentication
  ```

## Usage

### 1. Admin - Manage Notifications

Visit `/global-notification/notification-types` to create new Notification Types (e.g., `order_placed`).

- **Variables**: Define available variables (e.g. `order_id, amount`) when creating a Type.
- **Templates**: Create templates for each channel. Use the defined variables (e.g. `{{ amount }}`).
- **Testing**: Use the "**Send Test**" button on any template to send a dummy notification to yourself immediately.

### 2. Triggering Notifications

#### Manual Trigger

Use the `NotificationService` to send a notification programmatically:

```php
use AmjitK\GlobalNotification\Services\NotificationService;

$service = new NotificationService();
$service->send('order_placed', $user, ['order_id' => 123, 'amount' => '$50']);
```

#### Automatic Trigger (Model Events)

Add the `AutoNotifyTrait` to your Eloquent model:

```php
use AmjitK\GlobalNotification\Traits\AutoNotifyTrait;

class Order extends Model
{
    use AutoNotifyTrait;

    // Map system events to Notification Type keys
    public $notificationRules = [
        'created' => 'order_placed',   // Fires 'order_placed' when Order is created
        'updated' => 'order_updated',  // Fires 'order_updated' when Order is updated
    ];
}
```

### 3. Notification Logs

View all system notifications at:
`/global-notification/logs`

This page displays a global log of all notifications triggered in the system.

### 4. Scheduled Notifications

You can schedule notifications using the package's `send` method combined with Laravel's Scheduler. This creates a powerful workflow where you can define templates in the CMS types and trigger them via Cron jobs.

**Why use the package's `send` method instead of `Notification::send` or `Mail::send`?**
Using `$service->send()` allows you to:
1.  **Use CMS Templates**: Manage the content of your scheduled emails/alerts dynamically without deploying code.
2.  **Separate Logging**: By tagging them with `withSource('scheduled')`, they appear in the dedicated **Scheduled Logs** dashboard, separating them from transactional or user alerts.

**Example: Sending a Daily Digest**

```php
// app/Console/Kernel.php or app/Console/Commands/SendDailyDigest.php

use AmjitK\GlobalNotification\Services\NotificationService;

protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $service = app(NotificationService::class);
        $users = \App\Models\User::all();
        
        foreach ($users as $user) {
            // 'daily_digest' is a Notification Type defined in your CMS
            $service->withSource('scheduled')
                    ->send('daily_digest', $user, [
                        'date' => now()->toFormattedDateString(),
                        'stats' => $user->getDailyStats()
                    ]);
        }
    })->dailyAt('09:00');
}
```

### 5. In-App Widget (Toast)

To display a real-time toast notification in your application whenever a new notification arrives:

1.  Make sure you have published the views (or reference the package view).
2.  Add the following line to your main layout file (e.g., `resources/views/layouts/app.blade.php`), preferably just before the closing `</body>` tag:

    ```blade
    @include('global-notification::components.toast')
    ```

3.  Ensure your application layout includes Alpine.js or vanilla JS support (the component uses vanilla JS but allows for easy customization).

### 6. User Preferences

Users can manage their notification preferences (opt-in/out of specific channels) by visiting:
`/global-notification/preferences`

- The interface allows users to toggle channels (e.g., turn off Email for "Order Updates" but keep it for "Promotions").
- The system automatically checks these preferences before sending any notification.

## Testing

To run the package tests:

```bash
composer install
vendor/bin/phpunit
```
