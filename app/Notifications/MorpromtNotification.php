<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MorpromtNotification extends Notification
{
    use Queueable;

    protected $messages;
    protected $chatId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $messages, $chatId)
    {
        $this->messages = $messages;
        $this->chatId = $chatId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['morpromt'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMorpromt($notifiable)
    {
        return [            
            'chat_id' => $this->chatId,
            'messages' => $this->messages
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
