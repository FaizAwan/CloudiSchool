@extends('layouts.app')

@section('content')
@php
  $timetable = collect($timetable);
  $attendance = collect($attendance);
  $exams = collect($exams);

  $formatTime = function($timeStr) {
      if (!$timeStr) return 'N/A';
      // Strip spaces (e.g., "9 : 00" -> "9:00")
      $clean = str_replace(' ', '', $timeStr);
      try {
          return \Carbon\Carbon::parse($clean)->format('h:i A');
      } catch (\Exception $e) {
          try {
              return \Carbon\Carbon::parse($timeStr)->format('h:i A');
          } catch (\Exception $ex) {
              return $timeStr; // fallback to raw string if completely unparseable
          }
      }
  };
@endphp
<div class="pagetitle">
  <h1 style="font-family: 'Outfit', sans-serif !important; font-weight: 800 !important; color: #1e293b; text-transform: uppercase !important; letter-spacing: 2px !important; font-size: 1.5rem !important;">
    <i class="bi bi-person-vcard-fill me-2" style="color: #004ac6;"></i> Teacher Profile
  </h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('teachers') }}">Teachers</a></li>
      <li class="breadcrumb-item active">{{ $teacher->teacherName }}</li>
    </ol>
  </nav>
</div>

<div class="row">
  <!-- Sidebar Profile Card Widget -->
  <div class="col-xl-3 col-lg-3">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff; border: 1px solid #f1f5f9 !important;">
      <div style="background: linear-gradient(135deg, rgba(0, 74, 198, 0.07) 0%, rgba(30, 64, 175, 0.02) 100%); position: relative; border-bottom: 1px solid rgba(0, 74, 198, 0.08);"></div>
      <div class="card-body text-center pt-0" style="position: relative; margin-top: -45px; padding: 25px !important;">
        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 90px; height: 90px; border: 3px solid #fff; overflow: hidden; box-shadow: 0 8px 16px -4px rgba(0, 74, 198, 0.08) !important;">
          <span style="font-size: 2.2rem; font-weight: 800; color: #004ac6; font-family: 'Outfit', sans-serif;">
            {{ strtoupper(substr($teacher->teacherName, 0, 1)) }}
          </span>
        </div>
        
        <h4 class="fw-bold mt-3 mb-1" style="font-family: 'Outfit', sans-serif; color: #1e293b;">{{ $teacher->teacherName }}</h4>
        <span class="badge" style="background-color: rgba(0, 74, 198, 0.08); color: #004ac6; font-size: 0.78rem; font-weight: 700; padding: 6px 12px; border-radius: 6px;">
          <i class="bi bi-shield-check me-1"></i> Academic Instructor
        </span>

        <hr class="my-4" style="opacity: 0.08;">

        <div class="text-start">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6;">
              <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
              <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Email Address</small>
              <a href="mailto:{{ $teacher->email }}" class="text-decoration-none fw-bold" style="color: #475569; font-size: 0.88rem;">{{ $teacher->email }}</a>
            </div>
          </div>

          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6;">
              <i class="bi bi-telephone-fill"></i>
            </div>
            <div>
              <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Phone Number</small>
              <span class="fw-bold" style="color: #475569; font-size: 0.88rem;">{{ $teacher->phone ?: 'N/A' }}</span>
            </div>
          </div>

          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6;">
              <i class="bi bi-building"></i>
            </div>
            <div>
              <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">School Branch</small>
              <span class="fw-bold" style="color: #475569; font-size: 0.88rem;">{{ $teacher->schoolName }}</span>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <a href="{{ route('teachers') }}" class="btn btn-light rounded-pill px-4 w-100 fw-bold border" style="font-size: 0.85rem; color: #475569; border-color: #dee2e6 !important;">
            <i class="bi bi-arrow-left me-1"></i> Back to Instructors
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Tabs Area -->
  <div class="col-xl-9 col-lg-9">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff;">
      <div class="card-body" style="padding: 25px !important; margin: 0 !important;">
        
        <!-- Premium Designed Navigation Tabs -->
        <ul class="nav nav-tabs nav-tabs-bordered d-flex gap-2" id="teacherTabs" role="tablist" style="border-bottom: 2px solid #f1f5f9; padding-bottom: 5px;">
          <li class="nav-item" role="presentation">
            <button class="nav-link active custom-profile-tab" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
              <i class="bi bi-person-fill-gear me-1"></i> Personal Info
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link custom-profile-tab" id="timetable-tab" data-bs-toggle="tab" data-bs-target="#timetable" type="button" role="tab">
              <i class="bi bi-calendar3 me-1"></i> Class Timetable
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link custom-profile-tab" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
              <i class="bi bi-clipboard2-check me-1"></i> Attendance Logs
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link custom-profile-tab" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">
              <i class="bi bi-journal-text me-1"></i> Exam Schedules
            </button>
          </li>
        </ul>

        <div class="tab-content pt-4" id="teacherTabsContent">
          <!-- 1. PERSONAL INFO TAB -->
          <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Primary Details</h5>
            <div class="row g-4 mb-4">
              <div class="col-md-6">
                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1.5px solid #f1f5f9;">
                  <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Assigned Core Class</span>
                  <span class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">
                    <i class="bi bi-journal-bookmark text-primary me-2"></i> {{ $teacher->classNameFromJoin ?? $teacher->className ?: 'None Assigned' }}
                  </span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1.5px solid #f1f5f9;">
                  <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Account Role Status</span>
                  <span class="fw-bold text-success" style="font-size: 0.95rem;">
                    <i class="bi bi-patch-check-fill me-2"></i> Authorized Teacher Account
                  </span>
                </div>
              </div>
            </div>

            <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px; margin-top: 30px;">Workplace Details</h5>
            <div class="row g-3">
              <div class="col-md-12">
                <table class="table table-bordered align-middle" style="border-radius: 12px; overflow: hidden; border-color: #e2e8f0;">
                  <tbody>
                    <tr>
                      <td class="bg-light fw-bold text-muted ps-3" style="width: 30%; font-size: 0.85rem;">Branch Name</td>
                      <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">{{ $teacher->schoolName }}</td>
                    </tr>
                    <tr>
                      <td class="bg-light fw-bold text-muted ps-3" style="font-size: 0.85rem;">Office Address</td>
                      <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">{{ $teacher->address ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td class="bg-light fw-bold text-muted ps-3" style="font-size: 0.85rem;">Created On</td>
                      <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">
                        {{ $teacher->created_at ? \Carbon\Carbon::parse($teacher->created_at)->format('F d, Y') : 'N/A' }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- 2. TIMETABLE TAB -->
          <div class="tab-pane fade" id="timetable" role="tabpanel" aria-labelledby="timetable-tab">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Weekly Class Schedule</h5>
            
            @if($timetable->isEmpty())
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Schedule Entries Found</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">There are currently no active timetable periods scheduled for this instructor in the database.</p>
              </div>
            @else
              <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important;">
                <table class="table table-hover align-middle mb-0" style="margin-bottom: 0 !important;">
                  <thead class="bg-light">
                    <tr>
                      <th class="ps-3 py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Day</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Period</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Time Slot</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Subject</th>
                      <th class="py-3 pe-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Class Assigned</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php 
                      $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                      $sortedTimetable = $timetable->sortBy(function($item) use ($dayOrder) {
                          return array_search($item->day, $dayOrder);
                      });
                    @endphp
                    @foreach($sortedTimetable as $item)
                      <tr>
                        <td class="ps-3 fw-bold" style="color: #004ac6;">{{ $item->day }}</td>
                        <td>
                          <span class="badge rounded-pill bg-light text-primary border" style="font-weight: 700; font-size: 0.78rem; padding: 5px 10px;">
                            {{ $item->periodName }}
                          </span>
                        </td>
                        <td class="fw-semibold text-muted" style="font-size: 0.85rem;">
                          <i class="bi bi-clock me-1 text-primary"></i> 
                          {{ $formatTime($item->startTime) }} - {{ $formatTime($item->endTime) }}
                        </td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $item->subject }}</td>
                        <td class="pe-3">
                          <span class="badge bg-primary-light text-primary px-3 py-2 fw-bold" style="border-radius: 6px;">
                            <i class="bi bi-mortarboard me-1"></i> {{ $item->className }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

          <!-- 3. ATTENDANCE LOGS TAB -->
          <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Monthly Attendance Statistics</h5>
            
            @if($attendance->isEmpty())
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-clipboard2-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Attendance Logs Available</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">No attendance records have been registered or logged for this instructor in the database.</p>
              </div>
            @else
              @php
                $totalDays = count($attendance);
                $presents = $attendance->filter(fn($item) => in_array(strtolower($item->status), ['present', 'p', 'active']))->count();
                $absents = $attendance->filter(fn($item) => in_array(strtolower($item->status), ['absent', 'a']))->count();
                $leaves = $attendance->filter(fn($item) => in_array(strtolower($item->status), ['leave', 'l', 'leave approved']))->count();
                $attendanceRate = $totalDays > 0 ? round(($presents / $totalDays) * 100) : 0;
              @endphp

              <!-- Metrics cards -->
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center" style="background: white; border: 1.5px solid #e2e8f0;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Marked Days</span>
                    <h3 class="fw-bold mb-0" style="color: #1e293b; font-family: 'Outfit', sans-serif;">{{ $totalDays }}</h3>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center" style="background: white; border: 1.5px solid #e2e8f0;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Present Rate</span>
                    <h3 class="fw-bold mb-0 text-success" style="font-family: 'Outfit', sans-serif;">{{ $attendanceRate }}%</h3>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center" style="background: white; border: 1.5px solid #e2e8f0;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Absents / Leaves</span>
                    <h3 class="fw-bold mb-0 text-danger" style="font-family: 'Outfit', sans-serif;">{{ $absents }} / {{ $leaves }}</h3>
                  </div>
                </div>
              </div>

              <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #1e293b; font-size: 0.95rem;">Attendance Registry</h5>
              <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important; max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" style="margin-bottom: 0 !important;">
                  <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                      <th class="ps-3 py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Date</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Status</th>
                      <th class="py-3 pe-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($attendance as $log)
                      <tr>
                        <td class="ps-3 fw-semibold text-muted" style="font-size: 0.88rem;">
                          <i class="bi bi-calendar-event me-2 text-primary"></i> 
                          {{ \Carbon\Carbon::parse($log->date)->format('F d, Y') }}
                        </td>
                        <td>
                          @if(in_array(strtolower($log->status), ['present', 'p', 'active']))
                            <span class="badge badge-soft-success px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem;">Present</span>
                          @elseif(in_array(strtolower($log->status), ['absent', 'a']))
                            <span class="badge badge-soft-danger px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem;">Absent</span>
                          @else
                            <span class="badge badge-soft-info px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem;">{{ $log->status }}</span>
                          @endif
                        </td>
                        <td class="text-muted pe-3" style="font-size: 0.85rem;">{{ $log->remarks ?: 'No remarks recorded' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

          <!-- 4. EXAM SCHEDULES TAB -->
          <div class="tab-pane fade" id="exams" role="tabpanel" aria-labelledby="exams-tab">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Managed Exams</h5>
            
            @if($exams->isEmpty())
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-journals" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Exams Managed Yet</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">There are no active online or manual examinations registered under this instructor in the database.</p>
              </div>
            @else
              <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important;">
                <table class="table table-hover align-middle mb-0" style="margin-bottom: 0 !important;">
                  <thead class="bg-light">
                    <tr>
                      <th class="ps-3 py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Exam Name</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Subject</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Class</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Scheduled Date</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Marks</th>
                      <th class="py-3 pe-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($exams as $exam)
                      <tr>
                        <td class="ps-3 fw-bold" style="color: #1e293b;">{{ $exam->exam_name }}</td>
                        <td class="fw-semibold text-muted" style="font-size: 0.85rem;">{{ $exam->subject_name ?: 'N/A' }}</td>
                        <td>
                          <span class="badge bg-light text-primary border" style="font-weight: 700; font-size: 0.75rem; padding: 4px 8px;">
                            {{ $exam->className }}
                          </span>
                        </td>
                        <td class="fw-semibold text-muted" style="font-size: 0.85rem;">
                          @if($exam->exam_date)
                            <i class="bi bi-calendar-check text-primary me-1"></i> 
                            {{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }} 
                            @if($exam->exam_time)
                              | {{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}
                            @endif
                          @else
                            <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> Unschedueled</span>
                          @endif
                        </td>
                        <td class="fw-bold" style="color: #475569; font-size: 0.85rem;">
                          {{ $exam->total_marks }} <small class="text-muted">(Pass: {{ $exam->passing_marks }})</small>
                        </td>
                        <td class="pe-3">
                          @if($exam->status === 'published')
                            <span class="badge badge-soft-success px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.3px;">Live</span>
                          @elseif($exam->status === 'completed')
                            <span class="badge badge-soft-secondary px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.3px;">Completed</span>
                          @else
                            <span class="badge badge-soft-info px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.3px;">Draft</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
  /* Custom highly prominent premium profiles tabs */
  .custom-profile-tab {
    border: none !important;
    border-bottom: 3px solid transparent !important;
    font-weight: 600 !important;
    color: #64748b !important;
    font-size: 0.9rem !important;
    padding: 10px 16px !important;
    background: transparent !important;
    transition: all 0.3s ease !important;
    border-radius: 0 !important;
  }
  .custom-profile-tab:hover {
    color: #004ac6 !important;
  }
  .custom-profile-tab.active {
    color: #004ac6 !important;
    border-color: #004ac6 !important;
    font-weight: 700 !important;
  }
</style>
@endsection
