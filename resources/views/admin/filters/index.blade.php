@extends('layouts.app')

@section('title', 'Manage Filters')

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
        max-width: 900px;
        /* Dipersempit sedikit karena input lebih sedikit */
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
        font-size: 40px;
        font-weight: 300;
        color: #4a4845;
        letter-spacing: 0.5px;
    }

    .page-subtitle {
        font-size: 14px;
        color: #8b8680;
        font-weight: 400;
        letter-spacing: 0.5px;
        margin-top: 4px;
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

    /* Alert */
    .alert-success {
        background: #d4f4dd;
        border: 1px solid #a8e6c1;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 28px;
        font-size: 14px;
        color: #2d7a47;
    }

    /* Form Card */
    .form-card,
    .table-card {
        background: rgba(255, 255, 255, 0.7);
        padding: 36px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        margin-bottom: 24px;
    }

    .card-section-title {
        font-size: 13px;
        font-weight: 500;
        color: #4a4845;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e8e6e1;
    }

    /* Form Grid Baru (2 Kolom yang Rapi) */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-field.span-2 {
        grid-column: span 2;
    }

    .form-field label {
        font-size: 13px;
        color: #6b6865;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .form-field input[type="text"],
    .form-field input[type="number"] {
        padding: 13px 16px;
        font-size: 15px;
        border: 1.5px solid #d4d2cd;
        border-radius: 12px;
        background: white;
        color: #4a4845;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-field input:focus {
        outline: none;
        border-color: #8b8680;
        box-shadow: 0 0 0 3px rgba(139, 134, 128, 0.1);
    }

    .input-hint {
        font-size: 11px;
        color: #a8a6a1;
        font-style: italic;
    }

    .section-divider {
        grid-column: span 2;
        height: 1px;
        background: #e8e6e1;
        margin: 10px 0;
    }

    /* Checkbox */
    .checkbox-field {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: white;
        border: 1.5px solid #d4d2cd;
        border-radius: 12px;
        cursor: pointer;
        grid-column: span 2;
        transition: all 0.3s ease;
    }

    .checkbox-field:hover {
        border-color: #8b8680;
        background: #fafaf8;
    }

    .checkbox-field input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #8b8680;
        cursor: pointer;
    }

    .btn-submit {
        width: 100%;
        padding: 14px 40px;
        font-size: 14px;
        background: #8b8680;
        color: white;
        border: 1.5px solid #8b8680;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 15px;
    }

    .btn-submit:hover {
        background: #6f6c68;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .data-table th {
        background: #f5f3ef;
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
        font-size: 14px;
        color: #5a5754;
        border-bottom: 1px solid #f0eee9;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #fafaf8;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .filter-name {
        font-weight: 500;
        color: #4a4845;
    }

    .filter-params {
        font-size: 12px;
        color: #8b8680;
        line-height: 1.6;
    }

    .btn-delete {
        padding: 8px 16px;
        font-size: 12px;
        border-radius: 20px;
        background: transparent;
        color: #c44747;
        border: 1.5px solid #ffb3b3;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
    }

    .btn-delete:hover {
        background: #fff0f0;
        border-color: #c44747;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #8b8680;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-field.span-2,
        .section-divider,
        .checkbox-field {
            grid-column: span 1;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-content">

        <div class="header-section">
            <div>
                <h1 class="page-title">Manage Filters</h1>
                <p class="page-subtitle">Konfigurasi The Golden 8 (Cepat, Ringan, Profesional)</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-back">← Dashboard</a>
        </div>

        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="form-card">
            <div class="card-section-title">Editor Filter Esensial</div>

            <form action="{{ route('admin.filters.store') }}" method="POST">
                @csrf
                <div class="form-grid">

                    {{-- Nama Filter --}}
                    <div class="form-field span-2">
                        <label>Nama Filter</label>
                        <input type="text" name="name" placeholder="cth. Aesthetic Retro, Midnight Seoul..." required>
                    </div>

                    <div class="section-divider"></div>

                    {{-- CAHAYA & KONTRAS --}}
                    <div class="form-field">
                        <label>Brightness (-100 s/d 100)</label>
                        <input type="number" name="brightness" value="0" min="-100" max="100">
                        <span class="input-hint">Kecerahan dasar foto</span>
                    </div>

                    <div class="form-field">
                        <label>Contrast (-100 s/d 100)</label>
                        <input type="number" name="contrast" value="0" min="-100" max="100">
                        <span class="input-hint">Ketajaman perbedaan terang/gelap</span>
                    </div>

                    <div class="form-field">
                        <label>Highlights (-100 s/d 100)</label>
                        <input type="number" name="highlights" value="0" min="-100" max="100">
                        <span class="input-hint">(+) untuk meredupkan area silau</span>
                    </div>

                    <div class="form-field">
                        <label>Shadows (-100 s/d 100)</label>
                        <input type="number" name="shadows" value="0" min="-100" max="100">
                        <span class="input-hint">(+) untuk menerangkan area bayangan/gelap</span>
                    </div>

                    <div class="form-field span-2">
                        <label>Gamma (0.1 s/d 9.9)</label>
                        <input type="number" name="gamma" value="1.00" step="0.01" min="0.1" max="9.9">
                        <span class="input-hint">Kecerahan Mid-tone. Default: 1.00. Kurangi (cth: 0.95) untuk lebih gelap/moody.</span>
                    </div>

                    <div class="section-divider"></div>

                    {{-- WARNA & DETAIL --}}
                    <div class="form-field">
                        <label>Warmth (-100 s/d 100)</label>
                        <input type="number" name="warmth" value="0" min="-100" max="100">
                        <span class="input-hint">(+) Kuning/Hangat | (-) Biru/Dingin</span>
                    </div>

                    <div class="form-field">
                        <label>Tint (-100 s/d 100)</label>
                        <input type="number" name="tint" value="0" min="-100" max="100">
                        <span class="input-hint">(+) Magenta/Pink | (-) Hijau</span>
                    </div>

                    <div class="form-field span-2">
                        <label>Sharpen (0 s/d 100)</label>
                        <input type="number" name="sharpen" value="0" min="0" max="100">
                        <span class="input-hint">Menambah ketajaman tekstur (Rekomendasi: 10-20)</span>
                    </div>

                    <div class="checkbox-field">
                        <input type="checkbox" name="greyscale" id="gs_check">
                        <label for="gs_check">Aktifkan Mode Hitam & Putih (Greyscale)</label>
                    </div>

                </div>

                <button type="submit" class="btn-submit">Simpan Konfigurasi</button>
            </form>
        </div>

        {{-- DAFTAR FILTER --}}
        <div class="table-card">
            <div class="card-section-title">Daftar Filter Aktif</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Filter</th>
                        <th>Detail Konfigurasi</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filters as $filter)
                    <tr>
                        <td class="filter-name">{{ $filter->name }}</td>
                        <td>
                            <div class="filter-params">
                                <strong>Light:</strong>
                                Br:{{ $filter->parameters['brightness'] ?? 0 }} |
                                Co:{{ $filter->parameters['contrast'] ?? 0 }} |
                                Hi:{{ $filter->parameters['highlights'] ?? 0 }} |
                                Sh:{{ $filter->parameters['shadows'] ?? 0 }} |
                                Ga:{{ $filter->parameters['gamma'] ?? 1.0 }}<br>

                                <strong>Color:</strong>
                                Warm:{{ $filter->parameters['warmth'] ?? 0 }} |
                                Tint:{{ $filter->parameters['tint'] ?? 0 }} |
                                B&W:{{ !empty($filter->parameters['greyscale']) ? 'Ya' : 'Tidak' }}<br>

                                <strong>Detail:</strong>
                                Sharp:{{ $filter->parameters['sharpen'] ?? 0 }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('admin.filters.destroy', $filter->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus filter ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="empty-state">Belum ada filter yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    // Memastikan input kosong kembali ke nilai default (0 atau 1.00 untuk gamma)
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value === "") {
                this.value = (this.name === "gamma") ? "1.00" : "0";
            }
        });
    });
</script>
@endsection