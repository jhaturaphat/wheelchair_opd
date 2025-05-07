<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( Auth::check() && Auth::user()->isAdmin() ) {
            return $next($request);
        } else {
            abort(403, 'ท่านไม่มีสิทธิ์์เข้าใช้หน้านี้');
            // $depart = DB::table('depart')->get();
            // return response()->view('formadd.formadd',['data'=>$depart]);
        }
    }
}
