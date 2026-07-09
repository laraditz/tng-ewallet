<?php

use Illuminate\Support\Facades\Route;
use Laraditz\TngEwallet\Http\Controllers\NotifyPaymentController;

Route::post(config('tng-ewallet.notify_path', '/tng-ewallet/notify'), NotifyPaymentController::class);
