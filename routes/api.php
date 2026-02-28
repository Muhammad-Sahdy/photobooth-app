<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PhotoCaptureController;

// Webhook QRIS / payment gateway
Route::post('/payments/qris/webhook', [PaymentWebhookController::class, 'handle'])
    ->name('payments.qris.webhook');

Route::post('/test-webhook', function () {
    return response()->json(['ok' => true]);
});

// Group untuk API photobooth (kalau mau pakai middleware tambahan bisa ditambah di sini)
Route::prefix('photobooth')->group(function () {

    // Capture foto tiap 5 detik
    Route::post('/{code}/capture', [PhotoCaptureController::class, 'capture'])
        ->name('photobooth.capture');

    // Pilih 2 foto + template untuk final compose
    Route::post('/{code}/choose-photos', [PhotoCaptureController::class, 'choosePhotos'])
        ->name('photobooth.choose-photos');

    Route::get('/filter-preview/{photo_id}/{filter_slug}', [PhotoCaptureController::class, 'getPreview'])
    ->name('photobooth.filter-preview');
});
