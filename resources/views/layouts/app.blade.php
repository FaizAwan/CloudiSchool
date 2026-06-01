<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <!-- DNS Prefetch & Preconnect for external domains -->
  <link rel="dns-prefetch" href="//fonts.googleapis.com">
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
  <link rel="dns-prefetch" href="//code.jquery.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>CloudiSchool</title>
  <meta name="description" content="School Management System">
  <meta name="keywords" content="School, Management, Education">
  <meta name="author" content="Smartest Developers">
  <link rel="icon" type="image/x-icon" href="{{asset('cloudiSchool.png')}}">

  <meta property="og:title" content="School Management System" />
  <meta property="og:description" content="School Management System" />
  <meta property="og:image" content="{{asset('cloudiSchool.png')}}" />

  <!-- Favicons -->
  <link href="{{ asset('NiceAdmin/assets/img/favicon.png')}}" rel="icon">
  <link href="{{ asset('NiceAdmin/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

  <!-- Critical CSS - Bootstrap & Template (render-blocking, needed immediately) -->
  <link href="{{ asset('NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/css/style.css')}}" rel="stylesheet">

  <!-- Non-critical CSS - loaded async via media trick -->
  <link href="{{ asset('NiceAdmin/assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="{{ asset('NiceAdmin/assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="{{ asset('NiceAdmin/assets/vendor/simple-datatables/style.css')}}" rel="stylesheet" media="print" onload="this.media='all'">

  <!-- Quill CSS - only needed on pages with rich editors -->
  @hasSection('needs-quill')
  <link href="{{ asset('NiceAdmin/assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{ asset('NiceAdmin/assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
  @endif

  <!-- External CSS - async loaded -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" media="print" onload="this.media='all'">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">

  <!-- Google Fonts - non-blocking with display=swap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

  <!-- Google Analytics - deferred to not block rendering -->
  <script>
    window.addEventListener('load', function() {
      var s = document.createElement('script');
      s.src = 'https://www.googletagmanager.com/gtag/js?id=G-FX90V8QF1G';
      s.async = true;
      document.head.appendChild(s);
      s.onload = function() {
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-FX90V8QF1G');
      };
    });
  </script>
  <style>
    :root {
        --base-60: #F8FAFC;
        --secondary-30: #0EA5E9;
        --accent-10: #1E293B;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
    }

    body {
        background-color: var(--base-60) !important;
        color: var(--accent-10);
        font-family: 'Inter', sans-serif !important;
    }

    .pagetitle h1, .page-title-box h1 {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 800 !important;
        color: var(--accent-10);
        text-transform: uppercase !important;
        letter-spacing: 12px !important;
        font-size: 1.75rem !important;
        margin-bottom: 0.5rem !important;
    }

    .glass-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: var(--glass-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.2);
    }

    .card-premium {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: var(--glass-shadow);
    }

    .card-header {
        background: transparent !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        padding: 1.5rem !important;
    }

    .card-title {
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        color: var(--accent-10) !important;
        margin: 0 !important;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        padding: 0 !important;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--secondary-30);
        font-weight: 600;
    }

    .btn-gradient, .btn-cinematic {
        background: var(--secondary-30);
        color: white !important;
        border: none;
        font-weight: 700;
        letter-spacing: 0.025em;
        transition: all 0.3s ease;
        border-radius: 0.75rem;
    }

    .btn-gradient:hover, .btn-cinematic:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.4);
        color: white !important;
        background: #0284c7;
    }

    /* Premium Component Architecture */
    .card-header-premium, .modal-header-premium {
        background: linear-gradient(135deg, #1e40af 0%, #172554 100%) !important;
        padding: 1.5rem 2rem !important;
        border: none !important;
        color: white !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        font-family: 'Outfit', sans-serif !important;
        display: flex !important;
        align-items: center !important;
        border-radius: 1rem 1rem 0 0 !important;
    }
    .card-header-premium i, .modal-header-premium i {
        color: white !important;
    }

    /* Modal Sophistication */
    .modal-content-premium {
        border-radius: 30px !important;
        border: none !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        overflow: hidden !important;
    }
    .modal-header-premium {
        border-radius: 0 !important; /* Managed by content-premium */
        padding: 2rem 2.5rem !important;
    }
    .modal-footer-premium {
        padding: 1.5rem 2.5rem 2.5rem 2.5rem !important;
        border: none !important;
        display: flex !important;
        gap: 1rem !important;
    }

    /* Professional Grid Scaling */
    .table-premium {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 8px !important;
    }
    .table-premium thead th {
        background: #f8fafc !important;
        color: #64748b !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        font-size: 0.75rem !important;
        letter-spacing: 1px;
        padding: 1.25rem 1rem !important;
        border-bottom: 2px solid #f1f5f9 !important;
        border-top: none !important;
    }
    .table-premium tbody tr {
        background: white !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
    }
    .table-premium tbody tr:hover {
        background-color: #f8fafc !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 15px rgba(0,0,0,0.05) !important;
    }
    .table-premium td {
        padding: 1.25rem 1rem !important;
        vertical-align: middle !important;
        border: none !important;
        color: var(--accent-10) !important;
    }

    /* Table Professional Styling */
    .table-premium, #parentsTable, .table-cinematic {
        border-collapse: separate !important;
        border-spacing: 0 4px !important;
        background-color: transparent !important;
    }

    .table-premium thead th, #parentsTable thead th, .table-cinematic thead th {
        background: #f1f5f9 !important;
        border: none !important;
        color: var(--accent-10) !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 1.25rem 1rem !important;
        letter-spacing: 0.05em;
    }

    .table-premium tbody tr, #parentsTable tbody tr, .table-cinematic tbody tr {
        background: white;
        border-bottom: 1px solid #f1f5f9 !important;
        transition: all 0.2s ease;
    }

    .table-premium tbody tr:hover, #parentsTable tbody tr:hover, .table-cinematic tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .table-premium td, #parentsTable td, .table-cinematic td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        color: var(--accent-10);
        font-weight: 500;
    }

    .badge-soft-info { background: #e0f2fe; color: #0369a1; }
    .badge-soft-success { background: #dcfce7; color: #15803d; }
    .badge-soft-secondary { background: #f1f5f9; color: #475569; }

    .shimmer {
        background: #f6f7f8;
        background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
        background-repeat: no-repeat;
        background-size: 800px 104px;
        display: inline-block;
        position: relative; 
        animation-duration: 1s;
        animation-fill-mode: forwards; 
        animation-iteration-count: infinite;
        animation-name: shimmer;
        animation-timing-function: linear;
    }

    @keyframes shimmer {
        0% { background-position: -468px 0; }
        100% { background-position: 468px 0; }
    }

    .navbar-brand,
    .nav-link {
      color: #C9DC86;
    }

    .card-body {
      margin: 20px !important;
    }

    .active {
      color: #000 !important;
    }

    .header {
      background: #4169E1;
      /* fallback for old browsers */
      background: -webkit-linear-gradient(to right, #f2fcfe, #4169E1);
      /* Chrome 10-25, Safari 5.1-6 */
      background: linear-gradient(to right, #f2fcfe, #4169E1);
      /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .card-header {
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important;
    }

    .dashboard .info-card h6 {
      font-size: 15px !important;
    }

    /* Global consistent sidebar width across all modules */
    @media (min-width: 992px) {
      aside.sidebar {
        width: 260px !important;
      }

      .sidebar .sidebar-nav {
        width: 260px !important;
      }

      main.main,
      #main.main,
      #main {
        margin-left: 260px !important;
      }
    }

    /* Global Fix for Bootstrap 5 Floating Labels (Top Clipping Issue) */
    .form-floating > .form-control {
      height: calc(3.5rem + 2px) !important;
      padding-top: 1.625rem !important;
      padding-bottom: 0.625rem !important;
      line-height: 1.5 !important;
    }

    .form-floating > label {
      padding: 1rem 0.75rem !important;
      color: #899bbd !important;
      z-index: 5 !important;
    }

    /* Ensure label stays small and correctly positioned when input has content or focus */
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label {
      transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem) !important;
      background-color: transparent !important;
      opacity: 1 !important;
      font-weight: 600 !important;
      color: #4169E1 !important;
    }

    /* Global compact, full-width layout like exams across all modules */
    @media (min-width: 992px) {

      .container,
      .container-fluid,
      .container-xxl,
      .container-xl,
      .container-lg,
      .container-md,
      .container-sm {
        max-width: 100% !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
      }

      main.main {
        padding-left: 8px !important;
        padding-right: 8px !important;
      }

      .row {
        --bs-gutter-x: .5rem !important;
      }

      .card {
        margin: 8px 0 !important;
      }

      .card-body {
        padding: 8px !important;
        margin: 0 !important;
      }

      .table-responsive {
        overflow-x: hidden !important;
      }

      /* Make listing tables fit the screen and keep actions visible */
      table.table {
        table-layout: fixed;
        width: 100% !important;
      }

      table.table th,
      table.table td {
        padding: 6px 8px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      table.table td:last-child,
      table.table td.actions {
        overflow: visible !important;
        text-overflow: clip !important;
      }

      table.table td:last-child .btn,
      table.table td.actions .btn {
        padding: 2px 8px;
        font-size: 12px;
        white-space: nowrap;
      }

      table.table td:last-child .btn+.btn,
      table.table td.actions .btn+.btn {
        margin-left: 6px;
      }
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap');
    @media (min-width: 1200px) {
      .logo {
        width: 150px !important;
      }

      .logo-container {
        font-family: 'Poppins', sans-serif;

        font-weight: 700;
        letter-spacing: -2px;
      }

      /* Shiny Navy Blue & Gold Effect */
      .cloudi {
        background: linear-gradient(to bottom, #3b82f6 0%, #1e40af 50%, #172554 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(2px 4px 4px rgba(0, 0, 0, 0.3));
      }

      .school {
        background: linear-gradient(to bottom, #f3d078 0%, #c9a041 50%, #8a6e2a 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(2px 4px 4px rgba(0, 0, 0, 0.3));
      }

      .cloudi,
      .school {
        position: relative;
        display: inline-block;
      }

      /* STITCH SKILL: Shiny Navy Unification */
      :root {
        --cloudi-navy-light: #3b82f6;
        --cloudi-navy-mid: #1e40af;
        --cloudi-navy-dark: #172554;
        --cloudi-gold: #c9a041;
        --bs-primary: #1e40af;
        --bs-primary-rgb: 30, 64, 175;
        --bs-link-color: #1e40af;
        --bs-link-hover-color: #172554;
        --premium-shadow: 0 10px 30px rgba(23, 37, 84, 0.1);
        --primary-light: rgba(30, 64, 175, 0.1);
        --success-light: rgba(25, 135, 84, 0.1);
        --warning-light: rgba(255, 193, 7, 0.1);
        --info-light: rgba(13, 202, 240, 0.1);
      }

      .bg-primary-light {
        background-color: var(--primary-light) !important;
      }

      .bg-success-light {
        background-color: var(--success-light) !important;
      }

      .bg-warning-light {
        background-color: var(--warning-light) !important;
      }

      .bg-info-light {
        background-color: var(--info-light) !important;
      }

      /* Global Reset & Premium Base */
      body {
        background-color: #f6f9ff !important;
        font-family: 'Poppins', 'Inter', sans-serif !important;
      }

      /* Premium Card Styling */
      .card {
        border: none !important;
        border-radius: 20px !important;
        box-shadow: var(--premium-shadow) !important;
        overflow: hidden !important;
        transition: transform 0.3s ease !important;
      }

      .card-header {
        background: linear-gradient(135deg, #1e40af 0%, #172554 100%) !important;
        padding: 1.25rem !important;
        border-bottom: none !important;
      }

      .card-header h1,
      .card-header h2,
      .card-header h3,
      .card-header h4,
      .card-header h5,
      .card-header h6 {
        color: #fff !important;
        margin-bottom: 0 !important;
        font-weight: 700 !important;
      }

      /* Override Bootstrap Primary Buttons - Shiny Navy */
      .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #172554 100%) !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(30, 64, 175, 0.4) !important;
        transition: all 0.3s ease !important;
      }

      .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #172554 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(23, 37, 84, 0.5) !important;
      }

      /* Text and Links - Shiny Navy */
      a:not(.btn):not(.btn-link),
      .text-primary,
      .sidebar-nav .nav-link,
      .sidebar-nav .nav-link i,
      .sidebar-nav .nav-content a:hover,
      .sidebar-nav .nav-content a.active,
      .pagetitle h1,
      .card-title,
      .nav-tabs-bordered .nav-link.active,
      .nav-tabs-bordered .nav-link:hover {
        color: #1e40af !important;
      }

      .card-header .card-title {
        color: #fff !important;
      }

      /* NiceAdmin Specific Navy Overrides */
      .header-nav .profile .dropdown-item i,
      .header-nav .notifications .notification-item i,
      .back-to-top {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
      }

      .back-to-top:hover {
        background: #172554 !important;
      }

      /* Clickable Card Hover Match */
      .clickable-card:hover .card-title,
      .card-title {
        color: #172554 !important;
      }

      /* Badge Primary */
      .bg-primary {
        background-color: #1e40af !important;
      }

      /* Premium Table Styling */
      .table {
        border-collapse: separate !important;
        border-spacing: 0 8px !important;
      }

      .table thead th {
        background-color: transparent !important;
        border: none !important;
        color: #64748b !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        font-size: 0.75rem !important;
        letter-spacing: 1px;
        padding: 1rem !important;
      }

      .table tbody tr {
        background-color: #fff !important;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02) !important;
        border-radius: 12px !important;
        transition: all 0.2s ease;
      }

      .table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05) !important;
        background-color: #fff !important;
      }

      .table td {
        border: none !important;
        padding: 1.25rem 1rem !important;
        vertical-align: middle !important;
      }

      .table tbody tr td:first-child {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
      }

      .table tbody tr td:last-child {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
      }

      /* DataTables Search Input */
      .dataTables_filter input {
        border-radius: 50px !important;
        padding: 0.6rem 1.5rem !important;
        border: 1px solid #e2e8f0 !important;
        outline: none !important;
        transition: all 0.3s;
      }

      .dataTables_filter input:focus {
        border-color: #1e40af !important;
        box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1) !important;
      }

      /* Premium Forms */
      .form-control,
      .form-select {
        border-radius: 12px !important;
        padding: 0.75rem 1.25rem !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.3s !important;
      }

      .form-control:focus,
      .form-select:focus {
        border-color: #1e40af !important;
        box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1) !important;
      }

      label {
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 0.5rem !important;
        font-size: 0.9rem !important;
      }

      /* Sidebar Active State Fix */
      .sidebar-nav .nav-link:not(.collapsed) {
        background-color: rgba(30, 64, 175, 0.1) !important;
      }

      /* Remove Legacy Extra Headers Pattern */
      .extra-card-header {
        display: none !important;
      }

      /* Sidebar Active State Fix */
      .sidebar-nav .nav-link:not(.collapsed) {
        background-color: rgba(30, 64, 175, 0.1) !important;
      }

      /* Fix for hiding text in sidebar sub-menus */
      .sidebar-nav .nav-content a span {
        white-space: normal !important;
        line-height: 1.3 !important;
        display: inline-block !important;
        vertical-align: middle !important;
        padding-right: 10px !important;
      }
      
      .sidebar-nav .nav-link span {
        white-space: normal !important;
        line-height: 1.3 !important;
      }
  </style>


</head>

<body class="sb-nav-fixed">

  <div id="app">
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

      <div class="d-flex align-items-center">
        <span class="logo d-flex align-items-center" style="cursor: default;">

          <div class="logo-container">
            <span class="cloudi">Cloudi</span><span class="school">School</span>
          </div>
        </span>
        <i class="bi bi-list toggle-sidebar-btn" style="margin-left: 15px;"></i>
      </div><!-- End Logo -->

      <!--<div class="search-bar">-->
      <!--  <form class="search-form d-flex align-items-center" method="POST" action="#">-->
      <!--    <input type="text" name="query" placeholder="Search" title="Enter search keyword">-->
      <!--    <button type="submit" title="Search"><i class="bi bi-search"></i></button>-->
      <!--  </form>-->
      <!--</div><!-- End Search Bar -->

      <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

          <!--<li class="nav-item d-block d-lg-none">-->
          <!--  <a class="nav-link nav-icon search-bar-toggle " href="#">-->
          <!--    <i class="bi bi-search"></i>-->
          <!--  </a>-->
          <!--</li><!-- End Search Icon-->

          <li class="nav-item dropdown">


            <!--<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">-->
            <!--  <li class="dropdown-header">-->
            <!--    You have 4 new notifications-->
            <!--    <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>-->
            <!--  </li>-->
            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="notification-item">-->
            <!--    <i class="bi bi-exclamation-circle text-warning"></i>-->
            <!--    <div>-->
            <!--      <h4>Lorem Ipsum</h4>-->
            <!--      <p>Quae dolorem earum veritatis oditseno</p>-->
            <!--      <p>30 min. ago</p>-->
            <!--    </div>-->
            <!--  </li>-->

            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="notification-item">-->
            <!--    <i class="bi bi-x-circle text-danger"></i>-->
            <!--    <div>-->
            <!--      <h4>Atque rerum nesciunt</h4>-->
            <!--      <p>Quae dolorem earum veritatis oditseno</p>-->
            <!--      <p>1 hr. ago</p>-->
            <!--    </div>-->
            <!--  </li>-->

            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="notification-item">-->
            <!--    <i class="bi bi-check-circle text-success"></i>-->
            <!--    <div>-->
            <!--      <h4>Sit rerum fuga</h4>-->
            <!--      <p>Quae dolorem earum veritatis oditseno</p>-->
            <!--      <p>2 hrs. ago</p>-->
            <!--    </div>-->
            <!--  </li>-->

            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="notification-item">-->
            <!--    <i class="bi bi-info-circle text-primary"></i>-->
            <!--    <div>-->
            <!--      <h4>Dicta reprehenderit</h4>-->
            <!--      <p>Quae dolorem earum veritatis oditseno</p>-->
            <!--      <p>4 hrs. ago</p>-->
            <!--    </div>-->
            <!--  </li>-->

            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->
            <!--  <li class="dropdown-footer">-->
            <!--    <a href="#">Show all notifications</a>-->
            <!--  </li>-->

            <!--</ul><!-- End Notification Dropdown Items -->

          </li><!-- End Notification Nav -->

          <li class="nav-item dropdown">

            <!--<a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">-->
            <!--  <i class="bi bi-chat-left-text"></i>-->
            <!--  <span class="badge bg-success badge-number">3</span>-->
            <!--</a><!-- End Messages Icon -->

            <!--<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">-->
            <!--  <li class="dropdown-header">-->
            <!--    You have 3 new messages-->
            <!--    <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>-->
            <!--  </li>-->
            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="message-item">-->
            <!--    <a href="#">-->
            <!--      <img src="{{ asset('NiceAdmin/assets/img/messages-1.jpg')}}" alt="" class="rounded-circle">-->
            <!--      <div>-->
            <!--        <h4>Maria Hudson</h4>-->
            <!--        <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>-->
            <!--        <p>4 hrs. ago</p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--  </li>-->
            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="message-item">-->
            <!--    <a href="#">-->
            <!--      <img src="{{ asset('NiceAdmin/assets/img/messages-2.jpg')}}" alt="" class="rounded-circle">-->
            <!--      <div>-->
            <!--        <h4>Anna Nelson</h4>-->
            <!--        <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>-->
            <!--        <p>6 hrs. ago</p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--  </li>-->
            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="message-item">-->
            <!--    <a href="#">-->
            <!--      <img src="{{ asset('NiceAdmin/assets/img/messages-3.jpg')}}" alt="" class="rounded-circle">-->
            <!--      <div>-->
            <!--        <h4>David Muldon</h4>-->
            <!--        <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>-->
            <!--        <p>8 hrs. ago</p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--  </li>-->
            <!--  <li>-->
            <!--    <hr class="dropdown-divider">-->
            <!--  </li>-->

            <!--  <li class="dropdown-footer">-->
            <!--    <a href="#">Show all messages</a>-->
            <!--  </li>-->

            <!--</ul><!-- End Messages Dropdown Items -->

          </li><!-- End Messages Nav -->

          @auth
          <li class="nav-item dropdown pe-3">

            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              @if(Auth::user()->profile_image)
              <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
              @else
              <img src="{{ asset('NiceAdmin/assets/img/profile-img.jpg')}}" alt="Profile" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
              @endif
              <span class="d-none d-md-block dropdown-toggle ps-2">{{Auth::user()->name}}</span>
            </a><!-- End Profile Image Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                @if(Auth::user()->role == 'teacher')
                @php
                $schoolName = \Cache::remember('school_name_'.Auth::id(), 600, function() {
                    return DB::table('schools')->where('id','=',Auth::user()->school_id)->value('schoolName') ?? 'My School';
                });
                @endphp
                <h6>{{ $schoolName }}</h6>
                <span>{{Auth::user()->role}}</span>
                @else
                <h6>F G F P S</h6>
                <span>{{Auth::user()->name}}</span>
                @endif

              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{route('profile.index')}}">
                  <i class="bi bi-person text-primary"></i>
                  <span>My Profile</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{route('profile.edit')}}">
                  <i class="bi bi-pencil text-warning"></i>
                  <span>Edit Profile</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{route('profile.settings')}}">
                  <i class="bi bi-gear text-info"></i>
                  <span>Account Settings</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                @php
                $logoutRoute = function_exists('tenant') && tenant() ? route('tenant.logout', tenant('id')) : route('logout');
                @endphp
                <a class="dropdown-item d-flex align-items-center" href="{{ $logoutRoute }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ $logoutRoute }}" method="POST" class="d-none">
                  @csrf
                </form>
              </li>
            </ul><!-- End Profile Dropdown Items -->
          </li><!-- End Profile Nav -->
          @else
          <li class="nav-item pe-3">
            <a class="nav-link" href="{{ route('login') }}">Login</a>
          </li>
          @endauth
        </ul>
      </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @auth
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link" href="#" onclick="return false;">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-heading">CMS Management</li>
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#cms-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-layout-text-window-reverse"></i><span>Website CMS</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="cms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('cms.news.index') }}">
                <i class="bi bi-circle"></i><span>News Updates</span>
              </a>
            </li>
            <li>
              <a href="{{ route('cms.events.index') }}">
                <i class="bi bi-circle"></i><span>School Events</span>
              </a>
            </li>
            <li>
              <a href="{{ route('cms.gallery.index') }}">
                <i class="bi bi-circle"></i><span>Photo Gallery</span>
              </a>
            </li>
            <li>
              <a href="{{ route('cms.announcements.index') }}">
                <i class="bi bi-circle"></i><span>Announcements</span>
              </a>
            </li>
            <li>
              <a href="{{ route('cms.blogs.index') }}">
                <i class="bi bi-circle"></i><span>School Blogs</span>
              </a>
            </li>
            <li>
              <a href="{{ route('cms.templates.index') }}">
                <i class="bi bi-circle"></i><span>Choose Template</span>
              </a>
            </li>
            <li>
              @php
              $school = \Cache::remember('school_data_'.Auth::id(), 600, function() {
                  return \DB::table('schools')->where('id', Auth::user()->tenant_id)->first();
              });
              $landingUrl = ($school && !empty($school->slug)) ? route('school.landing', $school->slug) : ($school ? route('school.landing', $school->tenant_id) : '#');
              @endphp
              <a href="{{ $landingUrl }}" target="_blank">
                <i class="bi bi-eye text-success"></i><span>View Website</span>
              </a>
            </li>
          </ul>
        </li>

        @if(Auth::user()->role === 'superadmin')
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navPackages" data-bs-toggle="collapse" href="#">
            <i class="bi bi-box-seam"></i><span>Packages</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navPackages" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('superadmin.packages.index') }}">
                <i class="bi bi-circle"></i><span>All Packages</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navTenants" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people"></i><span>Tenants</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navTenants" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('superadmin.tenants.all') }}">
                <i class="bi bi-circle"></i><span>All Tenants</span>
              </a>
            </li>
            <li>
              <a href="{{ route('superadmin.tenants.all') }}">
                <i class="bi bi-person-badge"></i><span>Tenants Impersonation</span>
              </a>
            </li>
          </ul>
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navBlogs" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-text"></i><span>Blogs</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navBlogs" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('superadmin.blogs.index') }}">
                <i class="bi bi-circle"></i><span>Manage Blogs</span>
              </a>
            </li>
          </ul>
        </li>
        @endif

        {{-- Admin can see menu, but routes restrict data; Superadmin has full access --}}
        @if(in_array(Auth::user()->role, ['admin','superadmin']) && Auth::user()->role !== 'superadmin')

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navSchools" data-bs-toggle="collapse" href="#">
            <i class="bi bi-buildings"></i><span>School Branch</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navSchools" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('schools')}}">
                <i class="bi bi-circle"></i><span>List School Branches</span>
              </a>
            </li>

            <li>
              <a href="{{route('academic-years.index')}}">
                <i class="bi bi-circle"></i><span>List Academic Year</span>
              </a>
            </li>
          </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navClasses" data-bs-toggle="collapse" href="#">
            <i class="bi bi-menu-button-wide"></i><span>Classes</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navClasses" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('classes')}}">
                <i class="bi bi-circle"></i><span>List Classes</span>
              </a>
            </li>
          </ul>
        </li><!-- End Components Nav -->

        <!-- Subjects Management -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navSubjects" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Subjects</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navSubjects" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('subjects.index') }}">
                <i class="bi bi-circle"></i><span>Manage Subjects</span>
              </a>
            </li>
            <li>
              <a href="{{ route('term-subjects.index') }}">
                <i class="bi bi-circle"></i><span>Term Subjects Wizard</span>
              </a>
            </li>
          </ul>
        </li><!-- End Subjects Nav -->




        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navTeachers" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Teachers</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navTeachers" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('teachers')}}">
                <i class="bi bi-circle"></i><span>List Teachers</span>
              </a>
            </li>
            <!-- <li>
        <a href="{{route('classes')}}">
          <i class="bi bi-circle"></i><span>Add New Class</span>
        </a>
      </li> -->
          </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navParents" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Parents</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navParents" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('parents')}}">
                <i class="bi bi-circle"></i><span>List Parents</span>
              </a>
            </li>
            <!-- <li>
        <a href="{{route('parents')}}">
          <i class="bi bi-circle"></i><span>Add New Parent</span>
        </a>
      </li> -->
          </ul>
        </li><!-- End Components Nav -->


        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navStudents" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navStudents" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('students')}}">
                <i class="bi bi-circle"></i><span>List Students</span>
              </a>
            </li>

            <li>
              <a href="{{route('studentsWithSameGRno')}}">
                <i class="bi bi-circle"></i><span>Duplicate GRNO</span>
              </a>
            </li>

            <li>
              <a href="{{route('studentsListGRno')}}">
                <i class="bi bi-circle"></i><span>Student List GRNO</span>
              </a>
            </li>

            <li>
              <a href="{{route('studentsListSLC')}}">
                <i class="bi bi-circle"></i><span>Student List SLC</span>
              </a>
            </li>



            <!-- <li>
        <a href="{{route('students')}}">
          <i class="bi bi-circle"></i><span>Add New student</span>
        </a>
      </li> -->
          </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navTimetable" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-text"></i><span>TimeTable</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navTimetable" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('periods')}}">
                <i class="bi bi-list"></i><span>Periods (Time Slot)</span>
              </a>
            </li>
            <li>
              <a href="{{route('weeklyTimetable')}}">
                <i class="bi bi-list"></i><span>Weekly Timetable</span>
              </a>
            </li>
            <!-- <li>
        <a href="components-accordion.html">
          <i class="bi bi-circle"></i><span>Add New Fee Type</span>
        </a>
      </li> -->
          </ul>
        </li><!-- End Components Nav -->


        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navFees" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-text"></i><span>Fees</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navFees" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('fees')}}">
                <i class="bi bi-circle"></i><span>List Fee Types</span>
              </a>
            </li>

            <li>
              <a href="{{route('feesManagement')}}">
                <i class="bi bi-circle"></i><span>Fee Management</span>
              </a>
            </li>

            <li>
              <a href="{{route('challan')}}">
                <i class="bi bi-circle"></i><span>Create Challan</span>
              </a>
            </li>

            <li>
              <a href="{{route('challanPaid')}}">
                <i class="bi bi-circle"></i><span>Challan Paid</span>
              </a>
            </li>

            <!--<li>-->
            <!--  <a href="{{route('classWiseChallan')}}">-->
            <!--    <i class="bi bi-circle"></i><span>Print Classwise Challan</span>-->
            <!--  </a>-->
            <!--</li>-->

            <!-- <li>
        <a href="components-accordion.html">
          <i class="bi bi-circle"></i><span>Add New Fee Type</span>
        </a>
      </li> -->
          </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navListTotalStudents" data-bs-toggle="collapse" href="#">
            <i class="bi bi-list-check"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navListTotalStudents" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('reportsClassWiseTotalStudents')}}">
                <i class="bi bi-list-check"></i><span>List Total Students</span>
              </a>
            </li>

            <li>
              <a href="{{route('reportsClassWiseTotalFees')}}">
                <i class="bi bi-list-check"></i><span>List Total Fees</span>
              </a>
            </li>

            <li>
              <a href="{{route('reportsCollectiveFees')}}">
                <i class="bi bi-list-check"></i><span>Collective Fees</span>
              </a>
            </li>



            <!-- <li>
        <a href="{{route('parents')}}">
          <i class="bi bi-circle"></i><span>Add New Parent</span>
        </a>
      </li> -->
          </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navListCashBook" data-bs-toggle="collapse" href="#">
            <i class="bi bi-list-check"></i><span>Accounts</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navListCashBook" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('cashBook')}}">
                <i class="bi bi-list-check"></i><span>Cash Book</span>
              </a>
            </li>
          </ul>
        </li><!-- End Components Nav -->


        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navAttendance" data-bs-toggle="collapse" href="#">
            <i class="bi bi-check2-square"></i><span>Attendance</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navAttendance" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ request()->getBaseUrl() }}/attendance/students">
                <i class="bi bi-person-check"></i><span>Student Attendance</span>
              </a>
            </li>
            <li>
              <a href="{{ request()->getBaseUrl() }}/attendance/teachers">
                <i class="bi bi-briefcase"></i><span>Teacher Attendance</span>
              </a>
            </li>
            <li>
              <a href="{{ request()->getBaseUrl() }}/attendance/reports/students">
                <i class="bi bi-table"></i><span>Student Monthly Report</span>
              </a>
            </li>
            <li>
              <a href="{{ request()->getBaseUrl() }}/attendance/reports/teachers">
                <i class="bi bi-table"></i><span>Teacher Monthly Report</span>
              </a>
            </li>
            <li>
              <a href="{{ request()->getBaseUrl() }}/attendance/reports/students-yearly">
                <i class="bi bi-calendar3"></i><span>Student Yearly Report</span>
              </a>
            </li>
            <li>
              <a href="{{ request()->getBaseUrl() }}/attendance/reports/teachers-yearly">
                <i class="bi bi-calendar3"></i><span>Teacher Yearly Report</span>
              </a>
            </li>
          </ul>
        </li><!-- End Attendance Nav -->

        <!-- Online Exams -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navOnlineExams" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-check"></i><span>Online Exams</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navOnlineExams" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('exams.index')}}">
                <i class="bi bi-journal-text"></i><span>All Exams</span>
              </a>
            </li>
            <li>
              <a href="{{route('exams.create')}}">
                <i class="bi bi-plus-square"></i><span>Create New Exam</span>
              </a>
            </li>
            <li>
              <a href="{{route('question-bank.index')}}">
                <i class="bi bi-collection"></i><span>Question Bank</span>
              </a>
            </li>
            <li>
              <a href="{{route('exam-schedule.index')}}">
                <i class="bi bi-calendar3"></i><span>Exam Schedule</span>
              </a>
            </li>
            <li>
              <a href="{{route('exam-reports.index')}}">
                <i class="bi bi-graph-up"></i><span>Exam Reports</span>
              </a>
            </li>
          </ul>
        </li><!-- End Online Exams Nav -->

        <!-- Manual Exams -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navManualExams" data-bs-toggle="collapse" href="#">
            <i class="bi bi-pencil-square"></i><span>Manual Exams</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navManualExams" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{route('manual-exams.index')}}">
                <i class="bi bi-list-ul"></i><span>Enter Marks</span>
              </a>
            </li>
            <li>
              <a href="{{route('manual-exams.print-entry')}}">
                <i class="bi bi-printer"></i><span>Print Reports</span>
              </a>
            </li>
            <li>
              <a href="{{route('principal-remarks.index')}}">
                <i class="bi bi-chat-quote"></i><span>Principal Remarks</span>
              </a>
            </li>
          </ul>
        </li><!-- End Manual Exams Nav -->

        @endif

        {{-- Admin access removed - only superadmin can access these features --}}

        {{-- Admin access to classes and teachers removed - only superadmin can access --}}

        {{-- Admin access to parents and students removed - only superadmin can access --}}


        {{-- Admin access to fees removed - only superadmin can access --}}

        {{-- Admin access to timetable removed - only superadmin can access --}}

        {{-- Admin access to reports and accounts removed - only superadmin can access --}}

      </ul>

    </aside><!-- End Sidebar-->
    @endauth

    <main id="main" class="main">

      @if(session()->has('impersonator_tenant_id'))
      <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
        <div>
          <strong>Impersonating:</strong> Tenant {{ session('impersonator_tenant_id') }}
        </div>
        <form method="POST" action="{{ route('superadmin.leave_impersonation') }}" class="m-0">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-dark">Leave Impersonation</button>
        </form>
      </div>
      @endif

      @yield('content')

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
      <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
        <div class="text-muted">© {{ date('Y') }} CloudiSchool. All rights reserved.</div>
        <div class="text-muted">
          <a href="{{ route('about') }}" class="me-2">About</a>
          <a href="{{ route('privacy') }}" class="me-2">Privacy</a>
          <a href="{{ route('terms') }}" class="me-2">Terms</a>
          <a href="{{ route('contact') }}">Contact</a>
        </div>
      </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Essential JS - jQuery & Bootstrap (needed immediately) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('NiceAdmin/assets/js/main.js')}}"></script>

    <!-- Deferred JS - Heavy libraries loaded AFTER page renders -->
    <script src="{{ asset('NiceAdmin/assets/vendor/simple-datatables/simple-datatables.js')}}" defer></script>

    <!-- Chart libraries - only load on dashboard -->
    @if(request()->is('/') || request()->is('home') || request()->is('dashboard'))
    <script src="{{asset('NiceAdmin/assets/vendor/apexcharts/apexcharts.min.js')}}" defer></script>
    <script src="{{ asset('NiceAdmin/assets/vendor/chart.js/chart.umd.js')}}" defer></script>
    <script src="{{ asset('NiceAdmin/assets/vendor/echarts/echarts.min.js')}}" defer></script>
    @endif

    <!-- Rich text editors - only load when needed -->
    @hasSection('needs-quill')
    <script src="{{ asset('NiceAdmin/assets/vendor/quill/quill.min.js')}}" defer></script>
    @endif
    @hasSection('needs-tinymce')
    <script src="{{ asset('NiceAdmin/assets/vendor/tinymce/tinymce.min.js')}}" defer></script>
    @endif

    <script src="{{ asset('NiceAdmin/assets/vendor/php-email-form/validate.js')}}" defer></script>

    <!-- Toastr JS - deferred -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (typeof toastr !== 'undefined') {
          toastr.options = {
            closeButton: true, progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000, extendedTimeOut: 1000,
            showMethod: 'fadeIn', hideMethod: 'fadeOut'
          };
        }
      });
    </script>

    <!-- Global auto-fit for wide tables to avoid horizontal scrolling -->
    <script>
      (function() {
        function fitAllTables() {
          try {
            document.querySelectorAll('.table-responsive').forEach(function(wrapper) {
              var table = wrapper.querySelector('table');
              if (!table) return;
              // reset
              table.style.transform = 'none';
              table.style.transformOrigin = 'left top';
              wrapper.style.height = 'auto';
              var fullW = table.scrollWidth;
              var avail = wrapper.clientWidth || wrapper.offsetWidth;
              if (fullW > avail && avail > 0) {
                var scale = avail / fullW;
                if (scale < 0.6) scale = 0.6; // keep readable
                if (scale > 1) scale = 1;
                var origH = table.offsetHeight;
                table.style.transform = 'scale(' + scale + ')';
                wrapper.style.height = Math.ceil(origH * scale) + 'px';
              }
            });
          } catch (e) {
            /* ignore */
          }
        }
        window.addEventListener('resize', fitAllTables);
        document.addEventListener('DOMContentLoaded', fitAllTables);
      })();
    </script>

    <!-- Page-specific scripts -->
    @yield('scripts')

</body>

</html>