<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\CategoryController;


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
    Route::patch('users/{user}/password', [UserController::class, 'update_password']);
    Route::apiResource('users', UserController::class);
});
Route::get('/vcards/{vcard}/transactions', [VcardController::class, 'myTransactions']);


Route::post('vcards/{vcard}/checkpassword', [VcardController::class, 'checkPassword']);
Route::post('vcards/{vcard}/checkconfirmationcode', [VcardController::class, 'checkConfirmationCode']);
Route::post('vcards/checkphonenumber', [VcardController::class, 'checkphonenumber']);
Route::post('vcards/{vcard}/piggybank', [VcardController::class, 'piggyBank']);
Route::apiResource('vcards', VcardController::class);


Route::apiResource('transactions', TransactionController::class);
Route::get('vcards/{vcard}', [VcardController::class, 'show']);
//  Route::APIResource('transactions', [TransactionController::class]);

Route::get('categories', [CategoryController::class , 'index']);
Route::get('categories/{category}', [CategoryController::class , 'show']);
Route::post('categories', [CategoryController::class , 'store']);
Route::get('vcards/{vcard}/categories', [CategoryController::class, 'getCategoriesOfVcard']);

