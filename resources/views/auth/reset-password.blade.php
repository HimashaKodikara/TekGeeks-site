@extends('layouts.base', ['pageTitle' => 'Reset Password'])

@section('content')
<style>
    h3, p, label, a {
        color: #505050 !important;
    }
    h4 {
        color: #163249;
    }
</style>

<section class="hero-section position-relative overflow-hidden" style="background-image: url(/img/cms_bg.jpg); background-size: cover; background-repeat: no-repeat; min-vh-100;">
    <div class="container d-flex align-items-center min-vh-100" style="position: relative; z-index: 1;">
        <div class="row justify-content-center w-100 g-0">
            
            <div class="col-11 col-md-8 col-lg-5 col-xl-5 d-flex align-items-center mb-5 mb-lg-0">
                <div>
                    <img alt="logo" class="mb-3" src="/img/short_logo_w.svg" style="width:200px;"/>
                    <h2 class="fw-bolder text-white text-uppercase">Assets Declaration Portal</h2>
                    <p class="text-light opacity-75">Update your credentials. Please ensure your new password is secure and meets the portal's safety requirements.</p>
                </div>
            </div>

            @if (session('otp'))
                <div class="alert alert-success small py-2 mb-4 border-0 shadow-sm" style="background-color: #d1e7dd; color: #0f5132;">
                    <i class="fal fa-check-circle me-1"></i> {{ session('otp') }}
                </div>
            @endif

            <div class="col-11 col-md-8 col-lg-5 col-xl-5">
                <div class="login-card px-4 py-5 bg-light rounded-2 shadow-lg">
                    <img alt="logo" class="mb-4" src="/img/dark_center_logo.svg"/>
                    <h4 class="mb-0 fw-bolder text-uppercase">Set New Password</h4>
                    <p class="opacity-75 mb-3">Complete your account recovery</p>
                    <hr>

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold small text-muted text-uppercase">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light"
                                   value="{{ old('email', session('reset_email')) }}" required readonly autocomplete="username">
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small text-muted text-uppercase">New Password</label>
                            <input type="password" name="password" class="form-control form-control-lg"
                                   placeholder="••••••••" required autofocus autocomplete="new-password">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold small text-muted text-uppercase">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg"
                                   placeholder="••••••••" required autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-warning bg-warning text-dark fw-bold" type="submit" style="height: 45px;">
                                UPDATE PASSWORD
                            </button>
                        </div>

                        <div class="text-center mt-4 border-top pt-3">
                            <a class="text-decoration-none small text-muted" href="{{ route('login') }}">
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
