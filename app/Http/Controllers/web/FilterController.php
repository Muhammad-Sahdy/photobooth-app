<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\FinalPhoto;
use App\Services\Photos\FilterService;
use Illuminate\Http\Request;
use App\Models\Filter;

class FilterController extends Controller
{
    protected $filterService;

    /**
     * Menggunakan Dependency Injection untuk FilterService
     */
    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * Menampilkan halaman pilihan filter ke user
     */
    /**
     * Menampilkan halaman pilihan filter ke user (Versi Optimalisasi)
     */
    public function show($code)
    {
        // 1. EAGER LOADING: Tarik relasi 'template' sekalian agar tidak terjadi N+1 Query di Blade
        $transaction = Transaction::with(['template'])->where('code', $code)->firstOrFail();

        $final = FinalPhoto::where('transaction_id', $transaction->id)->first();

        if (!$final) {
            return redirect()->route('flow.compose', $code)
                ->with('error', 'Silakan gabungkan foto terlebih dahulu.');
        }

        // 2. PASSING DATA: Ambil daftar filter di Controller, bukan di dalam file Blade
        $filters = Filter::all(); // Tambahkan ->where('is_active', true) jika ada kolom tersebut

        return view('flow.filter', compact('transaction', 'final', 'filters'));
    }

    /**
     * API AJAX untuk menerapkan filter secara dinamis
     */
    public function apply(Request $request, $code)
    {
        // Validasi input dari JavaScript
        $request->validate([
            'final_photo_id' => 'required|exists:final_photos,id',
            'filter_type'    => 'required|string', // Berisi slug seperti 'bw' atau 'vintage'
        ]);

        try {
            $finalPhoto = FinalPhoto::findOrFail($request->final_photo_id);

            /**
             * Memanggil FilterService untuk memproses ulang gambar.
             * Service akan membaca parameter dari database berdasarkan slug filter.
             */
            $updatedPhoto = $this->filterService->applyFilter($finalPhoto, $request->filter_type);

            return response()->json([
                'success'  => true,
                /**
                 * PENTING: Menambahkan timestamp (?t=) untuk mematikan cache browser.
                 * Ini memastikan gambar di preview template langsung berubah.
                 */
                'file_url' => asset('storage/' . $updatedPhoto->file_path) . '?t=' . time(),
                'message'  => 'Filter ' . $request->filter_type . ' berhasil diterapkan.'
            ]);
        } catch (\Exception $e) {
            // Memberikan pesan error yang jelas jika proses image processing gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses gambar: ' . $e->getMessage()
            ], 500);
        }
    }
}
