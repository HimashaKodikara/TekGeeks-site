@extends('layouts.base', ['pageTitle' => 'Login'])

@section('content')

<style>
    h3, p, label, a{
        color: #505050 !important;
    }

    h4{
        color: #163249;
    }
</style>
    <section class="hero-section position-relative overflow-hidden" style="background-image: url(/img/cms_bg.jpg); background-size: cover; background-repeat: no-repeat;">
        <div class="container" style="position: relative; z-index: 1;">
            <div class="row justify-content-center">
                <div class="col-11 col-md-8 col-lg-5 col-xl-5 d-flex align-items-center">
                    <div>
                        <img alt="logo" class="mb-3" src="{{url('/img/short_logo_w.svg')}}" style="width:200px;"/>
                        <h2 class="fw-bolder">ASSETS DECLARATION PORTAL</h2>
                        <p class="text-light">This support portal enables authorized CIABOC users to oversee and facilitate the asset and liability declaration process. It provides tools to monitor submissions, assist declarants, and ensure transparency in accordance with the Anti-Corruption Act.</p>
                        <p class="text-light">Access is restricted to authorized personnel only. Any unauthorized access, misuse, or attempted intrusion will be monitored and may result in legal consequences.</p>
                    </div>
                </div>

                <div class="col-11 col-md-8 col-lg-5 col-xl-5">
                    <div class="login-card px-6 py-4 bg-light rounded-2" id="regular-login">
                        <img alt="logo" class="mb-4" src="{{url('/img/dark_center_logo.svg')}}"/>
                        <h4 class="mb-0 fw-bolder">LOGIN</h4>
                        <p class="opacity-75 mb-3">Keep it all together and you'll be free</p>
                        <hr>
                        <form method="POST" action="{{ route('declarant-management-portal-login') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    id="email" name="email" required="" type="email"
                                    placeholder="name@example.com" autofocus value="{{ old('email') }}"/>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group">
                                    <input
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        id="password" name="password" required="" type="password"/>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="d-grid mb-3">
                                <button class="btn btn-warning bg-warning text-dark px-2" type="submit" style="height: 45px;">SIGN IN</button>
                            </div>
                            <div class="text-center mb-4">
                                <a class="text-decoration-none small" href="{{ route('password.request') }}">Forgot
                                    Password?</a>
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
        localStorage.removeItem('otp_expiry_time');

    </script>
    @vite(['resources/scripts/pages/auth-animation.js'])
@endsection
