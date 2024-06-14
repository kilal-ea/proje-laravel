<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class ProfitMarginController extends Controller
{
     // Retrieve products available for sale
    public function profitMargin(Request $req)
    {
        $profit_margin = DB::table('profit_margin')
        ->join('products','profit_margin.idp','=','products.id')
        ->select('profit_margin.*','products.name as name','products.qtt_piece_in_carton as qtt_piece_in_carton')
        ->get();
    
        return response()->json($profit_margin); 
    }


}
