@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Access Information</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Access Information</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-shield-exclamation display-1 text-warning mb-4"></i>
                        <h2 class="text-primary mb-4">Access Restricted</h2>
                        
                        <div class="alert alert-info">
                            <h4><i class="bi bi-info-circle"></i> Important Notice</h4>
                            <p class="mb-0">All system features and modules have been restricted to <strong>Superadmin</strong> access only for enhanced security and data protection.</p>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mx-auto">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5><i class="bi bi-person-gear text-primary"></i> Your Current Role</h5>
                                        <span class="badge bg-secondary fs-6 px-3 py-2">{{ ucfirst(Auth::user()->role) }}</span>
                                        
                                        <hr>
                                        
                                        <h6>Available Actions:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="bi bi-check-circle text-success"></i> View this information page</li>
                                            <li><i class="bi bi-check-circle text-success"></i> Edit your profile settings</li>
                                            <li><i class="bi bi-check-circle text-success"></i> Change your password</li>
                                            <li><i class="bi bi-x-circle text-danger"></i> Access system modules</li>
                                            <li><i class="bi bi-x-circle text-danger"></i> Manage data or records</li>
                                            <li><i class="bi bi-x-circle text-danger"></i> Perform administrative tasks</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-muted">
                                If you need access to system features, please contact the <strong>Superadmin</strong> for assistance.
                            </p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('profile.index') }}" class="btn btn-primary">
                                <i class="bi bi-person-circle"></i> View My Profile
                            </a>
                            <a href="{{ route('profile.change-password') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-lock"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.display-1 {
    font-size: 6rem;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.badge {
    font-size: 1rem;
}

.alert {
    border-radius: 10px;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.text-primary {
    color: #667eea !important;
}

.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    border-radius: 25px;
    padding: 12px 30px;
}

.btn-outline-secondary {
    border-radius: 25px;
    padding: 12px 30px;
}
</style>
@endsection