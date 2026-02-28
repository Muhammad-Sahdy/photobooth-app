<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\RegistrationController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\FlowController;
use App\Http\Controllers\Web\Admin\TemplateController;
use App\Http\Controllers\Web\Admin\FilterController as AdminFilterController;
use App\Http\Controllers\Web\FilterController as WebFilterController;

// Halaman registrasi awal
Route::get('/', [RegistrationController::class, 'showForm'])
    ->name('registration.form');

Route::post('/register', [RegistrationController::class, 'store'])
    ->name('registration.store');

// Ajax polling status pembayaran (dipanggil dari halaman payment)
Route::get('/transactions/{code}/status', [RegistrationController::class, 'checkStatus'])
    ->name('transactions.check-status');

//Flow 
Route::get('/session/{code}/template', [FlowController::class, 'template'])
    ->name('flow.template');

Route::get('/session/{code}/capture', [FlowController::class, 'capture'])
    ->name('flow.capture');

Route::get('/session/{code}/select-photos', [FlowController::class, 'selectPhotos'])
    ->name('flow.select-photos');

Route::get('/session/{code}/compose', [FlowController::class, 'compose'])
    ->name('flow.compose');

Route::get('/session/{code}/filter', [FlowController::class, 'filter'])
    ->name('flow.filter');

Route::get('/session/{code}/barcode', [FlowController::class, 'barcode'])
    ->name('flow.barcode');

// Proses Apply Filter (POST AJAX)
Route::post('/session/{code}/apply-filter', [WebFilterController::class, 'apply'])
    ->name('photobooth.apply-filter');

// Halaman gallery customer via token (QR/link)
Route::get('/gallery/{token}', [GalleryController::class, 'show'])
    ->name('gallery.show');

Route::get('/gallery/{code}/download-zip', [GalleryController::class, 'downloadZip'])->name('gallery.downloadZip');

// Admin login routes (public - no auth required)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Web\Admin\DashboardController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [DashboardController::class, 'login']);
});

// Admin protected routes (requires authentication)
Route::prefix('admin')
    ->middleware(['auth']) // Changed from auth.basic to auth (session-based)
    ->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Web\Admin\DashboardController::class, 'index'])
            ->name('dashboard');
        Route::resource('templates', TemplateController::class)
            ->except(['show']);
        Route::resource('filters', AdminFilterController::class)->except(['show']);
        Route::post('/logout', [DashboardController::class, 'logout'])
            ->name('logout');
    });
