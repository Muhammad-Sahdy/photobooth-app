@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

<div class="page-wrapper" id="page-wrapper">

    {{-- BACKGROUND IMAGE --}}
    <div class="bg-image"></div>

    {{-- PREVIEW FOTO FINAL (Sisi Kiri) --}}
    <div class="photo-panel">
        @if($finalPhoto)
        <img src="{{ asset('storage/' . $finalPhoto->file_path) }}" class="final-photo-img" alt="Final Photo">
        @endif
    </div>

    {{-- COUNTDOWN — angka besar di pojok kanan atas, di luar card --}}
    <div class="countdown-outside">
        <span id="seconds">20</span>
    </div>

    {{-- QR CODE CARD (Sisi Kanan — menempati area card di background) --}}
    <div class="qr-panel">
        <div class="qr-box">
            <canvas id="qrcode-canvas"></canvas>
        </div>
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
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
        transform-origin: top left;
    }

    /* ---- BACKGROUND ---- */
    .bg-image {
        position: absolute;
        inset: 0;
        background: url("{{ asset('public_storage/images/barcode page.png') }}") center center / 100% 100% no-repeat;
        z-index: 0;
    }

    /* ---- FOTO FINAL (Kiri) ---- */
    .photo-panel {
        position: absolute;
        left: 60px;
        top: 130px;
        width: 640px;
        height: 580px;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        overflow: hidden;
    }

    .final-photo-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    }

    /* ---- QR PANEL (Kanan — di atas card background) ---- */
    .qr-panel {
        position: absolute;
        right: 128px;
        top: 110px;
        width: 460px;
        height: 570px;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }

    /* ---- COUNTDOWN LUAR CARD — pojok kanan atas seperti referensi ---- */
    .countdown-outside {
        position: absolute;
        right: 135px;
        top: 85px;
        z-index: 20;
        line-height: 1;
    }

    .countdown-outside span {
        font-family: 'Open Sans', sans-serif;
        font-size: 55px;
        font-weight: 900;
        color: #1a1a1a;
        font-variant-numeric: tabular-nums;
        transition: color 0.3s;
    }

    .countdown-outside span.urgent {
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

    .scan-label {
        font-family: 'Open Sans', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #ffffff;
        text-align: center;
        letter-spacing: 0.3px;
    }

    /* Kotak putih QR */
    .qr-box {
        background: #ffffff;
        padding: 16px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #qrcode-canvas {
        display: block;
    }

    .transaction-code {
        font-family: 'Open Sans', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 1px;
        text-align: center;
    }

    .scan-sub {
        font-family: 'Open Sans', sans-serif;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.75);
        text-align: center;
    }

    /* Countdown */
    .countdown-wrap {
        font-family: 'Open Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        background: rgba(0, 0, 0, 0.25);
        padding: 5px 16px;
        border-radius: 20px;
        text-align: center;
        letter-spacing: 0.3px;
    }

    .countdown-wrap #seconds {
        color: #fff;
        font-weight: 900;
    }

    .thank-you {
        font-family: 'Open Sans', sans-serif;
        font-size: 28px;
        font-weight: 900;
        color: #ffffff;
        text-align: center;
        letter-spacing: 0.5px;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.1/qrcode.min.js"></script>

<script>
    // ── ZOOM SCALER ──
    function applyZoom() {
        const scaleX = window.innerWidth / 1366;
        const scaleY = window.innerHeight / 768;
        const wrapper = document.getElementById('page-wrapper');
        wrapper.style.transform = `scale(${scaleX}, ${scaleY})`;
    }
    window.addEventListener('load', applyZoom);
    window.addEventListener('resize', applyZoom);
    document.addEventListener('fullscreenchange', applyZoom);

    window.onload = function() {
        applyZoom();

        // ── QR CODE ──
        const token = "{{ $transaction->access->access_token ?? '' }}";
        const canvas = document.getElementById('qrcode-canvas');
        if (token && canvas) {
            const galleryUrl = "{{ url('/gallery') }}/" + token;
            QRCode.toCanvas(canvas, galleryUrl, {
                width: 220,
                margin: 2
            });
        }

        // ── AUTO-REDIRECT 20 DETIK ──
        const TOTAL_TIME = 20;
        let timeLeft = TOTAL_TIME;
        const secondsEl = document.getElementById('seconds');
        const registerUrl = "{{ route('registration.form') }}";

        const countdown = setInterval(() => {
            timeLeft--;
            if (secondsEl) {
                secondsEl.innerText = timeLeft;
                if (timeLeft <= 7) secondsEl.classList.add('urgent');
            }
            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.href = registerUrl;
            }
        }, 1000);
    };
</script>

@endsection