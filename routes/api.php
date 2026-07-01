<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\HoldController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', fn() => response()->json(['status' => 'ok']));

Route::controller(AvailabilityController::class)->group(callback: function () {
    Route::get(uri: '/slots/availability', action: 'getAvailableSlots');
    Route::post(uri: '/slots/{id}/hold', action: 'addHold')->whereNumber('id');
});

Route::controller(HoldController::class)->group(callback: function () {
    Route::post('/holds/{id}/confirm', action: 'confirmHold')->whereNumber('id');
    Route::delete('/holds/{id}', action: 'deleteHold')->whereNumber('id');
});