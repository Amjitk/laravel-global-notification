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

        NotificationLog::create([
            'notifiable_id' => $notifiable->getKey(),
            'notifiable_type' => $notifiable->getMorphClass(),
            'notification_type_id' => $template->notification_type_id,
            'channel' => 'database',
            'data' => [
                'subject' => $subject,
                'content' => $content,
                'original_data' => $data
            ]
        ]);
    }
}
