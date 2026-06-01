@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1><i class="bi bi-pencil-square me-2"></i>Edit Profile</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Profile Information</h5>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Image -->
                        <div class="row mb-3">
                            <label for="profile_image" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                            <div class="col-md-8 col-lg-9">
                                <div class="d-flex align-items-center">
                                    @if($user->profile_image)
                                        <img id="profile-preview" src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <img id="profile-preview" src="{{ asset('dashra/img/profile-thumb.png') }}" alt="Profile" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <input class="form-control @error('profile_image') is-invalid @enderror" type="file" id="profile_image" name="profile_image" accept="image/*">
                                        <small class="text-muted">Max file size: 2MB (JPEG, PNG, JPG, GIF)</small>
                                        @error('profile_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                            <div class="col-md-8 col-lg-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                            <div class="col-md-8 col-lg-9">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                            <div class="col-md-8 col-lg-9">
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="row mb-3">
                            <label for="date_of_birth" class="col-md-4 col-lg-3 col-form-label">Date of Birth</label>
                            <div class="col-md-8 col-lg-9">
                                @php
                                    $dobRaw = old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null);
                                @endphp
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" value="{{ $dobRaw }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="row mb-3">
                            <label for="gender" class="col-md-4 col-lg-3 col-form-label">Gender</label>
                            <div class="col-md-8 col-lg-9">
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="row mb-3">
                            <label for="address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                            <div class="col-md-8 col-lg-9">
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role-specific fields -->
                        @if($user->role == 'teacher')
                            <hr>
                            <h6 class="text-primary">Teacher Information</h6>
                            
                            <div class="row mb-3">
                                <label for="qualification" class="col-md-4 col-lg-3 col-form-label">Qualification</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="text" class="form-control" id="qualification" name="qualification" 
                                           value="{{ old('qualification', $profileData['qualification'] ?? '') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="experience" class="col-md-4 col-lg-3 col-form-label">Experience (Years)</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="number" class="form-control" id="experience" name="experience" 
                                           value="{{ old('experience', $profileData['experience'] ?? '') }}" min="0" max="50">
                                </div>
                            </div>
                        @endif

                        @if($user->role == 'student')
                            <hr>
                            <h6 class="text-primary">Student Information</h6>
                            
                            <div class="row mb-3">
                                <label for="emergency_contact" class="col-md-4 col-lg-3 col-form-label">Emergency Contact</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" 
                                           value="{{ old('emergency_contact') }}">
                                </div>
                            </div>
                        @endif

                        @if($user->role == 'parent')
                            <hr>
                            <h6 class="text-primary">Parent Information</h6>
                            
                            <div class="row mb-3">
                                <label for="occupation" class="col-md-4 col-lg-3 col-form-label">Occupation</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="text" class="form-control" id="occupation" name="occupation" 
                                           value="{{ old('occupation', $profileData['occupation'] ?? '') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="monthly_income" class="col-md-4 col-lg-3 col-form-label">Monthly Income</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="number" class="form-control" id="monthly_income" name="monthly_income" 
                                           value="{{ old('monthly_income', $profileData['monthly_income'] ?? '') }}" min="0">
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Profile
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
            <!-- Profile Preview -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profile Preview</h5>
                    <div class="text-center">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <img src="{{ asset('dashra/img/profile-thumb.png') }}" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ ucfirst($user->role) }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        @if($user->phone)
                            <p><strong>Phone:</strong> {{ $user->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Links</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person"></i> View Profile
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-lock"></i> Change Password
                        </a>
                        <a href="{{ route('profile.settings') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-gear"></i> Account Settings
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
// Preview uploaded image
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
