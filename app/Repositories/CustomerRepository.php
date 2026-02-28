<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function create(array $data): Customer
    {
        return Customer::create([
            'name'  => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);
    }

    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }
}
