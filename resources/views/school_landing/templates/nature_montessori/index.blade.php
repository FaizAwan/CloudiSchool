@extends('school_landing.layout')

@section('title', 'Nature Montessori | ' . $school->schoolName)

@section('content')
<style>
    /* Nature Montessori Template Styles */
    :root {
        --nature-green: #4E6C50;
        --nature-sage: #A2B38B;
        --nature-cream: #F8F4EA;
        --nature-brown: #795548;
    }

    .hero-nature {
        background-color: var(--nature-cream);
        min-height: 80vh;
        margin-top: -80px;
        padding-top: 100px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .leaf {
        position: absolute;
        z-index: 1;
        opacity: 0.1;
        color: var(--nature-green);
    }

    .nature-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 4.5rem;
        color: var(--nature-green);
        margin-bottom: 25px;
    }

    .nature-card {
        background: white;
        border-radius: 60px 20px 60px 20px;
        padding: 40px;
        border: none;
        transition: 0.4s;
        box-shadow: 10px 10px 0px var(--nature-sage);
        height: 100%;
    }

    .nature-card:hover {
        transform: translateY(-10px);
        box-shadow: 15px 15px 0px var(--nature-green);
    }

    .btn-nature {
        background: var(--nature-green);
        color: white;
        border-radius: 40px;
        padding: 15px 40px;
        font-weight: 700;
        transition: 0.3s;
        border: none;
    }

    .btn-nature:hover {
        background: var(--nature-brown);
        color: white;
        transform: rotate(-2deg);
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: var(--nature-cream);
        padding-top: 0;
        color: #444;
    }

    .navbar {
        background: transparent !important;
    }

    .navbar.scrolled {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(10px);
    }
</style>

<section class="hero-nature">
    <!-- Decorative Leaves -->
    <i class="bi bi-leaf leaf" style="top: 10%; right: 5%; font-size: 10rem; transform: rotate(45deg);"></i>
    <i class="bi bi-leaf leaf" style="bottom: 10%; left: 5%; font-size: 8rem; transform: rotate(-15deg);"></i>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h6 class="text-uppercase fw-bold mb-3" style="color: var(--nature-brown); letter-spacing: 2px;">Nurturing Nature's Best</h6>
                <h1 class="nature-title">Growing With <br>Love & Care.</h1>
                <p class="lead mb-5 opacity-75">At {{ $school->schoolName }}, we believe in a natural approach to learning, where every child's curiosity is the compass for their education.</p>
                <div class="d-flex gap-3">
                    <a href="#news" class="btn btn-nature shadow-lg">Our Journey</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-success rounded-pill px-5 py-3 fw-bold">Parent Portal</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="zoom-in">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&q=80" alt="Nature School" class="img-fluid" style="border-radius: 100px 20px 100px 20px;">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="nature-title" style="font-size: 3rem;">Recent Blooms</h2>
            <p class="text-muted">Stay updated with our community's latest stories.</p>
        </div>
        <div class="row g-5">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="nature-card">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid mb-4" style="border-radius: 40px 10px 40px 10px; height: 180px; width: 100%; object-fit: cover;">
                    <h4 class="fw-bold mb-3" style="color: var(--nature-green)">{{ $item->title }}</h4>
                    <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="btn btn-link p-0 fw-bold text-decoration-none" style="color: var(--nature-brown)">READ STORY →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--nature-green); border-radius: 100px 100px 0 0;">
    <div class="container py-5 text-center text-white">
        <h2 class="fw-bold mb-4">Planting Seeds for Success</h2>
        <p class="mb-5 opacity-75 mx-auto" style="max-width: 600px;">Join our community of families dedicated to holistic and natural education.</p>
        <a href="#" class="btn btn-light rounded-pill px-5 py-3 fw-bold">SCHEDULE A VISIT</a>
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