<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitesController extends Controller
{
    public function Supplier()
    {
        $Supplier = DB::table('suppliers')->get();
        
        return response()->json($Supplier);
    }

    public function addproducts(Request $request)
    {
        
        $name = $request->input('name');
        $price = $request->input('price');
        $quantityPerCarton = $request->input('quantityPerCarton');
        $quantity = $request->input('quantity');
        $supplier = $request->input('supplier');
        $idstock = $request->input('idstock');
        
        
            $DB = DB::table('products')->insert([
                'name' => $name,
                'price' => $price,
                'idstock' => $idstock,
                'quantity' => $quantity,
                'supplier_id' => $supplier,
                'qtt_piece_in_carton' => $quantityPerCarton,
                'created_at' => now(),
            ]);
            
    
            return response()->json(['message' => 'Product added successfully', 'product' => $validatedData], 201);

        
    }
    
}
