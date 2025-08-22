<?php 

namespace App\Notifications;

use Illuminate\Support\Facades\Http;
use App\Notifications\MorpromtNotification;
use App\Models\Morpromt;

class MorpromtChanel
{    
    // private int $tokenTtl = 86400;
    public function send($notifiable, MorpromtNotification $notification)    
    {
        $data = $notification->toMorpromt($notifiable);
        $token = $this->getTokenCach();
        $api = config('morpromt.configs.api')."/api/v2/send-message/send-now";        

        $flex_message = $this->UpdateFlexJson($data);
        $payload = json_decode($flex_message, true);

        $response = Http::timeout(30)
        ->withToken($token)
        ->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($api, $payload);     
        if($response->getStatusCode() === 200){
            return TRUE;
        }
    }

    public function UpdateFlexJson($data): string{
        $json_file_path = __DIR__."/flex-message.json";
        $json_template_string = file_get_contents($json_file_path);
        // ตรวจสอบว่าอ่านไฟล์ได้หรือไม่
        if ($json_template_string === false) {
            die('ไม่พบไฟล์ หรือไม่สามารถอ่านไฟล์ได้');
        }

        // 3. กำหนดข้อมูลใหม่ที่ต้องการอัปเดตในรูปแบบ key-value array
        $data_to_update = [
            '__CID__'      => '3340701740851', // $data['chat_id'] LINE ID หรือข้อมูลที่ต้องการ
            '__SERVICE__'  => $data['service'] ?? "ไม่มี", 
            '__FULLNAME__' => $data['name'] ?? "ไม่มี",
            '__POINT__'    => $data['pickup'] ?? "ไม่มี",
            '__BEDNO__'    => $data['bednumber'] ?? "ไม่มี",
            '__CAR__'      => $data['equipment'] ?? "ไม่มี",
            '__NOTE__'     => $data['equipment_note'] ?? "ไม่มี",
            '__ENDPOINT__' => $data['send'] ?? "ไม่มี",
            '__LEVEL__'    => $data['note'] ?? "ไม่มี",
            '__DATE__'     => "วันที่ ".date("Y-m-d เวลา H:i:s", time())
        ];

        // 4. เตรียม array สำหรับการค้นหาและแทนที่
        $placeholders = array_keys($data_to_update);
        $new_values   = array_values($data_to_update);

        // 5. ใช้ str_replace เพื่อแทนที่ทุก placeholder ในครั้งเดียว
        $final_flex_message = str_replace($placeholders, $new_values, $json_template_string);

        // 6. แสดงผลลัพธ์        
        return $final_flex_message;

    }

    public function getTokenCach(){
        $tokenModel = Morpromt::first();
        // หาพบข้อมูล token ในฐานข้อมูล
        if($tokenModel){     
            // ตรวจสอบว่า token หมดอายุหรือยังด้วย getTokenExpirationTime
            if($this->getTokenExpirationTime($tokenModel->token) > 0){
                return $tokenModel->token;  
            }else{
                return $this->RefreshAccessToken($tokenModel->id);
            }        
        }else{
            return $this->RefreshAccessToken();
        }
    }

    public function RefreshAccessToken(?string $token = null): string {

        $username       = config('morpromt.configs.username');
        $password_hash  = strtoupper(hash_hmac('sha256', config('morpromt.configs.password'), config('morpromt.configs.secretkey')));        
        $hos_code       = config('morpromt.configs.hoscode');
        $url            = config('morpromt.configs.url');
        $result         = "";
        $queryParams = http_build_query([
            'Action'        => 'get_moph_access_token',
            'user'          => $username,
            'password_hash' => $password_hash,
            'hospital_code' => $hos_code,
        ]);
        
        $url = $url . "/token?" . $queryParams;
        
        $response = Http::timeout(30)
        ->withHeaders(['Content-Type' => 'application/json',])
        ->post($url);
        if($response->getStatusCode() === 200){
            try{                
                if(empty($token)){
                    // บันทึก token ลงฐานข้อมูล
                    $model = new Morpromt();
                    $model->token = $response->body();
                    $model->ttl = "86400";
                    $model->save();                    
                }else{
                    // Update token ใหม่ที่มีอยู่แล้ว
                    $model = Morpromt::first();
                    $model->token = $response->body(); 
                    $model->save();
                }
                $result = $response->body(); 
                   
            }catch(\Exception $ex){
                echo "Redis connection failed: " . $ex->getMessage(); 
                $result = "";            
            }
        }
        return $result;
    }

    public function getTokenExpirationTime(?string $token = null){
        
        $payload = $this->getTokenPayload($token);
        // dd($payload);
        if (!isset($payload['exp'])) {
            return 0;
        }

        $expiration = $payload['exp'];
        $currentTime = time();

        return max(0, $expiration - $currentTime);
        
    }

    public function getTokenPayload(string $token): array
    {
        try {
            // แยกส่วนของ JWT Token
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                throw new \Exception('Invalid JWT token structure');
            }
            
            // ดึง payload part (ส่วนที่ 2)
            $payload = $parts[1];
            
            // Decode base64url
            $payload = str_replace(['-', '_'], ['+', '/'], $payload);
            $mod4 = strlen($payload) % 4;
            if ($mod4) {
                $payload .= substr('====', $mod4);
            }
            
            $decoded = base64_decode($payload);
            $data = json_decode($decoded, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to decode JSON payload');
            }
            
            return $data;
            
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'payload_raw' => $payload ?? null
            ];
        }
    }
}