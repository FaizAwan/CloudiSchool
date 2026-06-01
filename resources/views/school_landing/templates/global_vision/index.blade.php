@extends('school_landing.layout')

@section('title', 'Global Vision | ' . $school->schoolName)

@section('content')
<style>
    /* Global Vision Template Styles */
    :root {
        --global-navy: #0F172A;
        --global-blue: #2563EB;
        --global-slate: #64748B;
    }

    .hero-global {
        background: white;
        min-height: 80vh;
        margin-top: -80px;
        padding-top: 100px;
        display: flex;
        align-items: center;
    }

    .global-title {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        font-size: 4.5rem;
        color: var(--global-navy);
        letter-spacing: -2px;
        margin-bottom: 30px;
    }

    .global-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 40px;
        transition: 0.3s;
        height: 100%;
    }

    .global-card:hover {
        background: white;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
        border-color: var(--global-blue);
    }

    .btn-global {
        background: var(--global-blue);
        color: white;
        border-radius: 8px;
        padding: 15px 40px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-global:hover {
        background: var(--global-navy);
        color: white;
        transform: translateY(-3px);
    }

    /* Override defaults */
    .hero-inner {
        display: none;
    }

    body {
        background: #fff;
        padding-top: 0;
        color: #334155;
    }

    .navbar {
        background: white !important;
        border-bottom: 1px solid #E2E8F0;
    }
</style>

<section class="hero-global">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="global-title">Think Global. <br>Lead Locally.</h1>
                <p class="lead mb-5 text-slate opacity-80">A world-class education for the leaders of tomorrow. {{ $school->schoolName }} provides an international curriculum designed for excellence.</p>
                <div class="d-flex gap-3">
                    <a href="#news" class="btn btn-global">View Admissions</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary border-2 px-5 py-3 fw-bold">Student Hub</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80" class="img-fluid rounded-3 shadow-2xl">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-slate-50" id="news">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-800 text-navy">Global Updates</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('school.news', $school->slug) }}" class="text-blue fw-bold text-decoration-none">Latest News Archive →</a>
            </div>
        </div>
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-lg-4" data-aos="fade-up">
                <div class="global-card">
                    <span class="text-blue fw-bold small mb-2 d-block">MANAGEMENT UNIT</span>
                    <h4 class="fw-bold mb-3 h5">{{ $item->title }}</h4>
                    <p class="text-slate small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                    <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="fw-bold text-navy text-decoration-none small">READ MORE</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    // Pure Clean Navigation
</script>
@endsection