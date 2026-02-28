@extends('layouts.app')

@section('title', 'Edit Template')

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
        max-width: 860px;
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
        font-family: 'Helvetica Neue', Arial, sans-serif;
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

    /* Error Alert */
    .alert-error {
        background: #fff0f0;
        border: 1px solid #ffb3b3;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 28px;
        color: #c44747;
        font-size: 14px;
    }

    .alert-error ul {
        margin: 0;
        padding-left: 18px;
    }

    .alert-error li {
        margin-bottom: 4px;
    }

    /* Form Card */
    .form-card {
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

    /* Form Fields */
    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 24px;
    }

    .form-field:last-child {
        margin-bottom: 0;
    }

    .form-field label {
        font-size: 13px;
        color: #6b6865;
        font-weight: 400;
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
        font-family: 'Helvetica Neue', Arial, sans-serif;
        width: 100%;
    }

    .form-field input[type="text"]:focus,
    .form-field input[type="number"]:focus {
        outline: none;
        border-color: #8b8680;
        box-shadow: 0 0 0 3px rgba(139, 134, 128, 0.1);
    }

    /* File Upload */
    .file-upload-area {
        border: 1.5px dashed #c8c5c0;
        border-radius: 12px;
        padding: 20px;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #8b8680;
        background: #fafaf8;
    }

    .file-upload-area input[type="file"] {
        display: block;
        font-size: 14px;
        color: #5a5754;
        width: 100%;
        cursor: pointer;
    }

    .file-hint {
        font-size: 12px;
        color: #a8a6a1;
        margin-top: 8px;
        letter-spacing: 0.3px;
    }

    /* Image Preview */
    .image-preview {
        margin-top: 14px;
    }

    .image-preview-label {
        font-size: 12px;
        color: #8b8680;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .preview-img-large {
        max-height: 200px;
        max-width: 100%;
        border-radius: 10px;
        border: 1px solid #e8e6e1;
        display: block;
        object-fit: cover;
    }

    .preview-img-thumb {
        height: 80px;
        width: auto;
        border-radius: 8px;
        border: 1px solid #e8e6e1;
        display: block;
        object-fit: cover;
    }

    .file-path-text {
        font-size: 12px;
        color: #a8a6a1;
        font-family: monospace;
        background: #f5f3ef;
        padding: 6px 10px;
        border-radius: 6px;
        display: inline-block;
        margin-top: 8px;
        word-break: break-all;
    }

    /* Slot count input container */
    .slot-count-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .slot-count-wrapper input {
        width: 100px !important;
    }

    .slot-count-hint {
        font-size: 13px;
        color: #8b8680;
    }

    /* Slots Container */
    #slots-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-top: 4px;
    }

    .slot-fieldset {
        background: white;
        border: 1.5px solid #e8e6e1;
        border-radius: 14px;
        padding: 20px 24px;
        transition: all 0.2s ease;
    }

    .slot-fieldset:hover {
        border-color: #c8c5c0;
    }

    .slot-legend {
        font-size: 13px;
        font-weight: 500;
        color: #6b6865;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .slot-badge {
        background: #f5f3ef;
        border: 1px solid #e8e6e1;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 12px;
        color: #8b8680;
    }

    .slot-inputs {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .slot-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .slot-input-group label {
        font-size: 12px;
        color: #8b8680;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .slot-input-group input {
        padding: 10px 12px;
        font-size: 14px;
        border: 1.5px solid #d4d2cd;
        border-radius: 10px;
        background: white;
        color: #4a4845;
        transition: all 0.3s ease;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        width: 100%;
        text-align: center;
    }

    .slot-input-group input:focus {
        outline: none;
        border-color: #8b8680;
        box-shadow: 0 0 0 3px rgba(139, 134, 128, 0.1);
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 8px;
    }

    .btn-submit {
        padding: 14px 40px;
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
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    .btn-submit:hover {
        background: #6f6c68;
        border-color: #6f6c68;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-cancel {
        padding: 14px 28px;
        font-size: 14px;
        background: transparent;
        color: #8b8680;
        border: 1.5px solid #c8c5c0;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 400;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    .btn-cancel:hover {
        background: rgba(139, 134, 128, 0.08);
        border-color: #8b8680;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 20px;
        }

        .page-title {
            font-size: 28px;
        }

        .header-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .form-card {
            padding: 24px 20px;
        }

        .slot-inputs {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- Header -->
        <div class="header-section">
            <div>
                <h1 class="page-title">Edit Template</h1>
                <p class="page-subtitle">#{{ $template->id }} — {{ $template->name }}</p>
            </div>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-back">← Kembali</a>
        </div>

        <!-- Error Alert -->
        @if($errors->any())
        <div class="alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.templates.update', $template) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Info Card -->
            <div class="form-card">
                <div class="card-section-title">Informasi Dasar</div>

                <div class="form-field">
                    <label>Nama Template</label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required placeholder="Masukkan nama template...">
                </div>
            </div>

            <!-- File Template Card -->
            <div class="form-card">
                <div class="card-section-title">File Template</div>

                <div class="form-field">
                    <label>Ganti File Template</label>
                    <div class="file-upload-area">
                        <input type="file" name="file" accept="image/*">
                        <p class="file-hint">Kosongkan jika tidak ingin mengganti file saat ini</p>
                    </div>
                    @if($template->file_path)
                    <div class="image-preview">
                        <div class="image-preview-label">Preview Saat Ini</div>
                        <img src="{{ Storage::url($template->file_path) }}" class="preview-img-large" alt="Template preview">
                        <span class="file-path-text">{{ $template->file_path }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Thumbnail Card -->
            <div class="form-card">
                <div class="card-section-title">Thumbnail</div>

                <div class="form-field">
                    <label>Ganti Thumbnail</label>
                    <div class="file-upload-area">
                        <input type="file" name="thumbnail" accept="image/*">
                        <p class="file-hint">Kosongkan jika tidak ingin mengganti thumbnail saat ini</p>
                    </div>
                    @if($template->thumbnail_path)
                    <div class="image-preview">
                        <div class="image-preview-label">Thumbnail Saat Ini</div>
                        <img src="{{ Storage::url($template->thumbnail_path) }}" class="preview-img-thumb" alt="Thumbnail preview">
                    </div>
                    @endif
                </div>
            </div>

            <!-- Slot Settings Card -->
            <div class="form-card">
                <div class="card-section-title">Pengaturan Slot Foto</div>

                <div class="form-field">
                    <label>Jumlah Slot</label>
                    <div class="slot-count-wrapper">
                        <input type="number" name="slot_count" id="slot_count"
                            value="{{ old('slot_count', $template->slot_count) }}" min="1" required>
                        <span class="slot-count-hint">slot foto</span>
                    </div>
                </div>

                <div id="form-data" data-existing-slots="{{ json_encode($template->slots ?? []) }}"></div>
                <div id="slots-container"></div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="{{ route('admin.templates.index') }}" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slotCountInput = document.getElementById('slot_count');
        const slotsContainer = document.getElementById('slots-container');
        const formDataEl = document.getElementById('form-data');

        const existingSlots = JSON.parse(formDataEl.dataset.existingSlots);

        function generateSlotFields(slotNum) {
            const data = existingSlots[slotNum - 1] || {
                x: 0,
                y: 0,
                width: 500,
                height: 300
            };

            return `
            <div class="slot-fieldset">
                <div class="slot-legend">
                    Slot <span class="slot-badge">${slotNum}</span>
                </div>
                <div class="slot-inputs">
                    <div class="slot-input-group">
                        <label>X</label>
                        <input type="number" name="slot${slotNum}_x" value="${data.x}" required>
                    </div>
                    <div class="slot-input-group">
                        <label>Y</label>
                        <input type="number" name="slot${slotNum}_y" value="${data.y}" required>
                    </div>
                    <div class="slot-input-group">
                        <label>Lebar (W)</label>
                        <input type="number" name="slot${slotNum}_width" value="${data.width}" required>
                    </div>
                    <div class="slot-input-group">
                        <label>Tinggi (H)</label>
                        <input type="number" name="slot${slotNum}_height" value="${data.height}" required>
                    </div>
                </div>
            </div>`;
        }

        function updateSlots() {
            const count = parseInt(slotCountInput.value) || 0;
            let html = '';
            for (let i = 1; i <= count; i++) {
                html += generateSlotFields(i);
            }
            slotsContainer.innerHTML = html;
        }

        slotCountInput.addEventListener('input', updateSlots);
        updateSlots();
    });
</script>
@endsection