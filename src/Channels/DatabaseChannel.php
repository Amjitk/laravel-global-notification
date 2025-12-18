<?php

namespace AmjitK\GlobalNotification\Channels;

use AmjitK\GlobalNotification\Contracts\NotificationChannel;
use AmjitK\GlobalNotification\Models\NotificationLog;
use AmjitK\GlobalNotification\Services\ContentParser;

class DatabaseChannel implements NotificationChannel
{
    public function send($notifiable, $template, array $data)
    {
        $content = ContentParser::parse($template->content, $data);
        $subject = ContentParser::parse($template->subject ?? '', $data);

        $id = ($notifiable instanceof \Illuminate\Database\Eloquent\Model) ? $notifiable->getKey() : 0;
        $type = ($notifiable instanceof \Illuminate\Database\Eloquent\Model) ? $notifiable->getMorphClass() : 'guest';
        
        // If guest, capture email in data if available
        if ($type === 'guest') {
             $data['guest_email'] = $notifiable->email ?? ($notifiable->routeNotificationForMail() ?? null);
        }

        NotificationLog::create([
            'notifiable_id' => $id,
            'notifiable_type' => $type,
            'notification_type_id' => $template->notification_type_id ?? null,
            'channel' => 'database',
            'data' => [
                'subject' => $subject,
                'content' => $content,
                'original_data' => $data,
            ],
            'meta' => [
                'is_manual' => $data['is_manual'] ?? false,
                'guest_email' => $data['guest_email'] ?? null,
                'source' => $data['source'] ?? 'system',
            ]
        ]);
    }
}
