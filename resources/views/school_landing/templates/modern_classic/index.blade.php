@extends('school_landing.layout')

@section('title', $school->meta_title ?? $school->schoolName . ' | Official School Website')

@section('content')
<style>
    /* Premium Hero Slider */
    .hero-slider {
        position: relative;
        height: 100vh;
        overflow: hidden;
        margin-top: -80px;
        /* Offset for transparent navbar */
    }

    .carousel-item {
        height: 100vh;
        min-height: 600px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .carousel-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(10, 108, 255, 0.4) 100%);
    }

    .carousel-content {
        position: relative;
        z-index: 10;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: white;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 25px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .hero-title {
        font-size: 5rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 30px;
        text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .hero-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        max-width: 600px;
        line-height: 1.6;
        margin-bottom: 40px;
    }

    .btn-hero {
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.4s;
    }

    /* Hide the default hero from layout */
    .hero-inner {
        display: none;
    }

    body {
        padding-top: 0;
    }

    .navbar {
        background: transparent !important;
        box-shadow: none;
    }

    .navbar.scrolled {
        background: white !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Animated Shapes */
    .shape {
        position: absolute;
        z-index: 1;
        opacity: 0.1;
    }

    /* Announcements Marquee */
    .news-ticker {
        background: white;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        border-radius: 100px;
        padding: 10px 30px;
        position: relative;
        margin-top: -45px;
        z-index: 100;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>

<!-- Hero Slider Section -->
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
        <div class="carousel-inner h-100">
            <!-- Slide 1 -->
            <div class="carousel-item active" style="background-image: url('https://images.unsplash.com/photo-152305085306e-52a6a9af271d?w=1920&q=80');">
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-8" data-aos="fade-up">
                            <span class="hero-badge"><i class="bi bi-stars me-2 text-warning"></i> Excellence in Education</span>
                            <h1 class="hero-title">Welcome to <br><span class="text-primary">{{ $school->schoolName }}</span></h1>
                            <p class="hero-subtitle">We empower children to fulfill their potential through a rich, diverse and multi-sensory curriculum.</p>
                            <div class="d-flex gap-3">
                                <a href="#news" class="btn btn-primary btn-hero shadow-lg">Our Stories</a>
                                <a href="{{ route('login') }}" class="btn btn-outline-light btn-hero">Student Portal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1920&q=80');">
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-8">
                            <span class="hero-badge"><i class="bi bi-lightbulb me-2 text-info"></i> Modern Facilities</span>
                            <h1 class="hero-title">Innovative <br>Learning Spaces</h1>
                            <p class="hero-subtitle">Equipping our students with 21st-century skills in an environment that inspires curiosity and critical thinking.</p>
                            <div class="d-flex gap-3">
                                <a href="#gallery" class="btn btn-primary btn-hero shadow-lg">View Gallery</a>
                                <a href="{{ route('school.events', $school->slug) }}" class="btn btn-outline-light btn-hero">Events Calender</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=1920&q=80');">
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-8">
                            <span class="hero-badge"><i class="bi bi-people me-2 text-primary"></i> Global Community</span>
                            <h1 class="hero-title">A World of <br>Opportunities</h1>
                            <p class="hero-subtitle">Where students from all backgrounds come together to grow, learn, and lead in a globalized world.</p>
                            <div class="d-flex gap-3">
                                <a href="{{ route('school.blogs', $school->slug) }}" class="btn btn-primary btn-hero shadow-lg">Read Our Blogs</a>
                                <a href="#home" class="btn btn-outline-light btn-hero">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon p-4 rounded-circle bg-dark bg-opacity-25" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon p-4 rounded-circle bg-dark bg-opacity-25" aria-hidden="true"></span>
        </button>

        <!-- Indicators -->
        <div class="carousel-indicators mb-4 pb-4">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active rounded-circle" style="width: 12px; height: 12px;"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" class="rounded-circle" style="width: 12px; height: 12px;"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" class="rounded-circle" style="width: 12px; height: 12px;"></button>
        </div>
    </div>
</section>

<!-- News Ticker -->
<div class="container">
    <div class="news-ticker d-flex align-items-center">
        <div class="bg-primary text-white px-4 py-2 rounded-pill me-4 fw-bold small text-uppercase flex-shrink-0">Latest</div>
        <marquee behavior="scroll" direction="left" class="fw-semibold text-dark">
            @forelse($announcements as $item)
            <span class="me-5"><i class="bi bi-megaphone-fill text-primary me-2"></i> {{ $item->title }}: {{ $item->content }}</span>
            @empty
            <span class="me-5">Welcome to {{ $school->schoolName }} Official Website! Stay tuned for more updates.</span>
            @endforelse
        </marquee>
    </div>
</div>

<!-- Rest of the content remains premium -->
<section class="py-5 mt-5" id="news">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-lg-8" data-aos="fade-right">
                <h6 class="text-primary fw-bold text-uppercase letter-spacing-2">School Updates</h6>
                <h2 class="display-5 fw-bold mb-0">Discover Latest News</h2>
                <div class="mt-3" style="width: 80px; height: 5px; background: var(--primary); border-radius: 10px;"></div>
            </div>
            <div class="col-lg-4 text-lg-end d-none d-lg-block">
                <a href="{{ route('school.news', $school->slug) }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">View All Articles</a>
            </div>
        </div>

        <div class="row g-4">
            @foreach($news as $item)
            @php 
                $imgUrl = $item->image;
                if ($imgUrl && !Str::startsWith($imgUrl, ['http', 'https', 'data:'])) {
                    // Check if it's a relative path, prefix with storage
                    $imgUrl = url('storage/' . $imgUrl);
                }
                $imgUrl = $imgUrl ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800';
            @endphp
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="card card-premium overflow-hidden h-100 border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="position-relative">
                        <img src="{{ $imgUrl }}" 
                             alt="{{ $item->title }}"
                             class="card-img-top news-card-img" 
                             style="height: 250px; object-fit: cover;"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-152305085306e-52a6a9af271d?w=800&fit=crop';">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-white text-primary fw-bold p-2 shadow-sm rounded-3">
                            <i class="bi bi-calendar-event me-1"></i> {{ $item->created_at->format('d M') }}
                        </span>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <h4 class="fw-bold mb-3 h5 text-dark">{{ $item->title }}</h4>
                        <p class="text-muted small mb-4" style="line-height: 1.6;">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                        <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="btn btn-link p-0 text-decoration-none fw-bold text-primary">Read Narrative <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-light" id="events">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=800&q=80" alt="Events" class="img-fluid rounded-5 shadow-lg">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h6 class="text-primary fw-bold text-uppercase letter-spacing-2 mb-3">Save the Date</h6>
                <h2 class="display-5 fw-bold mb-4">Upcoming School Events</h2>

                <div class="event-stack">
                    @forelse($events as $event)
                    <div class="d-flex align-items-center p-4 bg-white rounded-4 shadow-sm mb-3 border-start border-5 border-primary">
                        <div class="date-box bg-light p-3 rounded-3 text-center me-4" style="min-width: 80px;">
                            <span class="d-block fw-bold fs-4 text-primary">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</span>
                            <span class="d-block small text-muted text-uppercase">{{ \Carbon\Carbon::parse($event->event_date)->format('M') }}</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $event->title }}</h5>
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1 text-primary"></i> {{ $event->location }}</p>
                        </div>
                        <a href="{{ route('school.event.show', [$school->slug, $event->slug]) }}" class="ms-auto btn btn-primary btn-sm rounded-circle p-2"><i class="bi bi-chevron-right"></i></a>
                    </div>
                    @empty
                    <p class="text-muted">No upcoming events scheduled at the moment.</p>
                    @endforelse
                </div>

                <a href="{{ route('school.events', $school->slug) }}" class="btn btn-primary mt-4 rounded-pill px-5 fw-bold">Full Calendar</a>
            </div>
        </div>
    </div>
</section>

<script>
    // Header Scroll Effect
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // Carousel Autoplay speed
    var myCarousel = document.querySelector('#heroCarousel')
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 5000,
        wrap: true
    })
</script>
@endsection