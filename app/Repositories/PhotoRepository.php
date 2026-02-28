<?php

namespace App\Repositories;

use App\Models\Photo;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class PhotoRepository
{
    public function create(Transaction $transaction, string $filePath): Photo
    {
        return Photo::create([
            'transaction_id' => $transaction->id,
            'file_path'      => $filePath,
            'taken_at'       => now(),
        ]);
    }

    public function getByTransaction(Transaction $transaction): Collection
    {
        return Photo::where('transaction_id', $transaction->id)
            ->orderBy('taken_at')
            ->get();
    }

    public function findSelected(array $photoIds, Transaction $transaction): Collection
    {
        return Photo::whereIn('id', $photoIds)
            ->where('transaction_id', $transaction->id)
            ->get();
    }
}
