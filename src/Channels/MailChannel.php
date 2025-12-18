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
        } elseif (is_object($notifiable) && isset($notifiable->email)) {
            $email = $notifiable->email;
        } elseif (is_string($notifiable)) {
             $email = $notifiable; // Direct email string
        }

        if (!$email) { // Changed from `if ($email)` to `if (!$email)` to maintain original logic flow of returning if no email is found.
            return;
        }

        $content = ContentParser::parse($template->content, $data);
        $subject = ContentParser::parse($template->subject ?? '', $data);

        if ($email) {
            Mail::raw($content, function($message) use ($email, $subject, $data) {
               $message->to($email)->subject($subject);
               
               if (!empty($data['from_email'])) {
                   $message->from($data['from_email'], $data['from_name'] ?? null);
               }
            });
            
             $id = ($notifiable instanceof \Illuminate\Database\Eloquent\Model) ? $notifiable->getKey() : 0;
             $type = ($notifiable instanceof \Illuminate\Database\Eloquent\Model) ? $notifiable->getMorphClass() : 'guest';

             NotificationLog::create([
                'notifiable_id' => $id,
                'notifiable_type' => $type,
                'notification_type_id' => $template->notification_type_id ?? null, // Handle null for manual
                'channel' => 'mail',
                'data' => [
                    'subject' => $subject, 
                    'content' => $content,
                ],
                'meta' => [
                    'is_manual' => $data['is_manual'] ?? false,
                    'guest_email' => $email,
                    'source' => $data['source'] ?? 'system',
                ],
                // 'read_at' => \Illuminate\Support\Carbon::now(), // Emails are inherently "read" or "delivered" but generic logs usually stay unread until user confirmation.
                // Actually, logging email sending as 'read_at' => now() is standard for "sent" logs, or null if we track open. 
                // Using now() for compatibility with previous code.
                'read_at' => \Illuminate\Support\Carbon::now(),
            ]);
        }
    }
}
