<?php

namespace App\Notifications;

use App\Channels\SMSChannel;
use Illuminate\Notifications\Notification;

class SendVerificationSMSCode extends Notification
{
    private $notifiable;

    /**
     * Create a new notification instance.
     *
     * @param $notifiable
     */
    public function __construct($notifiable)
    {
        $this->notifiable = $notifiable;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SMSChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toSMS($notifiable)
    {
        $generalSettings = getGeneralSettings();
        $siteName = $generalSettings['site_name'] ?? '';

        $content = trans('update.code') . ': ' . $notifiable->code;
        $content .= PHP_EOL;
        $content .= trans('update.your_validation_code_on_the_site', ['site' => $siteName]);

        return [
            'to' => $notifiable->mobile,
            'content' => $content,
        ];
    }
}
