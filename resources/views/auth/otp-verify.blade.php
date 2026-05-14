@extends('layouts.base', ['pageTitle' => 'Verify Identity'])

@section('content')
<style>
    /* Modern OTP Card Styling */
    .otp-card { border-radius: 24px; border: none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); background: #fff; }
    .otp-digit {
        width: 14%; height: 65px; font-size: 1.8rem; border: 2px solid #e2e8f0;
        border-radius: 12px; text-align: center; font-weight: 800; color: #163249;
        transition: all 0.2s ease;
    }
    .otp-digit:focus { border-color: #ffc107; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15); outline: none; }
    .otp-digit.is-invalid { border-color: #dc3545; background-color: #fff8f8; }
    
    /* Button States */
    .btn-verify { border-radius: 12px; padding: 14px; font-weight: 700; transition: 0.3s; }
    .btn-loading { pointer-events: none; opacity: 0.8; }
</style>

<section class="d-flex align-items-center min-vh-100 bg-light" style="background-image: url(/img/cms_bg.jpg); background-size: cover; background-repeat: no-repeat;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card otp-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="/img/dark_center_logo.svg" alt="Logo" class="mb-4" width="180">
                        <h4 class="fw-bold text-dark">Verify Your Identity</h4>
                        <p class="text-muted small">We've sent a 6-digit security code to your registered devices. Please enter it below.</p>
                    </div>

                    <form id="otp-ajax-form">
                        @csrf
                        <div class="d-flex justify-content-between mb-4" id="otp-input-group">
                            @for($i = 0; $i < 6; $i++)
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
                            @endfor
                        </div>

                        @if(session('status'))
                        <div class="alert alert-success py-2 px-3 small text-center mb-3">
                            <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
                        </div>
                        @endif

                        <div id="otp-feedback" class="alert alert-danger d-none py-2 px-3 small text-center mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i> <span id="error-text"></span>
                        </div>

                        <button type="submit" id="submit-btn" class="btn btn-warning w-100 btn-verify shadow-sm mb-3 text-dark">
                            <span class="btn-text">VERIFY & SIGN IN</span>
                            <output class="spinner-border spinner-border-sm d-none" aria-live="polite"></output>
                        </button>

                        <div class="text-center mt-2">
                            <span class="text-muted small">Didn't receive a code?</span>
                            <button type="button" id="resend-btn" class="btn btn-link btn-sm text-decoration-none fw-bold p-0 ms-1" {{ $secondsRemaining > 0 ? 'disabled' : '' }}>
                                @if($secondsRemaining > 0)
                                    Resend in <span id="timer">{{ sprintf('%02d:%02d', floor($secondsRemaining / 60), $secondsRemaining % 60) }}</span>
                                @else
                                    Resend New Code
                                @endif
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="{{ route('otp.cancel') }}" class="text-decoration-none text-secondary small fw-bold" id="cancel-link">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
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
$(document).ready(function() {
    const $inputs = $('.otp-digit');
    const $btn = $('#submit-btn');
    const $btnText = $btn.find('.btn-text');
    const $spinner = $btn.find('.spinner-border');
    const $feedback = $('#otp-feedback');

    // 1. Initial State
    $inputs.eq(0).focus();

    // 2. Input Handling
    $inputs.on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        $inputs.removeClass('is-invalid');
        $feedback.addClass('d-none');
        const index = $inputs.index(this);
        if (this.value && index < 5) $inputs.eq(index + 1).focus();
        checkAndSubmit();
    });

    $inputs.on('keydown', function(e) {
        const index = $inputs.index(this);
        if (e.key === 'Backspace' && !this.value && index > 0) $inputs.eq(index - 1).focus();
    });

    $inputs.on('paste', function(e) {
        e.preventDefault();
        const data = (e.originalEvent.clipboardData || window.clipboardData).getData('text').trim().slice(0, 6);
        if (/^\d+$/.test(data)) {
            data.split('').forEach((char, i) => $inputs.eq(i).val(char));
            checkAndSubmit();
        }
    });

    function checkAndSubmit() {
        let otp = "";
        $inputs.each(function() { otp += $(this).val(); });
        if (otp.length === 6) $('#otp-ajax-form').submit();
    }

    // 3. AJAX Submission
    $('#otp-ajax-form').on('submit', function(e) {
        e.preventDefault();
        
        let otpCode = "";
        $inputs.each(function() { otpCode += $(this).val(); });

        if (otpCode.length < 6) {
            $inputs.addClass('is-invalid');
            $feedback.removeClass('d-none').find('#error-text').text('Please enter all 6 digits of the verification code.');
            $inputs.eq(0).focus();
            return;
        }

        // Reset UI State
        $feedback.addClass('d-none');
        $inputs.removeClass('is-invalid');
        $btn.addClass('btn-loading').prop('disabled', true);
        $btnText.text('VERIFYING...');
        $spinner.removeClass('d-none');

        $.ajax({
            url: "{{ route('otp.submit') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", otp: otpCode },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                if (res.status === 'success') {
                    $btnText.text('SUCCESS! REDIRECTING...');
                    window.location.href = res.redirect;
                }
            },
            error: function(xhr) {
                $btn.removeClass('btn-loading').prop('disabled', false);
                $btnText.text('VERIFY & SIGN IN');
                $spinner.addClass('d-none');

                if (xhr.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                let errorMsg = "The code is invalid. Please try again.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                $feedback.removeClass('d-none').find('#error-text').text(errorMsg);
                $inputs.addClass('is-invalid');
            }
        });
    });

    // 4. Timer Logic
    let timeLeft = {{ $secondsRemaining }};

    if (timeLeft > 0) {
        const tick = setInterval(() => {
            if (--timeLeft <= 0) {
                clearInterval(tick);
                $('#resend-btn').prop('disabled', false).html('Resend New Code');
            } else {
                const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
                const s = (timeLeft % 60).toString().padStart(2, '0');
                $('#timer').text(`${m}:${s}`);
            }
        }, 1000);
    }

    // 5. Resend OTP
    $('#resend-btn').on('click', function() {
        $('<form>', { method: 'POST', action: "{{ route('otp.resend') }}" })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }))
            .appendTo('body').submit();
    });
});
</script>
@endsection
