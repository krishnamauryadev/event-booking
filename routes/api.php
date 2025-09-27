<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventsController;
use App\Http\Controllers\API\TicketsController;
use App\Http\Controllers\API\BookingController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class,'logout']);
    Route::get('me', [AuthController::class,'me']);

    Route::get('events', [EventsController::class,'index']);
    Route::get('events/{id}', [EventsController::class,'show']);
    Route::post('events', [EventsController::class,'store'])->middleware('role:organizer');
    Route::put('events/{id}', [EventsController::class,'update'])->middleware('role:organizer');
    Route::delete('events/{id}', [EventsController::class,'destroy'])->middleware('role:organizer');

    Route::post('events/{event_id}/tickets', [TicketsController::class,'store'])->middleware('role:organizer');
    Route::put('tickets/{id}', [TicketsController::class,'update'])->middleware('role:organizer');
    Route::delete('tickets/{id}', [TicketsController::class,'destroy'])->middleware('role:organizer');

    Route::post('tickets/{id}/bookings', [BookingController::class,'store'])->middleware('role:customer')->middleware('prevent.double.booking');
    Route::get('bookings', [BookingController::class,'index'])->middleware('role:customer');
    Route::put('bookings/{id}/cancel', [BookingController::class,'cancel'])->middleware('role:customer');
});

