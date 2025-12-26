<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect()->route('devices.index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Device Routes
Route::resource('devices', DeviceController::class);
Route::post('devices/{device}/activate', [DeviceController::class, 'activate'])->name('devices.activate');
Route::post('devices/{device}/deactivate', [DeviceController::class, 'deactivate'])->name('devices.deactivate');

// Transaction Routes
Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
