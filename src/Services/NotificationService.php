<?php

namespace AmjitK\GlobalNotification\Services;


use AmjitK\GlobalNotification\Models\NotificationType;
use AmjitK\GlobalNotification\Models\NotificationLog;
use Illuminate\Database\Eloquent\Model;
use AmjitK\GlobalNotification\Channels\DatabaseChannel;
use AmjitK\GlobalNotification\Channels\MailChannel;


class NotificationService
{
    protected $channels = [
        'database' => DatabaseChannel::class,
        'mail' => MailChannel::class,
        // Register new channels here
    ];

    public function send(string $typeName, Model $notifiable, array $data = [])
    {
        $type = NotificationType::where('name', $typeName)->first();
        
        if (!$type) {
            return;
        }

        $templates = $type->templates()->where('is_active', true)->get();

        foreach ($templates as $template) {
            if (isset($this->channels[$template->channel])) {
                $channelClass = $this->channels[$template->channel];
                (new $channelClass)->send($notifiable, $template, $data);
            }
        }
    }
}

