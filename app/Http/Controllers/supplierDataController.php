<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierDataController extends Controller
{
    public function supplierData(Request $request)
    {
        
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'phone' => 'max:20',
            'email' => 'string|email|max:255',
        ]);
        
        // Insert validated data into the suppliers table
        DB::table('suppliers')->insert([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'created_at' => now(),
        ]);
        
        return response()->json(['message' => 'Supplier added successfully', 'supplier' => $validatedData], 201);
    }
}
