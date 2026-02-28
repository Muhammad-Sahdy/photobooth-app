<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'code',
        'amount',
        'status',
        'payment_reference',
        'paid_at',
        'template_id',   // <— tambahkan ini
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function finalPhotos(): HasMany
    {
        return $this->hasMany(FinalPhoto::class);
    }

    public function access(): HasOne
    {
        return $this->hasOne(TransactionAccess::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
