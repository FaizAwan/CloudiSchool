@extends('layouts.saas')

@section('title', 'About Us')
@section('header_title', 'Our Mission')
@section('header_subtitle', 'Digitizing education for a better tomorrow.')

@section('content')
<h2 class="fw-bold mb-4">Empowering Educational Institutions</h2>
<p>CloudiSchool was founded with a single vision: to simplify school management through cutting-edge cloud technology. We believe that administrators and teachers should spend less time on paperwork and more time on what matters most—student success.</p>

<div class="row mt-5 g-4">
    <div class="col-md-6">
        <div class="p-4 rounded-4 bg-light">
            <h5 class="fw-bold text-primary mb-3">Innovation First</h5>
            <p class="small text-muted">We use AI and real-time cloud analytics to provide insights that were never before possible in traditional school systems.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-4 rounded-4 bg-light">
            <h5 class="fw-bold text-primary mb-3">Global Reach</h5>
            <p class="small text-muted">Serving thousands of schools across the globe, CloudiSchool is built to scale from small private academies to massive public institutions.</p>
        </div>
    </div>
</div>

<h3 class="fw-bold mt-5 mb-4">Our Values</h3>
<ul>
    <li><strong>Transparency:</strong> Open communication with our clients and stakeholders.</li>
    <li><strong>Security:</strong> Protecting student and faculty data is our top priority.</li>
    <li><strong>Simplicity:</strong> Powerful tools don't have to be complicated to use.</li>
</ul>
@endsection