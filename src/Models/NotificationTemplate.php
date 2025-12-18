<?php

namespace AmjitK\GlobalNotification\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'gn_notification_templates';
    
    protected $fillable = [
        'notification_type_id', 'channel', 'subject', 'content', 'configurations', 'is_active'
    ];
    
    protected $casts = [
        'configurations' => 'array',
        'is_active' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }
}
