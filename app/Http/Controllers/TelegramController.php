<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TelegramNotification;
use App\User;
use App\Token;

class TelegramController extends Controller
{
    public function getUpdates()
    {
        $token = config('telegram.telegram.bot_token');
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
    public function getUpdateLast()
    {
        $token = config('telegram.telegram.bot_token');
        $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates");
        
        if($response->failed()){
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
        
        $data = $response->json();

        if (empty($data['result'])) {
            return response()->json(['error' => 'No messages found'], 404);
        }

        $last_message = collect($data['result'])->sortByDesc('update_id')->first();
        
        // ตรวจสอบว่ามีข้อมูล message และ from หรือไม่
        if (!isset($last_message['message']['from']['id'])) {
            return response()->json(['error' => 'Invalid message format'], 400);
        }

        $chat_id = $last_message['message']['from']['id'];
        if($last_message['message']['text'] === '/start'){           
            try {
                // Notification::send(null, new TelegramNotification('คุณชื่ออะไร ครับ', $chat_id));
                return view('telegram.index')->with('data', $last_message);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Notification failed',
                    'message' => $e->getMessage()
                ], 500);
            }         
        }
    }

    public function search($query){
        return Token::select("id","ssn_name")->where('ssn_name','LIKE','%'.$query.'%')->get();
    }
    

    // บันทึกข้อมูล chat ID
    public function store(Request $request){
        var_dump($request);
    }
}
