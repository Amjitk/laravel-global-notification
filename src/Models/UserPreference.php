<?php

namespace AmjitK\GlobalNotification\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $table = 'gn_user_preferences';

    protected $fillable = [
        'user_id',
        'notification_type_id',
        'channel',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }

    // Optional: Only if we need to access the user back
    // public function user() { return $this->belongsTo(User::class); }
}
