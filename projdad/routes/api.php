<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\UserController;


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
Route::post('auth/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/me', [UserController::class, 'show_me']);
    });

Route::get('users', [UserController::class, 'index']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::patch('users/{user}/password', [UserController::class, 'update_password']);

Route::get('vcards/{vcard}', [VcardController::class, 'show']);
//  Route::APIResource('transactions', [TransactionController::class]);
// Route::APIResource('category', [CategoryController::class]);