<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use Modules\Client\Http\Controllers\API\RegisterController;

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
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('forgot_pin', [RegisterController::class, 'forgotPin']);
Route::post('validate_phone', [RegisterController::class, 'validatePhone']);

//Route::middleware('auth:client-api')->get('/client', function (Request $request) {
//    return $request->user();
//});

Route::prefix('client')->middleware('auth:client-api')->group(function () {
    Route::post('otp', [RegisterController::class, 'verifyOTP']);
    Route::post('refresh_otp', [RegisterController::class, 'refreshOTP']);
    Route::post('setup_pin', [RegisterController::class, 'registerPin']);
    Route::post('get_question', [RegisterController::class, 'getSecurityQuestion']);
    Route::post('register_question', [RegisterController::class, 'registerSecurityQuestion']);
    Route::post('verify_question', [RegisterController::class, 'verifySecurityQuestion']);
    Route::post('setup_profile', [RegisterController::class, 'registerClientProfile']);
    Route::post('get_profile', [RegisterController::class, 'getClientProfile']);
    Route::post('update_profile', [RegisterController::class, 'updateClientProfile']);
});