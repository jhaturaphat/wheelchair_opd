<?php 

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Notifications\TelegramNotification;

class TelegramChannel
{
    public function send($notifiable, TelegramNotification $notification)
    {
        $message = $notification->toTelegram($notifiable);

        $response = Http::post("https://api.telegram.org/bot{$message['token']}/sendMessage", [
            'chat_id' => $message['chat_id'],
            'text' => $message['text'],
            'parse_mode' => $message['parse_mode'] ?? 'HTML',
        ]);

        return $response->json();
    }
}