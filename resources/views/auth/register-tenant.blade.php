@extends('layouts.login')

@section('title', 'Sign Up for cloudischool – Free School Management SaaS Trial | Start Today')

@section('content')
<div class="auth-container">
    <!-- Left Side: Hero Image -->
    <div class="auth-hero">
        <img src="{{ secure_asset('public/images/rebrand/register_hero.png') }}" alt="Cinematic 3D cloudischool classroom visual" loading="eager">
        <div class="auth-hero-overlay">
            <h2 class="display-4 fw-bold mb-3">Empowering the Future of Education</h2>
            <p class="lead opacity-90">Join thousands of schools transforming their digital presence and operational efficiency with cloudischool.</p>
        </div>
    </div>

    <!-- Right Side: Registration Form -->
    <div class="auth-form-side">
        <div class="auth-card">
            <div class="text-center mb-5">
                <a href="{{ route('landing') }}" class="d-inline-flex justify-content-center text-decoration-none mb-4">
                    <div class="logo-container">
                        <span class="cloudi">Cloudi</span><span class="school">School</span>
                    </div>
                </a>
                <h3 class="fw-bold text-dark">Get Started for Free</h3>
                <p class="text-muted">Create your school instance in seconds</p>
            </div>

            <form id="registrationForm" method="POST" action="{{ route('tenant.register.store') }}">
                @csrf

                <div id="schoolInfoSection">
                    <div class="mb-4">
                        <label class="form-label fw-semiboldSmall">School Name</label>
                        <input type="text" name="school_name" id="school_name" class="form-control" required placeholder="e.g. Green Valley High">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semiboldSmall">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control" placeholder="e.g. +923001234567" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semiboldSmall">Admin Name</label>
                            <input type="text" name="admin_name" id="admin_name" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semiboldSmall">Admin Email</label>
                            <input type="email" name="admin_email" id="admin_email" class="form-control" required placeholder="admin@school.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semiboldSmall">Password</label>
                            <input type="password" name="admin_password" id="admin_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semiboldSmall">Confirm Password</label>
                            <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="button" id="sendOtpBtn" class="btn btn-primary">
                            Send Verification OTP
                        </button>
                    </div>
                </div>

                <div id="otpSection" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="alert alert-info py-2 small">
                            An OTP has been sent to <span id="displayEmail" class="fw-bold"></span>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold d-block">Enter 4-Digit OTP</label>
                        <div class="d-flex justify-content-center gap-2">
                            <input type="text" name="otp" id="otp" class="form-control text-center fs-4 fw-bold" maxlength="4" style="letter-spacing: 15px; width: 150px;" placeholder="0000">
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" id="registerBtn" class="btn btn-success">
                            Verify & Launch Dashboard
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button" id="changeEmailBtn" class="btn btn-link btn-sm text-decoration-none">Change Email</button>
                    </div>
                </div>

                <div id="messageBox" class="mt-3"></div>

                <div class="text-center mt-4">
                    <p class="text-muted small">Already have a school? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Log In Here</a></p>
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
        const $form = $('#registrationForm');
        const $sendOtpBtn = $('#sendOtpBtn');
        const $registerBtn = $('#registerBtn');
        const $schoolInfoSection = $('#schoolInfoSection');
        const $otpSection = $('#otpSection');
        const $messageBox = $('#messageBox');
        const $displayEmail = $('#displayEmail');

        // CSRF Setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        function showMessage(msg, type = 'danger') {
            $messageBox.html(`<div class="alert alert-${type} py-2 small">${msg}</div>`);
        }

        $sendOtpBtn.on('click', function() {
            const email = $('#admin_email').val();
            if (!email) {
                showMessage('Please enter an admin email first.');
                return;
            }

            $sendOtpBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...');
            $messageBox.empty();

            $.ajax({
                url: "{{ route('tenant.register.send_otp') }}",
                method: "POST",
                data: {
                    email: email
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $displayEmail.text(email);
                        $schoolInfoSection.fadeOut(300, function() {
                            $otpSection.fadeIn(300);
                        });
                    } else {
                        showMessage(response.message);
                        $sendOtpBtn.prop('disabled', false).text('Send Verification OTP');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to send OTP. Please try again.';
                    showMessage(msg);
                    $sendOtpBtn.prop('disabled', false).text('Send Verification OTP');
                }
            });
        });

        $('#changeEmailBtn').on('click', function() {
            $otpSection.fadeOut(300, function() {
                $schoolInfoSection.fadeIn(300);
                $sendOtpBtn.prop('disabled', false).text('Send Verification OTP');
            });
        });

        $form.on('submit', function(e) {
            e.preventDefault();

            $registerBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verifying & Creating School...');
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
                        }, 1500);
                    } else {
                        showMessage(response.message);
                        $registerBtn.prop('disabled', false).text('Verify & Launch Dashboard');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Registration failed. Please check your inputs.';
                    showMessage(msg);
                    $registerBtn.prop('disabled', false).text('Verify & Launch Dashboard');
                }
            });
        });
    });
</script>

<style>
    /* Smooth Transitions */
    #schoolInfoSection,
    #otpSection {
        transition: all 0.3s ease-in-out;
    }

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