@extends('layouts.app')

@section('title', 'Ambil Foto')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* --- DASAR & BACKGROUND --- */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        overflow: hidden;
        background: #9d9d9d;
    }

    #mainLayout {
        width: 100vw;
        height: 100vh;
        background-image:url("{{ asset('public_storage/images/foto.png') }}");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }

    /* --- TIMER --- */
    #timer-container {
        position: absolute;
        top: 2%;
        right: 7%;
        z-index: 100;
        text-align: right;
    }

    #timer-display {
        font-size: 3vw;
        font-weight: 800;
        color: #1a1a1a;
        letter-spacing: 2px;
        /* Tambahan: transisi agar perubahan warna terlihat halus */
        transition: color 0.3s ease;
    }

    /* --- AREA KAMERA --- */
    .camera-section {
        position: absolute;
        left: 10%;
        top: 12%;
        width: 50%;
        height: 62%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .video-wrapper {
        width: 100%;
        height: 100%;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }

    #camera-stream {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
    }

    .camera-mask {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 15%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 10;
        pointer-events: none;
    }

    .mask-left {
        left: 0;
    }

    .mask-right {
        right: 0;
    }

    /* --- AREA GALERI --- */
    .gallery-section {
        position: absolute;
        top: 12%;
        bottom: 22%;
        right: 4.5%;
        width: 25%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        overflow: hidden;
    }

    #photo-list {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        overflow-y: auto;
        padding: 10px 0;

        /* ✅ LOGIKA BARU: Menghilangkan scrollbar di Firefox & IE */
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    /* ✅ LOGIKA BARU: Menghilangkan scrollbar di Chrome, Safari, Edge */
    #photo-list::-webkit-scrollbar {
        display: none;
    }

    #photo-list img {
        width: 85%;
        border-radius: 10px;
        border: 2px solid #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* --- TOMBOL JEPRET --- */
    .controls {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 110;
    }

    #manual-capture-btn {
        width: 70px;
        height: 70px;
        background: #f1f1f1;
        border: 3px solid #333;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #manual-capture-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #999;
    }

    #manual-capture-btn::before {
        content: "";
        width: 30px;
        height: 30px;
        background-color: #333;
        -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M4 4h3l2-2h6l2 2h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm8 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6z'/%3E%3C/svg%3E") no-repeat center;
        mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M4 4h3l2-2h6l2 2h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm8 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6z'/%3E%3C/svg%3E") no-repeat center;
        mask-size: contain;
    }

    /* --- TOMBOL NEXT ARROW --- */
    .next-arrow-btn {
        position: absolute;
        right: 4%;
        bottom: 5%;
        width: 70px;
        height: 70px;
        background: #1a1a1a;
        color: #fff;
        border: none;
        border-radius: 50%;
        display: none;
        /* 🔒 default hidden */
        justify-content: center;
        align-items: center;
        font-size: 24px;
        cursor: pointer;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    /* --- OVERLAYS --- */
    #countdown-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 50;
    }

    #countdown-number {
        font-size: 12vw;
        color: #fff;
        font-weight: bold;
    }

    #flash-effect {
        position: absolute;
        inset: 0;
        background: #fff;
        display: none;
        z-index: 150;
    }
</style>

<div id="mainLayout">
    <div id="timer-container">
        <div id="timer-display">01:30</div>
    </div>

    <div class="camera-section">
        <div class="video-wrapper">
            <video id="camera-stream" autoplay playsinline muted></video>
            <div class="camera-mask mask-left"></div>
            <div class="camera-mask mask-right"></div>
            <div id="countdown-overlay"><span id="countdown-number">5</span></div>
            <div id="flash-effect"></div>
        </div>
        <div class="controls">
            <button id="manual-capture-btn"></button>
        </div>
    </div>

    <div class="gallery-section">
        <div id="photo-list"></div>
    </div>

    <button id="nextBtn" class="next-arrow-btn" onclick="goToNextStep()">➔</button>
</div>

<script>
    const requiredPhotos = Number("{{ $transaction->template->slot_count }}");
    const captureUrl = "{{ route('photobooth.capture', $transaction->code) }}";
    const nextUrl = "{{ route('flow.select-photos', $transaction->code) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const video = document.getElementById('camera-stream');
    const photoList = document.getElementById('photo-list');
    const captureBtn = document.getElementById('manual-capture-btn');
    const timerDisplay = document.getElementById('timer-display');
    const nextBtn = document.getElementById('nextBtn');

    let sessionTime = 90;
    let isCapturing = false;
    let photoCounter = 0;

    async function initCamera() {
        try {

            // Constraint fleksibel → tidak memaksa resolusi tertentu
            const constraints = {
                video: {
                    facingMode: "user", // kamera depan
                    width: {
                        ideal: 1920
                    },
                    height: {
                        ideal: 1080
                    }
                },
                audio: false
            };

            const stream = await navigator.mediaDevices.getUserMedia(constraints);

            video.srcObject = stream;

            // Tunggu metadata agar videoWidth tersedia
            await new Promise(resolve => {
                video.onloadedmetadata = resolve;
            });

            const track = stream.getVideoTracks()[0];
            const settings = track.getSettings();

            console.log("=== Kamera Aktif ===");
            console.log("Width :", settings.width);
            console.log("Height:", settings.height);
            console.log("Aspect:", (settings.width / settings.height).toFixed(2));

            startSessionTimer();

        } catch (err) {

            console.warn("FullHD gagal, fallback ke default", err);

            // fallback tanpa constraint resolusi
            try {

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });

                video.srcObject = stream;

                await new Promise(resolve => {
                    video.onloadedmetadata = resolve;
                });

                startSessionTimer();

            } catch (err2) {

                console.error("Kamera gagal total:", err2);
                alert("Kamera tidak tersedia.");

            }
        }
    }


    function startSessionTimer() {
        const interval = setInterval(() => {
            sessionTime--;
            let mins = Math.floor(sessionTime / 60);
            let secs = sessionTime % 60;
            timerDisplay.innerText = `${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`;

            if (sessionTime <= 10) {
                timerDisplay.style.color = '#dc2626';
            }

            if (sessionTime <= 0) {
                clearInterval(interval);
                goToNextStep();
            }
        }, 1000);
    }

    function goToNextStep() {
        window.location.href = nextUrl;
    }

    // --- LOGIKA CAPTURE TANPA BATAS ---
    captureBtn.onclick = function() {
        if (isCapturing) return; // Mencegah klik ganda saat proses upload

        isCapturing = true;
        captureBtn.disabled = true; // Disabled sementara hanya saat animasi countdown

        let count = 5;
        document.getElementById('countdown-overlay').style.display = 'flex';
        document.getElementById('countdown-number').innerText = count;

        const timer = setInterval(() => {
            count--;
            document.getElementById('countdown-number').innerText = count;

            if (count <= 0) {
                clearInterval(timer);
                executeCapture();
            }
        }, 1000);
    };

    function executeCapture() {

        const flash = document.getElementById('flash-effect');

        flash.style.display = 'block';

        setTimeout(() => flash.style.display = 'none', 120);


        const videoWidth = video.videoWidth;
        const videoHeight = video.videoHeight;

        if (!videoWidth || !videoHeight) {
            alert("Resolusi kamera belum siap");
            resetUI();
            return;
        }


        const canvas = document.createElement("canvas");
        canvas.width = videoWidth;
        canvas.height = videoHeight;

        const ctx = canvas.getContext("2d");


        // mirror fix
        ctx.translate(videoWidth, 0);
        ctx.scale(-1, 1);

        ctx.drawImage(video, 0, 0, videoWidth, videoHeight);


        canvas.toBlob(blob => {

            const formData = new FormData();
            formData.append("image", blob, "capture.png");

            fetch(captureUrl, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {

                    if (data.file_url) {
                        addThumb(data.file_url);
                    }

                    resetUI();

                })
                .catch(err => {

                    console.error(err);
                    resetUI();

                });

        }, "image/png", 1.0);
    }


    function addThumb(url) {
        photoCounter++;

        const img = document.createElement('img');
        img.src = url;
        photoList.prepend(img);

        // ✅ LOGIKA KUNCI: 
        // Tombol Next muncul jika sudah mencapai slot_count, 
        // tapi TIDAK mematikan tombol jepret.
        if (photoCounter >= requiredPhotos) {
            nextBtn.style.display = 'flex';
        }
    }

    function resetUI() {
        isCapturing = false;

        // ✅ LOGIKA KUNCI: 
        // Tombol jepret selalu dinyalakan kembali setelah upload selesai,
        // tanpa peduli berapa jumlah foto yang sudah diambil.
        if (sessionTime > 0) {
            captureBtn.disabled = false;
        }

        document.getElementById('countdown-overlay').style.display = 'none';
    }

    window.onload = initCamera;
</script>
@endsection