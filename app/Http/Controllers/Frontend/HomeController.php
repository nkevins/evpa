<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

/**
 * Class HomeController
 */
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->take(4)->get();
        } catch (QueryException $e) {
            return view('system/errors/not_installed');
        }

        return view('home', [
            'users' => $users,
        ]);
    }
    
    /**
     * Show the application dashboard.
     */
    public function indexEpva()
    {
        if (Auth::user()) {
            return redirect('dashboard');
        }
        
        return view ('home');
    }
}
