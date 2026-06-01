@extends('layouts.saas')

@section('title', 'Contact Us')
@section('header_title', 'Get in Touch')
@section('header_subtitle', 'Revolutionize your institution with CloudiSchool.')

@section('content')
<div class="row g-5">
  <div class="col-lg-5">
    <h3 class="fw-bold mb-4">Contact Information</h3>
    <p class="text-muted mb-5">Have questions? Our team is dedicated to providing you with the best support and guidance.</p>

    <div class="d-flex mb-4">
      <div class="bg-primary-subtle p-3 rounded-circle me-3">
        <i class="bi bi-geo-alt fs-4 text-primary"></i>
      </div>
      <div>
        <h6 class="fw-bold mb-1">Office Address</h6>
        <p class="text-muted small mb-0">Smartest Developers Hub, Tech City, Pakistan</p>
      </div>
    </div>

    <div class="d-flex mb-4">
      <div class="bg-primary-subtle p-3 rounded-circle me-3">
        <i class="bi bi-envelope fs-4 text-primary"></i>
      </div>
      <div>
        <h6 class="fw-bold mb-1">Email Support</h6>
        <p class="text-muted small mb-0">smartestdevelopers@gmail.com</p>
      </div>
    </div>

    <div class="d-flex mb-4">
      <div class="bg-primary-subtle p-3 rounded-circle me-3">
        <i class="bi bi-whatsapp fs-4 text-primary"></i>
      </div>
      <div>
        <h6 class="fw-bold mb-1">WhatsApp</h6>
        <p class="text-muted small mb-0">+92 300 0000000</p>
      </div>
    </div>

    <div class="mt-5 p-4 rounded-4 bg-light">
      <h6 class="fw-bold mb-2">Connect with Us</h6>
      <div class="d-flex gap-3">
        <a href="#" class="btn btn-sm btn-dark rounded-circle"><i class="bi bi-facebook"></i></a>
        <a href="#" class="btn btn-sm btn-dark rounded-circle"><i class="bi bi-twitter-x"></i></a>
        <a href="#" class="btn btn-sm btn-dark rounded-circle"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card border-0 bg-light p-4 rounded-4">
      <h3 class="fw-bold mb-4">Send us a Message</h3>

      @if(session('success'))
      <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
        {{ session('success') }}
      </div>
      @endif

      <form action="{{ route('contact.store') }}" method="POST">
        @csrf
        <input type="text" name="honeypot" style="display: none;">

        <div class="mb-3">
          <label class="form-label small fw-bold">Full Name</label>
          <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" placeholder="e.g. John Doe" required>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Email Address</label>
          <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" placeholder="name@example.com" required>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Subject</label>
          <input type="text" name="subject" class="form-control form-control-lg border-0 shadow-sm" placeholder="How can we help?" required>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Message</label>
          <textarea name="message" class="form-control border-0 shadow-sm" rows="5" placeholder="Tell us more about your institutional requirements..." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-3 shadow">
          Send Message <i class="bi bi-send ms-2"></i>
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

@endsection