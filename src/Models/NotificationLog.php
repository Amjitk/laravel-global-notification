<?php

namespace AmjitK\GlobalNotification\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'gn_notification_logs';
    
    protected $fillable = [
        'notifiable_id', 'notifiable_type', 'notification_type_id', 'channel', 'data', 'meta', 'read_at'
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array',
        'meta' => 'array',
    ];

    public function type()
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
