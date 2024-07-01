<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
Route::post('/user', [\App\Http\Controllers\UserController::class, 'store']);
Route::patch('/user/{user}', [\App\Http\Controllers\UserController::class, 'edit']);
Route::delete('/user/{user}', [\App\Http\Controllers\UserController::class,'remove']);

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/createAccount', [\App\Http\Controllers\AccountController::class, 'createAccount']);
    Route::get('/getAccounts', [\App\Http\Controllers\AccountController::class, 'getAccounts']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/deposit', [\App\Http\Controllers\TransactionController::class, 'deposit']);
    Route::post('/withdraw', [\App\Http\Controllers\TransactionController::class, 'withdraw']);
    Route::post('/transfer', [\App\Http\Controllers\TransactionController::class, 'transfer']);
    Route::get('/showTransactions', [\App\Http\Controllers\TransactionController::class, 'showTransactions']);

});
