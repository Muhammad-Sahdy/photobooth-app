<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'thumbnail_path',
        'slots',
        'slot_count',
    ];
    protected $casts = [
        'slots' => 'array',
        'slot_count' => 'integer',
    ];

    // ✅ FORCE JSON decode - fallback untuk data lama
    public function getSlotsAttribute($value)
    {
        if (is_null($value)) return [];

        if (is_array($value)) return $value;

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    // ✅ Pastikan slot_count konsisten
    public function getSlotCountAttribute($value)
    {
        $count = (int) $value;
        $slots = $this->getSlotsAttribute($this->getRawOriginal('slots'));
        return max($count, count($slots)); // ambil yang terbesar
    }

    public function finalPhotos(): HasMany
    {
        return $this->hasMany(FinalPhoto::class);
    }
}
