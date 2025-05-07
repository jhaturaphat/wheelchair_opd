<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;

class CribbookingConrtoller extends Controller
{
    public function Cribbooking()
    {
        $depart = DB::table('depart')->orderBy('department')->get();
        return view('formadd.formadd',['data'=>$depart]);
    }

    public function select_depart(){
        $depart = DB::table('depart')->orderBy('department')->get();
        $service = DB::table('service_tokens')->orderBy('service')->get();
        return view('formadd.select_depart',['depart'=>$depart,'service'=>$service]);
    }

    function depart_(Request $req){
        $user = $req->username;
        $depart = $req->section;
        $service = $req->service;
        if($service == 'เคลื่อนย้ายผู้ป่วย' || $service == 'ตามรับยา'){
            $update = DB::table("users")->where('username',$user)->update(['service'=>$service,'depart'=>$depart,'updated_at'=>now()]);
            if($update == 1){
                return redirect()->route('Cribbooking');
            }
        }
    }

    function sent_to(Request $req){
        // dd($req->all());
        DB::beginTransaction();
        if($req->select_ssn == '1'){
            $save_book = new Book;
            $save_book->user_add = $req->confirm_doc;
            $save_book->service = $req->service;
            $save_book->section = $req->section01;
            $save_book->hn = $req->hn;
            $save_book->name = $req->name;
            $save_book->pickup = $req->pickup;
            $save_book->bednumber = $req->bednumber;
            $save_book->equipment = $req->equipment;
            $save_book->equipment_type = $req->equipment_type;
            $save_book->send = $req->send;
            $save_book->datetime = $req->datetime;
            $save_book->note = $req->note;
            $save_book->equipment_note = $req->equipment_note;
            $save_book->danger_note = $req->danger_note;
        }else if($req->select_ssn == '2'){
            $save_book = new Book;
            $save_book->user_add = $req->confirm_doc;
            $save_book->section = $req->section01;
            $save_book->service = $req->service;
            $save_book->hn = $req->hn;
            $save_book->name = $req->name;
            $save_book->pickup = $req->pickup;
            $save_book->bednumber = $req->bednumber;
            $save_book->equipment = $req->equipment;
            $save_book->equipment_type = $req->equipment_type;
            $save_book->send = $req->send;
            $save_book->datetime = $req->datetime;
            $save_book->note = $req->note;
            $save_book->equipment_note = $req->equipment_note;
            $save_book->danger_note = $req->danger_note;
            $save_book->name_ssn = $req->name_ssn;
            $save_book->shift = $req->shift;
            $save_book->verify = 1;
            $save_book->time_in = now();
        }

        if($save_book->save()){
            $id = $req->hiddenEdit;
            $confirm = Book::where('ID', $id)->update([
                'confirm_doc' => $req->confirm_doc,
                'verify' => 2,
                'time_out' => now()
            ]);
            if($confirm == 1){
                $is_data = Book::where('ID',$id)->first();
                $startTime = Carbon::parse($is_data->time_in);
                $endTime = Carbon::parse($is_data->time_out);
                $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S');
                $confirm_p = Book::where('ID', $id)
                            ->update([
                                'time_total' => $totalDuration,
                                'rate' => $req->rate,
                                'note_doc' => $req->note_doc . "(เคสส่งต่อ)",
                            ]);
                if($confirm_p == 1){
                    if($req->select_ssn == '1'){
                        $status = [
                            'status' => true
                        ];
                    }else if($req->select_ssn == '2'){
                        $token = Token::where('ssn_name',$req->name_ssn)->first();
                        $ref = $token->ssn_token;
                        // dd($noti->service);
                        $header = "*คุณมีงานเข้า*";
                        // แอดเพิ่มตรงนี้
                        $pickup = $req->pickup;
                        $bednumber = $req->bednumber;
                        $equipment = $req->equipment;
                        $equipment_type = $req->equipment_type;
                        $send = $req->send;
                        $equipment_note = $req->equipment_note;
                        $name = $req->name;
                        $note = $req->note;
                        $danger_note = $req->danger_note;
                        $service = $req->service;

                        $message = $header.
                                "\n". "📃บริการ: " . $service .
                                "\n". "👫🏽ชื่อคนไข้: " . $name .
                                "\n". "🏨สถานที่รับ: " . $pickup .
                                "\n". "🛌หมายเลขเตียง: " . $bednumber .
                                "\n". "🧑🏾‍🦽อุปกรณ์: " . $equipment .",". $equipment_type.
                                "\n". "🔊หมายเหตุ: " . $equipment_note .
                                "\n". "🏥สถานที่ส่ง: " . $send .
                                "\n". "🕐ระดับความเร่งด่วน: " . $note . " " . $danger_note;
                            if ($service <> "" ||
                                    $pickup <> ""  ||
                                    $bednumber <> ""  ||
                                    $equipment <> ""  ||
                                    $equipment_type <> ""  ||
                                    $send <> "" || $note <> "" || $danger_note <> "" ) {
                                    $res = $this->sendlinemesg($ref);
                                    header('Content-Type: text/html; charset=utf8');
                                    $res = notify_message($message);
                                    if($res->message == "ok"){
                                        $status = [
                                            'status' => true
                                        ];
                                    }else{
                                        $status = [
                                            'status' => false
                                        ];
                                    }
                            }else {
                                $status = [
                                    'status' => false
                                ];
                            }
                    }
                }else{
                    $status = [
                        'status' => false
                    ];
                }
            }else{
                $status = [
                    'status' => false
                ];
            }
        }
        DB::commit();
        return $status;
    }

    function save(Request $req){
        $token = DB::table('service_tokens')->where('service',$req->service)->first();
        $token_id = $token->token;
        DB::beginTransaction();
        $save_book = new Book;
        $save_book->user_add = $req->user_add;
        $save_book->service = $req->service;
        $save_book->section = $req->section;
        $save_book->hn = $req->hn;
        $save_book->name = $req->name;
        $save_book->pickup = $req->pickup;
        $save_book->bednumber = $req->bednumber;
        if(!empty($req->equipment)){
            $save_book->equipment = implode(",",$req->equipment);

        }
        if(!empty($req->equipment_type)){
            $save_book->equipment_type = implode(",",$req->equipment_type);
        }
        $save_book->equipment_note = $req->equipment_note;
        $save_book->send = $req->send;
        $save_book->datetime = $req->datetime;
        $save_book->note = $req->note;
        $save_book->danger_note = $req->danger_note;
        if($save_book->save()){
            $header = "**มีรายการคำขอ**";
            // แอดเพิ่มตรงนี้
            $section = $save_book->section;
            $name = $save_book->name;
            $service = $save_book->service;
            $pickup = $save_book->pickup;
            $bednumber = $save_book->bednumber;
            $equipment = $save_book->equipment;
            $equipment_note = $save_book->equipment_note;
            $equipment_type = $save_book->equipment_type;
            $send = $save_book->send;
            $datetime = $save_book->datetime;
            $note = $save_book->note;
            $danger_note = $save_book->danger_note;
                // คำพูดตรงนี้
                $message = $header.
                "\n". "📃บริการ: " . $service .
                "\n". "👫🏽ชื่อคนไข้: " . $name .
                "\n". "🏨สถานที่รับ: " . $pickup .
                "\n". "🛌หมายเลขเตียง: " . $bednumber .
                "\n". "🧑🏾‍🦽อุปกรณ์: " . $equipment .",". $equipment_type.
                "\n". "🔊หมายเหตุ: " . $equipment_note .
                "\n". "🏥สถานที่ส่ง: " . $send .
                "\n". "🕐วันเวลารับ: " . $datetime .
                "\n". "⏱ระดับความเร่งด่วน: " . $note . " " . $danger_note;

          if ($section <> "" ||
                $name <> "" ||
                $pickup <> ""  ||
                $bednumber <> ""  ||
                $equipment <> ""  ||
                $equipment_note <> "" ||
                $equipment_type <> ""  ||
                $send <> ""  ||
                $datetime <> "" ||
                $note <> "") {
                // $res = $this->sendlinemesg($token_id);
                // header('Content-Type: text/html; charset=utf8');
                // $res = notify_message($message);
                // echo "<script>alert('ลงทะเบียนเรียบร้อย');</script>";

                Notification::send(null, new TelegramNotification($message));
                echo "<script>alert('ลงทะเบียนเรียบร้อย');</script>";
            }else {
                echo "<script>alert('กรุณากรอกข้อมูล');</script>";
            }
        }else{

        }
        DB::commit();
        // if($res->message == "ok"){
        //     $status = 'success';
        // }else{
        //     $status = 'error';
        // }
        // DB::commit();

        return $status = 'success';
    }

    function sendlinemesg($token){

        define('LINE_API' , "https://notify-api.line.me/api/notify");
        define('LINE_TOKEN' , $token);
        // JYXUUXCNC3DrW6hZCs8RRVxz3EA0pYbiLBKUmqF7kXN
        // uNrwN6UMdsQaQZbgyNUqB3ASTkZxuS2oy69LAaLBT8M กลุ่ม สสน


        function notify_message($message){
            $queryData = array('message' => $message);
            $queryData = http_build_query($queryData,'','&');
            $headerOptions = array(
                'http'=>array(
                    'method'=>'POST',
                    'header'=> "Content-Type: application/x-www-form-urlencoded\r\n"
                            ."Authorization: Bearer ".LINE_TOKEN."\r\n"
                            ."Content-Length: ".strlen($queryData)."\r\n",
                    'content' => $queryData
                )
            );
            $context = stream_context_create($headerOptions);
            $result = file_get_contents(LINE_API,FALSE,$context);
            $res = json_decode($result);
            return $res;

        }
    }

    public function Showdata(){
        $depart = DB::table('depart')->orderBy('department')->get();
        return view('formadd.showdata',['data'=>$depart]);
    }

    public function showsucc(){
        return view('formadd.showsucc');
    }


    function dataBooking(Request $req){
        $section = $req->section;
        // $pickup = DB::table('books')->where('pickup',$section)->whereIn('verify',[0,1])->whereNotNull("section")->where("deleted_at",null);
        // $send['data'] = DB::table('books')->union($pickup)->where('send',$section)->whereIn('verify',[0,1])->whereNotNull("section")->where("deleted_at",null)->get();
        $send['data'] = DB::connection('mysql')->select("SELECT *,time_in as time,DATE_FORMAT(DATE_ADD(time_in, INTERVAL 15 MINUTE), '%Y-%m-%d %H:%i:%s') AS complete,DATE_FORMAT(now(), '%Y-%m-%d %H:%i:%s') AS now FROM books WHERE (pickup = '$section' or send = '$section' or section = '$section') AND verify IN (0,1) AND deleted_at IS NULL ");

        // $ssn['data'] = DB::table("books")
        //         ->whereIn('verify',[0,1])
        //         ->whereNotNull("section")
        //         ->where("deleted_at",null)
        //         ->get();
         return $send;

        // $data['data'] = Book::where('deleted_at',null)->get();
        // return $data;

    }

    function dataBooking_succ(Request $req){
        $section = $req->section;


        // $pickup = DB::table('books')->where('pickup',$section)->where('verify','2')->whereNotNull("section")->where("deleted_at",null);
        // $send['data'] = DB::table('books')->union($pickup)->where('send',$section)->where('verify','2')->whereNotNull("section")->where("deleted_at",null)->get();

        $send['data'] = DB::table('books')
                        ->where(function ($query) use ($section) {
                            $query->where('pickup','=',$section)
                                ->orWhere('send','=',$section);
                        })
                        ->whereDate('created_at', Carbon::today())
                        ->where('verify','=','2')
                        ->whereNull('deleted_at')
                        ->whereNotNull('section')
                        ->get();
         return $send;

    }

    function data_ssn(){
        $ssn['data'] = DB::table("books")
                ->whereNotNull("name_ssn")
                ->where("deleted_at",null)
                ->get();
        return $ssn;
    }

    function conFirm(Request $req){
        $id = $req->id_hidden;
        $confirm = Book::where('ID', $id)->update([
            'verify' => 2,
            'time_out' => now(),
            'confirm_doc' => $req->confirm_doc,
            'rate' => $req->rate,
            'note_doc' => $req->note_doc
        ]);
        if($confirm == 1){
            $is_data = Book::where('ID',$id)->first();
            $startTime = Carbon::parse($is_data->time_in);
            $endTime = Carbon::parse($is_data->time_out);

            $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S');

            $confirm_p = Book::where('ID', $id)->update(['time_total' => $totalDuration]);
            if($confirm_p == 1){
                $data = [
                    "status" => true
                ];
            }else{
                $data = [
                    "data" => false
                ];
            }
        }else{
            $data = [
                "data" => false
            ];
        }
        return $data;
    }

    function conFirm2(Request $req){
        // dd($req->all());
        $id = $req->id_hidden;
        $confirm = Book::where('ID', $id)->update([
            'verify' => 2,
            'time_out' => now(),
            'confirm_doc' => $req->confirm_doc,
            'rate' => $req->rate,
            'note_doc' => $req->note_doc
        ]);
        if($confirm == 1){
            $is_data = Book::where('ID',$id)->first();
            $startTime = Carbon::parse($is_data->time_in);
            $endTime = Carbon::parse($is_data->time_out);

            $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S');

            $confirm_p = Book::where('ID', $id)->update(['time_total' => $totalDuration]);
            if($confirm_p == 1){
                $data = [
                    "status" => true
                ];
            }else{
                $data = [
                    "data" => false
                ];
            }
        }else{
            $data = [
                "data" => false
            ];
        }
        return $data;
    }

    function softDelete(Request $req){
        $id = $req->get_id;
        $delete = Book::where('ID',$id)->update(['deleted_at' => now()]);
        if($delete == 1){
            $data = [
                "status" => true
            ];
        }else{
            $data = [
                "data" => false
            ];
        }
        return $data;
    }
    function count(Request $req){
        $section = $req->section;
        $count['book'] = DB::table('books')
                        ->where(function ($query) use ($section) {
                            $query->where('pickup','=',$section)
                                ->orWhere('send','=',$section)
                                ->orWhere('section','=',$section);
                        })
                        // ->whereDate('created_at', Carbon::today())
                        ->where('verify','=','1')
                        ->whereNull('deleted_at')
                        ->whereNotNull('section')
                        ->count();
        return $count;
    }
    
    function count_wait(Request $req){
        $section = $req->section;
        $count['book'] = DB::table('books')
                        ->where(function ($query) use ($section) {
                            $query->where('pickup','=',$section)
                                ->orWhere('send','=',$section);
                        })
                        ->whereDate('created_at', Carbon::today())
                        ->where('verify','=','1')
                        ->whereNull('deleted_at')
                        ->whereNotNull('section')
                        ->count();
                    // dd($count['book']);
        return $count;
    }

    function count_succ(Request $req){

        $section = $req->section;
        // dd($section);
        $count['book'] = DB::table('books')
                        ->where(function ($query) use ($section) {
                            $query->where('pickup','=',$section)
                                ->orWhere('send','=',$section);
                        })
                        ->whereDate('created_at', Carbon::today())
                        ->where('verify','=','2')
                        ->whereNull('deleted_at')
                        ->whereNotNull('section')
                        ->count();
        return $count;
    }

    public function edit(Request $req){
        $get = Book::where('ID',$req->id)->first();
        return $get;
    }


}
