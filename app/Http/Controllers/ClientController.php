<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function clientsadd(Request $req)
    {
        $name = $req->input('name');
        $phone = $req->input('phone');
        $email = $req->input('email');
        $city = $req->input('city');

        try {
            DB::table('clients')->insert([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'city' => $city,
            ]);
            
            return response()->json(['message' => 'Client added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add client', 'error' => $e->getMessage()], 500);
        }
    }
    public function clientsshow(Request $req)
    {
        
       $client = DB::table('clients')->get();
            
       return response()->json($client);
        
    }
}
