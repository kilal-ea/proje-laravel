<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function user(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            return response()->json(['msg' => true , 'user' => $credentials], 200);
        } else {
            
            return back()->withInput()->withErrors(['email' => 'Invalid email or password']);
        }
    }
}
