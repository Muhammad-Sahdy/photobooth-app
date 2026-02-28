<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldPhotos extends Command
{
    /**
     * Nama dan signature command.
     */
    protected $signature = 'photobooth:cleanup-old-photos';

    /**
     * Deskripsi command.
     */
    protected $description = 'Hapus foto raw dan final yang sudah lewat masa akses (lebih dari 2 hari)';

    /**
     * Jalankan command.
     */
    public function handle(): int
    {
        $this->info('Mulai cleanup foto lama...');

        $expiredTransactions = Transaction::whereHas('access', function ($q) {
            $q->where('expired_at', '<', now());
        })
            ->with(['photos', 'finalPhotos', 'access'])
            ->get();

        $disk = 'public';

        foreach ($expiredTransactions as $transaction) {
            // Pastikan $transaction adalah instance dari Model, jika bukan, skip atau debug
            if (!$transaction instanceof Transaction) {
                $this->error("Data bukan merupakan Model Transaction.");
                continue;
            }

            $code = $transaction->code;
            $this->line("Cleaning storage for: {$code}");

            // 1. HAPUS FOLDER FISIK
            $directoryPath = "photos/{$code}";
            if (Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->deleteDirectory($directoryPath);
                $this->info("Folder storage dihapus.");
            }

            // 2. HAPUS RECORD DATABASE
            // Menggunakan delete() pada query builder relasi
            $transaction->photos()->delete();      // Ini menghapus semua baris di tabel 'photos'
            $transaction->finalPhotos()->delete(); // Ini menghapus semua baris di tabel 'final_photos'

            // 3. HAPUS ACCESS TOKEN
            if ($transaction->access) {
                $transaction->access->delete();
            }

            $this->line("Data database {$code} dibersihkan.");
        }

        $this->info('Cleanup selesai.');
        return self::SUCCESS;
    }
}
