<?php

namespace AmjitK\GlobalNotification\Contracts;

interface NotificationChannel
{
    /**
     * Send the notification.
     *
     * @param mixed $notifiable
     * @param mixed $template
     * @param array $data
     * @return void
     */
    public function send($notifiable, $template, array $data);
}
