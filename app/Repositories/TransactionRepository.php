<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransactionRepository
{
    public function findByCode(string $code): ?Transaction
    {
        return Transaction::where('code', $code)->first();
    }

    public function findOrFailByCode(string $code): Transaction
    {
        return Transaction::where('code', $code)->firstOrFail();
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function updateStatus(Transaction $transaction, string $status): Transaction
    {
        $transaction->status = $status;

        if ($status === 'paid' && is_null($transaction->paid_at)) {
            $transaction->paid_at = now();
        }

        $transaction->save();

        return $transaction;
    }

    public function paginateForAdmin(
        string $from,
        string $to,
        ?string $status = null,
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = Transaction::with('customer')
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function summaryPaid(string $from, string $to): ?object
    {
        return Transaction::where('status', 'paid')
            ->whereBetween('paid_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->selectRaw('COUNT(*) as count, SUM(amount) as total')
            ->first();
    }

    public function getForCleanup(\DateTimeInterface $before): Collection
    {
        return Transaction::whereHas('access', function ($q) use ($before) {
                $q->where('expired_at', '<', $before);
            })
            ->with(['photos', 'finalPhotos', 'access'])
            ->get();
    }
}
