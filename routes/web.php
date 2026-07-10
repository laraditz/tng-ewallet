<?php

use Illuminate\Support\Facades\Route;
use Laraditz\TngEwallet\Http\Controllers\NotifyPaymentController;
use Laraditz\TngEwallet\Http\Middleware\VerifyTngNotifySignature;

Route::post(config('tng-ewallet.notify_path', '/tng-ewallet/notify'), NotifyPaymentController::class)
    ->middleware(VerifyTngNotifySignature::class)
    ->name('tng-ewallet.notify');
