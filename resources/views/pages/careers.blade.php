@extends('layouts.saas')

@section('title', 'Careers')
@section('header_title', 'Join the Team')
@section('header_subtitle', 'Build the future of edtech with CloudiSchool.')

@section('content')
<h2 class="fw-bold mb-4">Why Work at CloudiSchool?</h2>
<p>We are a remote-first company of designers, engineers, and educators passionate about transforming the way the world learns. If you're driven by impact, we want to hear from you.</p>

<h4 class="fw-bold mt-5 mb-4">Open Positions</h4>
<div class="list-group list-group-flush">
    <div class="list-group-item py-4 bg-transparent border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Senior Laravel Developer</h5>
                <p class="small text-muted mb-0">Remote | Full-Time</p>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Apply</a>
        </div>
    </div>
    <div class="list-group-item py-4 bg-transparent border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Product Designer (UI/UX)</h5>
                <p class="small text-muted mb-0">Remote | Full-Time</p>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Apply</a>
        </div>
    </div>
    <div class="list-group-item py-4 bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Customer Success Manager</h5>
                <p class="small text-muted mb-0">Lahore, PK | On-site</p>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Apply</a>
        </div>
    </div>
</div>
@endsection