@extends('layouts.app')

@section('title', 'Galeri Kadaluarsa - Atmos Archive')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">

<style>
    :root {
        --bg: #0e0e0e;
        --surface: #161616;
        --border: #272727;
        --text: #e8e4dc;
        --muted: #5a5a5a;
        --accent: #c8b89a;
        --white: #f5f2ed;
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
        font-family: 'DM Mono', monospace;
        min-height: 100vh;
        overflow: hidden;
        /* Mencegah scroll pada halaman kosong */
    }

    /* ── GRAIN OVERLAY ── */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        pointer-events: none;
        z-index: 10;
        opacity: 0.6;
    }

    /* ── LAYOUT CENTERED ── */
    .expired-wrap {
        position: relative;
        z-index: 20;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 0 24px;
        text-align: center;
    }

    /* ── ANIMASI ── */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .expired-content {
        opacity: 0;
        animation: fadeUp 0.8s ease forwards;
        max-width: 480px;
    }

    /* ── TYPOGRAPHY ── */
    .eyebrow {
        font-size: 10px;
        letter-spacing: 4px;
        color: var(--muted);
        text-transform: uppercase;
        margin-bottom: 24px;
    }

    .title {
        font-family: 'Playfair Display', serif;
        font-size: 42px;
        font-weight: 400;
        color: var(--white);
        line-height: 1.2;
        margin-bottom: 24px;
    }

    .title em {
        font-style: italic;
        color: var(--accent);
    }

    .divider-line {
        width: 40px;
        height: 1px;
        background: var(--border);
        margin: 0 auto 24px;
    }

    .description {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.8;
        letter-spacing: 0.5px;
        margin-bottom: 40px;
    }

    .action-btn:hover svg {
        transform: translateX(-4px);
    } 

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .title {
            font-size: 32px;
        }

        .description {
            font-size: 12px;
        }
    }
</style>

<div class="expired-wrap">
    <div class="expired-content">
        <div class="eyebrow">Atmos Archive</div>

        <h1 class="title">Access <em>Expired</em></h1>

        <div class="divider-line"></div>

        <p class="description">
            Maaf, galeri foto ini sudah tidak tersedia.<br>
            Sesuai kebijakan privasi dan penyimpanan sistem kami, memori dihapus secara otomatis setelah melewati batas waktu 48 jam.
        </p>
    </div>
</div>

@endsection