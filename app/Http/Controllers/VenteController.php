<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class VenteController extends Controller
{
     // Retrieve products available for sale
    public function productsVente(Request $req)
    {
        $products = DB::table('products')
            ->where('idstock', 2)
            ->where('quantity', '>', 0)
            ->get();
    
        return response()->json($products); 
    }

     // Process a sale transaction
     public function Vente(Request $req)
     {
         // Extract data from request
         $clientid = $req->input('client');
         $qtt = (array) $req->input('qtt');
         $products = (array) $req->input('products');
         $prices = (array) $req->input('price'); 
     
         $priceTotal = 0;
     
         // Update product quantities and calculate total price
         foreach ($products as $key => $productId) {
             $product = DB::table('products')
                 ->where('id', $productId)
                 ->first();
     
             if (!$product) {
                 return response()->json(['error' => 'Product not found: ' . $productId], 400);
             }
     
             $newQuantity = $product->quantity - $qtt[$key];
             DB::table('products')
                 ->where('id', $productId)
                 ->update([
                     'quantity' => $newQuantity,
                     'updated_at' => now()
                 ]);
         }
     
         // Generate a unique sale code
         $code = random_int(10000000, 99999999); 
         $codes = DB::table('bons_sale')->pluck('code')->toArray();
     
         while (in_array($code, $codes)) {
             $code = random_int(10000000, 99999999); 
         }
     
         // Insert sale details into database
         foreach ($products as $key => $productId) {
             $oricetotalsele = $prices[$key] * $qtt[$key];
     
             try {
                 DB::table('bons_sale')->insert([
                     'code' => $code,
                     'idus' => 1,
                     'idc' => $clientid,
                     'idp' => $products[$key],
                     'quantity_piece' => $qtt[$key],
                     'price' => $prices[$key],
                     'price_total' => $oricetotalsele,
                     'created_at' => now(),
                 ]);
             } catch (\Illuminate\Database\QueryException $e) {
                 return response()->json(['error' => 'Failed to insert into bons_sale: ' . $e->getMessage()], 500);
             }
     
             // Update profit margin
             $profit_margin = DB::table('profit_margin')
                 ->where('idp', $products[$key])
                 ->first();
     
             if (!$profit_margin) {
                 return response()->json(['error' => 'Profit margin record not found for product: ' . $products[$key]], 400);
             }
     
             DB::table('profit_margin')
                 ->where('idp', $products[$key])
                 ->update([
                     'Quantity_sell' => $profit_margin->Quantity_sell + $qtt[$key],
                     'Total_sell_price' => $profit_margin->Total_sell_price + $oricetotalsele,
                     'profit_margin' => $profit_margin->Total_purchase_price - $profit_margin->Total_sell_price + $oricetotalsele,
                     'updated_at' => now(),
                 ]);
     
             $profit = DB::table('profit_margin')
                 ->where('idp', $products[$key])
                 ->first();
     
             DB::table('profit_margin')
                 ->where('idp', $products[$key])
                 ->update([
                     'profit_margin' => $profit->Total_sell_price - $profit->Total_purchase_price,
                 ]);
     
             $priceTotal += $prices[$key] * $qtt[$key];
         }
     
         // Insert sale record into sales table
         $d = DB::table('sales')->insert([
             'code' => $code,
             'ids' => 1,
             'idc' => $clientid,
             'priceTotal' => $priceTotal,
             'created_at' => now(),
         ]);
         // Return success response
         return response()->json(true);
     }
     
     
    
     // Retrieve all sales
    public function Bonvente()
    {
        $ventes = DB::table('sales')
            ->get();
    
        return response()->json($ventes);  
    }

    // Retrieve details of a specific sale
    public function Ventebondetails(Request $req)
    {
       $code = $req->input('code');

       $ventes = DB::table('bons_sale')
            ->join('products', 'bons_sale.idp', '=', 'products.id')
            ->join('sales', 'bons_sale.code', '=', 'sales.code')
            ->join('clients', 'bons_sale.idc', '=', 'clients.id')
            ->select('products.id', 'products.name', 'bons_sale.price', 'bons_sale.quantity_piece as quantity', 'bons_sale.created_at','clients.name as nameclients')
            ->where('bons_sale.code', '=', $code)
            ->get();

       return response()->json($ventes);  
    }

}
