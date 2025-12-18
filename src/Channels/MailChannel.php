<?php

namespace AmjitK\GlobalNotification\Channels;

use AmjitK\GlobalNotification\Contracts\NotificationChannel;
use AmjitK\GlobalNotification\Services\ContentParser;
use AmjitK\GlobalNotification\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class MailChannel implements NotificationChannel
{
    public function send($notifiable, $template, array $data)
    {
        $email = null;

        if (method_exists($notifiable, 'routeNotificationForMail')) {
            $email = $notifiable->routeNotificationForMail();
        }

        if (!$email && isset($notifiable->email)) {
             $email = $notifiable->email;
        }

        if (!$email) {
            return;
        }

        $content = ContentParser::parse($template->content, $data);
        $subject = ContentParser::parse($template->subject ?? '', $data);

        Mail::raw($content, function($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        // Log that email was sent
        NotificationLog::create([
            'notifiable_id' => $notifiable->getKey(),
            'notifiable_type' => $notifiable->getMorphClass(),
            'notification_type_id' => $template->notification_type_id,
            'channel' => 'mail',
            'data' => ['subject' => $subject, 'content' => $content],
            'read_at' => Carbon::now(),
        ]);
    }
}
