@extends('layouts.base', ['pageTitle' => 'Verify OTP'])

@section('content')
<style>
    h3, p, label, a {
        color: #505050 !important;
    }
    h4 {
        color: #163249;
    }
    .otp-input {
        width: 14%;
        height: 60px;
        font-size: 1.5rem;
        border: 1px solid #ced4da;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        color: #163249 !important;
    }
    .otp-input:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        outline: none;
    }
</style>

<section class="hero-section position-relative overflow-hidden" style="background-image: url(/img/cms_bg.jpg); background-size: cover; background-repeat: no-repeat; min-vh-100;">
    <div class="container d-flex align-items-center min-vh-100" style="position: relative; z-index: 1;">
        <div class="row justify-content-center w-100">
            
            <div class="col-11 col-md-8 col-lg-5 col-xl-5 d-flex align-items-center mb-5 mb-lg-0">
                <div>
                    <img alt="logo" class="mb-3" src="/img/short_logo_w.svg" style="width:200px;"/>
                    <h2 class="fw-bolder text-white">ASSETS DECLARATION PORTAL</h2>
                    <p class="text-light opacity-75">Secure Access Protocol: Please enter the verification code sent to your registered devices to complete your login to the Assets Management System.</p>
                </div>
            </div>

            <div class="col-11 col-md-8 col-lg-5 col-xl-5">
                <div class="login-card px-4 py-5 bg-light rounded-2 shadow-lg">
                    <img alt="logo" class="mb-4" src="/img/dark_center_logo.svg"/>
                    <h4 class="mb-0 fw-bolder">VERIFY IDENTITY</h4>
                    <p class="opacity-75 mb-3">Two-Step Verification Required</p>
                    <hr>

                    <form method="POST" action="{{ route('password.otp.update') }}" id="otp-form">
                        @csrf
                        <input type="hidden" name="otp" id="final_otp">
                        <input type="hidden" name="email" id="final_email" value="{{ request()->get('email') ?? session('reset_email') }}">
                        
                        <div class="mb-4">
                            <label for="otp" class="form-label d-block text-center mb-3">Enter 6-Digit Code</label>
                            <div class="d-flex justify-content-between">
                                @for($i = 1; $i <= 6; $i++)
                                    <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric" id="otp-{{ $i }}">
                                @endfor
                            </div>
                            @error('otp')
                                <div class="text-danger small mt-2 text-center"><i class="fal fa-exclamation-circle me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-warning bg-warning text-dark px-2 fw-bold" type="submit" style="height: 45px;">VERIFY & SIGN IN</button>
                        </div>

                        <div class="text-center mb-2">
                            <p class="small mb-0">Didn't receive the code?</p>
                            <button type="button" id="resend_btn" class="btn btn-link text-primary fw-bold text-decoration-none small p-0" disabled>
                                Resend in <span id="timer_display">05:00</span>
                            </button>
                        </div>

                        <div class="text-center mt-4 border-top pt-3">
                            <a class="text-decoration-none small" href="{{ route('otp.cancel') }}">
                                <i class="fal fa-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
    const STORAGE_KEY = 'otp_expiry_time';
    const TIMER_DURATION = 300;

    let timeLeft;
    const now = Math.floor(Date.now() / 1000);
    const savedExpiry = localStorage.getItem(STORAGE_KEY);

    if (savedExpiry && parseInt(savedExpiry) > now) {
        timeLeft = parseInt(savedExpiry) - now;
    } else {
        timeLeft = TIMER_DURATION;
        localStorage.setItem(STORAGE_KEY, now + TIMER_DURATION);
    }

    const resendBtn = document.getElementById('resend_btn');
    const timerDisplay = document.getElementById('timer_display');

    function formatTime(seconds) {
        let m = Math.floor(seconds / 60).toString().padStart(2, '0');
        let s = (seconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function updateTimer() {
        if (timeLeft > 0) {
            if (resendBtn) resendBtn.disabled = true;
            if (timerDisplay) timerDisplay.innerText = formatTime(timeLeft);
            timeLeft--;
        } else {
            clearInterval(countdown);
            localStorage.removeItem(STORAGE_KEY);
            if (resendBtn) {
                resendBtn.innerHTML = '<i class="fal fa-redo me-1 text-primary"></i> Resend New Code';
                resendBtn.disabled = false;
            }
        }
    }

    updateTimer();
    const countdown = setInterval(updateTimer, 1000);

    if (resendBtn) {
        resendBtn.addEventListener('click', function() {
            localStorage.removeItem(STORAGE_KEY);
            
            this.innerHTML = '<i class="fal fa-spinner fa-spin me-1"></i> Sending...';
            this.disabled = true;

            let f = document.createElement('form');
            f.method = 'POST';
            f.action = "{{ route('password.otp.resend') }}";
            
            let token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = "{{ csrf_token() }}";
            
            f.appendChild(token);
            document.body.appendChild(f);
            f.submit();
        });
    }

    const cancelLink = document.querySelector('a[href*="otp.cancel"]');
    if (cancelLink) {
        cancelLink.addEventListener('click', function() {
            localStorage.removeItem(STORAGE_KEY);
        });
    }

    const inputs = document.querySelectorAll('.otp-input');
    const finalInput = document.getElementById('final_otp');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length > 0 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            combineOtp();
        });
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            const data = e.clipboardData.getData('text').trim();
            if (data.length === 6 && !isNaN(data)) {
                data.split('').forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                combineOtp();
                document.getElementById('otp-form').submit();
            }
        });
    });

    function combineOtp() {
        let otp = "";
        inputs.forEach(i => otp += i.value);
        if (finalInput) finalInput.value = otp;
        if (otp.length === 6) document.getElementById('otp-form').submit();
    }
</script>
@endsection
