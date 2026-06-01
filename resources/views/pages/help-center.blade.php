@extends('layouts.saas')

@section('title', 'Help Center')
@section('header_title', 'Help Center')
@section('header_subtitle', 'Find answers and tutorials to master CloudiSchool.')

@section('content')
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="p-4 border rounded-4 text-center h-100">
            <i class="bi bi-rocket-takeoff fs-1 text-primary mb-3"></i>
            <h5 class="fw-bold">Getting Started</h5>
            <p class="small text-muted">Learn the basics of setting up your school profile and departments.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-4 border rounded-4 text-center h-100">
            <i class="bi bi-people fs-1 text-primary mb-3"></i>
            <h5 class="fw-bold">Managing Students</h5>
            <p class="small text-muted">Guides on admissions, attendance, and student performance tracking.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-4 border rounded-4 text-center h-100">
            <i class="bi bi-cash-stack fs-1 text-primary mb-3"></i>
            <h5 class="fw-bold">Billing & Fees</h5>
            <p class="small text-muted">How to automate fee collection and manage financial reporting.</p>
        </div>
    </div>
</div>

<h4 class="fw-bold mb-4">Frequently Asked Questions</h4>
<div class="accordion accordion-flush" id="faqAccordion">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                How do I import student data?
            </button>
        </h2>
        <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-muted">
                You can import bulk data using our CSV/Excel import tools located in the Students module.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                Is there a mobile app for parents?
            </button>
        </h2>
        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-muted">
                Yes! Parents can download the CloudiSchool Parent App from the App Store or Google Play.
            </div>
        </div>
    </div>
</div>
@endsection