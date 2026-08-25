<?php

use App\Http\Controllers\IzipayWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/izipay/webhook', IzipayWebhookController::class)->name('izipay.webhook');
