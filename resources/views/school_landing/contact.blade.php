@extends('school_landing.layout')

@section('page_title', 'Contact Us')

@section('content')
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="fw-bold mb-4 text-primary">Get in Touch</h2>
                <p class="lead text-muted mb-5">We are here to answer any questions you may have about our programs,
                    admissions, or school life. Reach out to us and we'll respond as soon as we can.</p>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-geo-alt fs-5"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-1">Visit Us</h5>
                        <p class="text-muted mb-0">{{ $school->address ?? $school->schoolCity }}</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-envelope fs-5"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-1">Email Us</h5>
                        <p class="text-muted mb-0"><a href="mailto:{{ $school->schoolAdminEmail }}"
                                class="text-decoration-none text-muted">{{ $school->schoolAdminEmail }}</a></p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-telephone fs-5"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-1">Call Us</h5>
                        <p class="text-muted mb-0">{{ $school->phone ?? $school->whatsapp_number }}</p>
                    </div>
                </div>

                @if($school->facebook_url || $school->twitter_url || $school->instagram_url || $school->linkedin_url)
                    <div class="mt-5">
                        <h6 class="text-uppercase text-muted fw-bold mb-3">Follow Us</h6>
                        <div class="d-flex gap-2">
                            @if($school->facebook_url)
                                <a href="{{ $school->facebook_url }}" class="btn btn-outline-primary rounded-circle"><i
                                        class="bi bi-facebook"></i></a>
                            @endif
                            @if($school->twitter_url)
                                <a href="{{ $school->twitter_url }}" class="btn btn-outline-info rounded-circle"><i
                                        class="bi bi-twitter"></i></a>
                            @endif
                            @if($school->instagram_url)
                                <a href="{{ $school->instagram_url }}" class="btn btn-outline-danger rounded-circle"><i
                                        class="bi bi-instagram"></i></a>
                            @endif
                            @if($school->linkedin_url)
                                <a href="{{ $school->linkedin_url }}" class="btn btn-outline-primary rounded-circle"><i
                                        class="bi bi-linkedin"></i></a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="card card-premium p-4 p-md-5">
                    <h3 class="fw-bold mb-4">Send a Message</h3>
                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="school_id" value="{{ $school->id }}">
                        <input type="hidden" name="school_name" value="{{ $school->schoolName }}">
                        
                        <!-- Honeypot for spam protection -->
                        <div style="display:none;">
                            <input type="text" name="honeypot" value="">
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Your Name</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name"
                                placeholder="John Doe" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control form-control-lg bg-light border-0" id="email"
                                name="email" placeholder="john@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">Subject</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="subject"
                                name="subject" placeholder="Inquiry about admissions" required>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">Message</label>
                            <textarea class="form-control form-control-lg bg-light border-0" id="message" name="message"
                                rows="5" placeholder="How can we help you?" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm">Send
                                Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-0 mt-5">
        <div style="width: 100%; height: 400px; background: #eee;">
            <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                src="https://maps.google.com/maps?q={{ urlencode($school->schoolCity) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
        </div>
    </div>
@endsection