<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\DefaultCategoryController;


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
Route::post('vcards', [VcardController::class, 'store']);
Route::post('vcards/checkphonenumber', [VcardController::class, 'checkphonenumber']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/me', [UserController::class, 'show_me']);
    
    Route::apiResource('users', UserController::class);

    //Vcard Routes
    Route::post('vcards/{vcard}/transactions', [VcardController::class, 'myTransactions'])->middleware('can:update,vcard');
    Route::post('vcards/{vcard}/confirmdelete', [VcardController::class, 'confirmDelete'])->middleware('can:update,vcard');
    Route::post('vcards/{vcard}/checkconfirmationcode', [VcardController::class, 'checkConfirmationCode']);
    Route::patch('vcards/{vcard}', [VcardController::class, 'update_max_debit'])->middleware('can:block,vcard');
    Route::patch('vcards/alterblockedStatus/{vcard}', [VcardController::class, 'alterBlockedStatus'])->middleware('can:block,vcard');
    Route::patch('vcards/{vcard}/updatePassword', [VcardController::class, 'updatePassword'])->middleware('can:update,vcard');
    Route::patch('vcards/{vcard}/confirmation_code', [VcardController::class, 'updateconfirmation_code'])->middleware('can:update,vcard');
    Route::patch('vcards/{vcard}/piggybank', [VcardController::class, 'piggyBank'])->middleware('can:update,vcard');
    Route::patch('vcards/{vcard}/settings', [VcardController::class, 'updateSettings'])->middleware('can:update,vcard');
    Route::get('vcards/{vcard}/categories', [VcardController::class, 'getCategoriesOfVcard'])->middleware('can:update,vcard');
    Route::patch('vcards/{vcard}/markreadnotifications', [VcardController::class, 'markReadNotifications']);
    Route::get('vcards', [VcardController::class, 'index'])->middleware('can:viewAny,App\Models\Vcard');
    Route::get('vcards/{vcard}', [VcardController::class, 'show'])->middleware('can:view,vcard');
    Route::put('vcards/{vcard}', [VcardController::class, 'update'])->middleware('can:update,vcard');
    Route::delete('vcards/{vcard}', [VcardController::class, 'destroy'])->middleware('can:delete,vcard');
    
    //Admin Routes
    Route::patch('admins/{admin}/updatePassword', [AdminController::class, 'updatePassword'])->middleware('can:update,admin');
    Route::apiResource('admins', AdminController::class);

    //Transaction Routes
    Route::apiResource('transactions', TransactionController::class);

    //Category Routes
    Route::apiResource('default_categories', DefaultCategoryController::class);
    Route::apiResource('categories', CategoryController::class);
});








Route::patch('admins/updatePassword/{admin}', [AdminController::class, 'updatePassword']);

//stats

//Despesas
Route::get('totalDebit/{vcard}', [VcardController::class, 'getDebitTransactionsTotal']);
Route::get('totalDebit/{vcard}/{month}', [VcardController::class, 'getDebitTransactionsTotalByMonth']);

//Ganhos
Route::get('totalCredit/{vcard}', [VcardController::class, 'getCreditTransactionsTotal']);
Route::get('totalCredit/{vcard}/{month}', [VcardController::class, 'getCreditTransactionsTotalByMonth']);

//transações
Route::get('totalTransactions/{vcard}', [VcardController::class, 'getTotalTransactions']);
Route::get('totalTransactions/{vcard}/{month}', [VcardController::class, 'getTotalTransactionsByMonth']);

Route::get('months', [TransactionController::class, 'getAllMonths']);

//balanço

Route::get('balance/sum', [AdminController::class, 'getSumOfBalances']);
Route::get('balance/{vcard}', [VcardController::class, 'getBalanceInfo']);

Route::get('stats/vcards/count/blocked', [AdminController::class, 'getNumberOfBlockedVcards']);
Route::get('stats/vcards/count/created', [AdminController::class, 'getCountOfCreatedVcardsPerMonth']);
Route::get('stats/vcards/count', [AdminController::class, 'getNumberOfVcards']);

