<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class EmailNotification extends Notification
{
    use Queueable;
    protected $message, $attachments, $subject, $user;


    /**
     * Create a new notification instance.
     */
    public function __construct($message, $user, $subject = null, $attachments = [])
    {
        $this->message = $message;
        $this->user = $user;
        $this->subject = $subject ?? __("Email Notification");
        $this->attachments = $attachments;
    }



    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->line(new HtmlString(nl2br($this->message)))
            ->salutation(__("This email is computer generated and does not need to be returned"));
        foreach ($this->attachments as $path) {
            $mail->attach(Storage::path($path));
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
