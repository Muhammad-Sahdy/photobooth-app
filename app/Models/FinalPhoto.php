<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalPhoto extends Model
{
    protected $fillable = [
        'transaction_id',
        'template_id',
        'file_path',
        'original_file_path',
        'template_file_path',
        'filter_type',
        'photo_ids',
    ];

    protected $casts = [
        'photo_ids' => 'array',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
