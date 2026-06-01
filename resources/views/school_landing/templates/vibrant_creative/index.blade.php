@extends('school_landing.layout')

@section('title', 'Welcome to ' . $school->schoolName)
@section('hide_navbar', true)

@section('content')
    <!-- Import elegant fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />

    <style>
        /* Elegant Creative Redesign */
        :root {
            --tpl-primary: #012970;
            --tpl-secondary: #007bff;
            --tpl-accent: #c5a059;
            /* Gold accent */
            --tpl-text: #1a1a1a;
            --tpl-light: #f8faff;
            --font-main: 'Outfit', sans-serif;
            --font-heading: 'Playfair Display', serif;
        }

        body {
            font-family: var(--font-main);
            color: var(--tpl-text);
            background-color: white;
            overflow-x: hidden;
        }

        .hero-elegant {
            min-height: 95vh;
            display: flex;
            align-items: center;
            background: radial-gradient(circle at 80% 20%, rgba(197, 160, 89, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 10% 80%, rgba(1, 41, 112, 0.03) 0%, transparent 40%);
            position: relative;
            padding: 100px 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-eyebrow {
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: 600;
            font-size: 14px;
            color: var(--tpl-accent);
            margin-bottom: 20px;
            display: block;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: 5rem;
            line-height: 1.1;
            color: var(--tpl-primary);
            margin-bottom: 30px;
        }

        .hero-title span {
            color: var(--tpl-secondary);
            font-style: italic;
        }

        .hero-description {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 40px;
            max-width: 600px;
            font-weight: 300;
        }

        .hero-image-wrap {
            position: relative;
            z-index: 1;
        }

        .hero-image-main {
            width: 100%;
            height: 600px;
            object-fit: cover;
            border-radius: 40px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
        }

        .hero-floating-card {
            position: absolute;
            bottom: 50px;
            left: -30px;
            background: white;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            z-index: 3;
            max-width: 250px;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .btn-elegant {
            padding: 18px 40px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
        }

        .btn-elegant-primary {
            background: var(--tpl-primary);
            color: white;
            border: none;
        }

        .btn-elegant-primary:hover {
            background: var(--tpl-secondary);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(1, 41, 112, 0.2);
            color: white;
        }

        .btn-elegant-outline {
            border: 2px solid #eee;
            color: var(--tpl-primary);
            background: transparent;
        }

        .btn-elegant-outline:hover {
            border-color: var(--tpl-primary);
            background: var(--tpl-primary);
            color: white;
        }

        /* Section Styling */
        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-eyebrow {
            color: var(--tpl-accent);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 15px;
            display: block;
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            color: var(--tpl-primary);
        }

        .news-card {
            border: none;
            background: #fff;
            border-radius: 30px;
            padding: 0;
            overflow: hidden;
            transition: all 0.5s ease;
            height: 100%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .news-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
        }

        .news-img-wrap {
            position: relative;
            padding-top: 75%;
            /* 4:3 aspect ratio */
            overflow: hidden;
        }

        .news-img-wrap img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .news-card:hover .news-img-wrap img {
            transform: scale(1.1);
        }

        .news-content {
            padding: 40px;
        }

        .news-date {
            color: var(--tpl-accent);
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 15px;
            display: block;
        }

        .news-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--tpl-primary);
            line-height: 1.4;
        }

        .event-strip {
            background: white;
            margin-bottom: 20px;
            padding: 30px;
            border-radius: 20px;
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .event-strip:hover {
            border-left-color: var(--tpl-accent);
            transform: translateX(10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
        }

        .event-date-box {
            background: var(--tpl-light);
            min-width: 90px;
            height: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            margin-right: 30px;
        }

        .date-day {
            font-size: 24px;
            font-weight: 800;
            color: var(--tpl-primary);
        }

        .date-month {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--tpl-accent);
        }

        /* Custom Navbar for Landing Page */
        .navbar-landing {
            position: absolute;
            width: 100%;
            padding: 30px 0;
            z-index: 1000;
            transition: all 0.4s ease;
        }

        .navbar-landing.scrolled {
            position: fixed;
            background: white !important;
            padding: 15px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        /* Hide standard header */
        .hero-inner {
            display: none;
        }

        #header {
            display: none;
        }

        /* Specific to your layout structure */
        .swiper {
            width: 100%;
            height: 85vh;
        }

        .slide-inner {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .overlay-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(1, 41, 112, 0.9) 0%, rgba(1, 41, 112, 0.4) 100%);
        }

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: translateY(-3px) scale(1.02);
        }

        .ls-2 {
            letter-spacing: 2px;
        }

        .hero-title span {
            color: var(--tpl-secondary);
            /* Or keep it as secondary color */
            position: relative;
            z-index: 1;
        }

        /* Make span text mimic the gold accent if on dark blue background for contrast */
        .hero-title span {
            color: #4facfe;
            background: -webkit-linear-gradient(#4facfe, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    <!-- Custom Landing Nav -->
    <nav class="navbar navbar-expand-lg navbar-landing" id="landingNav">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 text-primary" href="#">
                <img src="{{ $school->school_logo ?? asset('cloudiSchool.png') }}" height="50" class="me-2"
                    onerror="this.src='{{ asset('cloudiSchool.png') }}';">
                <span style="font-family: var(--font-heading); color: var(--tpl-primary)">{{ $school->schoolName }}</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-4">
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#news">Updates</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#events">Explore</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('login') }}">Portal Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-slider-section p-0">
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="slide-inner"
                        style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1920&auto=format&fit=crop');">
                        <div class="overlay-gradient"></div>
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-8" data-aos="fade-up" data-aos-duration="1200">
                                    <span
                                        class="badge bg-warning text-dark mb-4 px-3 py-2 fw-bold rounded-pill text-uppercase ls-2">Welcome
                                        to {{ $school->schoolName }}</span>
                                    <h1 class="hero-title text-white display-2 fw-bold mb-4">Shaping <span>Minds</span>,<br>
                                        Defining <span>Futures</span>.</h1>
                                    <p class="hero-desc text-white-50 lead mb-5 w-75">A dedicated sanctuary of learning
                                        where every child is empowered to reach their zenith through innovation, values, and
                                        world-class mentorship.</p>
                                    <div class="d-flex gap-3">
                                        <a href="#news"
                                            class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary shadow-lg hover-scale">Discover
                                            More</a>
                                        <a href="{{ route('login') }}"
                                            class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold hover-scale">Portal
                                            Access</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="slide-inner"
                        style="background-image: url('https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1920&auto=format&fit=crop');">
                        <div class="overlay-gradient"></div>
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-8" data-aos="fade-up" data-aos-duration="1200">
                                    <span
                                        class="badge bg-info text-dark mb-4 px-3 py-2 fw-bold rounded-pill text-uppercase ls-2">Creative
                                        Excellence</span>
                                    <h1 class="hero-title text-white display-2 fw-bold mb-4">Unleash Your
                                        <span>Creativity</span> &<br> <span>Potential</span>.</h1>
                                    <p class="hero-desc text-white-50 lead mb-5 w-75">Our vibrant arts and sciences programs
                                        are designed to inspire the next generation of innovators and thinkers.</p>
                                    <div class="d-flex gap-3">
                                        <a href="#events"
                                            class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary shadow-lg hover-scale">Explore
                                            Programs</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="slide-inner"
                        style="background-image: url('https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=1920&auto=format&fit=crop');">
                        <div class="overlay-gradient"></div>
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-8" data-aos="fade-up" data-aos-duration="1200">
                                    <span
                                        class="badge bg-success text-white mb-4 px-3 py-2 fw-bold rounded-pill text-uppercase ls-2">Holistic
                                        Growth</span>
                                    <h1 class="hero-title text-white display-2 fw-bold mb-4">A Community of
                                        <span>Learners</span> &<br> <span>Leaders</span>.</h1>
                                    <p class="hero-desc text-white-50 lead mb-5 w-75">Join a supportive community that
                                        values character, academic excellence, and social responsibility.</p>
                                    <div class="d-flex gap-3">
                                        <a href="{{ route('school.contact', $school->slug) }}"
                                            class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary shadow-lg hover-scale">Contact
                                            Admissions</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next text-white d-none d-lg-flex"></div>
            <div class="swiper-button-prev text-white d-none d-lg-flex"></div>
        </div>
    </section>

    <!-- Slick Slider for Highlights/Logos -->
    <section class="py-5 bg-light border-bottom">
        <div class="container">
            <h5 class="text-center text-muted text-uppercase ls-2 mb-5 fw-bold" data-aos="fade-up">Trusted by Leading
                Educational Partners</h5>
            <div class="partners-slider" data-aos="fade-up" data-aos-delay="100">
                <div class="px-4 text-center">
                    <h3 class="fw-bold text-secondary opacity-50">Cambridge</h3>
                </div>
                <div class="px-4 text-center">
                    <h3 class="fw-bold text-secondary opacity-50">Pearson</h3>
                </div>
                <div class="px-4 text-center">
                    <h3 class="fw-bold text-secondary opacity-50">Microsoft Edu</h3>
                </div>
                <div class="px-4 text-center">
                    <h3 class="fw-bold text-secondary opacity-50">Google for Edu</h3>
                </div>
                <div class="px-4 text-center">
                    <h3 class="fw-bold text-secondary opacity-50">Oxford</h3>
                </div>
                <div class="px-4 text-center">
                    <h3 class="fw-bold text-secondary opacity-50">IB World</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white" id="news">
        <div class="container py-5">
            <div class="section-header">
                <span class="section-eyebrow">News & Insights</span>
                <h2 class="section-title">Campus <span>Chronicles</span></h2>
            </div>
            <div class="row g-5">
                @forelse($news as $item)
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="news-card">
                            <div class="news-img-wrap">
                                <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=800' }}"
                                    alt="{{ $item->title }}">
                            </div>
                            <div class="news-content">
                                <span class="news-date">{{ \Carbon\Carbon::parse($item->created_at)->format('d M, Y') }}</span>
                                <h4 class="news-title">{{ $item->title }}</h4>
                                <p class="text-muted mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                                <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}"
                                    class="btn btn-link p-0 text-decoration-none fw-bold" style="color: var(--tpl-accent);">Read
                                    Article <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Stay tuned for exciting campus news.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-5" id="events" style="background: #fbfbfc;">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5" data-aos="fade-right">
                    <span class="section-eyebrow">Upcoming Events</span>
                    <h2 class="section-title text-start mb-4">Engaging <br><span>School Life</span></h2>
                    <p class="lead text-muted mb-5">From academic symposiums to athletic meets, our calendar is vibrant and
                        full of opportunities for holistic growth.</p>
                    <a href="{{ route('school.events', $school->slug) }}"
                        class="btn btn-elegant btn-elegant-primary">Explore Calendar</a>
                </div>
                <div class="col-lg-7">
                    @forelse($events as $event)
                        <div class="event-strip d-flex align-items-center" data-aos="fade-left"
                            data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="event-date-box">
                                <span class="date-day">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</span>
                                <span class="date-month">{{ \Carbon\Carbon::parse($event->event_date)->format('M') }}</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1" style="color: var(--tpl-primary);">{{ $event->title }}</h5>
                                <p class="text-muted mb-0"><i class="bi bi-geo-alt me-2"></i> {{ $event->location }}</p>
                            </div>
                            <div class="ms-auto">
                                <i class="bi bi-chevron-right text-muted"></i>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 bg-white rounded-4">
                            <p class="text-muted mb-0">No upcoming events scheduled at this time.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 mt-5">
        <div class="container bg-primary p-5 rounded-5" style="background: var(--primary-gradient) !important;"
            data-aos="zoom-in">
            <div class="row align-items-center text-center text-lg-start">
                <div class="col-lg-8">
                    <h2 class="text-white fw-bold mb-3 fs-1">Ready to start your journey?</h2>
                    <p class="text-white-50 fs-5 mb-0">Join us today and unlock a world of educational possibilities.</p>
                </div>
                <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('school.contact', $school->slug) }}"
                        class="btn btn-elegant bg-white text-primary px-5 btn-lg">Contact Us Now</a>
                </div>
            </div>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script>
        // Init Swiper
        var swiper = new Swiper(".heroSwiper", {
            spaceBetween: 0,
            effect: "fade",
            speed: 1000,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

        // Init Slick
        $(document).ready(function () {
            $('.partners-slider').slick({
                infinite: true,
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: false,
                dots: false,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                        }
                    }
                ]
            });
        });

        window.addEventListener('scroll', function () {
            const nav = document.getElementById('landingNav');
            if (window.scrollY > 80) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Custom Navbar logic to hide the main app header if on this specific page
        document.addEventListener('DOMContentLoaded', function () {
            const appHeader = document.getElementById('header');
            if (appHeader) appHeader.style.display = 'none';

            const appSidebar = document.getElementById('sidebar');
            if (appSidebar) appSidebar.style.display = 'none';

            const appMain = document.getElementById('main');
            if (appMain) {
                appMain.style.margin = '0';
                appMain.style.padding = '0';
            }
        });
    </script>
@endsection