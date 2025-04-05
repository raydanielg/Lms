<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNotifications extends Mailable
{
    use SerializesModels;

    public $notification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $notification = $this->notification;

        if (!empty($notification)) {
            $generalSettings = getGeneralSettings();

            return $this->subject($notification['title'])
                ->from(!empty($generalSettings['site_email']) ? $generalSettings['site_email'] : env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->cc(!empty($notification['cc']) ? $notification['cc'] : [])
                ->view('web.default.emails.notification', [
                    'notification' => $notification,
                    'generalSettings' => $generalSettings
                ]);
        }
    }
}
