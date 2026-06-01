@extends('school_landing.layout')

@section('title', 'Kinder Garden | ' . $school->schoolName)

@section('content')
<style>
    /* Kinder Garden Template Styles */
    :root {
        --kinder-pink: #FF9AA2;
        --kinder-blue: #B5EAD7;
        --kinder-yellow: #FFFFD8;
        --kinder-purple: #C7CEEA;
    }

    .hero-kinder {
        background: var(--kinder-yellow);
        min-height: 80vh;
        margin-top: -80px;
        padding-top: 100px;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .kinder-shape {
        position: absolute;
        z-index: 1;
        opacity: 0.5;
    }

    .kinder-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 4rem;
        color: #555;
        margin-bottom: 25px;
    }

    .kinder-card {
        background: white;
        border-radius: 50px;
        padding: 40px;
        border: 8px solid var(--kinder-blue);
        height: 100%;
        transition: 0.3s;
    }

    .kinder-card:hover {
        transform: scale(1.05) rotate(1deg);
        border-color: var(--kinder-pink);
    }

    .btn-kinder {
        background: var(--kinder-pink);
        color: white;
        border-radius: 50px;
        padding: 15px 40px;
        font-weight: 800;
        font-size: 1.2rem;
        border: 4px solid white;
        box-shadow: 0 10px 0 rgba(0, 0, 0, 0.05);
    }

    .btn-kinder:hover {
        background: var(--kinder-purple);
        color: white;
        transform: translateY(-5px);
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: #fdfdfd;
        padding-top: 0;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: white !important;
        border-radius: 0 0 40px 40px;
    }
</style>

<section class="hero-kinder">
    <!-- Playful Shapes -->
    <div class="kinder-shape" style="top: 10%; left: 5%; width: 100px; height: 100px; background: var(--kinder-pink); border-radius: 30% 70% 70% 30%;"></div>
    <div class="kinder-shape" style="bottom: 10%; right: 5%; width: 150px; height: 150px; background: var(--kinder-purple); border-radius: 70% 30% 30% 70%;"></div>

    <div class="container position-relative" style="z-index: 10;">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h6 class="text-uppercase fw-bold mb-3" style="color: grey; letter-spacing: 2px;">Small Steps, Big Dreams</h6>
                <h1 class="kinder-title">Play, Learn <br>& Bloom Today!</h1>
                <p class="lead mb-5 opacity-75">Every day is a new adventure at {{ $school->schoolName }}. We provide a safe, colorful and fun environment for your little ones to grow.</p>
                <div class="d-flex gap-3">
                    <a href="#news" class="btn btn-kinder">FUN STORIES</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-bold">ADMIN BOX</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="zoom-in">
                <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&q=80" alt="Kinder" class="img-fluid" style="border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%; border: 15px solid white; box-shadow: 0 20px 50px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <h2 class="text-center fw-bold mb-5" style="font-family: 'Outfit', sans-serif; color: #555;">Little News</h2>
        <div class="row g-5">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="kinder-card">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid rounded-pill mb-4" style="height: 180px; width: 100%; object-fit: cover; border: 5px solid #eee;">
                    <h4 class="fw-bold mb-3">{{ $item->title }}</h4>
                    <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="fw-bold text-decoration-none" style="color: var(--kinder-pink)">VIEW STORY</a>
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
            nav.classList.add('scrolled', 'shadow-sm');
        } else {
            nav.classList.remove('scrolled', 'shadow-sm');
        }
    });
</script>
@endsection