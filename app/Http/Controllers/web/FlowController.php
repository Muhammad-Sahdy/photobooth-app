<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Template;
use App\Models\FinalPhoto;
use App\Models\TransactionAccess;

class FlowController extends Controller
{
    public function template(string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();
        if ($transaction->status !== 'paid') {
            return redirect()->route('registration.form')->with('error', 'Belum dibayar');
        }

        $templates = Template::all();

        return view('flow.template', compact('transaction', 'templates'));
    }

    public function capture(string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();

        if (request()->filled('template_id')) {
            $transaction->update([
                'template_id' => request('template_id'),
            ]);
        }

        return view('flow.capture', compact('transaction'));
    }

    public function selectPhotos(string $code)
    {
        $transaction = Transaction::with('photos', 'template')
            ->where('code', $code)
            ->firstOrFail();

        if (! $transaction->template) {
            return redirect()->route('flow.template', $transaction->code)
                ->with('error', 'Template belum dipilih.');
        }

        $slots = $transaction->template->slots;

        return view('flow.select-photos', compact('transaction', 'slots'));
    }

    public function compose(string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();
        // bisa ambil FinalPhoto terakhir untuk kode ini
        $final = FinalPhoto::where('transaction_id', $transaction->id)->latest()->first();

        return view('flow.compose', compact('transaction', 'final'));
    }

    public function filter(string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();
        $final = FinalPhoto::where('transaction_id', $transaction->id)->latest()->first();

        return view('flow.filter', compact('transaction', 'final'));
    }
    // Di FlowController.php
    public function barcode(string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();

        // Buat token akses jika belum ada di database
        TransactionAccess::firstOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'access_token' => \Illuminate\Support\Str::uuid(),
                'expired_at'   => now()->addDays(2),
            ]
        );

        // Muat relasi access agar token tersedia di Blade
        $transaction->load('access');
        $finalPhoto = FinalPhoto::where('transaction_id', $transaction->id)->latest()->first();

        return view('flow.barcode', compact('transaction', 'finalPhoto'));
    }
}
