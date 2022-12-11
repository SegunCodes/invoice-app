<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/get-all-invoices', [InvoiceController::class, 'getAllInvoices']);
Route::get('/search', [InvoiceController::class, 'search']);
Route::get('/create', [InvoiceController::class, 'create']);
Route::get('/customers', [CustomerController::class, 'allCustomers']);
Route::get('/products', [ProductController::class, 'allProducts']);
