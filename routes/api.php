<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::group([

   'middleware' => 'api',
   'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class,'login'])->name('login');
    Route::get('register',[AuthController::class,'registeration_form'])->name('register');
    Route::post('confirm_registration',[AuthController::class,'confirm_registration'])->name('confirm_registration');
    Route::post('verify_code',[AuthController::class,'verify_code'])->name('verify_code');

    
});


Route::group(
        [
        'middleware' => ['api','jwt.verify']
        ]
, function() {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class,'me']);
    Route::post('send-invite',[AuthController::class,'send_invite']);
    Route::post('update-account',[AuthController::class,'update']);
});