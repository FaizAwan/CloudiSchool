@extends('layouts.saas')

@section('title', 'System Status')
@section('header_title', 'System Status')
@section('header_subtitle', 'Real-time performance of CloudiSchool services.')

@section('content')
<div class="mb-5 p-4 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 d-flex align-items-center">
    <i class="bi bi-check-circle-fill text-success fs-3 me-3"></i>
    <div>
        <h5 class="mb-0 fw-bold text-success">All Systems Operational</h5>
        <p class="small mb-0 opacity-75">Updated 2 minutes ago</p>
    </div>
</div>

<div class="list-group list-group-flush border rounded-4 overflow-hidden">
    <div class="list-group-item p-4 d-flex justify-content-between align-items-center">
        <span>SaaS Core Platform</span>
        <span class="badge bg-success">Operational</span>
    </div>
    <div class="list-group-item p-4 d-flex justify-content-between align-items-center">
        <span>Parent & Student Portal</span>
        <span class="badge bg-success">Operational</span>
    </div>
    <div class="list-group-item p-4 d-flex justify-content-between align-items-center">
        <span>Payment Processing (Stripe)</span>
        <span class="badge bg-success">Operational</span>
    </div>
    <div class="list-group-item p-4 d-flex justify-content-between align-items-center">
        <span>Email Delivery Services</span>
        <span class="badge bg-success">Operational</span>
    </div>
    <div class="list-group-item p-4 d-flex justify-content-between align-items-center">
        <span>API & Integrations</span>
        <span class="badge bg-success">Operational</span>
    </div>
</div>
@endsection