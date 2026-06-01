@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1><i class="bi bi-gear me-2"></i>Account Settings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account Preferences</h5>

                    <form method="POST" action="{{ route('profile.update-settings') }}">
                        @csrf

                        <!-- Notification Settings -->
                        <div class="mb-4">
                            <h6 class="text-primary">📧 Notification Preferences</h6>
                            <p class="text-muted small">Choose how you want to receive notifications from the system.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_notifications" 
                                       name="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    <strong>Email Notifications</strong>
                                    <small class="text-muted d-block">Receive important updates via email</small>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                       name="sms_notifications" {{ $settings['sms_notifications'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_notifications">
                                    <strong>SMS Notifications</strong>
                                    <small class="text-muted d-block">Receive urgent notifications via SMS</small>
                                </label>
                            </div>
                        </div>

                        @if(in_array($user->role, ['student', 'parent', 'teacher']))
                        <!-- Academic Notifications -->
                        <div class="mb-4">
                            <h6 class="text-success">🎓 Academic Notifications</h6>
                            <p class="text-muted small">Control notifications related to academic activities.</p>

                            @if(in_array($user->role, ['student', 'parent']))
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exam_reminders" 
                                       name="exam_reminders" {{ $settings['exam_reminders'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="exam_reminders">
                                    <strong>Exam Reminders</strong>
                                    <small class="text-muted d-block">Get notified about upcoming exams</small>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="result_notifications" 
                                       name="result_notifications" {{ $settings['result_notifications'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="result_notifications">
                                    <strong>Result Notifications</strong>
                                    <small class="text-muted d-block">Get notified when exam results are published</small>
                                </label>
                            </div>
                            @endif

                            @if($user->role == 'teacher')
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exam_reminders" 
                                       name="exam_reminders" {{ $settings['exam_reminders'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="exam_reminders">
                                    <strong>Exam Schedule Reminders</strong>
                                    <small class="text-muted d-block">Get reminded about scheduled exams</small>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="result_notifications" 
                                       name="result_notifications" {{ $settings['result_notifications'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="result_notifications">
                                    <strong>Grading Reminders</strong>
                                    <small class="text-muted d-block">Get reminded about pending grading tasks</small>
                                </label>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if(in_array($user->role, ['student', 'parent', 'admin', 'accountant']))
                        <!-- Financial Notifications -->
                        <div class="mb-4">
                            <h6 class="text-warning">💰 Financial Notifications</h6>
                            <p class="text-muted small">Manage notifications about fees and payments.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="fee_reminders" 
                                       name="fee_reminders" {{ $settings['fee_reminders'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="fee_reminders">
                                    <strong>Fee Reminders</strong>
                                    <small class="text-muted d-block">Get notified about due fees and payment reminders</small>
                                </label>
                            </div>
                        </div>
                        @endif

                        <!-- Language & Region Settings -->
                        <div class="mb-4">
                            <h6 class="text-info">🌐 Language & Region</h6>
                            <p class="text-muted small">Set your preferred language and timezone.</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="language" class="form-label">Language</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="en" {{ $settings['language'] == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ur" {{ $settings['language'] == 'ur' ? 'selected' : '' }}>Urdu</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="Asia/Karachi" {{ $settings['timezone'] == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi (PKT)</option>
                                        <option value="Asia/Dubai" {{ $settings['timezone'] == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                                        <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Settings
                            </button>
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Settings Summary -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Current Settings</h5>
                    
                    <div class="mb-3">
                        <strong>Email Notifications:</strong>
                        <span class="badge bg-{{ $settings['email_notifications'] ? 'success' : 'secondary' }}">
                            {{ $settings['email_notifications'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>SMS Notifications:</strong>
                        <span class="badge bg-{{ $settings['sms_notifications'] ? 'success' : 'secondary' }}">
                            {{ $settings['sms_notifications'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>

                    @if(in_array($user->role, ['student', 'parent', 'teacher']))
                    <div class="mb-3">
                        <strong>Exam Reminders:</strong>
                        <span class="badge bg-{{ $settings['exam_reminders'] ? 'success' : 'secondary' }}">
                            {{ $settings['exam_reminders'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    @endif

                    @if(in_array($user->role, ['student', 'parent', 'admin', 'accountant']))
                    <div class="mb-3">
                        <strong>Fee Reminders:</strong>
                        <span class="badge bg-{{ $settings['fee_reminders'] ? 'success' : 'secondary' }}">
                            {{ $settings['fee_reminders'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Language:</strong>
                        <span class="badge bg-info">{{ $settings['language'] == 'en' ? 'English' : 'Urdu' }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Timezone:</strong>
                        <span class="badge bg-info">{{ $settings['timezone'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Privacy & Security -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Privacy & Security</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-lock"></i> Change Password
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </a>
                        <a href="{{ route('profile.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-person"></i> View Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help & Support -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Help & Support</h5>
                    <p class="text-muted small">
                        Need help with your settings? Contact support for assistance.
                    </p>
                    <div class="d-grid">
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="bi bi-headset"></i> Contact Support
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.form-check {
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: background-color 0.2s;
}

.form-check:hover {
    background-color: #f8f9fa;
}

.form-check-input {
    margin-top: 0.25rem;
}

.form-check-label {
    width: 100%;
    cursor: pointer;
}

.form-check-label strong {
    color: #495057;
}
</style>
@endsection
