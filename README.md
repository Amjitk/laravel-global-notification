<p align="center">
  <img src="resources/img/logo.png" width="200" alt="Global Notification Logo">
</p>

# Laravel Global Notification

A comprehensive notification system for Laravel applications. This package allows you to manage notification types and templates via a CMS and automatically trigger them based on system events.

## Features

- **CMS Interface**: Manage notification types and templates (Email, Database, SMS, etc.).
- **Multi-Channel Support**: Configure different templates for different channels for the same event.
- **Auto-Triggering**: Automatically fire notifications on Eloquent model events (created, updated, etc.).
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
- **Middleware**: Middleware for admin routes (default: `['web']`).

## Usage

### 1. Admin - Manage Notifications

Visit `/global-notification/notification-types` to create new Notification Types (e.g., `order_placed`).
Inside a Type, create Templates for each channel.

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

### 3. User - View Notifications

Users can view their notifications at:
`/global-notification/my-notifications`

## Testing

To run the package tests:

```bash
composer install
vendor/bin/phpunit
```
