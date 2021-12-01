<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use DB;

class BackendController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $clients = \DB::table('clients')->count();
        $transactions = \DB::table('transactions')->whereDate('created_at', Carbon::today())->count();
        $revenueToday = \DB::table('transactions')->where('meta','LIKE','%reload%')->whereDate('created_at', Carbon::today())->sum('amount');
        //$revenueToday = \DB::table('transactions')->where('meta','LIKE','%reload%')->whereDate('created_at', Carbon::today())->count();
        //$random = (string)Str::orderedUuid();
        //var_dump($random);
        //die;
        return view('backend.index', compact('clients','transactions', 'revenueToday'));
    }
}
