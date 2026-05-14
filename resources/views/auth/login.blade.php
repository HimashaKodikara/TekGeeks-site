@extends('layouts.base', ['pageTitle' => 'Login'])

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

    :root {
        --tg-dark:    #05080f;
        --tg-navy:    #0b1220;
        --tg-card:    #0f1828;
        --tg-border:  rgba(255,255,255,0.07);
        --tg-orange:  #f97316;
        --tg-orange2: #fb923c;
        --tg-blue:    #38bdf8;
        --tg-muted:   rgba(255,255,255,0.45);
        --tg-text:    rgba(255,255,255,0.88);
    }

    * { box-sizing: border-box; }

    body {
        background: var(--tg-dark);
        font-family: 'DM Sans', sans-serif;
        color: var(--tg-text);
        min-height: 100vh;
        margin: 0;
    }

    /* ── Full-screen split layout ── */
    .login-wrapper {
        min-height: 100vh;
        display: flex;
        flex-direction: row;
    }

    /* ── LEFT PANEL ── */
    .login-left {
        flex: 0 0 55%;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 3rem 3.5rem;
        background: var(--tg-dark);
    }

    /* Background image layer */
    .login-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('/img/cms_bg.jpg');
        background-size: cover;
        background-position: center top;
        opacity: 0.35;
        z-index: 0;
    }

    /* Dark gradient overlay */
    .login-left::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            160deg,
            rgba(5,8,15,0.1) 0%,
            rgba(5,8,15,0.55) 45%,
            rgba(5,8,15,0.97) 100%
        );
        z-index: 1;
    }

    /* Orange accent glow – top right */
    .left-glow {
        position: absolute;
        top: -80px;
        right: -100px;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(249,115,22,0.22) 0%, transparent 70%);
        z-index: 1;
        pointer-events: none;
    }

    /* Blue accent glow – bottom left */
    .left-glow-blue {
        position: absolute;
        bottom: 40px;
        left: -60px;
        width: 340px;
        height: 340px;
        background: radial-gradient(circle, rgba(56,189,248,0.14) 0%, transparent 70%);
        z-index: 1;
        pointer-events: none;
    }

    .left-content {
        position: relative;
        z-index: 2;
    }

    .left-logo {
        position: absolute;
        top: 2.5rem;
        left: 3.5rem;
        z-index: 2;
    }

    .left-logo img {
        height: 38px;
        width: auto;
    }

    .left-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(249,115,22,0.12);
        border: 1px solid rgba(249,115,22,0.35);
        border-radius: 100px;
        padding: 0.3rem 0.9rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.72rem;
        font-weight: 500;
        color: var(--tg-orange2);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 1.4rem;
    }

    .left-badge span.dot {
        width: 6px;
        height: 6px;
        background: var(--tg-orange);
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(0.75); }
    }

    .left-heading {
        font-family: 'Syne', sans-serif;
        font-size: clamp(2.2rem, 3.5vw, 3.4rem);
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -0.02em;
        color: #fff;
        margin: 0 0 1.2rem;
    }

    .left-heading .accent {
        color: var(--tg-orange);
    }

    .left-desc {
        font-size: 0.92rem;
        line-height: 1.7;
        color: var(--tg-muted);
        max-width: 480px;
        margin-bottom: 0.85rem;
    }

    .left-warning {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        background: rgba(249,115,22,0.07);
        border: 1px solid rgba(249,115,22,0.2);
        border-left: 3px solid var(--tg-orange);
        border-radius: 6px;
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        color: rgba(255,255,255,0.5);
        line-height: 1.55;
        margin-top: 1.5rem;
    }

    .left-warning svg {
        flex-shrink: 0;
        margin-top: 1px;
        color: var(--tg-orange);
    }

    /* ── RIGHT PANEL ── */
    .login-right {
        flex: 0 0 45%;
        background: var(--tg-navy);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 2rem;
        position: relative;
        overflow: hidden;
    }

    /* Subtle grid pattern */
    .login-right::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
        background-size: 40px 40px;
        z-index: 0;
    }

    .right-glow {
        position: absolute;
        bottom: -100px;
        right: -100px;
        width: 360px;
        height: 360px;
        background: radial-gradient(circle, rgba(249,115,22,0.12) 0%, transparent 70%);
        z-index: 0;
        pointer-events: none;
    }

    .login-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 420px;
        background: rgba(15,24,40,0.9);
        border: 1px solid var(--tg-border);
        border-radius: 16px;
        padding: 2.5rem 2.2rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 24px 60px rgba(0,0,0,0.45), 0 0 0 1px rgba(255,255,255,0.04);
    }

    .card-logo {
        display: block;
        margin-bottom: 1.6rem;
    }

    .card-logo img {
        height: 32px;
        width: auto;
    }

    .card-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.55rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: #fff;
        margin: 0 0 0.2rem;
    }

    .card-subtitle {
        font-size: 0.83rem;
        color: var(--tg-muted);
        margin-bottom: 1.6rem;
    }

    .divider {
        border: none;
        border-top: 1px solid var(--tg-border);
        margin-bottom: 1.6rem;
    }

    /* Form */
    .form-label {
        font-size: 0.78rem;
        font-weight: 500;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.5);
        margin-bottom: 0.45rem;
    }

    .form-control {
        background: rgba(255,255,255,0.04) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        border-radius: 8px !important;
        color: #fff !important;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem;
        height: 48px;
        padding: 0 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control::placeholder { color: rgba(255,255,255,0.2) !important; }

    .form-control:focus {
        background: rgba(255,255,255,0.06) !important;
        border-color: rgba(249,115,22,0.55) !important;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.12) !important;
        outline: none;
    }

    .form-control.is-invalid {
        border-color: #f87171 !important;
    }

    .invalid-feedback {
        font-size: 0.78rem;
        color: #f87171;
    }

    /* Password toggle */
    .input-group { position: relative; }

    .pw-toggle {
        position: absolute;
        right: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: var(--tg-muted);
        z-index: 5;
        display: flex;
        align-items: center;
        transition: color 0.2s;
    }

    .pw-toggle:hover { color: var(--tg-orange); }

    #password { padding-right: 2.8rem; }

    /* Submit button */
    .btn-signin {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, var(--tg-orange) 0%, #ea580c 100%);
        border: none;
        border-radius: 8px;
        font-family: 'Syne', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #fff;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 20px rgba(249,115,22,0.35);
    }

    .btn-signin::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .btn-signin:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 28px rgba(249,115,22,0.5);
    }

    .btn-signin:hover::before { opacity: 1; }

    .btn-signin:active { transform: translateY(0); }

    /* Forgot password */
    .forgot-link {
        display: block;
        text-align: center;
        font-size: 0.8rem;
        color: var(--tg-muted);
        text-decoration: none;
        transition: color 0.2s;
        margin-top: 1rem;
    }

    .forgot-link:hover { color: var(--tg-orange2); }

    /* Security note */
    .security-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        font-size: 0.73rem;
        color: rgba(255,255,255,0.25);
        margin-top: 1.6rem;
        padding-top: 1.4rem;
        border-top: 1px solid var(--tg-border);
    }

    .security-note svg { color: rgba(255,255,255,0.3); }

    /* ── RESPONSIVE ── */
    @media (max-width: 991px) {
        .login-wrapper { flex-direction: column; }

        .login-left {
            flex: none;
            padding: 5rem 2rem 2.5rem;
            min-height: 320px;
        }

        .left-logo { left: 2rem; top: 1.8rem; }

        .left-heading { font-size: 2rem; }

        .login-right {
            flex: none;
            padding: 2rem 1.25rem 3rem;
        }
    }

    @media (max-width: 575px) {
        .login-card { padding: 2rem 1.5rem; }
        .login-left { padding: 5rem 1.5rem 2rem; }
    }
</style>

<div class="login-wrapper">

    {{-- ── LEFT PANEL ── --}}
    <div class="login-left">
        <div class="left-glow"></div>
        <div class="left-glow-blue"></div>

        {{-- Logo top-left --}}
        <div class="left-logo">
            <img src="{{ url('/img/short_logo_w.svg') }}" alt="Logo" />
        </div>

        {{-- Bottom content --}}
        <div class="left-content">
            <div class="left-badge">
                <span class="dot"></span>
                Secure Portal
            </div>

            <h1 class="left-heading">
                Assets<br>
                Declaration<br>
                <span class="accent">Portal.</span>
            </h1>

            <p class="left-desc">
                This portal enables authorized CIABOC users to oversee and facilitate
                the asset and liability declaration process — monitoring submissions,
                assisting declarants, and ensuring transparency.
            </p>

            <div class="left-warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <span>
                    Access is restricted to authorized personnel only. Unauthorized access or intrusion attempts
                    are monitored and may result in legal consequences under the Anti-Corruption Act.
                </span>
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="login-right">
        <div class="right-glow"></div>

        <div class="login-card" id="regular-login">

            {{-- Card logo --}}
            <a href="/" class="card-logo">
                <img src="{{ url('/img/dark_center_logo.svg') }}" alt="Logo" />
            </a>

            <h2 class="card-title">Sign In</h2>
            <p class="card-subtitle">Authorized access only — credentials required</p>

            <hr class="divider">

            <form method="POST" action="{{ route('declarant-management-portal-login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        type="email"
                        placeholder="you@ciaboc.gov.lk"
                        autofocus
                        value="{{ old('email') }}"
                        required
                    />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="••••••••••"
                            required
                        />
                        <button type="button" class="pw-toggle" id="pwToggle" aria-label="Toggle password visibility">
                            {{-- Eye icon --}}
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- Eye-off icon (hidden by default) --}}
                            <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-signin">
                    Sign In &nbsp;→
                </button>

                {{-- Forgot --}}
                <a href="{{ route('password.request') }}" class="forgot-link">
                    Forgot your password?
                </a>
            </form>

            {{-- Security badge --}}
            <div class="security-note">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                256-bit SSL encrypted &nbsp;·&nbsp; CIABOC Secure Network
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    localStorage.removeItem('otp_expiry_time');

    // Password toggle
    const pwToggle  = document.getElementById('pwToggle');
    const pwInput   = document.getElementById('password');
    const eyeIcon   = document.getElementById('eyeIcon');
    const eyeOff    = document.getElementById('eyeOffIcon');

    pwToggle.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type   = isHidden ? 'text' : 'password';
        eyeIcon.style.display  = isHidden ? 'none'  : 'block';
        eyeOff.style.display   = isHidden ? 'block' : 'none';
    });

    // Subtle card entrance animation
    const card = document.getElementById('regular-login');
    card.style.opacity   = '0';
    card.style.transform = 'translateY(18px)';
    card.style.transition = 'opacity 0.55s ease, transform 0.55s ease';
    requestAnimationFrame(() => {
        setTimeout(() => {
            card.style.opacity   = '1';
            card.style.transform = 'translateY(0)';
        }, 80);
    });
</script>
@vite(['resources/scripts/pages/auth-animation.js'])
@endsection