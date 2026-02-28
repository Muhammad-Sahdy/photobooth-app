@extends('layouts.app')

@section('title', 'Registrasi & Pembayaran')

@section('content')

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    /* Paksa fullscreen sejati */
    #page-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        z-index: 9999;
    }

    /* ====== START SCREEN ====== */
    #startScreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1;
    }

    #startScreen img.bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: fill;
        /* ← stretch penuh, tidak crop */
        z-index: 0;
    }

    #startButton {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 1.5vh 6vw;
        font-size: 1.2vw;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        letter-spacing: 3px;
        transition: background 0.2s, color 0.2s;
        white-space: nowrap;
    }

    #startButton:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* ====== REGISTRATION SCREEN ====== */
    #registrationScreen {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1;
    }

    #registrationScreen img.bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: fill;
        /* ← stretch penuh, tidak crop */
        z-index: 0;
    }

    /* LEFT */
    #left-side {
        position: absolute;
        top: 50%;
        left: 8vw;
        transform: translateY(-50%);
        width: 25vw;
        z-index: 1;
    }

    #form-fields label {
        font-family: 'Open Sans', sans-serif;
        font-size: 0.75vw;
        color: #555;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 0.6vh;
    }

    #form-fields input {
        width: 100%;
        padding: 1.3vh 1.2vw;
        border: none;
        border-bottom: 2px solid #ccc;
        border-radius: 0;
        font-size: 1vw;
        font-family: 'Open Sans', sans-serif;
        background: transparent;
        outline: none;
        color: #111;
        transition: border-color 0.3s ease, transform 0.2s ease;
        margin-bottom: 2.5vh;
    }

    #form-fields input:focus {
        border-bottom-color: #111;
        transform: scaleX(1.01);
    }

    #form-fields input::placeholder {
        color: #bbb;
        font-style: italic;
        font-size: 0.85vw;
    }

    #nextButton {
        margin-top: 1vh;
        background: #111;
        color: white;
        border: none;
        padding: 1.3vh 3vw;
        font-size: 0.85vw;
        font-family: 'Open Sans', sans-serif;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        transition: background 0.3s ease, letter-spacing 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    #nextButton:hover {
        background: #444;
        letter-spacing: 3px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    #transaction-details {
        font-family: 'Open Sans', sans-serif;
        font-weight: 700;
        font-size: 1.3vw;
        line-height: 2.5;
        color: #111;
        letter-spacing: 0.3px;
    }

    #transaction-details span,
    #transaction-details small {
        font-family: 'Open Sans', sans-serif;
        font-weight: 800;
        font-size: 1.3vw;
        color: #111;
    }

    #status-text {
        color: #2a7a2a !important;
        font-weight: 700;
    }

    /* RIGHT: QR */
    #right-side {
        position: absolute;
        top: 50%;
        right: 8vw;
        transform: translateY(-45%);
        width: 37vw;
        z-index: 1;
        display: none;
        text-align: center;
        padding-top: 8vh;
        padding-left: 2.5vw;
        padding-right: 2.5vw;
        padding-bottom: 3vh;
    }

    #qris-image-container {
        background: white;
        padding: 1.2vw;
        border-radius: 14px;
        display: block;
        width: 100%;
    }

    #qris-image-container img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px;
        max-height: 55vh;
        object-fit: contain;
    }

    .qr-placeholder {
        width: 100%;
        height: 25vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 0.9vw;
    }

    /* Hilangkan spinner pada input number */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700;800&display=swap" rel="stylesheet">

<div id="page-wrapper">

    {{-- ===== START SCREEN ===== --}}
    <div id="startScreen">
        <img class="bg" src="{{ asset('public_storage/images/Home.png') }}" alt="background">
        <button id="startButton">Start</button>
    </div>

    {{-- ===== REGISTRATION SCREEN ===== --}}
    <div id="registrationScreen">
        <img class="bg" src="{{ asset('public_storage/images/registrasi.png') }}" alt="background">

        <div id="left-side">
            <div id="registrationForm">
                <div id="form-fields">
                    <form id="ajaxRegistrationForm">
                        @csrf
                        <div>
                            <label>Nama</label>
                            <input type="text" id="name" name="name" placeholder="Masukkan Nama" autocomplete="off" required>
                        </div>
                        <div>
                            <label>No HP</label>
                            <input type="number" id="phone" name="phone" placeholder="Masukkan nomor HP aktif" autocomplete="off" required>
                        </div>
                        <button type="button" id="nextButton">Next &rarr;</button>
                    </form>
                </div>

                <div id="transaction-details" style="display: none;">
                    <p>Nama &nbsp;&nbsp;: <span id="display_name"></span></p>
                    <p>No HP &nbsp;: <span id="display_phone"></span></p>
                    <p>Status &nbsp;: <span id="status-text">pending</span></p>
                    <p>Kode &nbsp;&nbsp;: <small id="display_code"></small></p>
                </div>
            </div>
        </div>

        <div id="right-side">
            <div id="qris-image-container">
                <div class="qr-placeholder">Loading QR...</div>
            </div>
        </div>

    </div>
</div>

<script>
    // Auto fullscreen saat halaman dimuat
    function enterFullscreen() {
        const el = document.documentElement;
        if (el.requestFullscreen) el.requestFullscreen();
        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
        else if (el.mozRequestFullScreen) el.mozRequestFullScreen();
        else if (el.msRequestFullscreen) el.msRequestFullscreen();
    }

    // Trigger fullscreen saat klik Start (butuh interaksi user)
    document.getElementById('startButton').onclick = function() {
        enterFullscreen();
        document.getElementById('startScreen').style.display = 'none';
        document.getElementById('registrationScreen').style.display = 'block';
    };

    // Cegah keluar fullscreen tidak sengaja
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            enterFullscreen();
        }
    });

    document.getElementById('nextButton').onclick = function() {
        const btnNext = document.getElementById('nextButton');
        const name = document.getElementById('name').value;
        const phone = document.getElementById('phone').value;

        if (!name || !phone) {
            alert("Harap isi Nama dan No HP");
            return;
        }

        // --- LANGKAH PENGUNCIAN ---
        // Disable tombol agar tidak bisa diklik ulang
        btnNext.disabled = true;
        btnNext.innerText = "Processing..."; // Memberi feedback visual bahwa sistem sedang bekerja
        btnNext.style.opacity = "0.5";
        btnNext.style.cursor = "not-allowed";

        const formData = new FormData();
        formData.append('name', name);
        formData.append('phone', phone);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('registration.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('form-fields').style.display = 'none';
                    document.getElementById('right-side').style.display = 'block';


                    document.getElementById('display_name').innerText = data.name;
                    document.getElementById('display_phone').innerText = data.phone;
                    document.getElementById('display_code').innerText = data.transaction_code;

                    document.getElementById('qris-image-container').innerHTML =
                        `<img src="${data.qr_url}" alt="QRIS">`;

                    startPolling(data.transaction_code);
                } else {
                    // Jika server mengembalikan error (misal validasi gagal)
                    alert("Terjadi kesalahan: " + (data.message || "Gagal registrasi"));

                    // Buka kembali tombol agar user bisa mencoba lagi
                    btnNext.disabled = false;
                    btnNext.innerText = "Next →";
                    btnNext.style.opacity = "1";
                    btnNext.style.cursor = "pointer";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Koneksi bermasalah, silakan coba lagi.");

                // Buka kembali tombol jika terjadi error jaringan agar user tidak stuck
                btnNext.disabled = false;
                btnNext.innerText = "Next →";
                btnNext.style.opacity = "1";
                btnNext.style.cursor = "pointer";
            });
    };

    function startPolling(code) {
        const interval = setInterval(() => {
            fetch("{{ url('/transactions') }}/" + code + "/status")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('status-text').innerText = data.status;
                    if (data.status === 'paid') {
                        clearInterval(interval);
                        window.location.href = "{{ url('/session') }}/" + code + "/template";
                    }
                });
        }, 3000);
    }
</script>

@endsection