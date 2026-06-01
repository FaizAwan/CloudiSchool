@extends('school_landing.layout')

@section('title', 'Artistic Soul | ' . $school->schoolName)

@section('content')
<style>
    /* Artistic Soul Template Styles */
    :root {
        --art-pink: #FF007A;
        --art-yellow: #FFEA00;
        --art-blue: #00E5FF;
        --art-purple: #B000FF;
    }

    .hero-art {
        min-height: 90vh;
        background: white;
        margin-top: -80px;
        padding-top: 100px;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
    }

    .paint-splatter {
        position: absolute;
        width: 400px;
        height: 400px;
        filter: blur(80px);
        opacity: 0.2;
        z-index: 1;
    }

    .art-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 5.5rem;
        line-height: 0.85;
        margin-bottom: 30px;
        color: #1a1a1a;
    }

    .art-title span {
        display: block;
        transform: rotate(-2deg);
        background: var(--art-yellow);
        width: fit-content;
        padding: 5px 20px;
        margin-top: 10px;
    }

    .art-card {
        background: white;
        border: 2px solid #1a1a1a;
        padding: 0;
        transition: 0.3s;
        height: 100%;
        position: relative;
        bottom: 0;
        right: 0;
    }

    .art-card:hover {
        bottom: 10px;
        right: 10px;
        box-shadow: 10px 10px 0px #1a1a1a;
    }

    .btn-art {
        background: #1a1a1a;
        color: white;
        border-radius: 0;
        padding: 15px 40px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none;
    }

    .btn-art:hover {
        background: var(--art-pink);
        color: white;
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
        background: white !important;
        border-bottom: 2px solid #1a1a1a;
    }
</style>

<section class="hero-art">
    <div class="paint-splatter" style="top: 0; right: 0; background: var(--art-pink);"></div>
    <div class="paint-splatter" style="bottom: 0; left: 0; background: var(--art-blue);"></div>

    <div class="container position-relative" style="z-index: 10;">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <h1 class="art-title">CREATIVE <br>MINDS <span>EXPLODING</span></h1>
                <p class="lead mb-5 opacity-75 fs-4 fw-bold">Unleash the artist within at {{ $school->schoolName }}. We believe every child is a masterpiece in progress.</p>
                <div class="d-flex gap-3">
                    <a href="#news" class="btn btn-art">VIEW GALLERY</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-0 px-5 fw-bold">CANVAS LOGIN</a>
                </div>
            </div>
            <div class="col-lg-5" data-aos="zoom-in">
                <div class="position-relative">
                    <div style="width: 100%; height: 500px; border: 4px solid #1a1a1a; padding: 20px; background: white; transform: rotate(3deg);">
                        <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=800&q=80" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="news">
    <div class="container py-5">
        <h2 class="fw-900 mb-5 text-uppercase">Sketchbook Updates</h2>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="art-card">
                    <div class="p-4">
                        <h4 class="fw-bold mb-3">{{ $item->title }}</h4>
                        <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                        <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="fw-bold text-dark text-uppercase small letter-spacing-2">Details here →</a>
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