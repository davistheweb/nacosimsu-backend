<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventRegistrationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| Public Events
|--------------------------------------------------------------------------
*/

Route::get('/events', [EventController::class, 'index']);

Route::get('/events/{event}', [EventController::class, 'show']);

Route::post(
    '/events/{event}/register',
    [EventRegistrationController::class, 'store']
);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard/stats',
        [EventController::class, 'stats']
    );

});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    'admin',
])->prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    // List ALL events
    Route::get(
        '/events',
        [EventController::class, 'adminEvents']
    );

    // View single event by ID
    Route::get(
        '/events/{id}',
        [EventController::class, 'adminShow']
    );

    // Create event
    Route::post(
        '/events',
        [EventController::class, 'store']
    );

    // Update event
    Route::put(
        '/events/{id}',
        [EventController::class, 'update']
    );

    // Delete event
    Route::delete(
        '/events/{id}',
        [EventController::class, 'destroy']
    );

    // View registrations
    Route::get(
        '/events/{id}/registrations',
        [EventController::class, 'registrations']
    );

});