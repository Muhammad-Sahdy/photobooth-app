<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TransactionAccess;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaction;
use ZipArchive;

class GalleryController extends Controller
{
    public function show(string $token)
    {
        $access = TransactionAccess::where('access_token', $token)->first();

        // Jika data sudah dihapus oleh Cleanup Command (maka $access akan null)
        if (!$access) {
            return view('gallery.expired'); // User tetap tahu kalau galerinya sudah dihapus/kadaluarsa
        }

        // Jika kebetulan command belum jalan tapi sudah expired secara waktu
        if ($access->expired_at->isPast()) {
            return view('gallery.expired');
        }

        $transaction = $access->transaction;

        return view('gallery.show', [
            'transaction' => $transaction,
            'photos'      => $transaction->photos,
            'finalPhotos' => $transaction->finalPhotos,
        ]);
    }

    public function downloadZip($code)
    {
        // 1. Cari data transaksi dan foto-fotonya
        $transaction = Transaction::where('code', $code)->firstOrFail();
        $photos = $transaction->photos; // Asumsi Anda memiliki relasi 'photos'

        if ($photos->isEmpty()) {
            return back()->with('error', 'Tidak ada foto untuk didownload.');
        }

        // 2. Siapkan file Zip
        $zip = new ZipArchive;
        $zipFileName = 'Atmoz_Raw_' . $transaction->code . '.zip';

        // Simpan zip sementara di folder public
        $zipFilePath = public_path('storage/' . $zipFileName);

        // 3. Masukkan file ke dalam Zip
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($photos as $index => $photo) {
                $filePath = public_path('storage/' . $photo->file_path);

                if (file_exists($filePath)) {
                    // Beri nama berurutan di dalam zip: RAW_1.jpg, RAW_2.jpg, dst
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    $zip->addFile($filePath, 'RAW_' . ($index + 1) . '.' . $extension);
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        // 4. Download file dan otomatis hapus zip setelah selesai didownload
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
