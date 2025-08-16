<?php 

namespace App\Notifications;


use Illuminate\Support\Facades\Http;
use App\Notifications\MorpromtAlert;

class TelegramChannel
{    

    public function send($notifiable, MorpromtAlert $notification)    
    {
        $data = $notification->toMorpromt($notifiable);
        $password_hash = strtoupper(hash_hmac('sha256', $data['password'], $data['secretkey']));
        $username = $data['username'];
        $hos_code = $data['hoscode'];
        $queryParams = http_build_query([
            'Action'        => 'get_moph_access_token',
            'user'          => $username,
            'password_hash' => $password_hash,
            'hospital_code' => $hos_code,
        ]);
        
        $url = "https://cvp1.moph.go.th/token?" . $queryParams;
        $response = Http::post($url);
        
        return $response->json();        
    }
}