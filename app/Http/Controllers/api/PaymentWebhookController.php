<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans webhook received', $request->all());

        $orderId       = $request->input('order_id');
        $statusCode    = $request->input('status_code');
        $grossAmount   = $request->input('gross_amount');
        $signatureKey  = $request->input('signature_key');
        $status        = $request->input('transaction_status');

        // 1. Verifikasi Signature Key (KEAMANAN KRITIS MUTLAK)
        // Mengambil Server Key dari .env Anda
        $serverKey = env('PHOTOBOOTH_QRIS_SERVER_KEY');

        // Membentuk ulang hash sesuai rumus resmi Midtrans
        $calculatedSignature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        // Jika hash tidak cocok, tolak mentah-mentah
        if ($calculatedSignature !== $signatureKey) {
            Log::critical('Manipulasi Webhook Terdeteksi / Invalid Signature!', [
                'order_id' => $orderId,
                'ip' => $request->ip()
            ]);
            // Kembalikan error 403 Forbidden
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari Transaksi
        $transaction = Transaction::where('code', $orderId)->first();

        if (! $transaction) {
            Log::warning('Transaction not found for order_id', ['order_id' => $orderId]);
            return response()->json(['message' => 'OK (transaction not found)']);
        }

        // 3. Mapping Status Midtrans -> Status Internal
        $paymentStatus = match ($status) {
            'settlement', 'capture' => 'success',
            'pending'               => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default                 => 'failed',
        };

        // 4. Pencatatan Ganda ke Tabel Payment (Audit Trail)
        Payment::create([
            'transaction_id' => $transaction->id,
            'provider'       => 'midtrans-qris',
            'status'         => $paymentStatus,
            'raw_callback'   => $request->all(),
        ]);

        // 5. Update Status Utama Transaksi
        if ($paymentStatus === 'success') {
            $transaction->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);
        } elseif ($paymentStatus === 'failed' && $transaction->status !== 'paid') {
            $transaction->update([
                'status' => 'cancelled',
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
