<?php

namespace AmjitK\GlobalNotification\Traits;

use AmjitK\GlobalNotification\Services\NotificationService;
use Illuminate\Support\Facades\App;

trait AutoNotifyTrait
{
    /**
     * Boot the trait.
     */
    public static function bootAutoNotifyTrait()
    {
        static::created(function ($model) {
            $model->triggerNotification('created');
        });

        static::updated(function ($model) {
            $model->triggerNotification('updated');
        });
        
        static::deleted(function ($model) {
            $model->triggerNotification('deleted');
        });
    }

    /**
     * Trigger a notification for a specific event.
     * 
     * @param string $event
     */
    public function triggerNotification($event)
    {
        // Define rules in the model:
        // public $notificationRules = [
        //     'created' => 'order_placed',
        //     'updated' => 'order_updated'
        // ];
        
        if (isset($this->notificationRules) && isset($this->notificationRules[$event])) {
            $typeName = $this->notificationRules[$event];
            
            // Resolve Service
            $service = App::make(NotificationService::class);
            
            // Send
            $service->send($typeName, $this, $this->toArray()); // Pass model data as variables
        }
    }
}
