@extends('school_landing.layout')

@section('title', 'Tech Scholars | ' . $school->schoolName)

@section('content')
<style>
    /* Tech Scholars Template Styles */
    :root {
        --tech-bg: #050505;
        --tech-neon: #00f2ff;
        --tech-purple: #7000ff;
    }

    .hero-tech {
        background: var(--tech-bg);
        min-height: 100vh;
        margin-top: -80px;
        padding-top: 100px;
        position: relative;
        overflow: hidden;
        color: white;
        display: flex;
        align-items: center;
    }

    /* Animated Grid Background */
    .hero-tech::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: linear-gradient(rgba(0, 242, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 242, 255, 0.05) 1px, transparent 1px);
        background-size: 50px 50px;
        transform: perspective(500px) rotateX(60deg) translateY(-200px);
        z-index: 1;
    }

    .tech-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 6rem;
        background: linear-gradient(45deg, var(--tech-neon), var(--tech-purple));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 30px;
        position: relative;
        z-index: 10;
        filter: drop-shadow(0 0 30px rgba(0, 242, 255, 0.4));
    }

    /* STITCH SKILL: Primary Match */
    .btn-primary,
    .bg-primary {
        background: linear-gradient(135deg, #89b8e2 0%, #4a8dc2 50%, #2a5a8a 100%) !important;
        border: none !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4a8dc2 0%, #2a5a8a 100%) !important;
    }

    .tech-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 40px;
        border-radius: 20px;
        transition: 0.4s;
        height: 100%;
    }

    .tech-card:hover {
        background: rgba(0, 242, 255, 0.05);
        border-color: var(--tech-neon);
        box-shadow: 0 0 50px rgba(0, 242, 255, 0.1);
        transform: scale(1.05);
    }

    .neon-border-btn {
        background: transparent;
        border: 2px solid var(--tech-neon);
        color: var(--tech-neon);
        padding: 15px 45px;
        border-radius: 5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: 0.3s;
    }

    .neon-border-btn:hover {
        background: var(--tech-neon);
        color: black;
        box-shadow: 0 0 30px var(--tech-neon);
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: var(--tech-bg);
        color: #fff;
        padding-top: 0;
    }

    .navbar {
        background: rgba(5, 5, 5, 0.8) !important;
        backdrop-filter: blur(20px);
    }

    .nav-link,
    .navbar-brand {
        color: white !important;
    }

    .section-title {
        color: var(--tech-neon);
        font-weight: 900;
    }
</style>

<section class="hero-tech">
    <div class="container text-center text-lg-start">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="badge bg-primary text-white mb-3 p-2 rounded-0">UPGRADING THE FUTURE</span>
                <h1 class="tech-title">AI. TECH. <br>FUTURE.</h1>
                <p class="lead mb-5 opacity-75 fs-4">Coding the next generation of innovators at {{ $school->schoolName }}. Welcome to the most advanced school ecosystem.</p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="#news" class="neon-border-btn">OPERATING SYSTEM</a>
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-5 px-5 py-3 fw-bold">PORTAL LOGIN</a>
                </div>
            </div>
            <div class="col-lg-5" data-aos="zoom-in">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=800&q=80" class="img-fluid rounded shadow-lg border border-primary border-3" style="filter: hue-rotate(180deg);">
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <h2 class="section-title mb-5">SYSTEM LOGS</h2>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="tech-card">
                    <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="img-fluid mb-4 rounded opacity-75">
                    <h4 class="fw-bold mb-3 neon-text">{{ $item->title }}</h4>
                    <p class="text-white-50 small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="text-info fw-bold text-decoration-none small">EXECUTE_READ.EXE →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: linear-gradient(to right, #7000ff 0%, #00f2ff  100%);">
    <div class="container text-center py-4">
        <h2 class="text-white fw-900 mb-2">READY TO DECODE THE FUTURE?</h2>
        <p class="text-white opacity-75 mb-4">Admissions for technological batches are now open.</p>
        <a href="#" class="btn btn-light rounded-pill px-5 fw-bold">REGISTER NOW</a>
    </div>
</section>

<script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endsection