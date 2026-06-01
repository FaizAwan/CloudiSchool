@extends('school_landing.layout')

@section('title', 'Academic Excellence | ' . $school->schoolName)

@section('content')
<style>
    /* Academic Excellence Template Styles */
    :root {
        --academic-dark: #1b262c;
        --academic-blue: #0f4c75;
        --academic-accent: #3282b8;
    }

    .hero-academic {
        padding: 180px 0 100px;
        background: linear-gradient(rgba(27, 38, 44, 0.85), rgba(27, 38, 44, 0.85)), url('https://images.unsplash.com/photo-1541339907198-e08756ebafe3?w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        text-align: center;
        margin-top: -80px;
    }

    .academic-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 4rem;
        margin-bottom: 25px;
        letter-spacing: -1px;
    }

    .academic-divider {
        width: 100px;
        height: 3px;
        background: var(--academic-accent);
        margin: 30px auto;
    }

    .stat-card {
        background: white;
        border-radius: 4px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-top: 4px solid var(--academic-blue);
        height: 100%;
        transition: 0.3s;
    }

    .stat-card:hover {
        border-top-width: 8px;
        transform: translateY(-5px);
    }

    .academic-section-title {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--academic-dark);
        margin-bottom: 50px;
        display: flex;
        align-items: center;
    }

    .academic-section-title::after {
        content: '';
        flex-grow: 1;
        height: 1px;
        background: #dee2e6;
        margin-left: 20px;
    }

    .news-card-academic {
        background: white;
        border: 1px solid #eee;
        transition: 0.3s;
    }

    .news-card-academic:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
    }

    .btn-academic {
        border-radius: 0;
        padding: 12px 35px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: #fff;
        padding-top: 0;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: var(--academic-dark) !important;
    }

    .navbar.scrolled .nav-link,
    .navbar.scrolled .navbar-brand {
        color: white !important;
    }
</style>

<section class="hero-academic">
    <div class="container" data-aos="fade-up">
        <h6 class="text-uppercase letter-spacing-2 mb-3 fw-bold text-info">Tradition & Innovation Since 1995</h6>
        <h1 class="academic-title">Fostering Academic Excellence <br>at {{ $school->schoolName }}</h1>
        <div class="academic-divider"></div>
        <p class="lead opacity-75 mb-5 mx-auto" style="max-width: 750px;">Preparing the next generation of global citizens through rigorous academic standards and a community-focused learning environment.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#about" class="btn btn-info btn-academic text-white">Admissions Open</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-academic px-5">My Portal</a>
        </div>
    </div>
</section>

<section class="py-5" style="margin-top: -50px; position: relative; z-index: 10;">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3" data-aos="fade-up">
                <div class="stat-card">
                    <h2 class="fw-bold text-primary">2.5k</h2>
                    <p class="text-muted mb-0">Enrolled Students</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <h2 class="fw-bold text-primary">150+</h2>
                    <p class="text-muted mb-0">Expert Faculty</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <h2 class="fw-bold text-primary">98%</h2>
                    <p class="text-muted mb-0">Graduation Rate</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <h2 class="fw-bold text-primary">25</h2>
                    <p class="text-muted mb-0">Sports Clubs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 mt-5" id="news">
    <div class="container py-5">
        <h3 class="academic-section-title">Latest Academic News</h3>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-6" data-aos="fade-up">
                <div class="news-card-academic d-flex flex-column flex-md-row">
                    <div style="min-width: 250px;">
                        <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid h-100" style="object-fit: cover;">
                    </div>
                    <div class="p-4">
                        <span class="text-muted small fw-bold text-uppercase mb-2 d-block">{{ $item->created_at->format('M d, Y') }}</span>
                        <h4 class="fw-bold mb-3 h5">{{ $item->title }}</h4>
                        <p class="text-muted small mb-3">{{ Str::limit(strip_tags($item->content), 80) }}</p>
                        <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="fw-bold text-primary text-decoration-none">Continue Reading <i class="bi bi-chevron-right small"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" id="events" style="background: #fdfdfd;">
    <div class="container py-5">
        <h3 class="academic-section-title">Academic Calendar</h3>
        <div class="row g-4">
            @forelse($events as $event)
            <div class="col-lg-12">
                <div class="p-4 bg-white border d-flex align-items-center" data-aos="fade-up">
                    <div class="text-center me-5" style="min-width: 80px;">
                        <span class="d-block fs-3 fw-bold text-academic-blue">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</span>
                        <span class="d-block text-muted text-uppercase small">{{ \Carbon\Carbon::parse($event->event_date)->format('M Y') }}</span>
                    </div>
                    <div class="vr mx-3 text-muted"></div>
                    <div class="px-4">
                        <h5 class="fw-bold mb-1">{{ $event->title }}</h5>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> {{ $event->location }}</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('school.event.show', [$school->slug, $event->slug]) }}" class="btn btn-outline-dark btn-academic btn-sm">Event Details</a>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-muted col-12">No information available.</p>
            @endforelse
        </div>
    </div>
</section>

<script>
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            nav.classList.add('scrolled', 'shadow');
        } else {
            nav.classList.remove('scrolled', 'shadow');
        }
    });
</script>
@endsection