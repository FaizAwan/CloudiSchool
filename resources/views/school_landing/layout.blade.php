<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $school->meta_title ?? $school->schoolName . ' | Official School Website')</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="@yield('meta_description', $school->meta_description ?? 'Welcome to ' . $school->schoolName . '. Experience a world-class education system designed to nurture creativity and excellence in ' . $school->schoolCity . '.')">
    <meta name="keywords"
        content="{{ $school->meta_keywords ?? 'school, education, ' . $school->schoolName . ', ' . $school->schoolCity . ', admissions, student portal' }}">
    <meta name="author" content="CloudiSchool">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $school->meta_title ?? $school->schoolName)">
    <meta property="og:description"
        content="@yield('meta_description', $school->meta_description ?? 'Join ' . $school->schoolName . ' for a bright future.')">
    <meta property="og:image"
        content="@yield('og_image', $school->school_logo ?? asset('images/default-school-og.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', $school->meta_title ?? $school->schoolName)">
    <meta property="twitter:description"
        content="@yield('meta_description', $school->meta_description ?? 'Join ' . $school->schoolName . ' for a bright future.')">
    <meta property="twitter:image"
        content="@yield('og_image', $school->school_logo ?? asset('images/default-school-og.jpg'))">

    <link rel="icon" type="image/png" href="{{ $school->school_logo ?? asset('favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0a6cff;
            --dark: #0f172a;
            --light: #f8fafc;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--light);
            color: var(--dark);
        }

        .hero-inner {
            padding: 150px 0 80px;
            background: linear-gradient(135deg, var(--primary) 0%, #00d2ff 100%);
            color: white;
            margin-bottom: 50px;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .card-premium {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card-premium:hover {
            transform: translateY(-10px);
        }

        footer {
            background: var(--dark);
            color: white;
            padding: 50px 0 30px;
            margin-top: 100px;
        }
    </style>
</head>

<body>

    @if(!View::hasSection('hide_navbar'))
        <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold text-primary" href="{{ route('school.landing', $school->slug) }}">
                    <i class="bi bi-mortarboard-fill me-2"></i>{{ $school->schoolName }}
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#landingNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="landingNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark"
                                href="{{ route('school.landing', $school->slug) }}#home">Home</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark"
                                href="{{ route('school.news', $school->slug) }}">News</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark"
                                href="{{ route('school.events', $school->slug) }}">Events</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark"
                                href="{{ route('school.blogs', $school->slug) }}">Blogs</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark"
                                href="{{ route('school.gallery', $school->slug) }}">Gallery</a></li>
                    </ul>
                    <div class="ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Portal
                            Login</a>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <div class="hero-inner">
        <div class="container text-center py-5">
            <h1 class="display-3 fw-bold mb-3">@yield('page_title')</h1>
            <p class="lead opacity-75">Discover the latest updates and moments from {{ $school->schoolName }}</p>
        </div>
    </div>

    <div class="container pb-5">
        @yield('content')
    </div>

    <footer class="mt-auto pt-5 pb-4" style="background: #0f172a; border-top: 5px solid var(--primary);">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a class="footer-logo text-white text-decoration-none fw-bold fs-2 mb-4 d-block" href="#">
                        <i class="bi bi-mortarboard-fill me-2 text-primary"></i>{{ $school->schoolName }}
                    </a>
                    <p class="text-white-50 mb-4 fs-6">Empowering the next generation with cutting-edge education and
                        strong character building. Join us in our journey of excellence.</p>
                    <div class="social-links d-flex">
                        @if($school->facebook_url)
                            <a href="{{ $school->facebook_url }}" target="_blank"
                                class="btn btn-primary btn-sm rounded-circle me-3"><i class="bi bi-facebook"></i></a>
                        @endif
                        @if($school->twitter_url)
                            <a href="{{ $school->twitter_url }}" target="_blank"
                                class="btn btn-info btn-sm rounded-circle me-3"><i class="bi bi-twitter text-white"></i></a>
                        @endif
                        @if($school->instagram_url)
                            <a href="{{ $school->instagram_url }}" target="_blank"
                                class="btn btn-danger btn-sm rounded-circle me-3"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if($school->linkedin_url)
                            <a href="{{ $school->linkedin_url }}" target="_blank"
                                class="btn btn-primary btn-sm rounded-circle me-3"><i class="bi bi-linkedin"></i></a>
                        @endif
                        @if($school->youtube_url)
                            <a href="{{ $school->youtube_url }}" target="_blank"
                                class="btn btn-danger btn-sm rounded-circle"><i class="bi bi-youtube"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="text-white fw-bold mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="{{ route('school.landing', $school->slug) }}"
                                class="text-white-50 text-decoration-none hover-white"><i
                                    class="bi bi-chevron-right small me-1"></i> Home</a></li>
                        <li class="mb-3"><a href="{{ route('school.news', $school->slug) }}"
                                class="text-white-50 text-decoration-none hover-white"><i
                                    class="bi bi-chevron-right small me-1"></i> School News</a></li>
                        <li class="mb-3"><a href="{{ route('school.events', $school->slug) }}"
                                class="text-white-50 text-decoration-none hover-white"><i
                                    class="bi bi-chevron-right small me-1"></i> Events</a></li>
                        <li class="mb-3"><a href="{{ route('school.gallery', $school->slug) }}"
                                class="text-white-50 text-decoration-none hover-white"><i
                                    class="bi bi-chevron-right small me-1"></i> Photo Gallery</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="text-white fw-bold mb-4">Contact Details</h5>
                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt text-primary fs-5 me-3"></i>
                        <p class="text-white-50 mb-0">{{ $school->address ?? $school->schoolCity }}</p>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="bi bi-envelope text-primary fs-5 me-3"></i>
                        <p class="text-white-50 mb-0">{{ $school->schoolAdminEmail }}</p>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-whatsapp text-primary fs-5 me-3"></i>
                        <p class="text-white-50 mb-0">{{ $school->whatsapp_number }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="text-white fw-bold mb-4">Support Local School</h5>
                    <p class="text-white-50 mb-4">Subscribe to our newsletter for weekly updates and notices.</p>
                    <div class="input-group shadow-sm">
                        <input type="email" class="form-control border-0 bg-dark text-white p-3"
                            placeholder="Enter email" style="background: rgba(255,255,255,0.05) !important;">
                        <button class="btn btn-primary px-4" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="my-5 border-secondary opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50 small">&copy; 2026 <strong>{{ $school->schoolName }}</strong>. All
                        rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-white-50 small">Developed with <i class="bi bi-heart-fill text-danger mx-1"></i>
                        by <strong>CloudiSchool</strong></p>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .hover-white:hover {
            color: white !important;
            padding-left: 5px;
            transition: 0.3s;
        }

        .hero-inner h1 {
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary) !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>