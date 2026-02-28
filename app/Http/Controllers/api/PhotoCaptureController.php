<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoSelectRequest;
use App\Http\Requests\TemplateSelectRequest;
use App\Models\FinalPhoto;
use App\Models\Photo;
use App\Models\Template;
use App\Models\Transaction;
use App\Services\Photos\FilterService;
use App\Services\Photos\PhotoProcessingService;
use Illuminate\Http\Request;
use App\Models\Filter;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

class PhotoCaptureController extends Controller
{
    // dipanggil tiap 5 detik untuk simpan 1 jepretan
    public function capture(Request $request, string $code)
    {
        $transaction = Transaction::where('code', $code)->firstOrFail();

        // diasumsikan foto dikirim dari browser sebagai file 'image'
        $request->validate([
            'image' => 'required|image|max:4096',
        ]);

        $path = $request->file('image')
            ->store("photos/{$transaction->code}/raw", 'public');

        $photo = Photo::create([
            'transaction_id' => $transaction->id,
            'file_path'      => $path,
            'taken_at'       => now(),
        ]);

        return response()->json([
            'id'       => $photo->id,
            'file_url' => asset("storage/{$photo->file_path}"),
        ]);
    }

    public function choosePhotos(PhotoSelectRequest $request, string $code, PhotoProcessingService $photoProcessingService)
    {
        $transaction = Transaction::with('photos', 'template')->where('code', $code)->firstOrFail();

        $photoIds = $request->input('photo_ids'); // Contoh: [10, 5] jika user klik ID 10 lalu 5
        $requiredCount = $transaction->template->slot_count;

        // Gunakan orderByRaw FIELD untuk menjaga urutan sesuai array $photoIds
        $photos = $transaction->photos()
            ->whereIn('id', $photoIds)
            ->orderByRaw("FIELD(id, " . implode(',', $photoIds) . ")")
            ->get();

        if ($photos->count() !== $requiredCount) {
            return response()->json(['message' => "Harus memilih tepat {$requiredCount} foto."], 422);
        }

        $template = $transaction->template;

        // Sekarang $photos sudah terurut sesuai pilihan user
        $finalPhoto = $photoProcessingService->composeFinal(
            $transaction,
            $template,
            $photos
        );

        return response()->json([
            'id'       => $finalPhoto->id,
            'file_url' => asset("storage/{$finalPhoto->file_path}"),
        ]);
    }
    public function applyFilter(Request $request, string $code, FilterService $filterService)
    {
        $request->validate([
            'final_photo_id' => 'required|exists:final_photos,id',
            'filter_type'    => 'required|string',
        ]);

        $transaction = Transaction::where('code', $code)->firstOrFail();
        $finalPhoto = FinalPhoto::where('id', $request->final_photo_id)
            ->where('transaction_id', $transaction->id)
            ->firstOrFail();

        $finalPhoto = $filterService->applyFilter($finalPhoto, $request->filter_type);

        return response()->json([
            'success'  => true,
            'filter'   => $finalPhoto->filter_type,
            'file_url' => asset("storage/{$finalPhoto->file_path}") . '?t=' . time(),
        ]);
    }

    public function getPreview($photoId, $filterSlug)
    {
        try {
            $photo = Photo::findOrFail($photoId);
            $filterRecord = Filter::where('slug', $filterSlug)->first();

            $fullPath = storage_path('app/public/' . $photo->file_path);

            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'File tidak ditemukan'], 404);
            }

            $image = Image::read($fullPath);

            // KECILKAN UKURAN (Agar loading super cepat)
            $image->scale(height: 300);

            if ($filterRecord && $filterSlug !== 'original') {
                $params = $filterRecord->parameters;

                // --- LOGIKA THE GOLDEN 8 (Sama persis dengan FilterService) ---

                // 1. Cahaya & Kontras
                if (!empty($params['brightness'])) $image->brightness((int)$params['brightness']);
                if (!empty($params['contrast'])) $image->contrast((int)$params['contrast']);

                // 2. Eksposur (Gamma)
                $gamma = (float)($params['gamma'] ?? 1.0);
                if (!empty($params['shadows'])) $gamma -= ($params['shadows'] / 150);
                if (!empty($params['highlights'])) $gamma += ($params['highlights'] / 150);
                if ($gamma != 1.0) $image->gamma($gamma);

                // 3. Warna (Warmth & Tint)
                if (!empty($params['greyscale'])) $image->greyscale();

                $red = 0;
                $green = 0;
                $blue = 0;

                if (!empty($params['warmth'])) {
                    $red += (int)$params['warmth'];
                    $blue -= (int)$params['warmth'];
                }
                if (!empty($params['tint'])) {
                    $green += (int)$params['tint'];
                }

                // Kompatibilitas mundur
                if (!empty($params['colorize']) && is_array($params['colorize'])) {
                    $red   += ($params['colorize'][0] ?? 0);
                    $green += ($params['colorize'][1] ?? 0);
                    $blue  += ($params['colorize'][2] ?? 0);
                }

                $red   = max(-100, min(100, $red));
                $green = max(-100, min(100, $green));
                $blue  = max(-100, min(100, $blue));

                if ($red != 0 || $green != 0 || $blue != 0) {
                    $image->colorize($red, $green, $blue);
                }

                // 4. Detail
                if (!empty($params['sharpen']) && (int)$params['sharpen'] > 0) {
                    $image->sharpen((int)$params['sharpen']);
                }
            }

            // UBAH KE JPEG
            $encoded = $image->toPng();

            if (ob_get_contents()) ob_clean();

            return response($encoded)->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            Log::error("Filter Preview Error: " . $e->getMessage());
            return response()->file($fullPath);
        }
    }
}
