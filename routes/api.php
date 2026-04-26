<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/bookings/{id}/room', [BookingController::class, 'room']);
Route::get('/bookings/{id}/customer', [BookingController::class, 'customer']);

Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
Route::put('/bookings/{id}', [BookingController::class, 'update']);
Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
Route::get('/bookings/{id}/detail', [BookingController::class, 'detail']);