@extends('layouts.app')

@section('title', 'Template List')

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

    /* Header */
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
        align-items: center;
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
        font-family: 'Helvetica Neue', Arial, sans-serif;
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

    .btn-back {
        background: transparent;
        color: #8b8680;
        border-color: #c8c5c0;
    }

    .btn-back:hover {
        background: rgba(139, 134, 128, 0.08);
        border-color: #8b8680;
        transform: translateY(-1px);
    }

    /* Success Alert */
    .alert-success {
        background: #d4f4dd;
        border: 1px solid #a8e6c1;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 28px;
        font-size: 14px;
        color: #2d7a47;
        font-weight: 400;
        letter-spacing: 0.2px;
    }

    /* Table Card */
    .table-card {
        background: rgba(255, 255, 255, 0.7);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        overflow-x: auto;
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
        vertical-align: middle;
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

    /* Thumbnail */
    .template-thumb {
        height: 80px;
        width: auto;
        border-radius: 8px;
        object-fit: cover;
        display: block;
        border: 1px solid #e8e6e1;
    }

    /* Slot Info */
    .slot-count {
        font-size: 15px;
        font-weight: 400;
        color: #4a4845;
        margin-bottom: 6px;
    }

    .slot-detail {
        font-size: 12px;
        color: #8b8680;
        line-height: 1.7;
        letter-spacing: 0.2px;
    }

    .slot-empty {
        font-size: 13px;
        color: #b0ada8;
        font-style: italic;
    }

    /* Action Buttons */
    .action-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-edit {
        padding: 8px 20px;
        font-size: 13px;
        border-radius: 20px;
        background: transparent;
        color: #8b8680;
        border: 1.5px solid #c8c5c0;
        text-decoration: none;
        transition: all 0.2s ease;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 400;
        white-space: nowrap;
    }

    .btn-edit:hover {
        background: rgba(139, 134, 128, 0.08);
        border-color: #8b8680;
        transform: translateY(-1px);
    }

    .btn-delete {
        padding: 8px 20px;
        font-size: 13px;
        border-radius: 20px;
        background: transparent;
        color: #c44747;
        border: 1.5px solid #ffb3b3;
        cursor: pointer;
        transition: all 0.2s ease;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 400;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        white-space: nowrap;
    }

    .btn-delete:hover {
        background: #fff0f0;
        border-color: #c44747;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #8b8680;
        font-size: 15px;
        font-weight: 300;
    }

    /* ID badge */
    .id-badge {
        display: inline-block;
        background: #f5f3ef;
        border: 1px solid #e8e6e1;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 13px;
        color: #8b8680;
        font-weight: 400;
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

        .table-card {
            padding: 20px;
        }

        .data-table th,
        .data-table td {
            padding: 12px 8px;
            font-size: 13px;
        }

        .action-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- Header -->
        <div class="header-section">
            <h1 class="page-title">Pengaturan Template</h1>
            <div class="header-actions">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-back">← Kembali</a>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">+ Tambah Template</a>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Table Card -->
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Thumbnail</th>
                        <th>Slots</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr>
                        <td>
                            <span class="id-badge">#{{ $template->id }}</span>
                        </td>
                        <td>{{ $template->name }}</td>
                        <td>
                            @if($template->thumbnail_path)
                            <img src="{{ asset('storage/' . $template->thumbnail_path) }}" class="template-thumb" alt="{{ $template->name }}">
                            @else
                            <img src="{{ asset('storage/' . $transaction->template->file_path) }}" class="template-thumb" alt="{{ $template->name }}">
                            @endif
                        </td>
                        <td>
                            <div class="slot-count">
                                {{ $template->slot_count }} slot{{ $template->slot_count > 1 ? 's' : '' }}
                            </div>
                            @if($template->slots && count($template->slots) > 0)
                            <div class="slot-detail">
                                @foreach($template->slots as $i => $slot)
                                Slot {{ $i + 1 }}: x:{{ $slot['x'] }}, y:{{ $slot['y'] }}, {{ $slot['width'] }}×{{ $slot['height'] }}@if(!$loop->last)<br>@endif
                                @endforeach
                            </div>
                            @else
                            <span class="slot-empty">Tidak ada data slot</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.templates.edit', $template) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('admin.templates.destroy', $template) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus template ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            Belum ada template yang ditambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection