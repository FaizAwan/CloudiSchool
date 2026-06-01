<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
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

    <title>@yield('title', 'Login to cloudischool | Secure School Management Software Access')</title>
    <meta name="description" content="@yield('meta_description', 'Securely log in to your cloudischool dashboard. The leading cloud school ERP SaaS for student tracking, staff management, and efficient administration.')">
    <meta name="keywords" content="cloudischool login, school management login, admin dashboard, teacher portal">
    <meta name="author" content="cloudischool Team">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{asset('cloudiSchool.png')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --cloudi-navy-light: #3b82f6;
            --cloudi-navy-mid: #1e40af;
            --cloudi-navy-dark: #172554;
            --primary: #1e40af;
            --primary-hover: #172554;
            --secondary: #1E293B;
            --accent: #00C896;
            --font-main: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-main);
            color: #1e293b;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .auth-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .auth-hero {
            flex: 1 1 55%;
            position: relative;
            overflow: hidden;
            display: none;
        }

        @media (min-width: 992px) {
            .auth-hero {
                display: block;
            }
        }

        .auth-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
        }

        .auth-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.6) 0%, rgba(30, 41, 59, 0.8) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 4rem;
            color: white;
        }

        .auth-form-side {
            flex: 1 1 45%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #ffffff;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #172554 100%) !important;
            border: none !important;
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
            color: white !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #172554 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.4);
        }

        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
            border-color: #1e40af;
        }

        /* Metallic Logo Styles */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap');

        .logo-container {
            font-family: 'Poppins', sans-serif;
            font-size: 40px;
            font-weight: 700;
            letter-spacing: -2px;
            display: inline-flex;
            align-items: center;
        }

        .cloudi {
            background: linear-gradient(to bottom, #89b8e2 0%, #4a8dc2 50%, #2a5a8a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(2px 4px 3px rgba(0, 0, 0, 0.2));
            position: relative;
        }

        .school {
            background: linear-gradient(to bottom, #f3d078 0%, #c9a041 50%, #8a6e2a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(2px 4px 3px rgba(0, 0, 0, 0.2));
            position: relative;
        }
    </style>
  
  <!-- Vendor CSS Files -->
  <link href="{{ asset('NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/simple-datatables/style.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/css/style.css')}}" rel="stylesheet">
</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    
  <!-- Vendor JS Files -->
  <script src="{{ asset('NiceAdmin/assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/chart.js/chart.umd.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/quill/quill.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/simple-datatables/simple-datatables.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/vendor/php-email-form/validate.js')}}"></script>
  <script src="{{ asset('NiceAdmin/assets/js/main.js')}}"></script>
</body>

</html>