<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $fillable = ['name', 'slug', 'parameters', 'is_active'];

    // WAJIB ADA: Agar JSON di database otomatis jadi array di PHP
    protected $casts = [
        'parameters' => 'array',
    ];
}
