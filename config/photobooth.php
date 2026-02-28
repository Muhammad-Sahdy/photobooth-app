<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Harga Sesi Photobooth (dalam rupiah)
    |--------------------------------------------------------------------------
    */
    'price' => env('PHOTOBOOTH_PRICE', 35000),

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi QRIS / Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Sesuaikan dengan provider yang Anda gunakan (Midtrans / InterActive / dll).
    | Field ini akan dibaca di QrisPaymentService.
    |
    */

    'qris' => [
    'endpoint'   => env('PHOTOBOOTH_QRIS_ENDPOINT', 'https://api.sandbox.midtrans.com/v2/charge'),

        // Server key / API key untuk autentikasi ke provider
        'server_key' => env('PHOTOBOOTH_QRIS_SERVER_KEY', ''),

        // Acquirer / channel (sesuaikan dengan provider, misal: gopay, shopeepay, dll)
        'acquirer'   => env('PHOTOBOOTH_QRIS_ACQUIRER', 'gopay'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan File & Penyimpanan Foto
    |--------------------------------------------------------------------------
    */

    'storage_disk' => env('PHOTOBOOTH_STORAGE_DISK', 'public'),

    // Folder dasar untuk penyimpanan foto
    'paths' => [
        'raw'   => 'photos',          // nanti: photos/{code}/raw
        'final' => 'photos',          // nanti: photos/{code}/final
        'templates' => 'templates',   // tempat simpan file template
    ],
];
