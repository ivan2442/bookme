<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\Owner\DashboardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ShopController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/shops', [ShopController::class, 'index']);
    Route::get('/shops/{profile}', [ShopController::class, 'show']);
    Route::get('/shops/{profile}/services', [ServiceController::class, 'index']);

    Route::post('/availability', [AvailabilityController::class, 'check']);

    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/owner/dashboard/stats', [DashboardController::class, 'stats']);
});
