<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ config('app.name', 'Sistem Manajemen Parkir') }}</title>

    {{-- Google Fonts: Space Grotesk + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-ink:     #0B1120;
            --color-violet:  #6D28D9;
            --color-amber:   #F59E0B;
            --color-cloud:   #F8FAFC;
            --color-slate:   #94A3B8;
            --color-success: #10B981;
        }

        /* ── Keyframes ───────────────────────────────────────── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        @media (prefers-reduced-motion: reduce) {
            .form-panel { animation: none !important; }
            * { transition-duration: 0ms !important; }
        }

        /* ── Base ────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--color-ink);
            min-height: 100vh;
            display: flex;
        }

        /* ── Layout wrapper ──────────────────────────────────── */
        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════════════════════
           SISI KIRI — Branding panel
        ══════════════════════════════════════════════════════ */
        .brand-panel {
            display: none; /* hidden on mobile */
            flex-direction: column;
            justify-content: space-between;
            width: 60%;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(ellipse 60% 50% at 90% 10%, rgba(245,158,11,0.18) 0%, transparent 70%),
                linear-gradient(135deg, var(--color-ink) 0%, var(--color-violet) 100%);
        }

        /* subtle noise texture overlay */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .brand-top {
            position: relative;
            z-index: 1;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 9999px;
            padding: 6px 14px;
            margin-bottom: 2.5rem;
        }

        .brand-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-amber);
            box-shadow: 0 0 8px var(--color-amber);
        }

        .brand-badge-text {
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            font-weight: 500;
            color: rgba(255,255,255,0.6);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand-heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 3vw, 2.75rem);
            font-weight: 700;
            color: var(--color-cloud);
            line-height: 1.15;
            margin: 0 0 1rem 0;
        }

        .brand-heading span {
            color: var(--color-amber);
        }

        .brand-sub {
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: rgba(248,250,252,0.55);
            margin: 0;
            line-height: 1.6;
            max-width: 340px;
        }

        /* SVG barrier illustration at bottom */
        .brand-bottom {
            position: relative;
            z-index: 1;
        }

        .brand-barrier-svg {
            width: 100%;
            opacity: 0.65;
        }

        /* ══════════════════════════════════════════════════════
           SISI KANAN — Form panel
        ══════════════════════════════════════════════════════ */
        .form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 2rem 1.5rem;
            background-color: var(--color-cloud);
            animation: fadeSlideUp 220ms ease-out both;
        }

        .form-inner {
            width: 100%;
            max-width: 380px;
        }

        /* ── Form header ─────────────────────────────────────── */
        .form-eyebrow {
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--color-slate);
            margin: 0 0 1.75rem 0;
        }

        .form-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-ink);
            margin: 0 0 0.5rem 0;
            line-height: 1.2;
        }

        .form-subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            color: var(--color-slate);
            margin: 0 0 2.25rem 0;
        }

        /* ── Input fields ────────────────────────────────────── */
        .field-group {
            margin-bottom: 1.5rem;
        }

        .field-label {
            display: block;
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .field-input-wrap {
            position: relative;
        }

        .field-input {
            width: 100%;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            color: var(--color-ink);
            background: transparent;
            border: none;
            border-bottom: 2px solid #CBD5E1;
            padding: 0.5rem 0 0.5rem 0;
            outline: none;
            transition: border-color 180ms ease;
            /* make room for eye icon on password */
        }

        .field-input.has-toggle {
            padding-right: 2.25rem;
        }

        .field-input::placeholder {
            color: #CBD5E1;
        }

        .field-input:focus {
            border-color: var(--color-violet);
        }

        /* visible focus ring for keyboard nav */
        .field-input:focus-visible {
            outline: 2px solid var(--color-violet);
            outline-offset: 2px;
            border-radius: 2px;
        }

        /* password toggle button */
        .pw-toggle {
            position: absolute;
            right: 0;
            bottom: 8px;
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--color-slate);
            line-height: 0;
            transition: color 150ms ease;
            border-radius: 4px;
        }

        .pw-toggle:hover { color: var(--color-ink); }

        .pw-toggle:focus-visible {
            outline: 2px solid var(--color-violet);
            outline-offset: 2px;
        }

        /* field error message */
        .field-error {
            font-family: 'Inter', sans-serif;
            font-size: 0.78125rem;
            color: #DC2626;
            margin-top: 0.375rem;
            display: block;
        }

        /* ── Remember + submit row ───────────────────────────── */
        .remember-row {
            display: flex;
            align-items: center;
            margin-bottom: 1.75rem;
        }

        .remember-checkbox {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #CBD5E1;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        .remember-checkbox:checked {
            background-color: var(--color-violet);
            border-color: var(--color-violet);
        }

        .remember-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 1px;
            width: 6px;
            height: 9px;
            border: 2px solid #fff;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .remember-checkbox:focus-visible {
            outline: 2px solid var(--color-violet);
            outline-offset: 2px;
        }

        .remember-label {
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            color: #4B5563;
            margin-left: 0.5rem;
            cursor: pointer;
            user-select: none;
        }

        /* submit button */
        .btn-submit {
            display: block;
            width: 100%;
            padding: 0.8125rem 1rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--color-cloud);
            background-color: var(--color-ink);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: opacity 160ms ease, filter 160ms ease;
        }

        .btn-submit:hover {
            filter: brightness(1.35);
        }

        .btn-submit:focus-visible {
            outline: 2px solid var(--color-violet);
            outline-offset: 3px;
        }

        .btn-submit:active {
            opacity: 0.85;
        }

        /* forgot password link */
        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            color: var(--color-slate);
            text-decoration: none;
            transition: color 150ms ease;
        }

        .forgot-link:hover { color: var(--color-violet); }

        .forgot-link:focus-visible {
            outline: 2px solid var(--color-violet);
            outline-offset: 2px;
            border-radius: 3px;
        }

        /* session status */
        .session-status {
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            color: var(--color-success);
            background: rgba(16,185,129,0.08);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 6px;
            padding: 0.625rem 0.875rem;
            margin-bottom: 1.5rem;
        }

        /* ── Desktop breakpoint (≥ 768px) ────────────────────── */
        @media (min-width: 768px) {
            .brand-panel {
                display: flex;
            }
            .form-panel {
                width: 40%;
                padding: 3rem 2.5rem;
            }
        }

        /* ── Mobile: gradient bg for form panel ─────────────── */
        @media (max-width: 767px) {
            .form-panel {
                background:
                    radial-gradient(ellipse 70% 40% at 100% 0%, rgba(245,158,11,0.15) 0%, transparent 65%),
                    linear-gradient(150deg, var(--color-ink) 0%, var(--color-violet) 100%);
                padding: 2.5rem 1.5rem;
            }

            .form-inner {
                max-width: 420px;
            }

            .form-eyebrow   { color: rgba(248,250,252,0.5); }
            .form-title     { color: var(--color-cloud); }
            .form-subtitle  { color: rgba(248,250,252,0.5); }
            .field-label    { color: rgba(248,250,252,0.75); }
            .field-input    { color: var(--color-cloud); border-bottom-color: rgba(255,255,255,0.2); }
            .field-input:focus { border-color: var(--color-amber); }
            .field-input::placeholder { color: rgba(255,255,255,0.2); }
            .field-input:focus-visible { outline-color: var(--color-amber); }
            .remember-label { color: rgba(248,250,252,0.65); }
            .remember-checkbox { border-color: rgba(255,255,255,0.3); }
            .remember-checkbox:checked { background-color: var(--color-amber); border-color: var(--color-amber); }
            .pw-toggle  { color: rgba(248,250,252,0.5); }
            .pw-toggle:hover { color: var(--color-cloud); }
            .btn-submit { background-color: var(--color-amber); color: var(--color-ink); }
            .forgot-link { color: rgba(248,250,252,0.45); }
            .forgot-link:hover { color: var(--color-cloud); }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- ══════════════════════════════════════
         SISI KIRI — Branding / Identity panel
    ═══════════════════════════════════════ --}}
    <aside class="brand-panel" aria-hidden="true">
        <div class="brand-top">
            <div class="brand-badge">
                <span class="brand-badge-dot"></span>
                <span class="brand-badge-text">Sistem Aktif</span>
            </div>

            <h1 class="brand-heading">
                Selamat datang<br><span>kembali.</span>
            </h1>
            <p class="brand-sub">Pantau setiap kendaraan,<br>setiap saat.</p>
        </div>

        {{-- SVG: Boom Barrier (palang parkir) abstrak/geometris --}}
        <div class="brand-bottom">
            <svg class="brand-barrier-svg" viewBox="0 0 480 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                {{-- Ground line --}}
                <line x1="0" y1="148" x2="480" y2="148" stroke="rgba(248,250,252,0.1)" stroke-width="1"/>

                {{-- === Barrier unit kiri === --}}
                {{-- Tiang vertikal --}}
                <rect x="36" y="60" width="10" height="88" rx="3" fill="rgba(248,250,252,0.08)" stroke="rgba(248,250,252,0.15)" stroke-width="1"/>
                {{-- Housing kotak --}}
                <rect x="24" y="46" width="34" height="22" rx="4" fill="rgba(109,40,217,0.4)" stroke="rgba(248,250,252,0.18)" stroke-width="1"/>
                {{-- Lampu housing --}}
                <circle cx="41" cy="57" r="4" fill="var(--color-amber)" opacity="0.9"/>
                <circle cx="41" cy="57" r="7" fill="var(--color-amber)" opacity="0.18"/>
                {{-- Palang horizontal — terangkat 30° --}}
                <g transform="rotate(-28, 41, 46)">
                    <rect x="41" y="42" width="170" height="7" rx="3" fill="none" stroke="var(--color-amber)" stroke-width="2"/>
                    {{-- Striping palang: merah-putih abstrak --}}
                    <rect x="55"  y="42" width="14" height="7" rx="0" fill="rgba(245,158,11,0.25)"/>
                    <rect x="83"  y="42" width="14" height="7" rx="0" fill="rgba(245,158,11,0.25)"/>
                    <rect x="111" y="42" width="14" height="7" rx="0" fill="rgba(245,158,11,0.25)"/>
                    <rect x="139" y="42" width="14" height="7" rx="0" fill="rgba(245,158,11,0.25)"/>
                    <rect x="167" y="42" width="14" height="7" rx="0" fill="rgba(245,158,11,0.25)"/>
                    {{-- Ujung palang --}}
                    <circle cx="211" cy="45.5" r="5" fill="var(--color-amber)" opacity="0.7"/>
                </g>

                {{-- === Barrier unit kanan (dalam) — tertutup --}}
                <rect x="310" y="60" width="10" height="88" rx="3" fill="rgba(248,250,252,0.06)" stroke="rgba(248,250,252,0.1)" stroke-width="1"/>
                <rect x="298" y="46" width="34" height="22" rx="4" fill="rgba(109,40,217,0.25)" stroke="rgba(248,250,252,0.12)" stroke-width="1"/>
                <circle cx="315" cy="57" r="3" fill="var(--color-amber)" opacity="0.4"/>
                {{-- Palang horizontal — horizontal (tertutup) --}}
                <rect x="315" y="42" width="150" height="7" rx="3" fill="none" stroke="rgba(245,158,11,0.35)" stroke-width="1.5"/>
                <rect x="329" y="42" width="12" height="7" fill="rgba(245,158,11,0.12)"/>
                <rect x="355" y="42" width="12" height="7" fill="rgba(245,158,11,0.12)"/>
                <rect x="381" y="42" width="12" height="7" fill="rgba(245,158,11,0.12)"/>
                <rect x="407" y="42" width="12" height="7" fill="rgba(245,158,11,0.12)"/>
                <rect x="433" y="42" width="12" height="7" fill="rgba(245,158,11,0.12)"/>
                <circle cx="465" cy="45.5" r="4" fill="var(--color-amber)" opacity="0.3"/>

                {{-- Garis marka jalan (bawah) --}}
                <rect x="0"   y="155" width="60" height="4" rx="2" fill="rgba(248,250,252,0.06)"/>
                <rect x="80"  y="155" width="60" height="4" rx="2" fill="rgba(248,250,252,0.06)"/>
                <rect x="160" y="155" width="60" height="4" rx="2" fill="rgba(248,250,252,0.06)"/>
                <rect x="240" y="155" width="60" height="4" rx="2" fill="rgba(248,250,252,0.06)"/>
                <rect x="320" y="155" width="60" height="4" rx="2" fill="rgba(248,250,252,0.06)"/>
                <rect x="400" y="155" width="60" height="4" rx="2" fill="rgba(248,250,252,0.06)"/>

                {{-- Efek sorot lampu sodium dari kiri atas --}}
                <ellipse cx="80" cy="20" rx="90" ry="30" fill="rgba(245,158,11,0.06)"/>
            </svg>
        </div>
    </aside>

    {{-- ══════════════════════════════════════
         SISI KANAN — Form panel
    ═══════════════════════════════════════ --}}
    <main class="form-panel" id="main-content">
        <div class="form-inner">

            {{-- Session status (e.g., password reset success) --}}
            @if (session('status'))
                <div class="session-status" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <p class="form-eyebrow">Masuk ke Sistem</p>
            <h2 class="form-title">{{ config('app.name', 'Sistem Manajemen Parkir') }}</h2>
            <p class="form-subtitle">Masukkan kredensial Anda untuk melanjutkan.</p>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- ── Email ──────────────────────────────── --}}
                <div class="field-group">
                    <label class="field-label" for="email">Alamat Email</label>
                    <div class="field-input-wrap">
                        <input
                            class="field-input"
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                            autofocus
                            autocomplete="username"
                            aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                        >
                    </div>
                    @error('email')
                        <span class="field-error" id="email-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ── Password ────────────────────────────── --}}
                <div class="field-group">
                    <label class="field-label" for="password">Kata Sandi</label>
                    <div class="field-input-wrap">
                        <input
                            class="field-input has-toggle"
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        >
                        <button
                            type="button"
                            class="pw-toggle"
                            id="pw-toggle-btn"
                            aria-label="Tampilkan kata sandi"
                            aria-pressed="false"
                            onclick="togglePassword()"
                        >
                            {{-- Eye icon (default: show) --}}
                            <svg id="icon-eye-on" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- Eye-off icon (hidden by default) --}}
                            <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="field-error" id="password-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ── Remember me ─────────────────────────── --}}
                <div class="remember-row">
                    <input
                        type="checkbox"
                        class="remember-checkbox"
                        id="remember_me"
                        name="remember"
                    >
                    <label for="remember_me" class="remember-label">Ingat saya</label>
                </div>

                {{-- ── Submit ──────────────────────────────── --}}
                <button type="submit" class="btn-submit">
                    Masuk
                </button>

                {{-- ── Forgot password ─────────────────────── --}}
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Lupa kata sandi?
                    </a>
                @endif
            </form>

        </div>
    </main>

</div>

<script>
    function togglePassword() {
        const input   = document.getElementById('password');
        const btn     = document.getElementById('pw-toggle-btn');
        const iconOn  = document.getElementById('icon-eye-on');
        const iconOff = document.getElementById('icon-eye-off');

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        iconOn.style.display  = isHidden ? 'none'  : '';
        iconOff.style.display = isHidden ? ''      : 'none';

        btn.setAttribute('aria-label',   isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        btn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
    }
</script>

</body>
</html>
