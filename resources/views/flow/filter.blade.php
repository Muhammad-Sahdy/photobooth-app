@extends('layouts.app')

@section('title', 'Filter & Print')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

<div class="page-wrapper" id="page-wrapper">

    {{-- BACKGROUND IMAGE --}}
    <div class="bg-image"></div>

    {{-- TIMER --}}
    <div class="timer-panel">
        <div id="countdown-timer" class="timer-number">20</div>
    </div>

    {{-- PREVIEW FOTO FINAL --}}
    @if ($final)
    <div class="preview-panel">
        <div class="template-preview-wrapper">
            <img
                id="final-preview"
                src="{{ asset('storage/'.$final->file_path) }}?clear={{ time() }}"
                alt="Final Photo"
                class="template-base-img">

            <div id="loading-status" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:rgba(255,255,255,0.9); padding:15px 25px; border-radius:10px; color:#000; font-weight:bold; box-shadow: 0 4px 15px rgba(0,0,0,0.2); font-family:'Open Sans',sans-serif; z-index:20; white-space:nowrap;">
                Memproses...
            </div>
        </div>
    </div>

    {{-- DAFTAR FILTER (Thumbnail Diperbesar sesuai Gambar) --}}
    <div class="thumbs-panel">
        <div class="thumbs-inner">
            @php
            $previewThumb = $transaction->photos->first();
            $thumbUrl = $previewThumb ? asset('storage/'.$previewThumb->file_path) : asset('storage/'.$final->file_path);
            @endphp

            {{-- Original --}}
            <div class="filter-thumb-wrapper selected" data-filter="original">
                <div class="filter-img-container">
                    <img src="{{ $thumbUrl }}" class="filter-thumb">
                    <div class="thumb-overlay"></div>
                </div>
                <div class="filter-label">Original</div>
            </div>

            {{-- DAFTAR FILTER (Thumbnail Diperbesar) --}}
            @php
            // Memastikan data $filters tersedia (jika belum dipassing dari controller)
            $availableFilters = $filters ?? \App\Models\Filter::where('is_active', true)->get();
            @endphp

            @foreach($availableFilters as $f)
            <div class="filter-thumb-wrapper" data-filter="{{ $f->slug }}">
                <div class="filter-img-container">
                    {{-- PERUBAHAN DI SINI: Gunakan lazy-thumb dan data-src --}}
                    <img
                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                        data-src="{{ route('photobooth.filter-preview', [$transaction->photos->first()->id, $f->slug]) }}"
                        class="filter-thumb lazy-thumb"
                        style="background-color: #222;">
                    <div class="thumb-overlay"></div>
                </div>
                <div class="filter-label">{{ $f->name }}</div>
            </div>
            @endforeach
        </div>
        <div class="thumbs-panel">
            <div class="thumbs-inner">
            </div>
            <div class="scroll-gradient"></div>
        </div>
    </div>

    {{-- TOMBOL PRINT (Desain Lonjong / Pill-Shaped) --}}
    <div class="print-container">
        <button id="print-button" class="btn-print">
            PRINT
        </button>
    </div>

    @else
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; font-family:'Open Sans',sans-serif; color: white;">
        <p>Belum ada foto final.</p>
        <a href="{{ route('flow.compose', $transaction->code) }}" style="padding:8px 16px; background: white; text-decoration:none; color:black; border-radius:5px; margin-top:12px; display:inline-block;">Kembali</a>
    </div>
    @endif

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
        top: 0;
        left: 0;
        overflow: hidden;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    /* Munculkan setelah semua aset siap */
    .page-wrapper.is-ready {
        opacity: 1;
    }

    .bg-image {
        position: absolute;
        inset: 0;
        background: url("{{ asset('public_storage/images/pagess.png') }}") center center / 100% 100% no-repeat;
        z-index: 0;
    }

    /* TIMER */
    .timer-panel {
        position: absolute;
        left: 130px;
        top: 80px;
        z-index: 10;
    }

    .timer-number {
        font-size: 60px;
        font-weight: 900;
        color: #1a1a1a;
        font-family: 'Open Sans', sans-serif;
        transition: color 0.3s ease;
        /* Tambahkan transisi agar perubahan warna halus */
    }

    /* Warna merah dan animasi saat waktu kritis */
    .timer-number.urgent {
        color: #dc2626;
        /* Warna merah */
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

    /* PREVIEW PANEL */
    .preview-panel {
        position: absolute;
        left: 285px;
        top: 384px;
        transform: translateY(-50%);
        z-index: 10;
    }

    .template-preview-wrapper {
        position: relative;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
    }

    .template-base-img {
        display: block;
        max-height: 590px;
        width: auto;
    }

    /* THUMBS PANEL (Daftar Filter Sesuai Gambar) */
    .thumbs-panel {
        position: absolute;
        left: 870px;
        top: 90px;
        width: 260px;
        height: 565px;
        z-index: 10;
    }

    .thumbs-inner {
        display: flex;
        flex-direction: column;
        gap: 25px;
        height: 100%;
        overflow-y: auto;
        scrollbar-width: none;
        padding-right: 10px;
    }

    .thumbs-inner::-webkit-scrollbar {
        display: none;
    }

    .filter-thumb-wrapper {
        cursor: pointer;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
    }

    .filter-img-container {
        position: relative;
        width: 100%;
        height: 135px;
        /* Ukuran lebih besar */
        border: 3px solid transparent;
        overflow: hidden;
    }

    .filter-thumb-wrapper.selected .filter-img-container {
        border-color: #000;
    }

    .filter-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Checkmark bulat hitam di kiri atas seperti referensi */
    .filter-thumb-wrapper.selected .thumb-overlay::before {
        content: '✓';
        position: absolute;
        top: 8px;
        left: 8px;
        width: 24px;
        height: 24px;
        background: #000;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        z-index: 5;
    }

    .filter-label {
        background: #000;
        color: #fff;
        text-align: center;
        text-transform: uppercase;
        font-size: 13px;
        font-weight: bold;
        padding: 5px 0;
        font-family: 'Open Sans', sans-serif;
    }

    /* TOMBOL PRINT (Lonjong Pill) */
    .print-container {
        position: absolute;
        right: 40px;
        top: 384px;
        transform: translateY(-50%);
        z-index: 20;
    }

    .btn-print {
        background: #333;
        color: #fff;
        border: none;
        padding: 12px 35px;
        border-radius: 50px;
        /* Bentuk Pill */
        font-family: 'serif';
        font-size: 20px;
        font-weight: bold;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .btn-print:hover {
        background: #000;
        transform: scale(1.05);
    }

    .btn-print:active {
        transform: scale(0.95);
    }

    .btn-print:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

@if ($final)
<script>
    const BASE_W = 1366;
    const BASE_H = 768;

    function applyZoom() {
        const scaleX = window.innerWidth / BASE_W;
        const scaleY = window.innerHeight / BASE_H;
        const wrapper = document.getElementById('page-wrapper');
        wrapper.style.transform = `scale(${scaleX}, ${scaleY})`;
        wrapper.style.position = 'fixed';
        wrapper.style.top = '0px';
        wrapper.style.left = '0px';
        wrapper.style.transformOrigin = 'top left';
    }

    window.addEventListener('load', applyZoom);
    window.addEventListener('resize', applyZoom);
    document.addEventListener('fullscreenchange', applyZoom);

    // --- LOGIC FILTER & PRINT ---
    const filterUrl = "{{ route('photobooth.apply-filter', $transaction->code) }}";
    const finalPhotoId = "{{ $final->id }}";
    const TOTAL_TIME = 20;

    document.addEventListener('DOMContentLoaded', () => {
        const finalPreview = document.getElementById('final-preview');
        const timerEl = document.getElementById('countdown-timer');
        const loadingStatus = document.getElementById('loading-status');
        const printButton = document.getElementById('print-button');
        const filterWrappers = document.querySelectorAll('.filter-thumb-wrapper');

        let currentFilter = 'original';
        let timeLeft = TOTAL_TIME;
        let isPrinting = false;
        let isApplyingFilter = false;
        let timeIsUp = false;

        // Reset ke Original saat refresh (Cache Buster)
        finalPreview.src = "{{ asset('storage/'.$final->file_path) }}?refresh=" + Date.now();

        // --- DI DALAM setInterval ---
        const timerInterval = setInterval(() => {
            if (isPrinting) return;
            timeLeft--;
            timerEl.innerText = Math.max(timeLeft, 0);

            // TAMBAHKAN BARIS INI:
            // Jika waktu kurang dari atau sama dengan 7 detik, tambahkan class 'urgent'
            if (timeLeft <= 7) {
                timerEl.classList.add('urgent');
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timeIsUp = true;
                if (!isApplyingFilter) triggerPrint();
            }
        }, 1000);

        filterWrappers.forEach(wrapper => {
            wrapper.addEventListener('click', () => {
                if (timeIsUp || isPrinting || isApplyingFilter) return;

                const filterType = wrapper.dataset.filter;
                if (filterType === currentFilter) return;

                filterWrappers.forEach(el => el.classList.remove('selected'));
                wrapper.classList.add('selected');

                loadingStatus.style.display = 'block';
                finalPreview.style.opacity = '0.5';
                isApplyingFilter = true;

                fetch(filterUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            final_photo_id: finalPhotoId,
                            filter_type: filterType
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.file_url) {
                            finalPreview.src = data.file_url.split('?')[0] + '?t=' + Date.now();
                            currentFilter = filterType;
                        }
                    })
                    .finally(() => {
                        isApplyingFilter = false;
                        loadingStatus.style.display = 'none';
                        finalPreview.style.opacity = '1';
                        if (timeIsUp && !isPrinting) triggerPrint();
                    });
            });
        });

        function triggerPrint() {
            if (isPrinting) return;
            isPrinting = true;
            clearInterval(timerInterval);
            printButton.disabled = true;
            loadingStatus.innerText = "Mencetak...";
            loadingStatus.style.display = 'block';

            fetch(filterUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        final_photo_id: finalPhotoId,
                        filter_type: currentFilter
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const printUrl = data.file_url;
                    let iframe = document.getElementById('print-iframe');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = 'print-iframe';
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                    }

                    const pri = iframe.contentWindow;
                    pri.document.open();
                    pri.document.write(`
                <html>
        <head>
            <style>
                /* Menghilangkan margin browser sepenuhnya */
                @page { 
                    margin: 0; 
                    size: auto; 
                } 
                body { 
                    margin: 0; 
                    padding: 0; 
                    overflow: hidden; /* Mencegah scrollbar yang memicu halaman baru */
                } 
                img { 
                    width: 100vw; 
                    height: auto; 
                    display: block; /* Menghilangkan whitespace di bawah gambar */
                }
            </style>
        </head>
        <body>
            <img src="${printUrl}" onload="window.print();">
        </body>
    </html>
            `);
                    pri.document.close();

                    setTimeout(() => {
                        window.location.href = "{{ route('flow.barcode', $transaction->code) }}";
                    }, 3000);
                });
        }

        printButton.addEventListener('click', triggerPrint);
    });
    // --- LOGIC SEQUENTIAL LAZY LOADING THUMBNAILS ---
    window.addEventListener('load', () => {
        // 1. Tampilkan halaman utama seketika (tanpa menunggu thumbnail)
        document.getElementById('page-wrapper').classList.add('is-ready');

        // 2. Kumpulkan semua thumbnail yang belum dimuat
        const lazyThumbs = document.querySelectorAll('.lazy-thumb');
        let thumbIndex = 0;

        // 3. Fungsi untuk memuat gambar SATU PER SATU (Antre)
        function loadNextThumb() {
            if (thumbIndex >= lazyThumbs.length) return; // Semua selesai

            const img = lazyThumbs[thumbIndex];
            const src = img.getAttribute('data-src');

            // Jika berhasil dimuat, lanjut ke gambar berikutnya
            img.onload = () => {
                img.style.backgroundColor = 'transparent'; // Hapus warna loading
                thumbIndex++;
                loadNextThumb();
            };

            // Jika error/gagal, abaikan dan lanjut ke gambar berikutnya
            img.onerror = () => {
                thumbIndex++;
                loadNextThumb();
            };

            // Memicu XAMPP untuk memproses gambar INI SAJA
            img.src = src;
        }

        // 4. Mulai antrean pemuatan
        loadNextThumb();
    });
</script>
@endif

@endsection