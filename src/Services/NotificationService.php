<?php

namespace AmjitK\GlobalNotification\Services;


use AmjitK\GlobalNotification\Models\NotificationType;
use AmjitK\GlobalNotification\Models\NotificationLog;
use Illuminate\Database\Eloquent\Model;
use AmjitK\GlobalNotification\Channels\DatabaseChannel;
use AmjitK\GlobalNotification\Channels\MailChannel;


use AmjitK\GlobalNotification\Models\UserPreference;

class NotificationService
{
    protected $channels = [
        'database' => DatabaseChannel::class,
        'mail' => MailChannel::class,
        // Register new channels here
    ];

    protected $source = 'system';

    /**
     * Set the source for the next notification.
     * 
     * @param string $source
     * @return $this
     */
    public function withSource(string $source)
    {
        $this->source = $source;
        return $this;
    }

    public function send(string $typeName, Model $notifiable, array $data = [])
    {
        // Inject source into data
        $data['source'] = $this->source;

        // Reset source for next call
        $currentSource = $this->source;
        $this->source = 'system';

        $type = NotificationType::where('name', $typeName)->first();

        if (!$type) {
            return;
        }

        $templates = $type->templates()->where('is_active', true)->get();

        foreach ($templates as $template) {
            if (isset($this->channels[$template->channel])) {

                // Check User Preference
                if ($this->isChannelEnabled($notifiable, $type->id, $template->channel)) {
                    $channelClass = $this->channels[$template->channel];
                    (new $channelClass)->send($notifiable, $template, $data);
                }
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
        // Tag as manual (unless overridden by source, but manual is usually explicit)
        $data['is_manual'] = true;

        // Inject source (if set via withSource)
        if (!isset($data['source'])) {
            $data['source'] = $this->source;
        }

        // Reset source
        $this->source = 'system';

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

    /**
     * Check if a channel is enabled for a specific user and notification type.
     * 
     * @param mixed $notifiable
     * @param int $typeId
     * @param string $channel
     * @return bool
     */
    protected function isChannelEnabled($notifiable, $typeId, $channel)
    {
        // Only applicable if notifiable is a Model (e.g. User)
        if (!($notifiable instanceof Model)) {
            return true;
        }

        $preference = UserPreference::where('user_id', $notifiable->getKey())
            ->where('notification_type_id', $typeId)
            ->where('channel', $channel)
            ->first();

        // If no preference record exists, default to true (enabled)
        if (!$preference) {
            return true;
        }

        return $preference->is_enabled;
    }

    /**
     * Send a specific template immediately (Helper for testing).
     */
    public function sendTemplate($notifiable, $template, array $data = [])
    {
        // Inject source
        if (!isset($data['source'])) {
            $data['source'] = 'test'; // Default to test source
        }

        if (isset($this->channels[$template->channel])) {
            $channelClass = $this->channels[$template->channel];
            (new $channelClass)->send($notifiable, $template, $data);
            return true;
        }

        return false;
    }
}
