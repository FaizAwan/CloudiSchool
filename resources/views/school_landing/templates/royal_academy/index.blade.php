@extends('school_landing.layout')

@section('title', 'Royal Academy | ' . $school->schoolName)

@section('content')
<style>
    /* Royal Academy Template Styles */
    :root {
        --royal-gold: #c9a041;
        --royal-dark: #1a1a1a;
        --royal-cream: #f9f6f0;
    }

    .hero-royal {
        height: 100vh;
        background: radial-gradient(circle, rgba(26, 26, 26, 0.7) 0%, rgba(0, 0, 0, 0.9) 100%), url('https://images.unsplash.com/photo-1541339907198-e08756ebafe3?w=1920&q=80');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        margin-top: -80px;
        padding-top: 80px;
    }

    .royal-crest {
        width: 120px;
        height: 120px;
        border: 2px solid var(--royal-gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 40px;
        font-size: 3rem;
        color: var(--royal-gold);
        background: rgba(201, 160, 65, 0.1);
        backdrop-filter: blur(5px);
    }

    .royal-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 5rem;
        margin-bottom: 20px;
        color: var(--royal-gold);
        letter-spacing: 2px;
    }

    .royal-sep {
        height: 2px;
        width: 150px;
        background: linear-gradient(to right, transparent, var(--royal-gold), transparent);
        margin: 30px auto;
    }

    .royal-card {
        background: white;
        border: 1px solid #e5e5e5;
        padding: 40px;
        transition: 0.5s;
        height: 100%;
        position: relative;
    }

    .royal-card:hover {
        border-color: var(--royal-gold);
        transform: translateY(-10px);
    }

    .royal-card::before {
        content: '';
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        bottom: 10px;
        border: 1px solid #f0f0f0;
        pointer-events: none;
    }

    .btn-royal {
        background: var(--royal-gold);
        color: white;
        border-radius: 0;
        padding: 15px 40px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none;
        transition: 0.3s;
    }

    .btn-royal:hover {
        background: var(--royal-dark);
        color: var(--royal-gold);
    }

    /* Hide default */
    .hero-inner {
        display: none;
    }

    body {
        background: var(--royal-cream);
        padding-top: 0;
        font-family: 'Inter', sans-serif;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: var(--royal-dark) !important;
    }

    .navbar.scrolled .nav-link,
    .navbar.scrolled .navbar-brand {
        color: var(--royal-gold) !important;
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<section class="hero-royal">
    <div class="container" data-aos="fade-down">
        <div class="royal-crest shadow-lg">
            <i class="bi bi-mortarboard"></i>
        </div>
        <h6 class="text-uppercase letter-spacing-4 mb-3 fw-bold" style="color: var(--royal-gold)">The Pinnacle of Education</h6>
        <h1 class="royal-title text-uppercase">{{ $school->schoolName }}</h1>
        <div class="royal-sep"></div>
        <p class="lead mb-5 mx-auto opacity-75" style="max-width: 700px; font-style: italic;">Where aristocracy meets academic excellence. Join the elite community of future world leaders.</p>
        <div class="d-flex justify-content-center gap-4">
            <a href="#news" class="btn btn-royal shadow">Discover More</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-0 px-5 fw-bold">Portal Access</a>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="royal-title" style="font-size: 3rem;">Recent Journals</h2>
            <div class="royal-sep"></div>
        </div>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="royal-card">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid mb-4 border">
                    <h4 class="fw-bold mb-3" style="font-family: 'Playfair Display', serif;">{{ $item->title }}</h4>
                    <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="text-uppercase fw-bold small text-decoration-none" style="color: var(--royal-gold); letter-spacing: 2px;">Read Article →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--royal-dark); color: white;">
    <div class="container py-5 text-center">
        <h2 class="mb-5" style="font-family: 'Playfair Display', serif; color: var(--royal-gold)">Our Core Pillars</h2>
        <div class="row g-5">
            <div class="col-md-4">
                <i class="bi bi-shield-check fs-1 text-gold mb-3 d-block"></i>
                <h5 class="fw-bold">Integrity</h5>
                <p class="opacity-50">Developing character that stands the test of time through ethical leadership.</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-trophy fs-1 text-gold mb-3 d-block"></i>
                <h5 class="fw-bold">Excellence</h5>
                <p class="opacity-50">Striving for perfection in every academic and extracurricular pursuit.</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-globe fs-1 text-gold mb-3 d-block"></i>
                <h5 class="fw-bold">Unity</h5>
                <p class="opacity-50">Fostering a sense of belonging in a diverse and inclusive environment.</p>
            </div>
        </div>
    </div>
</section>

<script>
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            nav.classList.add('scrolled', 'shadow-lg');
        } else {
            nav.classList.remove('scrolled', 'shadow-lg');
        }
    });
</script>
@endsection