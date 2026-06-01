<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | CloudiSchool SaaS</title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FX90V8QF1G"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-FX90V8QF1G');
    </script>

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9964964373733994"
        crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --cloudi-blue-light: #89b8e2;
            --cloudi-blue-mid: #4a8dc2;
            --cloudi-blue-dark: #2a5a8a;
            --primary: #4a8dc2;
            --secondary: #1E293B;
            --light: #F8FAFC;
        }

        /* STITCH SKILL: Global Blue Unification */
        .btn-primary {
            background: linear-gradient(135deg, #89b8e2 0%, #4a8dc2 50%, #2a5a8a 100%) !important;
            border: none !important;
            color: white !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4a8dc2 0%, #2a5a8a 100%) !important;
        }

        .btn-outline-primary {
            color: #4a8dc2 !important;
            border-color: #4a8dc2 !important;
        }

        .btn-outline-primary:hover {
            background: #4a8dc2 !important;
            color: white !important;
        }

        a,
        .text-primary {
            color: #4a8dc2 !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fff;
            color: #334155;
        }

        .navbar-saas {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
        }

        .logo-container {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
        }

        .cloudi {
            background: linear-gradient(to bottom, #89b8e2 0%, #4a8dc2 50%, #2a5a8a 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .school {
            background: linear-gradient(to bottom, #f3d078 0%, #c9a041 50%, #8a6e2a 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header {
            background: linear-gradient(135deg, #4a8dc2 0%, #00C896 100%);
            padding: 100px 0 60px;
            color: white;
            text-align: center;
            margin-bottom: 80px;
        }

        .content-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            padding: 60px;
            margin-top: -120px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(0, 0, 0, 0.02);
            line-height: 1.8;
        }

        footer {
            background: var(--secondary);
            color: #94a3b8;
            padding: 60px 0 30px;
            margin-top: 100px;
        }

        footer a {
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
        }

        footer a:hover {
            color: white;
        }

        footer h6 {
            color: white;
            font-weight: 700;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-saas fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <div class="logo-container">
                    <span class="cloudi">Cloudi</span><span class="school">School</span>
                </div>
            </a>
            <div class="ms-auto">
                <a href="{{ route('landing') }}" class="btn btn-outline-primary rounded-pill px-4">Back to Home</a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="display-3 fw-800">@yield('header_title')</h1>
            <p class="lead opacity-75">@yield('header_subtitle')</p>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container text-center">
            <div class="logo-container mb-4">
                <span class="cloudi">Cloudi</span><span class="school">School</span>
            </div>
            <p class="mb-5 mx-auto" style="max-width: 600px;">The world's most trusted school management SaaS platform. Bringing innovation to every classroom.</p>
            <div class="d-flex justify-content-center gap-4 flex-wrap mb-5">
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('careers') }}">Careers</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('help-center') }}">Help Center</a>
                <a href="{{ route('status') }}">Status</a>
            </div>
            <hr class="border-secondary opacity-25 mb-4">
            <p class="small">&copy; {{ date('Y') }} CloudiSchool. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>