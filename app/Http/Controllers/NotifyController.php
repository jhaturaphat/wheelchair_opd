<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Token;
use App\role_tokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotifyController extends Controller
{
    public function notify(){
        return view('admin.notify.callback');
    }

    public function regis(){
        return view('regis');
    }

    public function regis_line(){
        return view('regis_line');
    }

    function save_token(Request $req){
        $role = $req->role;
        if($role == 'เคลื่อนย้ายผู้ป่วย' || $role == 'ตามรับยา'){
            // dd('1');
            DB::beginTransaction();
            $save_token = new token;
            $save_token->ssn_id = $req->ssn_id;
            $save_token->ssn_name = $req->ssn_name;
            $save_token->ssn_tel = $req->ssn_tel;
            $save_token->ssn_token = $req->ssn_token;
            $save_token->role = $req->role;
            $save_token->consent = $req->consent;

            if($save_token->save()){
                $status = 'success';
            }else{
                $status = 'error';
            }
            DB::commit();
            return $status;
        }else if($role == 'พนักงานขับรถ' || $role == 'พยาบาลรีเฟอร์'){
            // dd('2');
            DB::beginTransaction();
            $save_token = new role_tokens;
            $save_token->cid = $req->ssn_id;
            $save_token->name = $req->ssn_name;
            $save_token->tokens = $req->ssn_token;
            $save_token->tel = $req->ssn_tel;
            $save_token->role = $req->role;
            $save_token->consent = $req->consent;
            if($save_token->save()){
                $status = 'success';
            }else{
                $status = 'error';
            }
            DB::commit();
            return $status;
        }

    }
}
