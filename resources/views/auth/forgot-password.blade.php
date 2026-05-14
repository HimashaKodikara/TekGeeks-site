@extends('layouts.base', ['pageTitle' => 'Reset Password'])

@section('content')
<style>
    h3, p, label, a { color: #505050 !important; }
    h4 { color: #163249; }
    .otp-input {
        width: 14%; height: 60px; font-size: 1.5rem;
        border: 1px solid #ced4da; border-radius: 8px;
        text-align: center; font-weight: bold; color: #163249 !important;
    }
    .otp-input:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        outline: none;
    }
    /* Hide the second stage initially if there's no error or status */
    .step-2 { display: {{ (session('status') || $errors->has('otp')) ? 'block' : 'none' }}; }
    .step-1 { display: {{ (session('status') || $errors->has('otp')) ? 'none' : 'block' }}; }
</style>

<section class="hero-section position-relative overflow-hidden" style="background-image: url(/img/cms_bg.jpg); background-size: cover; min-vh-100;">
    <div class="container d-flex align-items-center min-vh-100" style="position: relative; z-index: 1;">
        <div class="row justify-content-center w-100">
            <div class="col-11 col-md-8 col-lg-5 col-xl-5">
                <div class="login-card px-4 py-5 bg-light rounded-2 shadow-lg">
                    <img alt="logo" class="mb-4" src="/img/dark_center_logo.svg"/>
                    <h4 class="mb-0 fw-bolder text-uppercase">Reset Password</h4>
                    <p class="opacity-75 mb-3">Recover your access to the portal</p>
                    <hr>

                    @if (session('status'))
                        <div class="alert alert-success small py-2 mb-4 border-0 shadow-sm" style="background-color: #d1e7dd; color: #0f5132;">
                            <i class="fal fa-check-circle me-1"></i> {{ session('status') }}
                        </div>
                    @endif

                    <div class="step-1">
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold small text-muted">EMAIL ADDRESS</label>
                                <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       placeholder="name@example.com" required autofocus value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="d-grid mb-3">
                                <button class="btn btn-warning bg-warning text-dark fw-bold" type="submit" style="height: 45px;">
                                    SEND OTP
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="step-2">
                        <form method="POST" action="{{ route('password.otp.update') }}" id="reset-form">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">
                            <input type="hidden" name="otp" id="final_otp">

                            <div class="mb-4">
                                <label class="form-label d-block text-center fw-bold small text-muted">6-DIGIT VERIFICATION CODE</label>
                                <div class="d-flex justify-content-between mb-2">
                                    @for($i = 1; $i <= 6; $i++)
                                        <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric" id="otp-{{ $i }}" required>
                                    @endfor
                                </div>
                                @error('otp') <div class="text-danger small text-center mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">NEW PASSWORD</label>
                                <input type="password" name="password" class="form-control form-control-lg" required placeholder="••••••••">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">CONFIRM PASSWORD</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg" required placeholder="••••••••">
                            </div>

                            <div class="d-grid mb-3">
                                <button class="btn btn-warning bg-warning text-dark fw-bold" type="submit" style="height: 45px;">
                                    UPDATE PASSWORD
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4 border-top pt-3">
                        <a class="text-decoration-none small text-muted" href="{{ route('login') }}">
                            <i class="fal fa-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
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
    });

    function combineOtp() {
        let otp = "";
        inputs.forEach(i => otp += i.value);
        finalInput.value = otp;
    }
</script>
@endsection