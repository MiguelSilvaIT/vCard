<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\DADVcardController;
use App\Http\Controllers\api\CategoryDefaultController;


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
    Route::patch('vcards/{vcard}', [VcardController::class, 'update_max_debit'])
        ->middleware('can:update_max_debit');

    Route::apiResource('users', UserController::class);

    Route::post('vcards/{vcard}/transactions', [VcardController::class, 'myTransactions']);

    Route::post('vcards/{vcard}/checkpassword', [VcardController::class, 'checkPassword']);
    Route::post('vcards/{vcard}/checkconfirmationcode', [VcardController::class, 'checkConfirmationCode']);
    
    
    Route::patch('vcards/{vcard}', [VcardController::class, 'update_max_debit']);
    Route::patch('vcards/alterblockedStatus/{vcard}', [VcardController::class, 'alterBlockedStatus']);
    Route::patch('vcards/updatePassword/{vcard}', [VcardController::class, 'updatePassword']);
    Route::patch('vcards/confirmation_code/{vcard}', [VcardController::class, 'updateconfirmation_code']);
    Route::patch('vcards/{vcard}/piggybank', [VcardController::class, 'piggyBank']);
    Route::patch('vcards/{vcard}/settings', [VcardController::class, 'updateSettings']);
    Route::get('vcards/{vcard}/categories', [VcardController::class, 'getCategoriesOfVcard']);
    Route::patch('vcards/{vcard}/markreadnotifications', [VcardController::class, 'markReadNotifications']);
    Route::apiResource('vcards', VcardController::class);

    Route::apiResource('transactions', TransactionController::class);
});

Route::post('vcards/checkphonenumber', [VcardController::class, 'checkphonenumber']);
Route::post('vcards', [VcardController::class, 'store']);



Route::get('categories/default', [CategoryDefaultController::class , 'index']);
Route::get('categories/default/{category}', [CategoryDefaultController::class , 'show']);
Route::post('categories/default', [CategoryDefaultController::class , 'store']);
Route::put('categories/default/{category}', [CategoryDefaultController::class , 'update']);
Route::delete('categories/default/{category}', [CategoryDefaultController::class , 'destroy']);

Route::get('categories', [CategoryController::class , 'index']);
Route::get('categories/{category}', [CategoryController::class , 'show']);
Route::post('categories', [CategoryController::class , 'store']);
Route::put('categories/{category}', [CategoryController::class , 'update']);
// Route::get('vcards/{vcard}/categories', [CategoryController::class, 'getCategoriesOfVcard']);
Route::delete('categories/{category}', [CategoryController::class , 'destroy']);



//Admin Routes
Route::apiResource('admins', AdminController::class);

Route::patch('admins/updatePassword/{admin}', [AdminController::class, 'updatePassword']);