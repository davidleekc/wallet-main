<?php

use Illuminate\Http\Request;
use Modules\Deposit\Http\Controllers\API\DepositController;

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

Route::middleware('auth:api')->get('/deposit', function (Request $request) {
    return $request->user();
});

Route::prefix('deposit')->middleware('auth:client-api')->group(function () {
    Route::post('reload', [DepositController::class, 'reload']);
    Route::post('transfer', [DepositController::class, 'transfer']);
    Route::post('check_balance', [DepositController::class, 'checkBalance']);
    Route::post('check_transaction', [DepositController::class, 'checkTransaction']);

});