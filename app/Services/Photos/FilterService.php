<?php

namespace App\Services\Photos;

use App\Models\FinalPhoto;
use App\Models\Filter;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class FilterService
{
    public function applyFilter(FinalPhoto $finalPhoto, string $filterSlug): FinalPhoto
    {
        $originalPath = $finalPhoto->original_file_path ?: $finalPhoto->file_path;
        $activeFull   = Storage::disk('public')->path($finalPhoto->file_path);
        $originalFull = Storage::disk('public')->path($originalPath);

        $filterRecord = Filter::where('slug', $filterSlug)->first();

        // JIKA ORIGINAL: Copy file asli
        if ($filterSlug === 'original' || !$filterRecord) {
            if (file_exists($originalFull)) {
                copy($originalFull, $activeFull);
            }
            $finalPhoto->update(['filter_type' => 'original']);
            return $finalPhoto->fresh();
        }

        // PROSES RE-COMPOSE
        $transaction = $finalPhoto->transaction;
        $template    = $transaction->template;

        $templateFull = $finalPhoto->template_file_path
            ? Storage::disk('public')->path($finalPhoto->template_file_path)
            : $originalFull;

        $templateImage = Image::read($templateFull);
        $slots = $template->slots;
        $photoIds = $finalPhoto->photo_ids ?? [];

        $photos = $transaction->photos()->whereIn('id', $photoIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $photoIds) . ')')
            ->get();

        foreach ($slots as $index => $slot) {
            if (!isset($photos[$index])) continue;

            $photoPath = Storage::disk('public')->path($photos[$index]->file_path);
            if (!file_exists($photoPath)) continue;

            $croppedPhoto = Image::read($photoPath);
            $croppedPhoto->cover($slot['width'], $slot['height']);

            // Terapkan filter Efisien ke SETIAP foto SEBELUM ditempel
            $this->applyDynamicFilter($croppedPhoto, $filterRecord->parameters);

            $templateImage->place($croppedPhoto, 'top-left', $slot['x'] ?? 0, $slot['y'] ?? 0);
            unset($croppedPhoto);
        }

        // Simpan hasil akhir
        $templateImage->save($activeFull, quality: 95);
        unset($templateImage);

        $finalPhoto->filter_type = $filterSlug;
        $finalPhoto->save();

        return $finalPhoto->fresh();
    }

    // --- LOGIKA FILTER: "THE GOLDEN 8" (Sangat Ringan) ---
    private function applyDynamicFilter($image, $params)
    {
        if (!is_array($params)) return;

        // 1. KELOMPOK CAHAYA (Brightness & Contrast)
        if (!empty($params['brightness'])) {
            $image->brightness((int)$params['brightness']);
        }
        if (!empty($params['contrast'])) {
            $image->contrast((int)$params['contrast']);
        }

        // 2. KELOMPOK EKSPOSUR (Gabungan Gamma, Shadows, Highlights)
        $gamma = (float)($params['gamma'] ?? 1.0);

        if (!empty($params['shadows'])) {
            $gamma -= ($params['shadows'] / 150); // Angka positif menerangkan bayangan
        }
        if (!empty($params['highlights'])) {
            $gamma += ($params['highlights'] / 150); // Angka positif meredupkan cahaya terang
        }

        // Terapkan gamma hanya 1 kali jika nilainya berubah dari default
        if ($gamma != 1.0) {
            $image->gamma($gamma);
        }

        // 3. KELOMPOK WARNA & MOOD
        if (!empty($params['greyscale'])) {
            $image->greyscale();
        }

        // Kalkulasi Cerdas Warna (Menggunakan Warmth & Tint)
        $red = 0;
        $green = 0;
        $blue = 0;

        // Warmth: Mengatur Hangat (Kuning/Merah) vs Dingin (Biru)
        if (!empty($params['warmth'])) {
            $red += (int)$params['warmth'];
            $blue -= (int)$params['warmth'];
        }

        // Tint: Mengatur rona Magenta (Pink) vs Hijau
        if (!empty($params['tint'])) {
            $green += (int)$params['tint'];
        }

        // Kompatibilitas Mundur: Jika masih ada parameter lama di DB (RGB Manual)
        if (!empty($params['colorize']) && is_array($params['colorize'])) {
            $red   += ($params['colorize'][0] ?? 0);
            $green += ($params['colorize'][1] ?? 0);
            $blue  += ($params['colorize'][2] ?? 0);
        }

        // Pastikan nilai RGB tidak melebihi batas -100 hingga 100
        $red   = max(-100, min(100, $red));
        $green = max(-100, min(100, $green));
        $blue  = max(-100, min(100, $blue));

        // Terapkan warna hanya 1 kali jika ada perubahan
        if ($red != 0 || $green != 0 || $blue != 0) {
            $image->colorize($red, $green, $blue);
        }

        // 4. KELOMPOK DETAIL
        if (!empty($params['sharpen']) && (int)$params['sharpen'] > 0) {
            $image->sharpen((int)$params['sharpen']);
        }
    }
}
