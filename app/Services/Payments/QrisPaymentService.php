<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class QrisPaymentService
{
    /**
     * Membuat pembayaran QRIS untuk 1 transaksi.
     * Return minimal: qr_url, payment_reference.
     */
    public function createPayment(Transaction $transaction): array
    {
        $endpoint  = config('photobooth.qris.endpoint');
        $serverKey = config('photobooth.qris.server_key');

        $orderId = $transaction->code;

        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $transaction->amount,
            ],
            'callbacks' => [
                'finish' => url("/payment/finish?order_id={$orderId}"),
            ],
        ];

        $response = Http::withOptions(['verify' => false])
            ->withBasicAuth($serverKey, '')
            ->post($endpoint, $payload);


        if (! $response->successful()) {
            Log::error('Midtrans charge failed', ['body' => $response->body()]);
            throw new \RuntimeException('Gagal membuat pembayaran QRIS');
        }

        $data = $response->json();
        Log::info('Midtrans QRIS response', $data);

        $qrUrl = null;
        if (!empty($data['actions']) && is_array($data['actions'])) {
            foreach ($data['actions'] as $action) {
                if (($action['name'] ?? null) === 'generate-qr-code' || ($action['name'] ?? null) === 'qr-code') {
                    $qrUrl = $action['url'] ?? null;
                    break;
                }
            }
            $qrUrl = $qrUrl ?? ($data['actions'][0]['url'] ?? null);
        }

        return [
            'qr_url' => $qrUrl,
            'payment_reference' => $data['transaction_id'] ?? (string) Str::uuid(),
        ];
    }
}
