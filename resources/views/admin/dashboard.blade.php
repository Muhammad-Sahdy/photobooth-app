@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        overflow-x: hidden;
    }

    .dashboard-container {
        min-height: 100vh;
        background: linear-gradient(180deg, #f5f3ef 0%, #e8e6e1 100%);
        padding: 40px;
        position: relative;
        overflow: hidden;
    }

    /* Removed heavy texture overlay for better performance */

    /* Simple light overlay */
    .dashboard-container::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
        z-index: 2;
        pointer-events: none;
    }

    .dashboard-content {
        position: relative;
        z-index: 10;
        max-width: 1400px;
        margin: 0 auto;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 48px;
        font-weight: 300;
        color: #4a4845;
        letter-spacing: 0.5px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn {
        padding: 12px 28px;
        font-size: 14px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 400;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
        border: 1.5px solid;
    }

    .btn-primary {
        background: #8b8680;
        color: white;
        border-color: #8b8680;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .btn-primary:hover {
        background: #6f6c68;
        border-color: #6f6c68;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-logout {
        background: #d17171;
        color: white;
        border-color: #d17171;
    }

    .btn-logout:hover {
        background: #b85d5d;
        border-color: #b85d5d;
        transform: translateY(-2px);
    }

    /* Filter Card */
    .filter-card {
        background: rgba(255, 255, 255, 0.7);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        margin-bottom: 30px;
    }

    .filter-title {
        font-size: 18px;
        font-weight: 400;
        color: #4a4845;
        margin-bottom: 20px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-field label {
        font-size: 13px;
        color: #6b6865;
        font-weight: 400;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .form-field input,
    .form-field select {
        padding: 12px 16px;
        font-size: 15px;
        border: 1.5px solid #d4d2cd;
        border-radius: 12px;
        background: white;
        color: #4a4845;
        transition: all 0.3s ease;
    }

    .form-field input::placeholder {
        color: #a8a6a1;
    }

    .form-field select option {
        background: white;
        color: #4a4845;
    }

    .form-field input:focus,
    .form-field select:focus {
        outline: none;
        background: white;
        border-color: #8b8680;
        box-shadow: 0 0 0 3px rgba(139, 134, 128, 0.1);
    }

    .btn-filter {
        padding: 12px 32px;
        font-size: 14px;
        background: #8b8680;
        color: white;
        border: 1.5px solid #8b8680;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 400;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .btn-filter:hover {
        background: #6f6c68;
        border-color: #6f6c68;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* Summary Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: rgba(255, 255, 255, 0.7);
        padding: 28px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
    }

    .summary-label {
        font-size: 13px;
        color: #6b6865;
        font-weight: 400;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .summary-value {
        font-size: 36px;
        font-weight: 300;
        color: #4a4845;
        letter-spacing: -0.5px;
    }

    /* Table */
    .table-card {
        background: rgba(255, 255, 255, 0.7);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        overflow-x: auto;
    }

    .table-title {
        font-size: 18px;
        font-weight: 400;
        color: #4a4845;
        margin-bottom: 20px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .data-table thead {
        background: #f5f3ef;
    }

    .data-table th {
        padding: 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 500;
        color: #4a4845;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-bottom: 1px solid #e8e6e1;
    }

    .data-table td {
        padding: 16px;
        font-size: 15px;
        color: #5a5754;
        border-bottom: 1px solid #f0eee9;
        font-weight: 300;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
    }

    .data-table tbody tr:hover {
        background: #fafaf8;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-paid {
        background: #d4f4dd;
        color: #2d7a47;
        border: 1px solid #a8e6c1;
    }

    .status-pending {
        background: #fff4e6;
        color: #c87a1a;
        border: 1px solid #ffd699;
    }

    .status-cancelled,
    .status-expired {
        background: #ffe6e6;
        color: #c44747;
        border: 1px solid #ffb3b3;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #8b8680;
        font-size: 15px;
        font-weight: 300;
    }

    /* Pagination */
    .pagination-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* Custom Pagination Styling */
    .pagination-container nav {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .pagination-container .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
    }

    .pagination-container .page-item {
        display: inline-block;
    }

    .pagination-container .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 8px 12px;
        font-size: 14px;
        color: #5a5754;
        background: white;
        border: 1px solid #d4d2cd;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 400;
    }

    .pagination-container .page-link:hover {
        background: #f5f3ef;
        border-color: #8b8680;
        transform: translateY(-1px);
    }

    .pagination-container .page-item.active .page-link {
        background: #8b8680;
        border-color: #8b8680;
        color: white;
        font-weight: 500;
    }

    .pagination-container .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Hide default Laravel pagination text */
    .pagination-container .hidden,
    .pagination-container [hidden] {
        display: none !important;
    }

    /* Style the SVG icons in pagination */
    .pagination-container svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 20px;
        }

        .page-title {
            font-size: 32px;
        }

        .header-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .table-card {
            padding: 20px;
        }

        .data-table {
            font-size: 13px;
        }

        .data-table th,
        .data-table td {
            padding: 12px 8px;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-content">
        <!-- Header -->
        <div class="header-section">
            <h1 class="page-title">Dashboard Admin</h1>
            <div class="header-actions">
                <a href="{{ route('admin.templates.index') }}" class="btn btn-primary">
                    Pengaturan Template
                </a>
                <a href="{{ route('admin.filters.index') }}" class="btn btn-primary">
                    Pengaturan Filters
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout">Logout</button>
                </form>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <h3 class="filter-title">Filter Transaksi</h3>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-form">
                <div class="form-field">
                    <label>Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $from }}">
                </div>
                <div class="form-field">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $to }}">
                </div>
                <div class="form-field">
                    <label>Status</label>
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="form-field">
                    <button type="submit" class="btn-filter">Filter</button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Transaksi Paid</div>
                <div class="summary-value">{{ $summary->count ?? 0 }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Omzet</div>
                <div class="summary-value">Rp {{ number_format($summary->total ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <h3 class="table-title">Daftar Transaksi</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Customer</th>
                        <th>No HP</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $trx->code }}</td>
                        <td>{{ $trx->customer->name }}</td>
                        <td>{{ $trx->customer->phone }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="status-badge status-{{ $trx->status }}">
                                {{ $trx->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            Tidak ada data transaksi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-container">
                @if ($transactions->hasPages())
                <nav role="navigation" aria-label="Pagination Navigation">
                    <ul class="pagination">
                        {{-- Previous --}}
                        @if ($transactions->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->previousPageUrl() }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        </li>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                        $start = max($transactions->currentPage() - 2, 1);
                        $end = min($start + 4, $transactions->lastPage());
                        $start = max($end - 4, 1);
                        @endphp

                        @if($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->url(1) }}">1</a>
                        </li>
                        @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if ($page==$transactions->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $transactions->url($page) }}">{{ $page }}</a>
                            </li>
                            @endif
                            @endfor

                            @if($end < $transactions->lastPage())
                                @if($end < $transactions->lastPage() - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $transactions->url($transactions->lastPage()) }}">{{ $transactions->lastPage() }}</a>
                                    </li>
                                    @endif

                                    {{-- Next --}}
                                    @if ($transactions->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $transactions->nextPageUrl() }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </span>
                                    </li>
                                    @endif
                    </ul>
                </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection