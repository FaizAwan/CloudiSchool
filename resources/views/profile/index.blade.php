@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1><i class="bi bi-person-circle me-2"></i>My Profile</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<section class="section profile">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <img src="{{ asset('dashra/img/profile-thumb.png') }}" alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                    @endif
                    <h2>{{ $user->name }}</h2>
                    <h3>{{ ucfirst($user->role) }}</h3>
                    
                    <div class="social-links mt-2">
                        <a href="mailto:{{ $user->email }}" class="email"><i class="bi bi-envelope"></i></a>
                        @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="phone"><i class="bi bi-phone"></i></a>
                        @endif
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body pt-3">
                    <!-- Profile Tabs Navigation -->
                    <ul class="nav nav-tabs nav-tabs-bordered">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">
                                <i class="bi bi-person"></i> Overview
                            </button>
                        </li>
                        
                        @if(in_array($user->role, ['teacher', 'student', 'parent']))
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-details">
                                <i class="bi bi-info-circle"></i> {{ ucfirst($user->role) }} Details
                            </button>
                        </li>
                        @endif
                        
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">
                                <i class="bi bi-gear"></i> Settings
                            </button>
                        </li>
                        
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">
                                <i class="bi bi-lock"></i> Change Password
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <!-- Profile Overview Tab -->
                        <div class="tab-pane fade show active profile-overview" id="profile-overview">
                            <h5 class="card-title">Profile Details</h5>
                            
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Profile Picture</div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_image)
                                                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('dashra/img/profile-thumb.png') }}" alt="Profile" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                            <input type="file" class="form-control" name="profile_image" accept="image/*">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Full Name *</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Email *</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Phone</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" placeholder="Enter phone number">
                                        @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Date of Birth</div>
                                    <div class="col-lg-9 col-md-8">
                                        @php
                                            $dobValue = $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '';
                                        @endphp
                                        <input type="date" class="form-control" name="date_of_birth" value="{{ $dobValue }}">
                                        @error('date_of_birth')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Gender</div>
                                    <div class="col-lg-9 col-md-8">
                                        <select class="form-select" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Address</div>
                                    <div class="col-lg-9 col-md-8">
                                        <textarea class="form-control" name="address" rows="3" placeholder="Enter your address">{{ $user->address }}</textarea>
                                        @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Role</div>
                                    <div class="col-lg-9 col-md-8">
                                        <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Member Since</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if($user->created_at)
                                            {{ $user->created_at->format('F d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Role-specific Details Tab -->
                        @if(in_array($user->role, ['teacher', 'student', 'parent']))
                        <div class="tab-pane fade profile-details" id="profile-details">
                            <h5 class="card-title">{{ ucfirst($user->role) }} Information</h5>

                            @if($user->role == 'teacher')
                            <form action="{{ route('profile.update-role-data') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role" value="teacher">
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Employee ID</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="employee_id" value="{{ $profileData['employee_id'] ?? '' }}" placeholder="Enter employee ID">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Class Assigned</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="className" value="{{ $profileData['className'] ?? '' }}" placeholder="Enter assigned class">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Subject</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="subject" value="{{ $profileData['subject'] ?? '' }}" placeholder="Enter subject">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Qualification</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="qualification" value="{{ $profileData['qualification'] ?? '' }}" placeholder="Enter qualification">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Experience (Years)</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="number" class="form-control" name="experience" value="{{ $profileData['experience'] ?? '' }}" placeholder="Years of experience" min="0">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Salary</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="number" class="form-control" name="salary" value="{{ $profileData['salary'] ?? '' }}" placeholder="Monthly salary" min="0" step="0.01">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Joining Date</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="date" class="form-control" name="joining_date" value="{{ $profileData['joining_date'] ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Update Teacher Information
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endif

                            @if($user->role == 'student')
                            <form action="{{ route('profile.update-role-data') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role" value="student">
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">GR Number</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="grno" value="{{ $profileData['grno'] ?? '' }}" placeholder="Enter GR number" readonly>
                                        <small class="text-muted">GR Number is assigned by administration</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Student Name</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="studentName" value="{{ $profileData['studentName'] ?? $user->name }}" placeholder="Enter student name">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Father's Name</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="fatherName" value="{{ $profileData['fatherName'] ?? '' }}" placeholder="Enter father's name">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Current Class</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="className" value="{{ $profileData['className'] ?? '' }}" placeholder="Current class" readonly>
                                        <small class="text-muted">Class is assigned by administration</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Session</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="session" value="{{ $profileData['session'] ?? '' }}" placeholder="Academic session" readonly>
                                        <small class="text-muted">Session is managed by administration</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Emergency Contact</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="emergency_contact" value="{{ $profileData['emergency_contact'] ?? '' }}" placeholder="Emergency contact number">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Status</div>
                                    <div class="col-lg-9 col-md-8">
                                        <span class="badge bg-{{ ($profileData['status'] ?? 'inactive') == 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($profileData['status'] ?? 'Inactive') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Update Student Information
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endif

                            @if($user->role == 'parent')
                            <form action="{{ route('profile.update-role-data') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role" value="parent">
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Father's Name</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="father_name" value="{{ $profileData['father_name'] ?? '' }}" placeholder="Enter father's name">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Mother's Name</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="mother_name" value="{{ $profileData['mother_name'] ?? '' }}" placeholder="Enter mother's name">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Occupation</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control" name="occupation" value="{{ $profileData['occupation'] ?? '' }}" placeholder="Enter occupation">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Monthly Income</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="number" class="form-control" name="monthly_income" value="{{ $profileData['monthly_income'] ?? '' }}" placeholder="Monthly income" min="0" step="0.01">
                                    </div>
                                </div>
                                
                                @if(isset($profileData['children']) && $profileData['children']->count() > 0)
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Children</div>
                                    <div class="col-lg-9 col-md-8">
                                        @foreach($profileData['children'] as $child)
                                            <span class="badge bg-info me-1 mb-1">{{ $child->studentName }} ({{ $child->className }})</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                <div class="row mb-3">
                                    <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Update Parent Information
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                        @endif

                        <!-- Settings Tab -->
                        <div class="tab-pane fade profile-settings" id="profile-settings">
                            <h5 class="card-title">Account Settings</h5>
                            
                            <form action="{{ route('profile.update-settings') }}" method="POST">
                                @csrf
                                
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <h6 class="text-primary"><i class="bi bi-bell"></i> Notification Preferences</h6>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Email Notifications</div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="email_notifications" id="email_notifications" 
                                                   {{ ($settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_notifications">
                                                Receive email notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">SMS Notifications</div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="sms_notifications" id="sms_notifications" 
                                                   {{ ($settings['sms_notifications'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sms_notifications">
                                                Receive SMS notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Exam Reminders</div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="exam_reminders" id="exam_reminders" 
                                                   {{ ($settings['exam_reminders'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exam_reminders">
                                                Receive exam reminders
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Result Notifications</div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="result_notifications" id="result_notifications" 
                                                   {{ ($settings['result_notifications'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="result_notifications">
                                                Receive result notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Fee Reminders</div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="fee_reminders" id="fee_reminders" 
                                                   {{ ($settings['fee_reminders'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fee_reminders">
                                                Receive fee reminders
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <h6 class="text-primary"><i class="bi bi-globe"></i> Display Preferences</h6>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Language</div>
                                    <div class="col-lg-9 col-md-8">
                                        <select class="form-select" name="language">
                                            <option value="en" {{ ($settings['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                            <option value="ur" {{ ($settings['language'] ?? 'en') == 'ur' ? 'selected' : '' }}>Urdu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Timezone</div>
                                    <div class="col-lg-9 col-md-8">
                                        <select class="form-select" name="timezone">
                                            <option value="Asia/Karachi" {{ ($settings['timezone'] ?? 'Asia/Karachi') == 'Asia/Karachi' ? 'selected' : '' }}>Pakistan (GMT+5)</option>
                                            <option value="Asia/Dubai" {{ ($settings['timezone'] ?? 'Asia/Karachi') == 'Asia/Dubai' ? 'selected' : '' }}>UAE (GMT+4)</option>
                                            <option value="Asia/Riyadh" {{ ($settings['timezone'] ?? 'Asia/Karachi') == 'Asia/Riyadh' ? 'selected' : '' }}>Saudi Arabia (GMT+3)</option>
                                            <option value="UTC" {{ ($settings['timezone'] ?? 'Asia/Karachi') == 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
                                        <button type="submit" class="btn btn-info">
                                            <i class="bi bi-gear"></i> Update Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade profile-change-password" id="profile-change-password">
                            <h5 class="card-title">Change Password</h5>
                            
                            <form action="{{ route('profile.update-password') }}" method="POST">
                                @csrf
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Password Security Tips:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use at least 8 characters</li>
                                        <li>Include uppercase and lowercase letters</li>
                                        <li>Include at least one number</li>
                                        <li>Include special characters (!@#$%^&*)</li>
                                    </ul>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Current Password *</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="password" class="form-control" name="current_password" required>
                                        @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">New Password *</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="password" class="form-control" name="new_password" id="new_password" required minlength="8">
                                        <div class="password-strength mt-2">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" id="password-strength-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small id="password-strength-text" class="text-muted">Enter a password to see strength</small>
                                        </div>
                                        @error('new_password')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Confirm New Password *</div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="password" class="form-control" name="new_password_confirmation" required minlength="8">
                                        <small class="text-muted">Re-enter your new password</small>
                                        @error('new_password_confirmation')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-lock"></i> Change Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.profile-card img {
    border: 4px solid #fff;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.social-links a {
    font-size: 18px;
    display: inline-block;
    background: #212529;
    color: #fff;
    line-height: 1;
    padding: 8px 12px;
    margin-right: 4px;
    border-radius: 50%;
    text-align: center;
    width: 36px;
    height: 36px;
    transition: 0.3s;
}

.social-links a:hover {
    background: #007bff;
    color: #fff;
    text-decoration: none;
}

.profile-overview .row {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.profile-overview .row:last-child {
    border-bottom: none;
}

.label {
    font-weight: 600;
    color: #2c384e;
    padding-top: 8px;
}

.form-control:focus {
    border-color: #4154f1;
    box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
}

.btn-primary {
    background-color: #4154f1;
    border-color: #4154f1;
}

.btn-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.tab-pane {
    padding: 20px 0;
}

.password-strength .progress {
    background-color: #e9ecef;
}

.progress-bar.bg-danger {
    background-color: #dc3545 !important;
}

.progress-bar.bg-warning {
    background-color: #ffc107 !important;
}

.progress-bar.bg-info {
    background-color: #0dcaf0 !important;
}

.progress-bar.bg-success {
    background-color: #198754 !important;
}
</style>
@endsection

@section('scripts')
<script>
// Password strength checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            updatePasswordStrengthDisplay(strength, strengthBar, strengthText);
        });
    }
    
    function calculatePasswordStrength(password) {
        let score = 0;
        let feedback = [];
        
        if (password.length >= 8) score += 1;
        else feedback.push('at least 8 characters');
        
        if (/[a-z]/.test(password)) score += 1;
        else feedback.push('lowercase letters');
        
        if (/[A-Z]/.test(password)) score += 1;
        else feedback.push('uppercase letters');
        
        if (/[0-9]/.test(password)) score += 1;
        else feedback.push('numbers');
        
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        else feedback.push('special characters');
        
        return { score, feedback };
    }
    
    function updatePasswordStrengthDisplay(strength, bar, text) {
        const { score, feedback } = strength;
        let width = (score / 5) * 100;
        let className = '';
        let message = '';
        
        if (score === 0) {
            width = 0;
            className = '';
            message = 'Enter a password to see strength';
        } else if (score <= 2) {
            className = 'bg-danger';
            message = `Weak - Missing: ${feedback.join(', ')}`;
        } else if (score <= 3) {
            className = 'bg-warning';
            message = `Fair - Missing: ${feedback.join(', ')}`;
        } else if (score <= 4) {
            className = 'bg-info';
            message = `Good - Missing: ${feedback.join(', ')}`;
        } else {
            className = 'bg-success';
            message = 'Strong password!';
        }
        
        bar.style.width = width + '%';
        bar.className = 'progress-bar ' + className;
        text.textContent = message;
        text.className = 'text-muted';
        
        if (score <= 2) text.className = 'text-danger';
        else if (score <= 3) text.className = 'text-warning';
        else if (score <= 4) text.className = 'text-info';
        else text.className = 'text-success';
    }
    
    // Form submission handling
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
                
                // Re-enable button after 5 seconds in case of error
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            }
        });
    });
});
</script>
@endsection
