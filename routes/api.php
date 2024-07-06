<?php

use App\Http\Controllers\APIS\AUTH\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//////////// start Auth

 // Get All Users
 Route::get('getallusers', [AuthController::class, 'getallusers']);

 // Login & Register
 Route::post('register', [AuthController::class, 'register']);
 Route::post('login', [AuthController::class, 'login']);

 // OTP Verification Code
 Route::post('loginWithOtp', [AuthController::class, 'loginWithOtp']);
 Route::any('resendOtp', [AuthController::class, 'resendOtp']); // resend otp /...

 // Reset Password
Route::post('reset-password', [AuthController::class, 'reset']); // update password

////////////// End Auth




