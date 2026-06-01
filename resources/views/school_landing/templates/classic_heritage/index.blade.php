@extends('school_landing.layout')

@section('title', 'Classic Heritage | ' . $school->schoolName)

@section('content')
<style>
    /* Classic Heritage Template Styles */
    :root {
        --heritage-red: #800000;
        --heritage-gold: #D4AF37;
        --heritage-paper: #FDFBF7;
        --heritage-ink: #2C2C2C;
    }

    .hero-heritage {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1541339907198-e08756ebafe3?w=1920&q=80');
        background-size: cover;
        background-position: center;
        min-height: 90vh;
        margin-top: -80px;
        padding-top: 100px;
        display: flex;
        align-items: center;
        text-align: center;
        color: white;
        border-bottom: 5px solid var(--heritage-gold);
    }

    .heritage-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 5rem;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .heritage-tagline {
        font-family: 'Inter', sans-serif;
        text-transform: uppercase;
        letter-spacing: 5px;
        font-size: 1.1rem;
        color: var(--heritage-gold);
        margin-bottom: 40px;
        display: block;
    }

    .btn-heritage {
        background: var(--heritage-red);
        color: white !important;
        border-radius: 0;
        padding: 15px 45px;
        border: 1px solid var(--heritage-gold);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .heritage-card {
        background: white;
        border: 1px solid #ddd;
        padding: 40px;
        border-bottom: 4px solid var(--heritage-red);
        height: 100%;
        transition: 0.3s;
    }

    .heritage-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: var(--heritage-paper);
        color: var(--heritage-ink);
        padding-top: 0;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: var(--heritage-red) !important;
    }

    .nav-link,
    .navbar-brand {
        color: white !important;
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<section class="hero-heritage">
    <div class="container" data-aos="zoom-in">
        <span class="heritage-tagline">Legacy of Knowledge</span>
        <h1 class="heritage-title">Where History Meets <br>The Future.</h1>
        <p class="lead mb-5 opacity-75" style="max-width: 800px; margin: 0 auto;">Establishing foundations for excellence since generations. Welcome to {{ $school->schoolName }}, where character is built alongside intellect.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#news" class="btn btn-heritage">Our Archives</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-0 px-5 fw-bold">Academic Login</a>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <h2 class="text-center mb-5 fw-bold" style="font-family: 'Playfair Display', serif; color: var(--heritage-red);">Academic Journals</h2>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="heritage-card">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid mb-4 border">
                    <h4 class="fw-bold mb-3">{{ $item->title }}</h4>
                    <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="text-decoration-none fw-bold" style="color: var(--heritage-red)">READ FULL JOURNAL →</a>
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