@extends('layouts.app')

@section('title', 'Galeri Fotomu')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gifshot/0.3.2/gifshot.min.js"></script>

<style>
    :root {
        --bg: #cdc9c1;
        --surface: #c2beb6;
        --border-dark: #5a564f;
        --border-mid: #8a867e;
        --text: #1a1916;
        --muted: #6e6a62;
        --red: #d42b2b;
        --dark: #1a1916;
        --white: #f5f2ed;
        --pill: #1a1916;
    }

    *,
    *::before,
    *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        background: var(--bg);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* ── HALFTONE DOT PATTERN ── */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(circle, rgba(0, 0, 0, 0.09) 1px, transparent 1px);
        background-size: 9px 9px;
        pointer-events: none;
        z-index: 0;
    }

    .gallery-wrap {
        position: relative;
        z-index: 1;
        max-width: 760px;
        margin: 0 auto;
        padding: 20px 20px 60px;
    }

    /* ══════════════════════════════
       OUTER FRAME with corner marks
    ══════════════════════════════ */
    .main-frame {
        background: var(--surface);
        border: 1px solid var(--border-dark);
        position: relative;
        padding: 48px 28px 32px;
        opacity: 0;
        animation: fadeUp 0.5s ease forwards;
    }

    /* Four corner brackets */
    .main-frame::before,
    .main-frame::after,
    .cf-bl,
    .cf-br {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        border-color: var(--dark);
        border-style: solid;
    }

    .main-frame::before {
        top: 6px;
        left: 6px;
        border-width: 2px 0 0 2px;
    }

    .main-frame::after {
        top: 6px;
        right: 6px;
        border-width: 2px 2px 0 0;
    }

    .cf-bl {
        bottom: 6px;
        left: 6px;
        border-width: 0 0 2px 2px;
    }

    .cf-br {
        bottom: 6px;
        right: 6px;
        border-width: 0 2px 2px 0;
    }

    /* Sparkle diamonds near corners */
    .sp {
        position: absolute;
        font-size: 9px;
        color: var(--dark);
        user-select: none;
    }

    .sp-tl {
        top: 22px;
        left: 30px;
    }

    .sp-tr {
        top: 22px;
        right: 30px;
    }

    .sp-bl {
        bottom: 22px;
        left: 30px;
    }

    .sp-br {
        bottom: 22px;
        right: 30px;
    }

    /* ══════════════════════
       HEADER
    ══════════════════════ */
    .site-header {
        text-align: center;
        padding-bottom: 26px;
        border-bottom: 1px solid var(--border-dark);
        margin-bottom: 32px;
    }

    .logo-brand {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.8rem, 11vw, 4.6rem);
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -1px;
        line-height: 0.88;
        color: var(--dark);
    }

    .logo-star {
        display: block;
        font-size: 13px;
        color: var(--dark);
        margin: 10px auto;
    }

    .logo-tagline {
        font-size: 12px;
        color: var(--muted);
        line-height: 1.65;
        max-width: 380px;
        margin: 0 auto;
    }

    .logo-tagline strong {
        color: var(--dark);
    }

    .session-pill {
        display: inline-block;
        margin-top: 14px;
        border: 1px solid var(--border-dark);
        border-radius: 100px;
        padding: 5px 20px;
        font-size: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--dark);
    }

    /* ══════════════════════
       SECTION HEADER
    ══════════════════════ */
    .sec-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sec-num {
        font-size: 9px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: var(--muted);
    }

    .sec-title {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--dark);
    }

    .sec-line {
        flex: 1;
        height: 1px;
        background: var(--border-dark);
    }

    /* ══════════════════════
       DIVIDER
    ══════════════════════ */
    .divider {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 36px 0;
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--muted);
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-dark);
    }

    /* ══════════════════════════════
       TOP 2-COL  (Final + GIF)
    ══════════════════════════════ */
    .top-section {
        opacity: 0;
        animation: fadeUp 0.5s 0.1s ease forwards;
    }

    .top-grid {
        display: grid;
        grid-template-columns: 1.55fr 0.8fr;
        gap: 12px;
        align-items: start;
    }

    /* ── FINAL PHOTO ZOOM OVERLAY ── */
    .final-photo-wrap {
        position: absolute;
        inset: 0;
    }

    .final-zoom-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
        z-index: 3;
    }

    .final-photo-wrap:hover .final-zoom-overlay {
        background: rgba(0, 0, 0, 0.35);
    }

    .final-zoom-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        color: var(--dark);
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 0.3s, transform 0.3s;
    }

    .final-photo-wrap:hover .final-zoom-icon {
        opacity: 1;
        transform: scale(1);
    }

    /* ── NEWSPAPER CARD ── */
    .news-card {
        background: var(--white);
        border: 1px solid var(--border-dark);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .news-head {
        background: var(--red);
        padding: 9px 12px 7px;
    }

    .news-head h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1rem, 3.8vw, 1.35rem);
        font-weight: 900;
        text-transform: uppercase;
        color: #fff;
        line-height: 1;
    }

    .news-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        margin-top: 5px;
    }

    .news-tags span {
        font-size: 7px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.9);
        padding: 1px 6px;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* slideshow frame inside news card */
    .news-slides {
        position: relative;
        background: #111;
        overflow: hidden;
        aspect-ratio: 3/4;
    }

    .news-slides::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.35) 0%, transparent 40%);
        z-index: 2;
        pointer-events: none;
    }

    .mySlide {
        opacity: 0 !important;
        z-index: 0 !important;
    }

    .mySlide.active {
        opacity: 1 !important;
        z-index: 1 !important;
    }

    .slide-counter {
        position: absolute;
        bottom: 8px;
        right: 10px;
        z-index: 3;
        font-size: 9px;
        letter-spacing: 2px;
        color: rgba(255, 255, 255, 0.55);
        background: rgba(0, 0, 0, 0.3);
        padding: 2px 7px;
    }

    .news-foot {
        padding: 6px 12px;
        background: var(--dark);
        font-size: 7px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
    }

    /* ── GIF CARD ── */
    .gif-card {
        background: var(--white);
        border: 1px solid var(--border-dark);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .gif-head {
        background: var(--dark);
        padding: 9px 12px 7px;
    }

    .gif-head h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1rem, 3.8vw, 1.35rem);
        font-weight: 900;
        text-transform: uppercase;
        color: #fff;
        line-height: 1;
    }

    .gif-head-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        margin-top: 5px;
    }

    .gif-head-tags span {
        font-size: 7px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        padding: 1px 6px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* GIF preview area — grid of raw photos OR actual gif */
    .gif-preview {
        position: relative;
        background: #eae7e0;
        overflow: hidden;
        aspect-ratio: 3/4;
    }

    /* actual animated gif fills the area */
    .gif-preview .gif-anim {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* 2x2 grid of raw photos as fallback */
    .gif-raw-grid {
        position: absolute;
        inset: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 2px;
        background: #000;
    }

    .gif-raw-grid img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(100%);
    }

    /* "gif" label overlay bottom-right */
    .gif-badge {
        position: absolute;
        bottom: 8px;
        right: 8px;
        z-index: 4;
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 900;
        color: #fff;
        background: rgba(0, 0, 0, 0.5);
        padding: 1px 9px 3px;
        letter-spacing: 1px;
        pointer-events: none;
    }

    /* "PROCESSING" overlay when no gif */
    .gif-processing {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.25);
    }

    .gif-processing span {
        font-size: 8px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.75);
        background: rgba(0, 0, 0, 0.4);
        padding: 5px 12px;
    }

    .gif-foot {
        padding: 6px 12px;
        background: var(--dark);
        font-size: 7px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
    }

    /* ── THUMBNAIL STRIP (below top cards) ── */
    .thumb-strip {
        display: flex;
        gap: 5px;
        overflow-x: auto;
        scrollbar-width: none;
        padding: 8px 0 2px;
    }

    .thumb-strip::-webkit-scrollbar {
        display: none;
    }

    .thumb-item {
        flex-shrink: 0;
        width: 44px;
        cursor: pointer;
        border: 2px solid transparent;
        overflow: hidden;
        opacity: 0.45;
        transition: border-color 0.2s, opacity 0.2s;
    }

    .thumb-item.active {
        border-color: var(--red);
        opacity: 1;
    }

    .thumb-item img {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        display: block;
    }

    /* ── DOWNLOAD PILL BUTTON ── */
    .btn-pill {
        display: block;
        text-align: center;
        padding: 11px 16px;
        background: var(--pill);
        color: var(--white);
        font-family: 'DM Sans', sans-serif;
        font-size: 10px;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 100px;
        cursor: pointer;
        transition: background 0.2s;
        margin: 14px 0 0;
    }

    .btn-pill:hover {
        background: var(--red);
    }

    .btn-pill-wide {
        display: block;
        text-align: center;
        padding: 13px 24px;
        background: var(--pill);
        color: var(--white);
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        letter-spacing: 3px;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 100px;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 18px;
    }

    .btn-pill-wide:hover {
        background: var(--red);
    }

    /* ══════════════════════════════
       BOTTOM: RAW PHOTOS 4-col GRID
    ══════════════════════════════ */
    .raw-section {
        opacity: 0;
        animation: fadeUp 0.5s 0.25s ease forwards;
    }

    .raw-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 3px;
        background: var(--border-dark);
        border: 1px solid var(--border-dark);
    }

    .raw-item {
        position: relative;
        overflow: hidden;
        background: #b0aca4;
        cursor: pointer;
        aspect-ratio: 4/3;
    }

    .raw-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        filter: grayscale(15%);
        transition: transform 0.4s ease, filter 0.3s;
    }

    .raw-item:hover img {
        transform: scale(1.07);
        filter: grayscale(0%);
    }

    .raw-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0);
        display: flex;
        align-items: flex-end;
        padding: 6px;
        transition: background 0.25s;
        z-index: 2;
    }

    .raw-item:hover .raw-overlay {
        background: rgba(212, 43, 43, 0.15);
    }

    .raw-save {
        display: none;
        font-size: 7px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #fff;
        text-decoration: none;
        background: var(--red);
        padding: 3px 7px;
    }

    .raw-item:hover .raw-save {
        display: block;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        padding: 38px 0;
        text-align: center;
        color: var(--muted);
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        border: 1px dashed var(--border-dark);
    }

    /* ── FOOTER ── */
    .site-footer {
        margin-top: 32px;
        padding-top: 16px;
        border-top: 1px solid var(--border-dark);
        display: flex;
        justify-content: space-between;
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--muted);
    }

    /* ── LIGHTBOX ── */
    .lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.96);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .lightbox.open {
        opacity: 1;
        pointer-events: all;
    }

    .lightbox img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        transform: scale(0.95);
        transition: transform 0.35s ease;
    }

    .lightbox.open img {
        transform: scale(1);
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 24px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: rgba(255, 255, 255, 0.5);
        font-size: 20px;
        background: none;
        border: none;
        z-index: 10000;
        transition: color 0.2s;
    }

    .lightbox-close:hover {
        color: #fff;
    }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 580px) {
        .gallery-wrap {
            padding: 12px 12px 50px;
        }

        .main-frame {
            padding: 32px 14px 24px;
        }

        .top-grid {
            grid-template-columns: 1fr;
        }

        .raw-grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .raw-item:hover .raw-save {
            display: block;
        }
    }

    @media (max-width: 768px) {

        /* Always show save link on mobile */
        .raw-save {
            display: block;
        }
    }
</style>

{{-- LIGHTBOX --}}
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lb-close">✕</button>
    <img id="lb-img" alt="Fullscreen">
</div>

<div class="gallery-wrap">
    <div class="main-frame">
        <div class="cf-bl"></div>
        <div class="cf-br"></div>
        <span class="sp sp-tl">✦</span>
        <span class="sp sp-tr">✦</span>
        <span class="sp sp-bl">✦</span>
        <span class="sp sp-br">✦</span>

        {{-- ══ HEADER ══ --}}
        <header class="site-header">
            <div class="logo-brand">Atmoz<br>Archive</div>
            <span class="logo-star">✦</span>
            <p class="logo-tagline">
                "Thanks for capture the Atmosphere with us,<br>
                <strong>{{ $transaction->code }}</strong>!
                Your exclusive AtmozArchive prints are ready to download!"
            </p>
            <div class="session-pill">Session ID: {{ $transaction->code }}</div>
        </header>

        {{-- ══ TOP SECTION: Final + GIF side by side ══ --}}
        <section class="top-section">
            <div class="top-grid">

                {{-- ── LEFT: Single Final Photo ── --}}
                <div>
                    <div class="news-card">
                        <div class="news-head">
                            <h2>Atmos Archive</h2>
                            <div class="news-tags">
                                <span>Final Print</span>
                                <span>{{ now()->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="news-slides" style="aspect-ratio:3/4;">
                            @if(!$finalPhotos->isEmpty())
                            <div class="final-photo-wrap"
                                data-src="{{ asset('storage/'.$finalPhotos->first()->file_path) }}"
                                id="final-photo-trigger"
                                style="position:absolute;inset:0;cursor:pointer;">
                                <img style="width:100%;height:100%;object-fit:cover;display:block;"
                                    src="{{ asset('storage/'.$finalPhotos->first()->file_path) }}"
                                    alt="Final Photo">
                                {{-- Zoom overlay on hover --}}
                                <div class="final-zoom-overlay">
                                    <span class="final-zoom-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8" />
                                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                            <line x1="11" y1="8" x2="11" y2="14" />
                                            <line x1="8" y1="11" x2="14" y2="11" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            @else
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.3);font-size:10px;letter-spacing:2px;">NO PHOTO</div>
                            @endif
                        </div>

                        <div class="news-foot">Session · {{ $transaction->code }}</div>
                    </div>

                    <a href="{{ !$finalPhotos->isEmpty() ? asset('storage/'.$finalPhotos->first()->file_path) : '#' }}"
                        download class="btn-pill">
                        ↓ &nbsp;Download your newspapers
                    </a>
                </div>

                {{-- ── RIGHT: Raw Photos Slideshow ── --}}
                <div>
                    <div class="gif-card">
                        <div class="gif-head">
                            <h2>Your Moments</h2>
                            <div class="gif-head-tags">
                                <span>Raw Captures</span><span>{{ $photos->count() }} Photos</span>
                            </div>
                        </div>

                        <div class="gif-preview" style="aspect-ratio:3/4;">
                            @if(!$photos->isEmpty())
                            @foreach($photos as $i => $photo)
                            <img class="mySlide {{ $i === 0 ? 'active' : '' }}"
                                src="{{ asset('storage/'.$photo->file_path) }}"
                                alt="Raw {{ $i+1 }}" loading="lazy"
                                style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity 0.6s ease;z-index:0;">
                            @endforeach
                            @else
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(0,0,0,0.3);font-size:10px;letter-spacing:2px;">NO PHOTO</div>
                            @endif
                            <div class="slide-counter" id="slide-counter">1 / {{ $photos->count() }}</div>
                        </div>

                        <div class="gif-foot">Session · {{ $transaction->code }}</div>
                    </div>

                    {{-- Thumbnail strip raw photos --}}
                    @if($photos->count() > 1)
                    <div class="thumb-strip" id="thumb-strip">
                        @foreach($photos as $i => $photo)
                        <div class="thumb-item {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                            <img src="{{ asset('storage/'.$photo->file_path) }}" loading="lazy">
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <button id="btn-generate-gif" class="btn-pill" style="width: 100%; border: none; font-family: 'DM Sans', sans-serif;">
                        ↓ &nbsp;Generate & Download GIF
                    </button>
                </div>

            </div>
        </section>

        {{-- ══ DIVIDER ══ --}}
        <div class="divider">Raw Captures</div>

        {{-- ══ BOTTOM: Raw Photos 4-col grid ══ --}}
        <section class="raw-section">
            <div class="sec-head">
                <span class="sec-num">02</span>
                <h2 class="sec-title">All Your Photos</h2>
                <div class="sec-line"></div>
            </div>

            @if($photos->isEmpty())
            <div class="empty-state">Tidak ada foto tersedia</div>
            @else
            <div class="raw-grid-4">
                @foreach($photos as $i => $photo)
                <div class="raw-item" data-src="{{ asset('storage/'.$photo->file_path) }}">
                    <img src="{{ asset('storage/'.$photo->file_path) }}"
                        alt="Raw {{ $i+1 }}" loading="lazy">
                    <div class="raw-overlay">
                        <a href="{{ asset('storage/'.$photo->file_path) }}"
                            download class="raw-save"
                            onclick="event.stopPropagation()">Download ↓</a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pastikan URL mengarah ke route yang akan kita buat --}}
            <a href="{{ url('/gallery/'.$transaction->code.'/download-zip') }}" class="btn-pill-wide">
                ↓ &nbsp;Download all raw photos (.zip)
            </a>
            @endif
        </section>

        {{-- ══ FOOTER ══ --}}
        <footer class="site-footer">
            <span>Atmos Archive Studio</span>
            <span>{{ now()->format('d · m · Y') }}</span>
        </footer>

    </div>{{-- .main-frame --}}
</div>{{-- .gallery-wrap --}}


@php
$imageUrls = $photos->map(function($photo) {
return asset('storage/' . $photo->file_path);
});
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── SLIDESHOW (Final Photos) ──
        const slides = document.querySelectorAll('.mySlide');
        const thumbItems = document.querySelectorAll('.thumb-item');
        const counter = document.getElementById('slide-counter');
        const strip = document.getElementById('thumb-strip');
        let current = 0,
            timer = null;

        function goToSlide(idx) {
            if (!slides.length) return;
            slides[current].classList.remove('active');
            thumbItems[current]?.classList.remove('active');
            current = (idx + slides.length) % slides.length;
            slides[current].classList.add('active');
            thumbItems[current]?.classList.add('active');
            if (counter) counter.textContent = `${current + 1} / ${slides.length}`;
            // Scroll thumb strip
            const t = thumbItems[current];
            if (t && strip) {
                strip.scrollTo({
                    left: t.offsetLeft - strip.offsetLeft - strip.clientWidth / 2 + t.clientWidth / 2,
                    behavior: 'smooth'
                });
            }
        }

        function startAuto() {
            timer = setInterval(() => goToSlide(current + 1), 1400);
        }

        function resetAuto() {
            clearInterval(timer);
            startAuto();
        }

        if (slides.length > 1) startAuto();
        thumbItems.forEach((th, i) => th.addEventListener('click', () => {
            goToSlide(i);
            resetAuto();
        }));

        // ── LIGHTBOX ──
        const lb = document.getElementById('lightbox');
        const lbImg = document.getElementById('lb-img');
        const lbClose = document.getElementById('lb-close');

        function openLb(src) {
            lbImg.src = src;
            lb.classList.add('open');
        }

        function closeLb() {
            lb.classList.remove('open');
            setTimeout(() => lbImg.removeAttribute('src'), 300);
        }

        document.querySelectorAll('.raw-item').forEach(item => {
            item.addEventListener('click', e => {
                if (e.target.closest('a')) return;
                const src = item.dataset.src;
                if (src) openLb(src);
            });
        });

        // Lightbox for final photo (left card)
        const finalTrigger = document.getElementById('final-photo-trigger');
        if (finalTrigger) {
            finalTrigger.addEventListener('click', () => {
                const src = finalTrigger.dataset.src;
                if (src) openLb(src);
            });
        }

        lbClose.addEventListener('click', closeLb);
        lb.addEventListener('click', e => {
            if (e.target === lb) closeLb();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeLb();
        });
    });

    // ── GENERATE GIF SECARA OTOMATIS (CLIENT-SIDE) ──
    const btnGif = document.getElementById('btn-generate-gif');

    if (btnGif) {
        btnGif.addEventListener('click', function() {
            // 1. Ubah tulisan tombol agar user tahu sistem sedang bekerja
            const originalText = this.innerHTML;
            this.innerHTML = '⏳ Processing... Please wait';
            this.disabled = true;
            this.style.opacity = '0.7';

            // 2. Ambil semua URL gambar raw dari Laravel PHP
            const imageUrls = JSON.parse('{!! json_encode($imageUrls) !!}') || [];

            // 3. Rakit gambar menjadi GIF menggunakan Gifshot
            gifshot.createGIF({
                images: imageUrls,
                interval: 0.5, // Waktu pergantian per frame (0.5 detik)
                gifWidth: 600, // Resolusi lebar GIF
                gifHeight: 800 // Resolusi tinggi GIF (Rasio 3:4)
            }, function(obj) {
                if (!obj.error) {
                    // 4. Jika berhasil, download file otomatis
                    const imageBase64 = obj.image;
                    const a = document.createElement('a');
                    a.href = imageBase64;
                    a.download = 'Atmoz_Moment_{{ $transaction->code }}.gif';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                } else {
                    alert('Oops! Gagal merakit GIF. Pastikan gambar tersedia.');
                }

                // Kembalikan tombol ke bentuk semula
                btnGif.innerHTML = originalText;
                btnGif.disabled = false;
                btnGif.style.opacity = '1';
            });
        });
    }
</script>

@endsection