<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Add this line to import the DB facade

class StockController extends Controller
{
    public function Stock(Request $request)
    {
        $idu = $request->input('idu');
        $stockProducts = DB::table('products')
            ->where('idstock', $idu)
            ->get();
        
        return response()->json($stockProducts);
    }
    public function Stocktr(Request $request)
    {
        
        $idu = $request->input('idu');
        $stockProducts = DB::table('products')
        ->where('idstock', $idu)
        ->where('quantity', '>', 0)
        ->get();
        
        return response()->json($stockProducts);
    }
}
