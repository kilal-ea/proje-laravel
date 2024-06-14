<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import the Log facade

class TransactionController extends Controller
{
    public function Transaction(Request $req)
    {
        $idp = (array) $req->input('id');
        $quantitySend = (array) $req->input('quantitySend');
        
        $code = random_int(10000000, 99999999); 
        $codes = DB::table('bons_transaction')->pluck('code')->toArray();
        while (in_array($code, $codes)) {
            $code = random_int(10000000, 99999999); 
        }
    
        // Fetch products with matching IDs and idstock, keyed by 'id'
        $stock = DB::table('products')
            ->whereIn('id', $idp)
            ->where('idstock', 1)
            ->get()
            ->keyBy('id');
        
        foreach ($idp as $key => $index) {
            // Check if the product exists in the fetched stock
            if (!isset($stock[$index])) {
                throw new \Exception('Product not found for id: ' . $index);
            }
    
            // Calculate the new quantity after the transaction
            $newQuantity = $stock[$index]->quantity - $quantitySend[$key];
    
            // Check for sufficient stock
            if ($newQuantity < 0) {
                throw new \Exception('Insufficient stock for product id: ' . $index);
            }
    
            // Update the product quantity in the database
            DB::table('products')
                ->where('id', $index)
                ->update(['quantity' => $newQuantity]);
        }
    
        // Insert transaction records into bons_transaction
        foreach ($idp as $key => $index) {
            DB::table('bons_transaction')->insert([
                'code' => $code,
                'idp' => $idp[$key],
                'quantity' => $quantitySend[$key],
                'created_at' => now(),
            ]);
        }
    
        // Insert a record into transactions
        DB::table('transactions')->insert([
            'code' => $code,
            'status' => 0,
            'created_at' => now(),
        ]);
    
        return response()->json(true);
    }
    
    public function Transactionbon(Request $req)
    {
        $Transactionbo = DB::table('transactions')
            ->get();
    
        return response()->json($Transactionbo);
    }
    
    public function Transactionbonmore(Request $req)
    {
        $code = $req->input('code');
        
            $bons_transaction_product = DB::table('bons_transaction')
            ->join('products', 'bons_transaction.idp', '=', 'products.id')
             ->select('products.id', 'products.name', 'products.qtt_piece_in_carton','products.price', 'bons_transaction.quantity', 'bons_transaction.created_at')
            ->where('bons_transaction.code', '=', $code)
            ->get();
                        
        return response()->json($bons_transaction_product);
    }
    
    public function Transactionbonatt(Request $req)
    {
        $Transactionbo = DB::table('transactions')
            ->where('status', 0)
            ->get();
        
        return response()->json($Transactionbo);
    }
    
    public function Transactionaccept(Request $req)
    {
        $code = $req->input('code');
        $frais = (array) $req->input('frais');
        $douanes = $req->input('douanes');
    
        try {
            // Fetch all transactions with the given code and join with products
            $Transactionbo = DB::table('bons_transaction')
                ->join('products', 'bons_transaction.idp', '=', 'products.id')
                ->select('products.id', 'products.name', 'products.price', 'products.supplier_id', 'products.qtt_piece_in_carton', 'bons_transaction.quantity as quantity', 'bons_transaction.created_at')
                ->where('bons_transaction.code', '=', $code)
                ->get();
    
            // Calculate total quantity of items in all transactions
            $TotalQuantity = 0;
            
            foreach ($Transactionbo as $key => $transaction) {
                $TotalQuantity += $transaction->quantity;
            }

            
    
            // Avoid division by zero
            if ($TotalQuantity === 0) {
                throw new \Exception('Total quantity is zero');
            }
            
            // Calculate douanes per item
            $fr = $douanes / $TotalQuantity;
            
            // Loop through each transaction and insert the updated product 
            foreach ($Transactionbo as $key => $transaction) {
                $douanesPerItem = $frais[$key];
                $newprice = $transaction->price + $douanesPerItem + $fr;
            
                $thirdDigit = intval(($newprice * 1000) % 10);

                
                if ($thirdDigit > 0) {

                    $third =  (10 - $thirdDigit ) / 1000;
                    
                    $newprice  += $third ;
                    
                }

                
                $newprice =  number_format($newprice, 2);

                
                // Check if the new price exceeds the maximum value for the price column
                if ($newprice > 99999999.999) { // Updated maximum value
                    Log::error("Calculated price exceeds maximum allowed value", [
                        'transaction' => $transaction,
                        'newprice' => $newprice,
                        'douanesPerItem' => $douanesPerItem,
                    ]);
                    continue; // Skip this transaction and log the error
                }
    
                // Insert the new product
                DB::table('products')->insert([
                    'name' => $transaction->name,
                    'price' => $newprice,
                    'supplier_id' => $transaction->supplier_id,
                    'quantity' => $transaction->quantity,
                    'idstock' => 2,
                    'qtt_piece_in_carton' => $transaction->qtt_piece_in_carton,
                    'created_at' => $transaction->created_at,
                    'updated_at' => now(),
                ]);

                $product = DB::table('products')
                ->where('name', $transaction->name)
                ->where('quantity', $transaction->quantity)
                ->where('updated_at', now())
                ->first();

                // Insert into the profit_margin table
                DB::table('profit_margin')->insert([
                    'idp' => $product->id,
                    'Quantity_before_sell' => $transaction->quantity,
                    'Quantity_sell' => null,
                    'Total_purchase_price' => $newprice * $transaction->quantity,
                    'Total_sell_price' => null,
                    'profit_margin' => null,
                    'created_at' => now(),
                ]);
            }
            
            // Update the status of the transactions
            DB::table('transactions')
                ->where('code', $code)
                ->update(['status' => 1]);
    
            return response()->json(true);
    
        } catch (\Exception $e) {
            Log::error("Error processing transaction", [
                'message' => $e->getMessage(),
                'code' => $code,
                'frais' => $frais,
                'douanes' => $douanes,
            ]);
            return response()->json(false, 500);
        }
    }
    
    
}
