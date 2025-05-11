<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\User;

class TelegramController extends Controller
{
    public function getUpdates()
    {
        $token = config('telegram.telegram_bot_token');
        $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates");
        $updates = $response->json();
        
        foreach ($updates['result'] as $update) {
            if (isset($update['message'])) {
                $chatId = $update['message']['chat']['id'];
                $userId = $update['message']['from']['id'];
                $username = $update['message']['from']['username'] ?? null;                
            }
        }
    }

    // ดึกข้อมูลล่าสุดจาก bot telegram
    public function getUpdateLast(){
        $token = config('telegram.telegram_bot_token');
        $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates");
        
        if($response->failed()){
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
        $data = $response->json();

        $last_message = collect($data['result'])->sortByDesc('update_id')->first();
        // return response()->json($last_message);
        return view('telegram.index')->with('data', $last_message);
    }
}
