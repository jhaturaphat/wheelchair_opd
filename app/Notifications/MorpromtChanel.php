<?php 

namespace App\Notifications;

use Illuminate\Support\Facades\Http;
use App\Notifications\MorpromtNotification;
use App\Models\Morpromt;

class MorpromtChanel
{    
    private int $tokenTtl = 86400;
    public function send($notifiable, MorpromtNotification $notification)    
    {
        $data = $notification->toMorpromt($notifiable);
        $result = $this->getTokenCach();
        dd($result);
        
    }

    public function getTokenCach(){
        $tokenModel = Morpromt::first();
        if($tokenModel){      
            if($this->getTokenExpirationTime($tokenModel->token)){
                return $this->getTokenExpirationTime($tokenModel->token); 
            }else{
                return $this->RefreshAccessToken();
            }        
        }else{
            return $this->RefreshAccessToken();
        }
    }

    public function RefreshAccessToken(): string {

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
                $model = new Morpromt();
                $model->token = $response->body();
                $model->ttl = "86400";
                $model->save();
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