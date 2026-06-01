@extends('school_landing.layout')

@section('title', $school->meta_title ?? $school->schoolName . ' | Official School Website')

@section('content')
<style>
    /* Hero Section Overrides for Home only */
    .hero {
        height: 90vh;
        background: linear-gradient(135deg, rgba(10, 108, 255, 0.05) 0%, rgba(255, 152, 0, 0.05) 100%);
        display: flex;
        align-items: center;
        position: relative;
        padding-top: 100px;
    }

    .hero-title {
        font-size: 4rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 25px;
        background: linear-gradient(to right, var(--primary), #00d2ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-inner {
        display: none;
    }

    /* Hide the default subpage hero */
    body {
        padding-top: 0;
    }

    .navbar {
        background: transparent !important;
        box-shadow: none;
    }

    .navbar.scrolled {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(15px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .news-img {
        height: 250px;
        object-fit: cover;
        width: 100%;
    }

    .news-item {
        background: white;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        transition: 0.3s;
    }

    .news-item:hover {
        transform: scale(1.02);
    }

    .module-card {
        background: white;
        padding: 40px;
        border-radius: 25px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.05);
        transition: all 0.4s;
        height: 100%;
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .module-card:hover {
        transform: translateY(-15px);
    }

    .gallery-img {
        border-radius: 20px;
        transition: 0.4s;
        cursor: pointer;
    }

    .gallery-img:hover {
        transform: scale(1.05);
        filter: brightness(0.8);
    }
</style>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <h1 class="hero-title">Shaping Future <br>Leaders at {{ $school->schoolName }}</h1>
                <p class="hero-subtitle mb-4 fs-5 text-muted">Experience a world-class education system designed to nurture creativity, critical thinking, and excellence in every student.</p>
                <div class="d-flex gap-3">
                    <a href="#news" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Explore School</a>
                    <a href="#events" class="btn btn-outline-primary rounded-pill px-5 py-3 fw-bold">Upcoming Events</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block" data-aos="zoom-in">
                <img src="{{ $school->school_logo ?? 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=800' }}" alt="{{ $school->schoolName }}" class="img-fluid rounded-circle shadow-lg border border-5 border-white">
            </div>
        </div>
    </div>
</section>

<!-- Announcements Ticker -->
@if($announcements->count() > 0)
<div class="bg-primary text-white py-2 overflow-hidden shadow-sm">
    <div class="container d-flex">
        <span class="fw-bold me-3 text-uppercase small">Announcements:</span>
        <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
            @foreach($announcements as $item)
            <span class="me-5"><i class="bi bi-info-circle me-1"></i> {{ $item->title }}: {{ $item->content }}</span>
            @endforeach
        </marquee>
    </div>
</div>
@endif

<!-- News Section -->
<section class="py-5 mt-5" id="news">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold">Latest School News</h2>
            <div class="mx-auto" style="width: 70px; height: 4px; background: var(--primary); border-radius: 2px;"></div>
        </div>
        <div class="row">
            @forelse($news as $item)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="news-item">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" alt="News" class="news-img">
                    <div class="p-4">
                        <span class="badge bg-primary-subtle text-primary mb-2">School News</span>
                        <h4 class="mb-3 fw-bold">{{ $item->title }}</h4>
                        <p class="text-muted">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                        <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="text-primary fw-bold text-decoration-none">Read Full Story <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">No news updates available.</div>
            @endforelse
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('school.news', $school->slug) }}" class="btn btn-outline-primary rounded-pill px-5">View All News</a>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="py-5 bg-white shadow-sm" id="events">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold">Upcoming Events</h2>
            <div class="mx-auto" style="width: 70px; height: 4px; background: var(--primary); border-radius: 2px;"></div>
        </div>
        <div class="row">
            @forelse($events as $item)
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <div class="module-card">
                    <div class="icon mb-4" style="width: 60px; height: 60px; background: rgba(10, 108, 255, 0.1); color: var(--primary); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <h5 class="text-primary fw-bold">{{ \Carbon\Carbon::parse($item->event_date)->format('d M, Y') }}</h5>
                    <h4 class="fw-bold mb-3">{{ $item->title }}</h4>
                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i> {{ $item->location }}</p>
                    <p class="text-muted mb-4">{{ Str::limit(strip_tags($item->content), 80) }}</p>
                    <a href="{{ route('school.event.show', [$school->slug, $item->slug]) }}" class="btn btn-link p-0 text-decoration-none fw-bold">View Full Event <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">No upcoming events.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-5 mt-5" id="gallery">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold">Moments that Matter</h2>
            <div class="mx-auto" style="width: 70px; height: 4px; background: var(--primary); border-radius: 2px;"></div>
        </div>
        <div class="row g-4">
            @foreach($gallery as $img)
            <div class="col-6 col-md-4" data-aos="zoom-in">
                <div class="position-relative overflow-hidden rounded-4">
                    <img src="{{ $img->image }}" class="img-fluid gallery-img" alt="{{ $img->title }}">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    // Specific script for Home index
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>
@endsection