<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceRuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/attendance-rules', [AttendanceRuleController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    Route::post('/check-token', [AuthController::class, 'checkToken']);
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::delete('/attendance/destroy/{id}', [AttendanceController::class, 'destroy']);
    Route::post('/attendance/absent', [AttendanceController::class, 'absent']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::get('/attendance/report', [AttendanceController::class, 'report']);
    Route::get('/attendance/report-all', [AttendanceController::class, 'reportAll']);
    Route::get('/attendance-rules/{id}', [AttendanceRuleController::class, 'show']);
    Route::put('/attendance-rules/{id}', [AttendanceRuleController::class, 'update']);
    Route::put('/attendance/update/{id}', [AttendanceController::class, 'update']);
    Route::get('/attendance/{id}', [AttendanceController::class, 'show']);

});
