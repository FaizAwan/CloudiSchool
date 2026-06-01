@extends('layouts.login')

@section('title', 'Login to cloudischool | Secure School Management Software Access')

@section('content')
<div class="auth-container">
    <!-- Left Side: Hero Image -->
    <div class="auth-hero">
        <img src="{{ secure_asset('public/images/rebrand/login_hero.png') }}" alt="Cinematic 3D cloudischool admin dashboard visual" loading="eager">
        <div class="auth-hero-overlay">
            <h2 class="display-4 fw-bold mb-3">Master Your School Administration</h2>
            <p class="lead opacity-90">Securely access the most powerful school ERP designed for high-performance educational leadership.</p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="auth-form-side">
        <div class="auth-card">
            <div class="text-center mb-5">
                <a href="{{ route('landing') }}" class="d-inline-flex justify-content-center text-decoration-none mb-4">
                    <div class="logo-container">
                        <span class="cloudi">Cloudi</span><span class="school">School</span>
                    </div>
                </a>
                <h3 class="fw-bold text-dark">Welcome Back</h3>
                <p class="text-muted">Enter your credentials to access your dashboard</p>
            </div>

            <form method="POST" action="{{ request()->route('tenant') ? route('tenant.login', ['tenant' => request()->route('tenant')]) : route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label fw-semiboldSmall">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@school.com">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form-label fw-semiboldSmall">Password</label>
                        <a class="text-primary small text-decoration-none" href="{{ route('password.otp.show') }}">Forgot?</a>
                    </div>
                    <div class="position-relative">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••" style="padding-right: 45px;">
                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent py-0 pe-3 text-muted" onclick="togglePassword('password', this)" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted small" for="remember">Keep me logged in</label>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary">
                        Sign In to cloudischool
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-muted small">Don't have an account? <a href="{{ route('tenant.register.show') }}" class="text-primary fw-bold text-decoration-none">Start Free Trial</a></p>
                </div>
            </form>

            <script>
                function togglePassword(inputId, btn) {
                    const input = document.getElementById(inputId);
                    const icon = btn.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                }
            </script>

            <div class="mt-5 pt-4 border-top text-center">
                <a href="{{ route('landing') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Homepage</a>
            </div>
        </div>
    </div>
</div>
@endsection