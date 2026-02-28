<?php

namespace App\Services\Photos;

use App\Models\FinalPhoto;
use App\Models\Photo;
use App\Models\Template;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class PhotoProcessingService
{
    /**
     * Gabungkan N foto (2-4) ke dalam template sesuai slot_count.
     *
     * @param Transaction $transaction
     * @param Template $template
     * @param Collection<int, Photo> $photos (jumlah sesuai template->slot_count)
     * @return FinalPhoto
     */
    public function composeFinal(
        Transaction $transaction,
        Template $template,
        Collection $photos
    ): FinalPhoto {
        $requiredSlots = $template->slot_count;
        $slots = $template->slots ?? [];

        // Validasi jumlah foto
        if ($photos->count() !== $requiredSlots) {
            throw new \InvalidArgumentException(
                "Jumlah foto ({$photos->count()}) tidak sesuai slot ({$requiredSlots})"
            );
        }

        // Load template
        $templatePath = Storage::disk('public')->path($template->file_path);
        $templateImage = Image::read($templatePath);

        // Proses setiap foto
        foreach ($photos->values() as $index => $photo) {
            if (!isset($slots[$index])) continue;

            $slot = $slots[$index];
            $photoPath = Storage::disk('public')->path($photo->file_path);
            $photoImage = Image::read($photoPath);

            $photoImage->cover($slot['width'], $slot['height']);
            $templateImage->place($photoImage, 'top-left', $slot['x'], $slot['y']);
        }

        // Generate filenames
        $baseDir = "photos/{$transaction->code}/final/";
        Storage::disk('public')->makeDirectory($baseDir);

        $templateName = $baseDir . Str::uuid() . '_template.png';
        $originalName = $baseDir . Str::uuid() . '_original.png';
        $activeName   = $baseDir . Str::uuid() . '.png';

        $encodedImage = $templateImage->encodeByExtension('png', quality: 90);
        Storage::disk('public')->put($templateName, $encodedImage);
        Storage::disk('public')->put($originalName, $encodedImage);
        Storage::disk('public')->copy($originalName, $activeName);

        // ✅ HANYA save yang esensial
        // Di dalam method composeFinal
        return FinalPhoto::create([
            'transaction_id'     => $transaction->id,
            'template_id'        => $template->id,
            'file_path'          => $activeName,
            'original_file_path' => $originalName,
            'template_file_path' => $templateName,
            'filter_type'        => 'original',
            'slot_count'         => $requiredSlots,
            // Simpan photo_ids sesuai urutan koleksi $photos yang sudah di-sort di controller
            'photo_ids'          => $photos->pluck('id')->all(),
        ]);
    }
}
