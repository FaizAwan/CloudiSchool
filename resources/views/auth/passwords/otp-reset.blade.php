@extends('layouts.login')

@section('title', 'Reset Your Password – cloudischool')

@section('content')
<div class="auth-container">
    <!-- Left Side: Hero Image -->
    <div class="auth-hero">
        <img src="{{ secure_asset('public/images/rebrand/login_hero.png') }}" alt="Cinematic 3D cloudischool visual" loading="eager">
        <div class="auth-hero-overlay">
            <h2 class="display-4 fw-bold mb-3">Recover Your Account</h2>
            <p class="lead opacity-90">Securely reset your password using email verification. We prioritize your school's data security.</p>
        </div>
    </div>

    <!-- Right Side: Reset Form -->
    <div class="auth-form-side">
        <div class="auth-card">
            <div class="text-center mb-5">
                <a href="{{ route('landing') }}" class="d-inline-flex justify-content-center text-decoration-none mb-4">
                    <div class="logo-container">
                        <span class="cloudi">Cloudi</span><span class="school">School</span>
                    </div>
                </a>
                <h3 class="fw-bold text-dark">Password Recovery</h3>
                <p class="text-muted">Reset your admin password via OTP</p>
            </div>

            <form id="resetForm" method="POST" action="{{ route('password.otp.reset') }}">
                @csrf

                <div id="emailSection">
                    <div class="mb-4">
                        <label class="form-label fw-semiboldSmall">Admin Email Address</label>
                        <input type="email" name="email" id="email_input" class="form-control" required placeholder="admin@school.com" value="{{ request('email') }}">
                    </div>

                    <div class="d-grid mb-4">
                        <button type="button" id="sendOtpBtn" class="btn btn-primary">
                            Send Reset OTP
                        </button>
                    </div>
                </div>

                <div id="otpSection" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="alert alert-info py-2 small">
                            A verification code has been sent to <span id="displayEmail" class="fw-bold"></span>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold d-block">Enter 4-Digit OTP</label>
                        <div class="d-flex justify-content-center gap-2">
                            <input type="text" name="otp" id="otp" class="form-control text-center fs-4 fw-bold" maxlength="4" style="letter-spacing: 15px; width: 150px;" placeholder="0000">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semiboldSmall">New Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" style="padding-right: 45px;">
                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent py-0 pe-3 text-muted" onclick="togglePassword('password', this)" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semiboldSmall">Confirm New Password</label>
                        <div class="position-relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="••••••••" style="padding-right: 45px;">
                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent py-0 pe-3 text-muted" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" id="resetBtn" class="btn btn-success">
                            Reset Password & Login
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button" id="changeEmailBtn" class="btn btn-link btn-sm text-decoration-none">Change Email</button>
                    </div>
                </div>

                <div id="messageBox" class="mt-3"></div>

                <div class="text-center mt-4">
                    <p class="text-muted small">Remembered your password? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Back to Login</a></p>
                </div>
            </form>

            <div class="mt-4 pt-4 border-top text-center">
                <a href="{{ route('landing') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Homepage</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const $form = $('#resetForm');
        const $sendOtpBtn = $('#sendOtpBtn');
        const $resetBtn = $('#resetBtn');
        const $emailSection = $('#emailSection');
        const $otpSection = $('#otpSection');
        const $messageBox = $('#messageBox');
        const $displayEmail = $('#displayEmail');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        function showMessage(msg, type = 'danger') {
            $messageBox.html(`<div class="alert alert-${type} py-2 small">${msg}</div>`);
        }

        $sendOtpBtn.on('click', function() {
            const email = $('#email_input').val();
            if (!email) {
                showMessage('Please enter your email address.');
                return;
            }

            $sendOtpBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending Reset Link...');
            $messageBox.empty();

            $.ajax({
                url: "{{ route('password.otp.send') }}",
                method: "POST",
                data: {
                    email: email
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $displayEmail.text(email);
                        $emailSection.fadeOut(300, function() {
                            $otpSection.fadeIn(300);
                        });
                    } else {
                        showMessage(response.message);
                        $sendOtpBtn.prop('disabled', false).text('Send Reset OTP');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to send OTP. Is the email correct?';
                    showMessage(msg);
                    $sendOtpBtn.prop('disabled', false).text('Send Reset OTP');
                }
            });
        });

        $('#changeEmailBtn').on('click', function() {
            $otpSection.fadeOut(300, function() {
                $emailSection.fadeIn(300);
                $sendOtpBtn.prop('disabled', false).text('Send Reset OTP');
            });
        });

        $form.on('submit', function(e) {
            e.preventDefault();

            $resetBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Resetting Password...');
            $messageBox.empty();

            $.ajax({
                url: $form.attr('action'),
                method: "POST",
                data: $form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        showMessage(response.message, 'success');
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else {
                        showMessage(response.message);
                        $resetBtn.prop('disabled', false).text('Reset Password & Login');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Reset failed. Please check your inputs and OTP.';
                    showMessage(msg);
                    $resetBtn.prop('disabled', false).text('Reset Password & Login');
                }
            });
        });
    });

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

<style>
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .fw-semiboldSmall {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4169E1 0%, #2e51bb 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(65, 105, 225, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(65, 105, 225, 0.4);
    }

    .auth-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        border-radius: 20px;
        padding: 2.5rem;
    }
</style>
@endsection