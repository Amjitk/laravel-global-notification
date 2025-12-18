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

    /**
     * Send a manual/ad-hoc notification without a pre-defined type.
     *
     * @param mixed $notifiable Model instance or Generic object with routeNotificationFor('mail')
     * @param string $subject
     * @param string $content
     * @param array $channels List of channels to use: ['database', 'mail']
     * @param array $data Extra data (e.g. from_email, from_name)
     */
    public function sendManual($notifiable, string $subject, string $content, array $channels, array $data = [])
    {
        // Tag as manual
        $data['is_manual'] = true;

        foreach ($channels as $channelName) {
            if (isset($this->channels[$channelName])) {
                // (Logic removed: We now Support guest logging in DatabaseChannel)

                // Create a dynamic object mimicking a template
                $template = new \stdClass();
                $template->channel = $channelName;
                $template->subject = $subject;
                $template->content = $content;
                $template->notification_type_id = null; 

                $channelClass = $this->channels[$channelName];
                (new $channelClass)->send($notifiable, $template, $data);
            }
        }
    }
}

