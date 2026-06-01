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
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>CloudiSchool</title>
  <link rel="icon" type="image/x-icon" href="{{asset('cloudiSchool.png')}}">

  <!-- Dashra CSS -->
  <link href="{{ asset('dashra/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/font-awesome-all.min.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/charts.min.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/datatables.min.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/jvector-map.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/slickslider.min.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/jquery-ui.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/css/reset.css')}}" rel="stylesheet">
  <link href="{{ asset('dashra/style.css')}}" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    .crancy-menu-icon i { font-size: 1.2rem; }
    .crancy-adashboard { padding-top: 100px; }
  </style>
</head>


<body id="crancy-dark-light">
  <div id="app" class="crancy-body-area">
    <!-- ======= Header ======= -->
    <!-- Start Header -->
<header class="crancy-header">
  <div class="container w-100">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="crancy-header__inner d-flex justify-content-between align-items-center">
          <div class="crancy-header__middle">
            <div class="crancy-header__left">
              <div class="crancy-header__nav-bottom">
                <div class="logo crancy-sidebar-padding">
                  <a class="crancy-logo">
                    <img class="crancy-logo__main" src="{{asset('cloudiSchool.png')}}" alt="CloudiSchool" style="max-height:40px;">
                  </a>
                </div>
              </div>
              <div id="crancy__sicon" class="crancy__sicon close-icon">
                <img src="{{asset('dashra/img/arrow-icon.svg')}}">
              </div>
            </div>
          </div>
          <!-- Right side -->
          <div class="crancy-header__right">
            <div class="crancy-header__menu">
              <ul>

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
    <!-- crancy Admin Menu -->
<div class="crancy-smenu" id="CrancyMenu">
  <div class="admin-menu__one crancy-sidebar-padding mg-top-20">
    <div class="menu-bar">
      <ul id="CrancyMenuInner" class="menu-bar__one crancy-dashboard-menu">

        <li class="nav-item">
          <a class="nav-link" href="#" onclick="return false;">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-heading">CMS Management</li>
        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#cms-nav">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-layout-text-window-reverse"></i></span>
              <span class="menu-bar__name">Website CMS</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="cms-nav" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{ route('cms.news.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">News Updates</span></span></a></li>
            <li><a href="{{ route('cms.events.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">School Events</span></span></a></li>
            <li><a href="{{ route('cms.gallery.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">Photo Gallery</span></span></a></li>
            <li><a href="{{ route('cms.announcements.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">Announcements</span></span></a></li>
            <li><a href="{{ route('cms.blogs.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">School Blogs</span></span></a></li>
            <li><a href="{{ route('cms.templates.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">Choose Template</span></span></a></li>
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
          </div>
        </li>

        @if(Auth::user()->role === 'superadmin')
        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navPackages">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-box-seam"></i></span>
              <span class="menu-bar__name">Packages</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navPackages" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{ route('superadmin.packages.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">All Packages</span></span></a></li>
            </ul>
          </div>
        </li>

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navTenants">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-people"></i></span>
              <span class="menu-bar__name">Tenants</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navTenants" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{ route('superadmin.tenants.all') }}"><span class="menu-bar__text"><span class="menu-bar__name">All Tenants</span></span></a></li>
            <li><a href="{{ route('superadmin.tenants.all') }}"><span class="menu-bar__text"><span class="menu-bar__name">Tenants Impersonation</span></span></a></li>
          </ul>
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-navBlogs" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-text"></i><span>Blogs</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-navBlogs" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ route('superadmin.blogs.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">Manage Blogs</span></span></a></li>
            </ul>
          </div>
        </li>
        @endif

        {{-- Admin can see menu, but routes restrict data; Superadmin has full access --}}
        @if(in_array(Auth::user()->role, ['admin','superadmin']) && Auth::user()->role !== 'superadmin')

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navSchools">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-buildings"></i></span>
              <span class="menu-bar__name">School Branch</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navSchools" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('schools')}}"><span class="menu-bar__text"><span class="menu-bar__name">List School Branches</span></span></a></li>

            <li><a href="{{route('academic-years.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Academic Year</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Components Nav -->

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navClasses">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-menu-button-wide"></i></span>
              <span class="menu-bar__name">Classes</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navClasses" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('classes')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Classes</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Components Nav -->

        <!-- Subjects Management -->
        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navSubjects">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-book"></i></span>
              <span class="menu-bar__name">Subjects</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navSubjects" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{ route('subjects.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">Manage Subjects</span></span></a></li>
            <li><a href="{{ route('term-subjects.index') }}"><span class="menu-bar__text"><span class="menu-bar__name">Term Subjects Wizard</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Subjects Nav -->




        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navTeachers">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-person"></i></span>
              <span class="menu-bar__name">Teachers</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navTeachers" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('teachers')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Teachers</span></span></a></li>
            <!-- <li><a href="{{route('classes')}}"><span class="menu-bar__text"><span class="menu-bar__name">Add New Class</span></span></a></li> -->
            </ul>
          </div>
        </li><!-- End Components Nav -->

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navParents">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-person"></i></span>
              <span class="menu-bar__name">Parents</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navParents" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('parents')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Parents</span></span></a></li>
            <!-- <li><a href="{{route('parents')}}"><span class="menu-bar__text"><span class="menu-bar__name">Add New Parent</span></span></a></li> -->
            </ul>
          </div>
        </li><!-- End Components Nav -->


        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navStudents">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-person"></i></span>
              <span class="menu-bar__name">Students</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navStudents" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('students')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Students</span></span></a></li>

            <li><a href="{{route('studentsWithSameGRno')}}"><span class="menu-bar__text"><span class="menu-bar__name">Duplicate GRNO</span></span></a></li>

            <li><a href="{{route('studentsListGRno')}}"><span class="menu-bar__text"><span class="menu-bar__name">Student List GRNO</span></span></a></li>

            <li><a href="{{route('studentsListSLC')}}"><span class="menu-bar__text"><span class="menu-bar__name">Student List SLC</span></span></a></li>



            <!-- <li><a href="{{route('students')}}"><span class="menu-bar__text"><span class="menu-bar__name">Add New student</span></span></a></li> -->
            </ul>
          </div>
        </li><!-- End Components Nav -->

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navTimetable">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-journal-text"></i></span>
              <span class="menu-bar__name">TimeTable</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navTimetable" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('periods')}}"><span class="menu-bar__text"><span class="menu-bar__name">Periods (Time Slot)</span></span></a></li>
            <li><a href="{{route('weeklyTimetable')}}"><span class="menu-bar__text"><span class="menu-bar__name">Weekly Timetable</span></span></a></li>
            <!-- <li><a href="components-accordion.html"><span class="menu-bar__text"><span class="menu-bar__name">Add New Fee Type</span></span></a></li> -->
            </ul>
          </div>
        </li><!-- End Components Nav -->


        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navFees">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-journal-text"></i></span>
              <span class="menu-bar__name">Fees</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navFees" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('fees')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Fee Types</span></span></a></li>

            <li><a href="{{route('feesManagement')}}"><span class="menu-bar__text"><span class="menu-bar__name">Fee Management</span></span></a></li>

            <li><a href="{{route('challan')}}"><span class="menu-bar__text"><span class="menu-bar__name">Create Challan</span></span></a></li>

            <li><a href="{{route('challanPaid')}}"><span class="menu-bar__text"><span class="menu-bar__name">Challan Paid</span></span></a></li>

            <!--<li>-->
            <!--  <a href="{{route('classWiseChallan')}}">-->
            <!--    <i class="bi bi-circle"></i><span>Print Classwise Challan</span>-->
            <!--  </a>-->
            <!--</li>-->

            <!-- <li><a href="components-accordion.html"><span class="menu-bar__text"><span class="menu-bar__name">Add New Fee Type</span></span></a></li> -->
            </ul>
          </div>
        </li><!-- End Components Nav -->

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navListTotalStudents">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-list-check"></i></span>
              <span class="menu-bar__name">Reports</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navListTotalStudents" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('reportsClassWiseTotalStudents')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Total Students</span></span></a></li>

            <li><a href="{{route('reportsClassWiseTotalFees')}}"><span class="menu-bar__text"><span class="menu-bar__name">List Total Fees</span></span></a></li>

            <li><a href="{{route('reportsCollectiveFees')}}"><span class="menu-bar__text"><span class="menu-bar__name">Collective Fees</span></span></a></li>



            <!-- <li><a href="{{route('parents')}}"><span class="menu-bar__text"><span class="menu-bar__name">Add New Parent</span></span></a></li> -->
            </ul>
          </div>
        </li><!-- End Components Nav -->

        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navListCashBook">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-list-check"></i></span>
              <span class="menu-bar__name">Accounts</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navListCashBook" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('cashBook')}}"><span class="menu-bar__text"><span class="menu-bar__name">Cash Book</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Components Nav -->


        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navAttendance">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-check2-square"></i></span>
              <span class="menu-bar__name">Attendance</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navAttendance" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{ request()->getBaseUrl() }}/attendance/students"><span class="menu-bar__text"><span class="menu-bar__name">Student Attendance</span></span></a></li>
            <li><a href="{{ request()->getBaseUrl() }}/attendance/teachers"><span class="menu-bar__text"><span class="menu-bar__name">Teacher Attendance</span></span></a></li>
            <li><a href="{{ request()->getBaseUrl() }}/attendance/reports/students"><span class="menu-bar__text"><span class="menu-bar__name">Student Monthly Report</span></span></a></li>
            <li><a href="{{ request()->getBaseUrl() }}/attendance/reports/teachers"><span class="menu-bar__text"><span class="menu-bar__name">Teacher Monthly Report</span></span></a></li>
            <li><a href="{{ request()->getBaseUrl() }}/attendance/reports/students-yearly"><span class="menu-bar__text"><span class="menu-bar__name">Student Yearly Report</span></span></a></li>
            <li><a href="{{ request()->getBaseUrl() }}/attendance/reports/teachers-yearly"><span class="menu-bar__text"><span class="menu-bar__name">Teacher Yearly Report</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Attendance Nav -->

        <!-- Online Exams -->
        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navOnlineExams">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-journal-check"></i></span>
              <span class="menu-bar__name">Online Exams</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navOnlineExams" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('exams.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">All Exams</span></span></a></li>
            <li><a href="{{route('exams.create')}}"><span class="menu-bar__text"><span class="menu-bar__name">Create New Exam</span></span></a></li>
            <li><a href="{{route('question-bank.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">Question Bank</span></span></a></li>
            <li><a href="{{route('exam-schedule.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">Exam Schedule</span></span></a></li>
            <li><a href="{{route('exam-reports.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">Exam Reports</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Online Exams Nav -->

        <!-- Manual Exams -->
        <li>
          <a class="collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#components-navManualExams">
            <span class="menu-bar__text">
              <span class="crancy-menu-icon" style="font-size:20px;margin-right:10px;"><i class="bi bi-pencil-square"></i></span>
              <span class="menu-bar__name">Manual Exams</span>
            </span>
            <span class="crancy__toggle"></span>
          </a>
          <div class="collapse crancy__dropdown" id="components-navManualExams" data-bs-parent="#CrancyMenuInner">
            <ul class="menu-bar__one-dropdown">
              <li><a href="{{route('manual-exams.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">Enter Marks</span></span></a></li>
            <li><a href="{{route('manual-exams.print-entry')}}"><span class="menu-bar__text"><span class="menu-bar__name">Print Reports</span></span></a></li>
            <li><a href="{{route('principal-remarks.index')}}"><span class="menu-bar__text"><span class="menu-bar__name">Principal Remarks</span></span></a></li>
            </ul>
          </div>
        </li><!-- End Manual Exams Nav -->

        @endif

        {{-- Admin access removed - only superadmin can access these features --}}

        {{-- Admin access to classes and teachers removed - only superadmin can access --}}

        {{-- Admin access to parents and students removed - only superadmin can access --}}


        {{-- Admin access to fees removed - only superadmin can access --}}

        {{-- Admin access to timetable removed - only superadmin can access --}}

        {{-- Admin access to reports and accounts removed - only superadmin can access --}}

      </ul></div></div></div><!-- End Sidebar-->
    @endauth

    <!-- Start crancy Dashboard -->
<section class="crancy-adashboard crancy-show">
  <div class="container container__bscreen">
    <div class="row">
      <main id="main" class="main w-100">

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

    </main></div></div></section>

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