@extends('school_landing.layout')

@section('title', 'Sports Academy | ' . $school->schoolName)

@section('content')
<style>
    /* Sports Academy Template Styles */
    :root {
        --sports-red: #E0115F;
        --sports-black: #121212;
        --sports-gold: #FFD700;
    }

    .hero-sports {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1461896704190-321aa77a0504?w=1920&q=80');
        background-size: cover;
        background-position: top;
        min-height: 90vh;
        margin-top: -80px;
        padding-top: 100px;
        display: flex;
        align-items: center;
        text-align: center;
        color: white;
        clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
    }

    .sports-title {
        font-family: 'Oswald', sans-serif;
        font-weight: 700;
        font-size: 6rem;
        text-transform: uppercase;
        letter-spacing: -2px;
        line-height: 0.9;
        margin-bottom: 30px;
    }

    .sports-card {
        background: white;
        border-radius: 0;
        border: none;
        padding: 0;
        transition: 0.3s;
        height: 100%;
        overflow: hidden;
        border-right: 5px solid var(--sports-red);
    }

    .sports-card:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-sports {
        background: var(--sports-red);
        color: white;
        border-radius: 0;
        padding: 18px 45px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none;
        transform: skewX(-15deg);
    }

    .btn-sports>span {
        display: inline-block;
        transform: skewX(15deg);
    }

    .btn-sports:hover {
        background: #000;
        color: var(--sports-red);
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: #f4f4f4;
        padding-top: 0;
        font-family: 'Inter', sans-serif;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: #000 !important;
    }

    .nav-link,
    .navbar-brand {
        color: white !important;
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">

<section class="hero-sports">
    <div class="container" data-aos="fade-up">
        <h6 class="text-uppercase fw-bold mb-3" style="color: var(--sports-gold); letter-spacing: 4px;">Train Like a Champion</h6>
        <h1 class="sports-title">SPEED. <br>POWER. <br>VICTORY.</h1>
        <p class="lead mb-5 opacity-90 mx-auto" style="max-width: 600px;">Where athletic discipline meets academic brilliance. Join the legacy of sports excellence at {{ $school->schoolName }}.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#news" class="btn btn-sports"><span>TRYOUTS 2026</span></a>
            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-0 px-5 fw-bold">TRAINING HUB</a>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <h2 class="fw-bold mb-5" style="font-family: 'Oswald', sans-serif; text-transform: uppercase; font-size: 3rem;">Field Reports</h2>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="sports-card">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid" style="height: 250px; width: 100%; object-fit: cover;">
                    <div class="p-4">
                        <h4 class="fw-bold mb-3 text-uppercase" style="font-family: 'Oswald', sans-serif;">{{ $item->title }}</h4>
                        <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                        <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="text-danger fw-bold text-decoration-none small">MATCH SUMMARY →</a>
                    </div>
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