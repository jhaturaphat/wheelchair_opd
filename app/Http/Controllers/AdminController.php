<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\books_drives;
use App\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TelegramNotification;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function charts()
    {
        return view('charts');
    }

    public function select_service(){
        $depart = DB::table('depart')->orderBy('department')->get();
        $service = DB::table('service_tokens')->orderBy('service')->get();
        return view('admin.select_service',['depart'=>$depart,'service'=>$service]);
    }

    public function drive_refer_from(){
        $service = Auth::user()->service;
        $depart = DB::table('role_tokens')->where('role',$service)->orderBy('name')->get();
        return view('drive_refer.drive_refer_from',['data'=>$depart]);
    }

    public function drive_refer_tables(){
        return view('drive_refer.drive_refer_tables');
    }

    public function refer_tables(){
        $data['data'] =  DB::connection('mysql')->select("SELECT *,DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS immunization_datetime,DATE_FORMAT(DATE_ADD(created_at, INTERVAL 15 MINUTE), '%Y-%m-%d %H:%i:%s') AS complete,DATE_FORMAT(now(), '%Y-%m-%d %H:%i:%s') AS now FROM `books_drives` WHERE verify in (0,1) AND deleted_at is NULL");
        return $data;
    }

    public function drive_refer_sum(){
        return view('drive_refer.drive_refer_sum');
    }

    function send_drive(Request $req){
        // dd($req->all());
        $token = DB::table('role_tokens')->where('cid',$req->name_ssn)->first();
        $token_id = $token->tokens;
        DB::beginTransaction();
        $save_book = new books_drives;
        $save_book->user_add = $req->user_add;
        $save_book->service = $req->service;
        $save_book->section = $req->section;
        $save_book->shift = $req->shift;
        $save_book->pickup = $req->pickup;
        $save_book->name_ssn = $token->name;
        $save_book->datetime = $req->datetime;
        $save_book->other = $req->other;
        $save_book->note = $req->note;
        $save_book->verify = "1";
        $save_book->danger_note = $req->danger_note;
        $save_book->time_in = now();
        if($save_book->save()){
            $header = "**มีรายการคำขอ**";
            // แอดเพิ่มตรงนี้
            $service = $save_book->service;
            $send = $save_book->send;
            $datetime = $save_book->datetime;
            $note = $save_book->note;
            $other = $save_book->other;
            $danger_note = $save_book->danger_note;
                // คำพูดตรงนี้
                $message = $header.
                "\n". "📃บริการ: " . $service .
                "\n". "🕐วันเวลาเรียก: " . $datetime .
                "\n". "⏱ระดับความเร่งด่วน: " . $note . " " . $danger_note .
                "\n". "🔊หมายเหตุ: " . $other ;

          if ($service <> "" ||
                $datetime <> "" ||
                $send <> ""  ||
                $note <> "") {

                $res = (object) ['message' => 'ok']; 
                // $res = $this->sendlinemesg($token_id);
                // header('Content-Type: text/html; charset=utf8');
                // $res = notify_message($message);
                // echo "<script>alert('ลงทะเบียนเรียบร้อย');</script>";
            }else {
                echo "<script>alert('กรุณากรอกข้อมูล');</script>";
            }
        }else{

        }
        if($res->message == "ok"){
            $status = 'success';
        }else{
            $status = 'error';
        }
        DB::commit();

        return $status;
    }



    function depart_admin(Request $req){
        $user = $req->username;
        // $depart = $req->section;
        $service = $req->service;
        if($service == 'พนักงานขับรถ' || $service == 'พยาบาลรีเฟอร์' ){
            $update = DB::table("users")->where('username',$user)->update(['service'=>$service,'depart'=>$service,'updated_at'=>now()]);
            if($update == 1){
                return redirect()->route('drive_refer_from');
            }
        }else{
            $update = DB::table("users")->where('username',$user)->update(['service'=>$service,'depart'=>$service,'updated_at'=>now()]);
            if($update == 1){
                return redirect()->route('tables');
            }
        }
    }


    public function test_table()
    {
        return view('admin.test_table');
    }

    public function otherform()
    {
        $depart = DB::table('depart')->get();
        return view('admin.otherform',['data'=>$depart]);
    }

    public function data_home()
    {
        $data = Book::select('id','created_at')->get()->groupBy(function($data){
            return Carbon::parse($data->created_at)->format('M');
        });

        $months = [];
        $monthCount = [];
        foreach($data as $month => $values){
            $months[] = $month;
            $monthCount[] = count($values);
        }
        return view('admin.home',['data'=>$data,'months'=>$months,'monthCount'=>$monthCount]);
        // var _ydata = JSON.parse('{!! json_encode($data)!!}');
    }

    public function data_home1()
    {
        $data = Book::select('id','name_ssn')->get()->groupBy('name_ssn');
        // $newData = json_decode($data);

        $name_ssn = [];

        foreach($data as $name_ssn => $values){
            $name_ssns[] = [
                "name" => $name_ssn,
                "y" => count($values)
            ];
        }
        return view('admin.home',['data'=>$data,'name_ssns'=>$name_ssns]);
    }
    public function home_test()
    {
        return view('home_test');
    }

    function search_1(Request $req){
        // dd($req->all());
        $date_from = $req->date_from;
        $date_to = $req->date_to;
        if($date_to == ''){
            $data3 = Book::select('id','rate','created_at')->where('verify',2)->whereDate('created_at', Carbon::today())->whereNotNull('rate')->orderBy('rate')->get()->groupBy('rate');
            $rate = [];
            $rate_coun = [];
            // dd($data3);
            foreach ($data3 as $rate => $values){
                $rates[] = $rate;
                $rate_coun[] = count($values);
                // dd($rate_coun);

            }

            $data2 = Book::select('id','pickup','created_at')->where('verify',2)->whereDate('created_at', Carbon::today())->orderBy('name_ssn')->get()->groupBy('pickup');
            $section = [];
            foreach($data2 as $section => $values){
                $sections[] = [
                    "name" => $section,
                    "y" => count($values)
                ];
            }

            $data1 = Book::select('id','created_at')->whereDate('created_at', Carbon::today())->get()->groupBy(function($data){
                return Carbon::parse($data->created_at)->format('M');
            });
            foreach($data1 as $month => $values){
                $months[] = $month;
                $monthCount[] = count($values);
            }

            $data = Book::select('id','name_ssn','created_at')->where('verify',2)->whereDate('created_at', Carbon::today())->orderBy('name_ssn')->get()->groupBy('name_ssn');
            $name_ssn = [];
            $name_ssnCount = [];
                foreach($data as $name_ssn => $values){
                    $name_ssns[] = $name_ssn;
                    $name_ssnCount[] = count($values);
                }
                if($data != null && $data1 != null){
                    $status = [
                        'data'=>$data,
                        'name_ssns'=>$name_ssns,
                        'name_ssnCount'=>$name_ssnCount,
                        'months'=>$months,
                        'monthCount'=>$monthCount,
                        'sections' =>$sections,
                        'rate' => $rates,
                        'rate_coun' => $rate_coun
                    ];
                }
        }else{
            $data3 = Book::select('id','rate','created_at')->where('verify',2)->whereDate('created_at','>=',$date_from)->whereDate('created_at','<=',$date_to)->whereNotNull('rate')->orderBy('rate')->get()->groupBy('rate');
            $rate = [];
            $rate_coun = [];
            foreach ($data3 as $rate => $values){
                $rates[] = $rate;
                $rate_coun[] = count($values);

            }

            $data2 = Book::select('id','pickup','created_at')->where('verify',2)->whereDate('created_at','>=',$date_from)->whereDate('created_at','<=',$date_to)->orderBy('name_ssn')->get()->groupBy('pickup');
            $section = [];
            foreach($data2 as $section => $values){
                $sections[] = [
                    "name" => $section,
                    "y" => count($values)
                ];
            }

            $data1 = Book::select('id','created_at')->whereDate('created_at','>=',$date_from)->whereDate('created_at','<=',$date_to)->get()->groupBy(function($data){
                return Carbon::parse($data->created_at)->format('M');
            });
            foreach($data1 as $month => $values){
                $months[] = $month;
                $monthCount[] = count($values);
            }

            $data = Book::select('id','name_ssn','created_at')->where('verify',2)->whereDate('created_at','>=',$date_from)->whereDate('created_at','<=',$date_to)->orderBy('name_ssn')->get()->groupBy('name_ssn');
            $name_ssn = [];
            $name_ssnCount = [];
                foreach($data as $name_ssn => $values){
                    $name_ssns[] = $name_ssn;
                    $name_ssnCount[] = count($values);
                }
                if($data != null && $data1 != null){
                    $status = [
                        'data'=>$data,
                        'name_ssns'=>$name_ssns,
                        'name_ssnCount'=>$name_ssnCount,
                        'months'=>$months,
                        'monthCount'=>$monthCount,
                        'sections' =>$sections,
                        'rate' => $rates,
                        'rate_coun' => $rate_coun
                    ];
                }
        }
        return $status;
    }

    function search_2(Request $req){
        $date_from = $req->date_from;
        $date_to = $req->date_to;
        $score1 = $req->score1;
        $score2 = $req->score2;
        $shift = $req->shift;
        if($score2 == ''){
            if($shift == ''){
                if($date_to == ''){
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->whereDate('created_at',Carbon::today())
                                ->where("deleted_at",null)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = Book::select('id','name_ssn','created_at')
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }else{
                if($date_to == ''){
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("shift",$shift)
                                ->where("deleted_at",null)
                                ->whereDate('created_at',Carbon::today())
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("shift",$shift)
                                ->where("deleted_at",null)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }
        }else{
            if($shift == ''){
                if($date_to == ''){
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }else{
                if($date_to == ''){
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = Book::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }
        }
        return $status;

    }

    public function summary(){
        return view('admin.table_summary');
    }


    public function tables()
    {
        $id = Auth::user()->id;
        $token = DB::connection('mysql')->table('tokens')->orderBy('updated_at','ASC')->get();
        // dd($token);
        // Token::get();
        $data =  DB::connection('mysql')->select("SELECT *,`name`,DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS immunization_datetime,DATE_FORMAT(DATE_ADD(created_at, INTERVAL 15 MINUTE), '%Y-%m-%d %H:%i:%s') AS complete,DATE_FORMAT(now(), '%Y-%m-%d %H:%i:%s') AS now FROM `books` WHERE verify in (0,1) AND deleted_at is NULL");
        return view('admin.tables',compact('token','id','data'));
        // return view('admin.tables',['data'=>$data]);
    }

    public function tables_ssn(){
        return view('admin.tables_ssn');
    }

    function data_ssn(){
        $ssn['data'] = DB::table("books")
                ->whereNotNull("name_ssn")
                ->where("deleted_at",null)
                ->get();
        return $ssn;
    }

    function data_sum(Request $req){
        $date_from = $req->date_from;
        $date_to = $req->date_to;
        $score1 = $req->score1;
        $score2 = $req->score2;
        $shift = $req->shift;
        // dd($req->all());
        if($score2 == ''){
            if($shift == ''){
                if($date_to == ''){
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }else{
                if($date_to == ''){
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where("shift",$shift)
                            ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where("shift",$shift)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }
        }else{
            if($shift == ''){
                if($date_to == ''){
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            // ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }else{
                if($date_to == ''){
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            ->where("shift",$shift)
                            // ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            ->where("shift",$shift)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }
        }
        // dd($ssn);
        return $ssn;
    }

    function dataBooking(){
        $data['data'] =  DB::connection('mysql')->select("SELECT *,`name`,DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS immunization_datetime,DATE_FORMAT(DATE_ADD(created_at, INTERVAL 15 MINUTE), '%Y-%m-%d %H:%i:%s') AS complete,DATE_FORMAT(now(), '%Y-%m-%d %H:%i:%s') AS now FROM `books` WHERE verify in (0,1) AND deleted_at is NULL");
        // Book::select(DB::raw("SELECT *,`name`,DATE_FORMAT(created_at, '%H:%i:%s') AS 'immunization_datetime',DATE_FORMAT(DATE_ADD(created_at, INTERVAL 30 MINUTE), '%H:%i:%s') AS 'complete',DATE_FORMAT(now(), '%H:%i:%s') AS 'NOW' FROM `books` WHERE verify in (0,1) AND deleted_at is NULL"));
        // $data['data'] = Book::where('deleted_at',null)
        //             ->whereIn('verify',[0,1])
        //             ->get();
        // dd($data);

        return $data;
    }

    function data_custom(){
        $data['data'] = DB::table('books')
                        ->where(function ($query) {
                            $query->whereIn('pickup',['หน้าตึก', 'เดินยา', 'เบิกเลือด', 'อื่นๆ'])
                                ->orWhereIn('send',['กลับบ้าน','ศูนย์เปล สสน']);
                        })
                        ->where('verify','1')
                        ->whereNull('deleted_at')
                        ->get();
        return $data;
    }

    function conFirm(Request $req){
        //หา ChatID ของพนักงาน
        $token = Token::where('id',$req->token_id)->first();
        //  DB::beginTransaction();
        $ref = $token->telegram_chat_id;
        $id = $req->idHidden;
        $confirm = Book::where('ID', $id)->update([
                'verify' => 1,
                'name_ssn' => $token->ssn_name,
                'shift' => $req->shift,
                'ssn_assign' => $req->ssn_assign,
                'time_in' => now()
        ]);
        // dd($req->token_id);
        $update_time_ssn = Token::where('id', $req->token_id)->update(['updated_at' => now()]);
        // dd($update_time_ssn);
        if($confirm == 1){
            $noti = Book::where('ID', $id)->first();
            // dd($noti->service);
            $header = "คุณมีงานเข้า";
            // แอดเพิ่มตรงนี้
            $pickup = $noti->pickup;
            $bednumber = $noti->bednumber;
            $equipment = $noti->equipment;
            $equipment_type = $noti->equipment_type;
            $send = $noti->send;
            $equipment_note = $noti->equipment_note;
            $name = $noti->name;
            $note = $noti->note;
            $danger_note = $noti->danger_note;
            $service = $noti->service;

            $message = $header.
                    "\n". "📃บริการ: " . $service .
                    "\n". "👫🏽ชื่อคนไข้: " . $name .
                    "\n". "🏨สถานที่รับ: " . $pickup .
                    "\n". "🛌หมายเลขเตียง: " . $bednumber .
                    "\n". "🧑🏾‍🦽อุปกรณ์: " . $equipment .",". $equipment_type.
                    "\n". "🔊หมายเหตุ: " . $equipment_note .
                    "\n". "🏥สถานที่ส่ง: " . $send .
                    "\n". "⏱ระดับความเร่งด่วน: " . $note . " " . $danger_note;

            $obj_data = [
                'service'   => $service,
                'name'      => $name,
                'pickup'    => $pickup,
                'bednumber' => $bednumber,
                'equipment' => $equipment.",".$equipment_type,
                'equipment_note' => $equipment_note,
                'send'      => $send,
                'note'      => $note . " " . $danger_note
            ];
                if ($service <> "" ||
                        $pickup <> ""  ||
                        $bednumber <> ""  ||
                        $equipment <> ""  ||
                        $equipment_type <> ""  ||
                        $send <> "" || $note <> "" || $danger_note <> "" ) {
                    $res = (object) ['message' => 'ok']; 
                    // ส่งการแจ้งเตือนไปยัง Telegram
                    Notification::send(null, new TelegramNotification($message, $ref));
                        // $res = $this->sendlinemesg($ref);
                        // header('Content-Type: text/html; charset=utf8');
                        // $res = notify_message($message);
                        // echo "<script>alert('ลงทะเบียนเรียบร้อย');</script>";
                    }else {
                        echo "<script>alert('กรุณากรอกข้อมูล');</script>";
                    }
        }else{

        }
        if($res->message == "ok"){
            $status = [
                'status' => true
            ];
        }else{
            $status = [
                'status' => false
            ];
        }
        // DB::commit();
        return $status;
    }


    function confirm_edit(Request $req){
        $token = Token::where('id',$req->token_id)->first();
        //  DB::beginTransaction();
        $ref = $token->ssn_token;
        $id = $req->idHidden2;
        $confirm = Book::where('ID', $id)->update([
                'verify' => 1,
                'name_ssn' => $token->ssn_name,
                'shift' => $req->shift
            ]);

        if($confirm == 1 ){
            $noti = Book::where('ID', $id)->first();
            $res = [];
            $header = "งานส่งต่อ";
            // แอดเพิ่มตรงนี้
            $pickup = $noti->pickup;
            $bednumber = $noti->bednumber;
            $equipment = $noti->equipment;
            $equipment_type = $noti->equipment_type;
            $send = $noti->send;
            
            $message = $header.
                        "\n". "🏨สถานที่รับ: " . $pickup .
                        "\n". "🛌หมายเลขเตียง: " . $bednumber .
                        "\n". "🧑🏾‍🦽อุปกรณ์: " . $equipment .",". $equipment_type.
                        "\n". "🏥สถานที่ส่ง: " . $send;
                if ($pickup <> ""  ||
                        $bednumber <> ""  ||
                        $equipment <> ""  ||
                        $equipment_type <> ""  ||
                        $send <> "") {
                            
                        // $res = $this->sendlinemesg($ref);
                        // header('Content-Type: text/html; charset=utf8');
                        // $res = notify_message($message);
                        // echo "<script>alert('ลงทะเบียนเรียบร้อย');</script>";
                    }else {
                        echo "<script>alert('กรุณากรอกข้อมูล');</script>";
                    }
        }else{

        }
        
        if($res["message"] == "ok"){
            $status = [
                'status' => true
            ];
        }else{
            $status = [
                'status' => false
            ];
        }
        // DB::commit();
        return $status;
    }


    function sendlinemesg($param){

        define('LINE_API' , "https://notify-api.line.me/api/notify");
        define('LINE_TOKEN' , $param);


        function notify_message($message){
            $queryData = array('message' => $message ,'stickerPackageId' => '6359' ,'stickerId' => '11069870');
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

        // function notify_message($message){
        //     $queryData = array('message' => $message);
        //     $queryData = http_build_query($queryData,'','&');
        //     $headerOption = array(
        //         'http' => array(
        //             'method' => 'POST',
        //             'header' => "Content-type: application/x-www-form-urlencoded\r\n"
        //                         ."Authorization: Bearer ".LINE_TOKEN."\r\n"
        //                         ."Content-Length".strlen($queryData)."\r\n",
        //             'content' => $queryData
        //         )
        //     );
        //     $context = stream_context_create($headerOption);
        //     $result = file_get_contents(LINE_API, FALSE ,$context);
        //     $res = json_decode($result);
        //     return $res;
        // }
    }

    function softDelete(Request $req){
        $id = $req->get_id;
        $service = Auth::user()->service;
        if($service == 'เคลื่อนย้ายผู้ป่วย' || $service == 'ตามรับยา'){
            $delete = Book::where('ID',$id)->update(['deleted_at' => now()]);
        }else if($service == 'พนักงานขับรถ' || $service == 'พยาบาลรีเฟอร์'){
            $delete = books_drives::where('ID',$id)->update(['deleted_at' => now()]);
        }
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
    function count(){
        $count['book'] = DB::table('books')
                        ->where('verify',0)
                        ->whereDate('created_at', Carbon::today())
                        ->where('deleted_at',null)
                        ->count();
        return $count;
    }

    function count_wait(){
        $service = Auth::user()->service;
        if($service == 'เคลื่อนย้ายผู้ป่วย' || $service == 'ตามรับยา'){
            $count['book'] = DB::table('books')
                        ->where('verify',1)
                        ->whereDate('created_at', Carbon::today())
                        ->where('deleted_at',null)
                        ->count();
        }else if($service == 'พนักงานขับรถ' || $service == 'พยาบาลรีเฟอร์'){
            $count['book'] = DB::table('books_drives')
                        ->where('verify',1)
                        ->whereDate('created_at', Carbon::today())
                        ->where('deleted_at',null)
                        ->count();
        }

        return $count;
    }

    function count_succ(){
        $service = Auth::user()->service;
        if($service == 'เคลื่อนย้ายผู้ป่วย' || $service == 'ตามรับยา'){
            $count['book'] = DB::table('books')
                        ->where('verify',2)
                        ->whereDate('created_at', Carbon::today())
                        ->where('deleted_at',null)
                        ->count();
        }else if($service == 'พนักงานขับรถ' || $service == 'พยาบาลรีเฟอร์'){
            $count['book'] = DB::table('books_drives')
                        ->where('verify',2)
                        ->whereDate('created_at', Carbon::today())
                        ->where('deleted_at',null)
                        ->count();
        }
        return $count;
    }

    function count_sum(Request $req){
        $date_from = $req->date_from;
        $date_to = $req->date_to;
        $score1 = $req->score1;
        $score2 = $req->score2;
        $shift = $req->shift;
        $service = Auth::user()->service;
        if($service == 'เคลื่อนย้ายผู้ป่วย' || $service == 'ตามรับยา'){
            if($score2 == ''){
                if($shift == ''){
                    if($date_to == ''){
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }else{
                    if($date_to == ''){
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where("shift",$shift)
                                ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where("shift",$shift)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }
            }else{
                if($shift == ''){
                    if($date_to == ''){
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                // ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }else{
                    if($date_to == ''){
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                // ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }
            }
        }else if($service == 'พนักงานขับรถ' || $service == 'พยาบาลรีเฟอร์'){
            if($score2 == ''){
                if($shift == ''){
                    if($date_to == ''){
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }else{
                    if($date_to == ''){
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where("shift",$shift)
                                ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where("shift",$shift)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }
            }else{
                if($shift == ''){
                    if($date_to == ''){
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                // ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }else{
                    if($date_to == ''){
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                // ->whereDate('created_at',Carbon::today())
                                ->count();
                    }else{
                        $count['book'] = DB::table("books_drives")
                                // ->whereNotNull("name_ssn")
                                // ->whereNotNull("time_out")
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->count();
                    }
                }
            }
        }

        return $count;
    }

    function search_hn(Request $req){
        $hn = $req->hn;
        $date_time = Carbon::now()->format('Y-m-d H:i:s');
        $data['data'] = DB::connection('mysql2')->table('patient')->select('hn','pname','fname','lname')->where('hn',$hn)->first();
        $name = $data;
        return $name;
        // $data['data'] = DB::connection('mysql2')
        //                 ->select(DB::raw("SELECT ovst.hn,ovst.an,patient.pname,patient.fname,patient.lname,ovst.vstdate FROM ovst
        //                 LEFT JOIN patient ON patient.hn = ovst.hn
        //                 WHERE ovst.hn = '$hn' AND DATE_FORMAT(CONCAT(vstdate,' ',vsttime ),'%Y-%m-%d %H:%i:%s') <= CURRENT_TIMESTAMP ORDER BY ovst.vstdate DESC LIMIT 1"));
        //                 foreach ($data['data'] as $opd){
        //                   $vn['data'] =  [
        //                         'an' => $opd->an,
        //                         'fname' => $opd->fname,
        //                         'hn' => $opd->hn,
        //                         'lname' => $opd->lname,
        //                         'pname' => $opd->pname,
        //                         'vstdate' => $opd->vstdate,
        //                     ];
        //                 }
        // if($vn['data']['an'] == null){
        //     $name = $vn;
        //     return $name;
        // }else{
        //     $name['data'] = DB::connection('mysql2')->table('an_stat')
        //                 ->select('ovst.hn', 'ovst.an', 'patient.pname', 'patient.fname', 'patient.lname', 'vstdate')
        //                 ->leftJoin('ovst','an_stat.an','=','ovst.an')
        //                 ->leftJoin('patient','ovst.hn','=','patient.hn')
        //                 ->where('an_stat.an','=',$vn['data']['an'])
        //                 ->first();
        //     return $name;
        // }
    }

    function conFirm_drive(Request $req){
        // dd($req->all());
        $id = $req->id_hidden;
        $confirm = books_drives::where('ID', $id)->update([
            'verify' => 2,
            'time_out' => now(),
            'confirm_doc' => $req->confirm_doc,
            'rate' => $req->rate,
            'note_doc' => $req->note_doc
        ]);
        if($confirm == 1){
            $is_data = books_drives::where('ID',$id)->first();
            $startTime = Carbon::parse($is_data->time_in);
            $endTime = Carbon::parse($is_data->time_out);

            $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S');

            $confirm_p = books_drives::where('ID', $id)->update(['time_total' => $totalDuration]);
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

    function data_drive_sum(Request $req){
        $date_from = $req->date_from;
        $date_to = $req->date_to;
        $score1 = $req->score1;
        $score2 = $req->score2;
        $shift = $req->shift;
        // dd($req->all());
        if($score2 == ''){
            if($shift == ''){
                if($date_to == ''){
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }else{
                if($date_to == ''){
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where("shift",$shift)
                            ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where("shift",$shift)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }
        }else{
            if($shift == ''){
                if($date_to == ''){
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            // ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }else{
                if($date_to == ''){
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            ->where("shift",$shift)
                            // ->whereDate('created_at',Carbon::today())
                            ->get();
                }else{
                    $ssn['data'] = DB::table("books_drives")
                            // ->whereNotNull("name_ssn")
                            // ->whereNotNull("time_out")
                            ->where('verify',2)
                            ->where("deleted_at",null)
                            ->where('rate','>=',$score1)
                            ->where('rate','<=',$score2)
                            ->where("shift",$shift)
                            ->whereDate('created_at','>=',$date_from)
                            ->whereDate('created_at','<=',$date_to)
                            ->get();
                }
            }
        }
        // dd($ssn);
        return $ssn;
    }

    function search_3(Request $req){
        $date_from = $req->date_from;
        $date_to = $req->date_to;
        $score1 = $req->score1;
        $score2 = $req->score2;
        $shift = $req->shift;
        if($score2 == ''){
            if($shift == ''){
                if($date_to == ''){
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->whereDate('created_at',Carbon::today())
                                ->where("deleted_at",null)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }else{
                if($date_to == ''){
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("shift",$shift)
                                ->where("deleted_at",null)
                                ->whereDate('created_at',Carbon::today())
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("shift",$shift)
                                ->where("deleted_at",null)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }
        }else{
            if($shift == ''){
                if($date_to == ''){
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }else{
                if($date_to == ''){
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }else{
                    $data = books_drives::select('id','name_ssn','created_at')
                                ->where('verify',2)
                                ->where("deleted_at",null)
                                ->where('rate','>=',$score1)
                                ->where('rate','<=',$score2)
                                ->where("shift",$shift)
                                ->whereDate('created_at','>=',$date_from)
                                ->whereDate('created_at','<=',$date_to)
                                ->orderBy('name_ssn')
                                ->get()
                                ->groupBy('name_ssn');
                    $name_ssn = [];
                    $name_ssnCount = [];
                        foreach($data as $name_ssn => $values){
                            $name_ssns[] = $name_ssn;
                            $name_ssnCount[] = count($values);
                        }
                        if($data != null){
                            $status = [
                                'name_ssns'=>$name_ssns,
                                'name_ssnCount'=>$name_ssnCount
                            ];
                        }
                }
            }
        }
        return $status;

    }
}
