<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/create-order', [PaymentController::class, 'createOrder'])->name('create.order');
Route::post('/capture-order', [PaymentController::class, 'captureOrder'])->name('capture.order');
