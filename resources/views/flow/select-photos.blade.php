@extends('layouts.app')

@section('title', 'Pilih Foto')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

<div class="page-wrapper" id="page-wrapper">

    {{-- BACKGROUND IMAGE --}}
    <div class="bg-image"></div>

    {{-- TIMER --}}
    <div class="timer-panel">
        <div id="countdown-timer" class="timer-number">45</div>
    </div>

    {{-- PREVIEW TEMPLATE --}}
    <div class="preview-panel">
        <div id="template-preview-wrapper" class="template-preview-wrapper">
            <img
                id="template-base"
                src="{{ asset('storage/' . $transaction->template->file_path) }}"
                alt="Template"
                class="template-base-img">

            @foreach($transaction->template->slots as $index => $slot)
            <img id="slot-{{ $index }}-preview"
                src=""
                class="slot-preview"
                data-slot-index="{{ $index }}"
                style="border-color: rgba(255,255,255,0.5);">
            @endforeach
        </div>
    </div>

    {{-- FOTO LIST --}}
    <div class="thumbs-panel">
        <div class="thumbs-inner">
            @if($transaction->photos->isEmpty())
            <p class="no-photo-msg">Belum ada foto.</p>
            @else
            <div id="photo-list" class="photo-list">
                @foreach($transaction->photos as $photo)
                <div class="photo-thumb-wrapper">
                    <img
                        src="{{ asset('storage/'.$photo->file_path) }}"
                        data-id="{{ $photo->id }}"
                        class="photo-thumb">
                    <div class="thumb-overlay"></div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- TOMBOL NEXT — sama dengan codingan 1: lingkaran hitam + panah SVG --}}
    <form
        id="confirm-form"
        style="
            position: absolute;
            right: 75px;
            top: 384px;
            transform: translateY(-50%);
            z-index: 20;
        ">
        <button
            id="confirm-photos"
            disabled
            class="btn-next"
            title="Gunakan foto yang dipilih">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
            </svg>
        </button>
    </form>

    {{-- INFO SLOT --}}
    <div class="slot-info" id="slot-info">
        Pilih <span id="slot-counter">0</span> / {{ $transaction->template->slot_count }} foto
    </div>

</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
        background: #000;
        width: 100vw;
        height: 100vh;
    }

    .page-wrapper {
        width: 1366px;
        height: 768px;
        position: absolute;
        /* Ubah dari relative ke absolute agar koordinat 0,0 pasti */
        top: 0;
        left: 0;
        overflow: hidden;
        /* Hapus margin auto jika ada */
    }

    /* ---- BACKGROUND ---- */
    .bg-image {
        position: absolute;
        inset: 0;
        background: url("{{ asset('storage/images/pages.png') }}") center center / 100% 100% no-repeat;
        z-index: 0;
    }

    /* ---- TIMER PANEL — gaya seperti codingan 1: teks polos tanpa box ---- */
    .timer-panel {
        position: absolute;
        left: 150px;
        top: 128px;
        transform: translateY(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
        z-index: 10;
    }

    .timer-label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 3px;
        color: #1a1a1a;
        text-transform: uppercase;
        font-family: 'Open Sans', sans-serif;
    }

    .timer-number {
        font-size: 64px;
        font-weight: 900;
        color: #1a1a1a;
        line-height: 1;
        font-family: 'Open Sans', sans-serif;
        font-variant-numeric: tabular-nums;
        transition: color 0.3s;
    }

    .timer-number.urgent {
        color: #dc2626;
        animation: pulse-urgent 0.5s ease-in-out infinite alternate;
    }

    @keyframes pulse-urgent {
        from {
            transform: scale(1);
        }

        to {
            transform: scale(1.06);
        }
    }

    /* ---- PREVIEW PANEL ---- */
    .preview-panel {
        position: absolute;
        left: 285px;
        top: 370px;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .template-preview-wrapper {
        position: relative;
        display: inline-block;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.35);
    }

    .template-base-img {
        display: block;
        max-height: 598px;
        max-width: 380px;
        width: auto;
        height: auto;
    }

    .slot-preview {
        position: absolute;
        display: none;
        object-fit: cover;
        border-width: 2px;
        border-style: solid;
        box-sizing: border-box;
        z-index: 10;
    }

    /* ---- THUMBS PANEL ---- */
    .thumbs-panel {
        position: absolute;
        left: 850px;
        top: 75px;
        width: 286px;
        height: 583px;
        z-index: 10;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .thumbs-inner {
        display: flex;
        flex-direction: column;
        gap: 0;
        height: 100%;
        overflow-y: auto;
        scrollbar-width: none;
    }

    .thumbs-inner::-webkit-scrollbar {
        display: none;
    }

    .photo-thumb-wrapper {
        position: relative;
        cursor: pointer;
        flex: 1;
        min-height: 0;
        border: 2px solid transparent;
        transition: border-color 0.2s, opacity 0.2s;
        overflow: hidden;
    }

    .photo-thumb-wrapper.disabled-thumb {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .photo-thumb-wrapper.selected {
        border-color: #111;
        box-shadow: inset 0 0 0 3px rgba(0, 0, 0, 0.7);
    }

    .photo-thumb {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        pointer-events: none;
    }

    .thumb-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        transition: background 0.2s;
    }

    .photo-thumb-wrapper.selected .thumb-overlay {
        background: rgba(0, 0, 0, 0.15);
    }

    /* Checkmark — sama dengan codingan 1 */
    .photo-thumb-wrapper.selected .thumb-overlay::after {
        content: '✓';
        font-size: 26px;
        color: white;
        font-weight: 900;
        font-family: 'Open Sans', sans-serif;
        background: rgba(0, 0, 0, 0.55);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 40px;
        text-align: center;
    }

    .no-photo-msg {
        color: #555;
        font-size: 13px;
        font-family: 'Open Sans', sans-serif;
        text-align: center;
        padding: 16px;
    }

    /* ---- TOMBOL NEXT — sama persis dengan codingan 1 ---- */
    .btn-next {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        border: none;
        background: #111;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: not-allowed;
        opacity: 0.3;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        padding: 0;
    }

    .btn-next:not(:disabled) {
        cursor: pointer;
        opacity: 1;
    }

    .btn-next:not(:disabled):hover {
        background: #333;
        transform: scale(1.1);
    }

    .btn-next:not(:disabled):active {
        transform: scale(0.95);
    }

    /* ---- SLOT INFO ---- */
    .slot-info {
        position: absolute;
        bottom: 46px;
        left: 475px;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.65);
        color: white;
        padding: 5px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        font-family: 'Open Sans', sans-serif;
        letter-spacing: 0.5px;
        z-index: 20;
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }
</style>

<script>
    // ── ZOOM SCALER: Menghilangkan potongan hitam dengan Stretch Fill ──
    const BASE_W = 1366;
    const BASE_H = 768;

    function applyZoom() {
        // Hitung skala masing-masing sumbu
        const scaleX = window.innerWidth / BASE_W;
        const scaleY = window.innerHeight / BASE_H;

        const wrapper = document.getElementById('page-wrapper');

        // Gunakan kedua skala agar menarik element memenuhi seluruh layar
        wrapper.style.transform = `scale(${scaleX}, ${scaleY})`;
        wrapper.style.position = 'fixed';

        // Reset posisi ke pojok kiri atas karena kita ingin memenuhi layar
        wrapper.style.top = '0px';
        wrapper.style.left = '0px';

        // Memastikan origin transformasi di pojok kiri atas
        wrapper.style.transformOrigin = 'top left';
    }

    // Eksekusi saat load dan resize
    window.addEventListener('load', applyZoom);
    window.addEventListener('resize', applyZoom);
    document.addEventListener('fullscreenchange', applyZoom);
</script>
<script>
    const templateSlots = JSON.parse('{!! json_encode($transaction->template->slots) !!}') || [];
    const maxPhotos = templateSlots.length;
    const TOTAL_TIME = 45;

    document.addEventListener('DOMContentLoaded', () => {
        const templateImg = document.getElementById('template-base');
        const confirmBtn = document.getElementById('confirm-photos');
        const timerEl = document.getElementById('countdown-timer');
        const slotCounter = document.getElementById('slot-counter');

        let selectedSlots = new Array(maxPhotos).fill(null);
        let timeLeft = TOTAL_TIME;

        // ---- TIMER ----
        const timerInterval = setInterval(() => {
            timeLeft--;
            timerEl.innerText = timeLeft;
            if (timeLeft <= 10) timerEl.classList.add('urgent');
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                autoConfirm();
            }
        }, 1000);

        // ---- SLOT PREVIEWS ----
        const slotPreviews = [];
        for (let i = 0; i < maxPhotos; i++) {
            const el = document.getElementById(`slot-${i}-preview`);
            if (el) slotPreviews.push(el);
        }

        let scaleX = 1,
            scaleY = 1;

        const calculateScale = () => {
            if (templateImg.naturalWidth) {
                scaleX = templateImg.clientWidth / templateImg.naturalWidth;
                scaleY = templateImg.clientHeight / templateImg.naturalHeight;
                updatePreviews();
            }
        };

        if (templateImg.complete) calculateScale();
        else templateImg.onload = calculateScale;

        // ---- UPDATE UI ----
        function updatePreviews() {
            selectedSlots.forEach((id, index) => {
                const previewEl = slotPreviews[index];
                if (!previewEl) return;

                if (id !== null) {
                    const thumb = document.querySelector(`.photo-thumb[data-id="${id}"]`);
                    const slotData = templateSlots[index];
                    if (thumb && slotData) {
                        previewEl.src = thumb.src;
                        previewEl.style.left = (slotData.x * scaleX) + 'px';
                        previewEl.style.top = (slotData.y * scaleY) + 'px';
                        previewEl.style.width = (slotData.width * scaleX) + 'px';
                        previewEl.style.height = (slotData.height * scaleY) + 'px';
                        previewEl.style.display = 'block';
                    }
                } else {
                    if (previewEl) {
                        previewEl.style.display = 'none';
                        previewEl.src = '';
                    }
                }
            });

            const filledCount = selectedSlots.filter(id => id !== null).length;
            slotCounter.innerText = filledCount;
            confirmBtn.disabled = (filledCount !== maxPhotos);

            // Redup foto yang belum dipilih jika slot penuh
            const isFull = filledCount >= maxPhotos;
            document.querySelectorAll('.photo-thumb-wrapper').forEach(wrapper => {
                const isSelected = wrapper.classList.contains('selected');
                wrapper.classList.toggle('disabled-thumb', isFull && !isSelected);
            });
        }

        // ---- CLICK FOTO ----
        document.querySelectorAll('.photo-thumb-wrapper').forEach((wrapper) => {
            wrapper.addEventListener('click', () => {
                const thumb = wrapper.querySelector('.photo-thumb');
                const id = parseInt(thumb.dataset.id, 10);
                const existingIndex = selectedSlots.indexOf(id);

                if (existingIndex !== -1) {
                    selectedSlots[existingIndex] = null;
                    wrapper.classList.remove('selected');
                } else {
                    const filledCount = selectedSlots.filter(id => id !== null).length;
                    if (filledCount >= maxPhotos) return;
                    const emptyIndex = selectedSlots.indexOf(null);
                    selectedSlots[emptyIndex] = id;
                    wrapper.classList.add('selected');
                }

                updatePreviews();
            });
        });

        // ---- AUTO CONFIRM ----
        function autoConfirm() {
            const availablePhotoIds = Array.from(document.querySelectorAll('.photo-thumb')).map(img => parseInt(img.dataset.id));
            for (let i = 0; i < maxPhotos; i++) {
                if (selectedSlots[i] === null) {
                    const nextId = availablePhotoIds.find(id => !selectedSlots.includes(id));
                    selectedSlots[i] = nextId || availablePhotoIds[0] || null;
                }
            }
            handleConfirm();
        }

        // ---- CONFIRM ----
        const handleConfirm = () => {
            clearInterval(timerInterval);
            const finalIds = selectedSlots.filter(id => id !== null);
            const nextBg = new Image();
            nextBg.src = "{{ asset('public_storage/images/pagess.png') }}";

            // 2. Preload Foto Gabungan (Compose)
            // Panggil saat user menekan tombol 'Confirm'
            const preloadFinalResult = (url) => {
                const img = new Image();
                img.src = url;
            };

            confirmBtn.disabled = true;
            confirmBtn.style.opacity = '0.5';

            fetch("{{ route('photobooth.choose-photos', $transaction->code) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        photo_ids: finalIds
                    }),
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Error');
                    window.location.href = "{{ route('flow.filter', $transaction->code) }}";
                })
                .catch(err => {
                    console.error(err);
                    alert(err.message);
                    confirmBtn.disabled = false;
                    confirmBtn.style.opacity = '';
                });
        };

        confirmBtn.addEventListener('click', handleConfirm);
    });
</script>
@endsection