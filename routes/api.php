<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProduitesController;
use App\Http\Controllers\supplierDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\ProfitMarginController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/user',[UserController::class, 'user']);

Route::post('/stock', [StockController::class, 'Stock']);
Route::post('/stocktr', [StockController::class, 'Stocktr']);

Route::get('/Supplier', [ProduitesController::class, 'Supplier']);
Route::post('/products',[ProduitesController::class, 'addproducts']);
Route::post('/supplierData',[supplierDataController::class, 'supplierData']);
Route::post('/Transaction',[TransactionController::class, 'Transaction']);
Route::get('/Transaction/bon',[TransactionController::class, 'Transactionbon']);
Route::post('/Transaction/bon/more',[TransactionController::class, 'Transactionbonmore']);




//mar
Route::get('/Transaction/bon/att',[TransactionController::class, 'Transactionbonatt']);
Route::post('/Transaction/bon/accept',[TransactionController::class, 'Transactionaccept']);
Route::post('/clients/add ',[ClientController::class, 'clientsadd']);
Route::get('/clients/show ',[ClientController::class, 'clientsshow']);
Route::get('/Vente/products ',[VenteController::class, 'productsVente']);
Route::post('/Vente ',[VenteController::class, 'Vente']);
Route::get('/Vente/bon ',[VenteController::class, 'Bonvente']);
Route::post('/bon/details ',[VenteController::class, 'Ventebondetails']);
Route::get('/profitMargin ',[ProfitMarginController::class, 'profitMargin']);


