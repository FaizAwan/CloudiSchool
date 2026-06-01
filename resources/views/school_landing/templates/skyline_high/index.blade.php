@extends('school_landing.layout')

@section('title', 'Skyline High | ' . $school->schoolName)

@section('content')
<style>
    /* Skyline High Template Styles */
    :root {
        --skyline-blue: #00A8FF;
        --skyline-navy: #192A56;
        --skyline-gray: #F5F6FA;
    }

    .hero-skyline {
        background: linear-gradient(rgba(25, 42, 86, 0.85), rgba(0, 168, 255, 0.3)), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&q=80');
        background-size: cover;
        background-position: center;
        min-height: 80vh;
        margin-top: -80px;
        padding-top: 100px;
        display: flex;
        align-items: center;
        color: white;
    }

    .skyline-title {
        font-family: 'Inter', sans-serif;
        font-weight: 900;
        font-size: 5rem;
        letter-spacing: -2px;
        margin-bottom: 25px;
    }

    .skyline-card {
        background: white;
        border-radius: 0;
        border: none;
        padding: 40px;
        box-shadow: 20px 20px 0px var(--skyline-blue);
        height: 100%;
        transition: 0.3s;
    }

    .skyline-card:hover {
        transform: translate(-10px, -10px);
        box-shadow: 30px 30px 0px var(--skyline-navy);
    }

    .btn-skyline {
        background: var(--skyline-blue);
        color: white;
        border-radius: 0;
        padding: 15px 40px;
        font-weight: 800;
        text-transform: uppercase;
        border: none;
    }

    .btn-skyline:hover {
        background: var(--skyline-navy);
        color: white;
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: var(--skyline-gray);
        padding-top: 0;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: white !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
</style>

<section class="hero-skyline">
    <div class="container">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-up">
                <h1 class="skyline-title">Reach For <br>The Skyline.</h1>
                <p class="lead mb-5 opacity-90" style="max-width: 600px;">A metropolitan approach to modern education. At {{ $school->schoolName }}, we build leaders for the global cities of tomorrow.</p>
                <div class="d-flex gap-3">
                    <a href="#news" class="btn btn-skyline">City Updates</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light rounded-0 px-5 fw-bold">Login Hub</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <h2 class="fw-900 mb-5" style="color: var(--skyline-navy)">LATEST BULLETINS</h2>
        <div class="row g-5">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="skyline-card">
                    <h5 class="text-primary fw-bold mb-2">{{ $item->created_at->format('H:i | d.m.y') }}</h5>
                    <h4 class="fw-bold mb-3">{{ $item->title }}</h4>
                    <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="fw-bold text-decoration-none" style="color: var(--skyline-navy)">READ REPORT →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
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