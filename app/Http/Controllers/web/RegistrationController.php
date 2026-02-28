<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\Payments\QrisPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function showForm()
    {
        return view('registration.form');
    }

    public function store(
        RegistrationRequest $request,
        QrisPaymentService $qrisPaymentService
    ) {
        // 1. Simpan customer
        $customer = Customer::create([
            'name'  => $request->input('name'),
            'phone' => $request->input('phone'),
        ]);

        // 2. Buat transaksi
        $code = strtoupper(Str::random(8));
        $transaction = Transaction::create([
            'customer_id'       => $customer->id,
            'code'              => $code,
            'amount'            => config('photobooth.price', 35000),
            'status'            => 'pending',
            'payment_reference' => null,
        ]);

        // 3. Panggil service QRIS
        $paymentData = $qrisPaymentService->createPayment($transaction);

        $transaction->update([
            'payment_reference' => $paymentData['payment_reference'] ?? null,
        ]);

        // 🔥 PERUBAHAN DI SINI: Kembalikan JSON, bukan View
        return response()->json([
            'success'          => true,
            'transaction_code' => $transaction->code,
            'qr_url'           => $paymentData['qr_url'] ?? null,
            'amount'           => number_format($transaction->amount, 0, ',', '.'),
            'name'             => $customer->name,
            'phone'            => $customer->phone,
        ]);
    }

    // endpoint untuk ajax polling status
    public function checkStatus(Request $request, string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();

        return response()->json([
            'status' => $transaction->status,
        ]);
    }
}
