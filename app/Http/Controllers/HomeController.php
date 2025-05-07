<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Token;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\Cast\Bool_;
class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    const ADMIN_TYPE = 1;
    const DEFAULT_TYPE = 0;

    public function isAdmin(){
        return $this->type === self::ADMIN_TYPE;
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(auth()->user()->isAdmin()) {
            return view('admin.home');
        } else {
            return view('formadd.formadd');
        }

    }
}
