@extends('layouts.app')

@section('title', 'Pilih Template')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@800;900&display=swap" rel="stylesheet">

<input type="hidden" id="template-id-hidden">

<div id="main-wrapper" style="
    --bg-image: url('{{ asset('public_storage/images/template page.png') }}');
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    font-family: 'Open Sans', sans-serif;
    background-image: var(--bg-image);
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
">

    {{-- ===== HEADER: Title + Timer ===== --}}
    <div style="
        position: absolute;
        top: 0; left: 0; right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 50px 0 60px;
        z-index: 10;
    ">
        <h1 style="
            font-size: clamp(40px, 5.5vw, 64px);
            font-weight: 300;
            font-style: italic;
            color: #111;
            margin: 0;
            letter-spacing: 0px;
            line-height: 1;
            font-family: 'Open Sans', sans-serif;
        ">What's new?</h1>


        {{-- Timer badge (sama style dengan badge jumlah template dulu) --}}
        {{-- Timer badge --}}
        <span id="timer-box" style="
    position: absolute;
    top: 72px;      /* ← jarak dari atas */
    right: 130px;    /* ← jarak dari kanan */
    font-size: 35px;
    font-weight: 900;
    color: #111;
    font-family: 'Open Sans', sans-serif;
    min-width: 60px;
    text-align: center;
    z-index: 10;
"><span id="countdown-display">30</span></span>

    </div>

    {{-- ===== TEMPLATE CARD AREA ===== --}}
    {{--
        Layout mengikuti gambar referensi:
        - Header ~85px
        - Bottom bar (garis + bintang) ~80px
        - Sisa tinggi untuk card
        - Card tampil 3 kolom besar, scroll vertikal jika lebih dari 3
    --}}
    <div id="scroll-area" style="
        position: absolute;
        top: 130px;
        left: 45px;
        right: 130px;
        bottom: 84px;
        overflow-y: auto;
        overflow-x: hidden;
    ">
        <div id="card-grid" style="
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            width: 100%;
            height: 100%;
            align-items: stretch;
        ">

            @foreach($templates as $template)
            <button
                type="button"
                class="template-item"
                data-template-id="{{ $template->id }}"
                style="
                    border: none;
                    border-radius: 18px;
                    padding: 0;
                    cursor: pointer;
                    background: #fff;
                    box-shadow: 0 10px 35px rgba(0,0,0,0.15);
                    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    outline: none;
                    user-select: none;
                    width: 100%;
                    min-height: 0;
                ">
                {{-- Image area — flex-grow agar mengisi tinggi card --}}
                <div style="
                    width: 100%;
                    flex: 1;
                    overflow: hidden;
                    border-radius: 12px;
                    background: #f3f4f6;
                    pointer-events: none;
                    min-height: 0;
                ">
                    <img
                        src="{{ asset('storage/' . $template->file_path) }}"
                        alt="{{ $template->name }}"
                        style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
            </button>
            @endforeach

        </div>
    </div>

    {{-- ===== TOMBOL PANAH (Next) - tengah kanan ===== --}}
    <form
        id="template-next-form"
        action="{{ route('flow.capture', $transaction->code) }}"
        method="GET"
        style="
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        ">
        <input type="hidden" name="template_id" id="template-id-field">

        <button
            type="submit"
            id="next-button"
            disabled
            title="Mulai Berfoto"
            style="
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
                box-shadow: 0 4px 20px rgba(0,0,0,0.35);
                padding: 0;
            ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
            </svg>
        </button>
    </form>

</div>

<style>
    * {
        box-sizing: border-box;
    }

    body,
    html {
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    /* Sembunyikan scrollbar */
    #scroll-area::-webkit-scrollbar {
        display: none;
    }

    #scroll-area {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Hover effect */
    .template-item:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.22) !important;
    }

    /* Selected state */
    .template-selected {
        outline: 4px solid #111 !important;
        outline-offset: 1px;
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 22px 50px rgba(0, 0, 0, 0.28) !important;
        z-index: 10;
        position: relative;
    }

    /* Next button active hover */
    #next-button:not(:disabled):hover {
        background: #333 !important;
        transform: scale(1.1);
        opacity: 1 !important;
    }

    /* Timer urgent */
    @keyframes urgentPulse {

        0%,
        100% {
            background: rgba(185, 28, 28, 0.25);
            color: #b91c1c;
        }

        50% {
            background: rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
    }

    .timer-urgent {
        animation: urgentPulse 0.6s infinite;
    }

    /* Saat hanya 1 baris, buat card mengisi tinggi penuh */
    #card-grid {
        /* Jika template <= 3, grid hanya 1 baris — isi tinggi penuh */
        grid-auto-rows: 1fr;
    }

    @media (max-width: 900px) {
        #card-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 560px) {
        #card-grid {
            grid-template-columns: repeat(1, 1fr) !important;
        }
    }
</style>

<script>
    // ── Template selection ──
    const templateButtons = document.querySelectorAll('.template-item');
    const templateIdField = document.getElementById('template-id-field');
    const nextButton = document.getElementById('next-button');
    const captureUrl = "{{ route('flow.capture', $transaction->code) }}";

    templateButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            templateIdField.value = btn.dataset.templateId;
            templateButtons.forEach(b => b.classList.remove('template-selected'));
            btn.classList.add('template-selected');
            nextButton.disabled = false;
            nextButton.style.cursor = 'pointer';
            nextButton.style.opacity = '1';
        });
    });

    // ── Atur tinggi grid: jika <=3 template, isi penuh; jika lebih, auto scroll ──
    const grid = document.getElementById('card-grid');
    const scrollArea = document.getElementById('scroll-area');
    const totalCards = templateButtons.length;

    if (totalCards <= 3) {
        // Isi tinggi area penuh, tanpa scroll
        grid.style.height = '100%';
        grid.style.gridAutoRows = '1fr';
    } else {
        // Tiap baris tinggi 90% area scroll agar 1 baris per layar
        const areaHeight = scrollArea.clientHeight;
        grid.style.gridAutoRows = (areaHeight * 0.92) + 'px';
        grid.style.height = 'auto';
    }

    // ── Countdown Timer ──
    const TOTAL_SECONDS = 30; // Ubah sesuai kebutuhan
    let secondsLeft = TOTAL_SECONDS;
    const display = document.getElementById('countdown-display');
    const timerBox = document.getElementById('timer-box');

    function redirectToCapture() {
        const selectedId = templateIdField.value || "{{ $templates->first()?->id ?? '' }}";
        window.location.href = captureUrl + '?template_id=' + selectedId;
    }

    const timerInterval = setInterval(() => {
        secondsLeft--;
        display.textContent = secondsLeft;
        if (secondsLeft <= 10) timerBox.classList.add('timer-urgent');
        if (secondsLeft <= 0) {
            clearInterval(timerInterval);
            redirectToCapture();
        }
    }, 1000);
</script>

@endsection