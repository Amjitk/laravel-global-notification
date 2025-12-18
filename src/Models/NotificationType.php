<?php

namespace AmjitK\GlobalNotification\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $table = 'gn_notification_types';
    
    protected $fillable = ['name', 'description', 'model_type'];

    public function templates()
    {
        return $this->hasMany(NotificationTemplate::class);
    }
}
