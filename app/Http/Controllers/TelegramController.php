<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TelegramNotification;
use App\Token;

class TelegramController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
               
        try {
            // Notification::send(null, new TelegramNotification('สวัสดี ค่ะ', $chat_id));
            return view('telegram.index')->with('data', $last_message);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Notification failed',
                'message' => $e->getMessage()
            ], 500);
        } 
    }

    public function testNotify($chat_id){
        Notification::send(null, new TelegramNotification('สวัสดี ค่ะ', $chat_id));
    }
    // ค้นหาพนักงานจากฐานข้อมูล
    public function search($query){
        return Token::select("id","ssn_name")->where('ssn_name','LIKE','%'.$query.'%')->get();
    }
    

    // บันทึกข้อมูล chat ID
    public function store(Request $request){
        
        // กำหนดกฎการ validate
    $validatedData = $request->validate([
        'id' => 'required|string',
        'chatid' => 'required|string',
        'fullname' => 'required|string|min:8|max:100',
        // เพิ่มฟิลด์อื่นๆ ที่ต้องการ validate
    ]);
    if($validatedData){        
        $model = Token::find($request->input('id'));
        $chatID = $model->telegram_chat_id;
        $model->telegram_chat_id = $request->input('chatid');
        // ต่อข้อความแบบ หลายบรรทัด
        $message = <<<EOT
            ✅ลงทะเบียนสำเร็จแล้ว ค่ะ
            🙋‍♂️{$model->ssn_name}
        EOT;
        // อัพเดทฐานข้อมูล
        
        if((strlen($chatID) < 3) && $model->save()){
            Notification::send(null, new TelegramNotification($message, $request->input('chatid')));
            $model = Token::findOrFail($request->input('id'));                      
            return view('telegram.show', [
                'model'=>$model,
                'success' => '✅ลงทะเบียนสำเร็จแล้ว'
            ]);                       
        }
        // ย้อนกลับหากมี Error
        return back()->with('error', '🚫 ผู้ใช้นี้เคยลงทะเบียนกับเราแล้ว ค่ะ');       
    }
    return back()->with('error', 'ข้อมูลไม่ครบกรุณาค้นหา ชื่อ-สกุล ใหม่ แล้วเลือกจากรายการค้นหา'); 
    
    }

    // หน้าแสดงรายละเอียด
    public function show(){
        return view("telegram.show");
    }
}
