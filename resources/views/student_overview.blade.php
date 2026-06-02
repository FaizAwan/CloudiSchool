@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1 style="font-family: 'Outfit', sans-serif !important; font-weight: 800 !important; color: #1e293b; text-transform: uppercase !important; letter-spacing: 2px !important; font-size: 1.5rem !important;">
    <i class="bi bi-person-badge-fill me-2" style="color: #004ac6;"></i> Student Overview
  </h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('students') }}">Students</a></li>
      <li class="breadcrumb-item active">{{ $profile['name'] ?? 'Student Profile' }}</li>
    </ol>
  </nav>
</div>

<div class="row">
  <!-- Sidebar Student Profile Card -->
  <div class="col-xl-3 col-lg-3">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff; margin-bottom: 25px; border: 1px solid #f1f5f9 !important;">
      <div style="background: linear-gradient(135deg, rgba(0, 74, 198, 0.07) 0%, rgba(30, 64, 175, 0.02) 100%); position: relative; border-bottom: 1px solid rgba(0, 74, 198, 0.08);"></div>
      <div class="card-body text-center pt-0" style="position: relative; margin-top: -45px; padding: 25px !important;">
        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 90px; height: 90px; border: 3px solid #fff; overflow: hidden; box-shadow: 0 8px 16px -4px rgba(0, 74, 198, 0.08) !important;">
          <span style="font-size: 2.2rem; font-weight: 800; color: #004ac6; font-family: 'Outfit', sans-serif;">
            {{ strtoupper(substr($profile['name'] ?? 'S', 0, 1)) }}
          </span>
        </div>
        
        <h4 class="fw-bold mt-3 mb-1" style="font-family: 'Outfit', sans-serif; color: #1e293b;">{{ $profile['name'] ?? 'N/A' }}</h4>
        <span class="badge mb-2" style="background-color: rgba(0, 74, 198, 0.08); color: #004ac6; font-size: 0.78rem; font-weight: 700; padding: 6px 12px; border-radius: 6px;">
          <i class="bi bi-mortarboard-fill me-1"></i> Student ID #{{ $profile['id'] ?? 'N/A' }}
        </span>

        <div class="d-flex justify-content-center gap-2 mt-1 mb-2">
          @if(strtolower($profile['status'] ?? '') === 'active')
            <span class="badge bg-success-light text-success fw-bold px-3 py-1.5" style="border-radius: 20px; font-size: 0.75rem;">
              <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Active
            </span>
          @else
            <span class="badge bg-danger-light text-danger fw-bold px-3 py-1.5" style="border-radius: 20px; font-size: 0.75rem;">
              <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ ucfirst($profile['status'] ?? 'Inactive') }}
            </span>
          @endif
          <span class="badge bg-secondary-light text-secondary fw-bold px-3 py-1.5" style="border-radius: 20px; font-size: 0.75rem; border: 1px solid #dee2e6;">
            GR: {{ $profile['grno'] ?? 'N/A' }}
          </span>
        </div>

        <hr class="my-4" style="opacity: 0.08;">

        <div class="text-start">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 animate-icon-box" style="width: 36px; height: 36px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6; flex-shrink: 0;">
              <i class="bi bi-person-fill"></i>
            </div>
            <div>
              <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Parent Name</small>
              <span class="fw-bold" style="color: #475569; font-size: 0.88rem;">{{ $profile['parent'] ?? 'N/A' }}</span>
            </div>
          </div>

          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 animate-icon-box" style="width: 36px; height: 36px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6; flex-shrink: 0;">
              <i class="bi bi-building"></i>
            </div>
            <div>
              <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">School Branch</small>
              <span class="fw-bold" style="color: #475569; font-size: 0.88rem;">{{ $profile['school'] ?? 'N/A' }}</span>
            </div>
          </div>

          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 animate-icon-box" style="width: 36px; height: 36px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6; flex-shrink: 0;">
              <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div>
              <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Current Term Session</small>
              <span class="fw-bold" style="color: #475569; font-size: 0.88rem;">{{ $profile['session'] ?? 'N/A' }}</span>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <a href="{{ route('students') }}" class="btn btn-light rounded-pill px-4 w-100 fw-bold border" style="font-size: 0.85rem; color: #475569; border-color: #dee2e6 !important;">
            <i class="bi bi-arrow-left me-1"></i> Back to Students
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Tab Panel Area -->
  <div class="col-xl-9 col-lg-9">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff; margin-bottom: 25px;">
      <div class="card-body" style="padding: 25px !important; margin: 0 !important;">
        
        <!-- Premium High-Contrast Horizontal Tab Navigation -->
        <div class="tab-scroller" style="overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; border-bottom: 2px solid #f1f5f9; padding-bottom: 5px;">
          <ul class="nav nav-tabs nav-tabs-bordered d-flex flex-nowrap gap-1" id="studentTabs" role="tablist" style="border-bottom: none;">
            <li class="nav-item" role="presentation">
              <button class="nav-link active custom-student-tab" id="tab-profile" data-bs-toggle="tab" data-bs-target="#pane-profile" type="button" role="tab">
                <i class="bi bi-person-badge-fill me-1"></i> Profile
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-results" data-bs-toggle="tab" data-bs-target="#pane-results" type="button" role="tab">
                <i class="bi bi-award-fill me-1"></i> Results
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-behavior" data-bs-toggle="tab" data-bs-target="#pane-behavior" type="button" role="tab">
                <i class="bi bi-emoji-smile-fill me-1"></i> Behavior
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-subjects" data-bs-toggle="tab" data-bs-target="#pane-subjects" type="button" role="tab">
                <i class="bi bi-book-half me-1"></i> Subjects
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-fees" data-bs-toggle="tab" data-bs-target="#pane-fees" type="button" role="tab">
                <i class="bi bi-wallet2 me-1"></i> Fees
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-online" data-bs-toggle="tab" data-bs-target="#pane-online" type="button" role="tab">
                <i class="bi bi-laptop me-1"></i> Online Exams
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-siblings" data-bs-toggle="tab" data-bs-target="#pane-siblings" type="button" role="tab">
                <i class="bi bi-people-fill me-1"></i> Siblings
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-attendance" data-bs-toggle="tab" data-bs-target="#pane-attendance" type="button" role="tab">
                <i class="bi bi-calendar3 me-1"></i> Attendance
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link custom-student-tab" id="tab-entries" data-bs-toggle="tab" data-bs-target="#pane-entries" type="button" role="tab">
                <i class="bi bi-receipt-cutoff me-1"></i> Recent Entries
              </button>
            </li>
          </ul>
        </div>

        <div class="tab-content pt-4" id="studentTabsContent">
          <!-- 1. PROFILE TAB -->
          <div class="tab-pane fade show active" id="pane-profile" role="tabpanel" aria-labelledby="tab-profile">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Academic Enrollment Details</h5>
            <div class="row g-4 mb-4">
              <div class="col-md-6">
                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1.5px solid #f1f5f9; height: 100%;">
                  <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;">Assigned Class & Section</span>
                  <span class="fw-bold" style="color: #1e293b; font-size: 0.98rem;">
                    <i class="bi bi-journal-bookmark text-primary me-2"></i> {{ $profile['class'] ?? 'N/A' }} @if(!empty($profile['section'])) - Section {{ $profile['section'] }} @endif
                  </span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1.5px solid #f1f5f9; height: 100%;">
                  <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;">Age & Birth Registry</span>
                  <span class="fw-bold" style="color: #1e293b; font-size: 0.98rem;">
                    <i class="bi bi-calendar-heart text-primary me-2"></i> 
                    @if(!empty($profile['dob']))
                      {{ \Carbon\Carbon::parse($profile['dob'])->format('M d, Y') }} @if(!empty($profile['age'])) ({{ $profile['age'] }} Years old) @endif
                    @else
                      N/A
                    @endif
                  </span>
                </div>
              </div>
            </div>

            <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px; margin-top: 30px;">Workplace & Contact Registry</h5>
            <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important;">
              <table class="table table-bordered align-middle mb-0" style="border-radius: 12px; overflow: hidden; border-color: #e2e8f0;">
                <tbody>
                  <tr>
                    <td class="bg-light fw-bold text-muted ps-3" style="width: 30%; font-size: 0.85rem;">GR No.</td>
                    <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">{{ $profile['grno'] ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="bg-light fw-bold text-muted ps-3" style="font-size: 0.85rem;">Gender</td>
                    <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">{{ $profile['gender'] ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="bg-light fw-bold text-muted ps-3" style="font-size: 0.85rem;">Address</td>
                    <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">{{ $profile['address'] ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="bg-light fw-bold text-muted ps-3" style="font-size: 0.85rem;">Registered Contact Phone</td>
                    <td class="ps-3" style="color: #1e293b; font-weight: 600; font-size: 0.88rem;">{{ $profile['phone'] ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="bg-light fw-bold text-muted ps-3" style="font-size: 0.85rem;">Email Address</td>
                    <td class="ps-3" style="color: #004ac6; font-weight: 600; font-size: 0.88rem;">
                      @if(!empty($profile['email']))
                        <a href="mailto:{{ $profile['email'] }}" class="text-decoration-none">{{ $profile['email'] }}</a>
                      @else
                        N/A
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 2. RESULTS TAB -->
          <div class="tab-pane fade" id="pane-results" role="tabpanel" aria-labelledby="tab-results">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Manual Examination Results</h5>
            
            @php $entries = $results['entries'] ?? []; @endphp
            @if(empty($entries))
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-journal-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Exam Results Recorded</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">No academic examination logs are currently active or logged for this student in the database.</p>
              </div>
            @else
              @foreach($entries as $e)
                @php 
                  $data = json_decode($e->data, true) ?? [];
                  $toImprove = json_decode($e->subjects_to_improve, true) ?? [];
                  $absents = json_decode($e->absent_subjects, true) ?? [];
                @endphp
                <div class="card border mb-4 rounded-4" style="border-color: #e2e8f0 !important; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
                  <div class="card-header d-flex justify-content-between align-items-center py-3" style="background: rgba(0, 74, 198, 0.02); border-bottom: 1.5px solid #e2e8f0;">
                    <div class="d-flex align-items-center">
                      <span class="badge bg-primary-light text-primary me-2 px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.82rem;">
                        {{ $e->term ?? 'Examination' }}
                      </span>
                      <small class="text-muted fw-semibold d-none d-md-inline-block">Logged Class ID: #{{ $e->class_id }}</small>
                    </div>
                    <div>
                      <span class="badge" style="background-color: #fea619; color: #fff; font-weight: 700; padding: 6px 12px; border-radius: 6px; font-size: 0.78rem;">
                        Grade: {{ $data['overall_grade'] ?? 'N/A' }}
                      </span>
                    </div>
                  </div>
                  <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color: #1e293b;"><i class="bi bi-bookmark-star-fill text-warning me-1"></i> Subject Marks Summary</h6>
                    
                    <div class="row g-3 mb-4">
                      @foreach($data as $subjName => $marks)
                        @if(!in_array($subjName, ['total_working_days', 'total_present', 'total_absent', 'improvement_studies', 'behavior_attributes', 'overall_grade']))
                          <div class="col-6 col-sm-4 col-md-3">
                            <div class="p-3 text-center rounded-3 border bg-white shadow-xs">
                              <span class="text-muted d-block text-truncate fw-semibold mb-1" style="font-size: 0.75rem; text-transform: capitalize;">{{ $subjName }}</span>
                              <h4 class="fw-bold mb-0" style="color: #1e293b; font-family: 'Outfit', sans-serif;">
                                @if(is_numeric($marks))
                                  {{ $marks }}<span style="font-size: 0.75rem; font-weight: 500;" class="text-muted">/100</span>
                                @else
                                  <span class="text-danger">{{ $marks }}</span>
                                @endif
                              </h4>
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>

                    @if(!empty($absents))
                      <div class="alert alert-danger-light border-0 d-flex align-items-center mb-4 py-2 px-3" role="alert" style="border-radius: 8px;">
                        <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.1rem;"></i>
                        <div>
                          <strong style="font-size: 0.82rem;">Absent in exams:</strong> 
                          <span style="font-size: 0.8rem; font-weight: 600;">{{ implode(', ', $absents) }}</span>
                        </div>
                      </div>
                    @endif

                    @if(!empty($toImprove))
                      <h6 class="fw-bold mb-3" style="color: #1e293b;"><i class="bi bi-arrow-up-circle-fill text-danger me-1"></i> Priorities & Improvement Plans</h6>
                      <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important; max-height: 250px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                          <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                              <th class="ps-3 py-2" style="color: #475569; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">Subject</th>
                              <th class="py-2" style="color: #475569; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">Score</th>
                              <th class="py-2" style="color: #475569; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">Reason</th>
                              <th class="py-2 pe-3 text-end" style="color: #475569; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">Priority</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($toImprove as $item)
                              <tr>
                                <td class="ps-3 fw-bold" style="color: #1e293b; font-size: 0.85rem;">{{ $item['subject_name'] ?? 'N/A' }}</td>
                                <td style="font-size: 0.85rem;">
                                  <span class="fw-bold">{{ $item['obtained_marks'] ?? '0' }}</span>
                                  <span class="text-muted" style="font-size: 0.75rem;">/{{ $item['total_marks'] ?? '100' }}</span>
                                </td>
                                <td class="text-muted" style="font-size: 0.82rem;">{{ $item['reason'] ?? '' }}</td>
                                <td class="pe-3 text-end">
                                  @if(strtolower($item['priority'] ?? '') === 'high')
                                    <span class="badge bg-danger-light text-danger fw-bold px-2.5 py-1" style="font-size: 0.7rem; border-radius: 4px;">HIGH</span>
                                  @elseif(strtolower($item['priority'] ?? '') === 'medium')
                                    <span class="badge bg-warning-light text-warning fw-bold px-2.5 py-1" style="font-size: 0.7rem; border-radius: 4px;">MEDIUM</span>
                                  @else
                                    <span class="badge bg-success-light text-success fw-bold px-2.5 py-1" style="font-size: 0.7rem; border-radius: 4px;">NORMAL</span>
                                  @endif
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    @endif

                    @if(!empty($data['improvement_studies']))
                      <div class="p-3 mt-3 rounded-3" style="background-color: rgba(0, 74, 198, 0.02); border-left: 4px solid #004ac6;">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Teacher's Evaluation Remarks</small>
                        <p class="mb-0 fw-semibold" style="font-size: 0.85rem; color: #475569;">"{{ $data['improvement_studies'] }}"</p>
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach
            @endif
          </div>

          <!-- 3. BEHAVIOR TAB -->
          <div class="tab-pane fade" id="pane-behavior" role="tabpanel" aria-labelledby="tab-behavior">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Behavioral Assessments</h5>
            
            <div class="text-center py-5 px-3 rounded-4 border shadow-xs" style="background-color: #fff; border-color: #e2e8f0 !important;">
              <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-emoji-smile" style="font-size: 2.8rem; color: #004ac6;"></i>
              </div>
              <h5 class="fw-bold" style="color: #1e293b;">Character & Discipline</h5>
              <p class="text-muted mx-auto" style="max-width: 480px; font-size: 0.88rem;">Behavioral evaluations, class participation, soft skills, and disciplinary records will appear here as soon as they are submitted by the branch instructor.</p>
              
              <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <span class="badge border bg-white text-muted px-3 py-2 fw-semibold" style="border-radius: 6px; font-size: 0.78rem;">
                  <i class="bi bi-heart-fill text-danger me-1"></i> Class Conduct: Excellent
                </span>
                <span class="badge border bg-white text-muted px-3 py-2 fw-semibold" style="border-radius: 6px; font-size: 0.78rem;">
                  <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Attendance: Punctual
                </span>
                <span class="badge border bg-white text-muted px-3 py-2 fw-semibold" style="border-radius: 6px; font-size: 0.78rem;">
                  <i class="bi bi-star-fill text-primary me-1"></i> Engagement: Positive
                </span>
              </div>
            </div>
          </div>

          <!-- 4. SUBJECTS TAB -->
          <div class="tab-pane fade" id="pane-subjects" role="tabpanel" aria-labelledby="tab-subjects">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Assigned Subjects for Class</h5>
            
            @if(empty($subjects))
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-book" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Subjects Registered</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">No subjects have been assigned to this student's class in the subjects registry database.</p>
              </div>
            @else
              <div class="row g-3">
                @foreach($subjects as $s)
                  <div class="col-md-6 col-lg-4">
                    <div class="p-3.5 rounded-3 border bg-white d-flex align-items-center shadow-xs card-subject-hover" style="border-color: #f1f5f9; transition: all 0.3s ease;">
                      <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(0, 74, 198, 0.06); color: #004ac6; font-size: 1.25rem;">
                        <i class="bi bi-bookmark-check-fill"></i>
                      </div>
                      <div style="min-width: 0;">
                        <h6 class="fw-bold text-truncate mb-0" style="color: #1e293b; font-size: 0.9rem;">{{ $s }}</h6>
                        <small class="text-success fw-bold" style="font-size: 0.72rem;"><i class="bi bi-check2"></i> Active Subject</small>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <!-- 5. FEES TAB -->
          <div class="tab-pane fade" id="pane-fees" role="tabpanel" aria-labelledby="tab-fees">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Challan Transaction Ledger</h5>
            
            @if(empty($fees) || ($fees['count'] ?? 0) === 0)
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-wallet2" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Fee Invoices Found</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">No monthly challans or fee records have been issued for this student.</p>
              </div>
            @else
              <!-- Financial Quick Summary Cards -->
              <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center" style="background: white; border: 1.5px solid #e2e8f0;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Issued Challans</span>
                    <h3 class="fw-bold mb-0" style="color: #1e293b; font-family: 'Outfit', sans-serif;">{{ $fees['count'] }}</h3>
                  </div>
                </div>
                <div class="col-6 col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center" style="background: white; border: 1.5px solid #e2e8f0;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Paid Ledgers</span>
                    <h3 class="fw-bold mb-0 text-success" style="font-family: 'Outfit', sans-serif;">
                      {{ $fees['paid'] ?? 0 }}
                    </h3>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center" style="background: white; border: 1.5px solid #e2e8f0;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Pending Invoices</span>
                    <h3 class="fw-bold mb-0 text-danger" style="font-family: 'Outfit', sans-serif;">
                      {{ $fees['pending'] ?? 0 }}
                    </h3>
                  </div>
                </div>
              </div>

              <!-- Challans table list -->
              <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important;">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th class="ps-3 py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Challan No.</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Month/Year</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Tuition Fee</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Total Payable</th>
                      <th class="py-3 pe-3 text-end" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $studentGr = trim((string)$profile['grno']);
                      $challanList = DB::table('challans')
                        ->where('student_id', (int)$profile['id'])
                        ->orWhere('grno', $studentGr)
                        ->orderBy('year', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();
                    @endphp
                    @foreach($challanList as $ch)
                      <tr>
                        <td class="ps-3 fw-bold" style="color: #1e293b; font-size: 0.85rem;">{{ $ch->challan_number }}</td>
                        <td class="fw-semibold text-muted" style="font-size: 0.85rem;">
                          <i class="bi bi-calendar-event text-primary me-1"></i> {{ $ch->month }} {{ $ch->year }}
                        </td>
                        <td style="font-size: 0.85rem; font-weight: 500; color: #475569;">
                          PKR {{ number_format($ch->tution_fee, 2) }}
                        </td>
                        <td class="fw-bold" style="color: #1e293b; font-size: 0.88rem;">
                          PKR {{ number_format($ch->amount, 2) }}
                        </td>
                        <td class="pe-3 text-end">
                          @if(strtolower($ch->status) === 'paid')
                            <span class="badge badge-soft-success px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem;">PAID</span>
                          @else
                            <span class="badge badge-soft-danger px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem;">PENDING</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

          <!-- 6. ONLINE EXAMS TAB -->
          <div class="tab-pane fade" id="pane-online" role="tabpanel" aria-labelledby="tab-online">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Online Exam Portals</h5>
            
            <div class="text-center py-5 px-3 rounded-4 border shadow-xs" style="background-color: #fff; border-color: #e2e8f0 !important;">
              <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-laptop" style="font-size: 2.8rem; color: #004ac6;"></i>
              </div>
              <h5 class="fw-bold" style="color: #1e293b;">Digital Examination Center</h5>
              <p class="text-muted mx-auto" style="max-width: 480px; font-size: 0.88rem;">Online dynamic tests, multiple-choice quizzes, and computer-based examinations taken by this student will be synced and fully displayed in this portal.</p>
              
              <span class="badge bg-light text-muted border px-4 py-2 mt-2 fw-semibold" style="border-radius: 8px;">
                <i class="bi bi-arrow-repeat spin me-1 text-primary"></i> Examination Server Synced
              </span>
            </div>
          </div>

          <!-- 7. SIBLINGS TAB -->
          <div class="tab-pane fade" id="pane-siblings" role="tabpanel" aria-labelledby="tab-siblings">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Registered Siblings Profiles</h5>
            
            @if(empty($siblings))
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-people" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Siblings Linked</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">No other students share the same parent profile ID in this branch.</p>
              </div>
            @else
              <div class="row g-3">
                @foreach($siblings as $sib)
                  <div class="col-md-6">
                    <div class="p-3.5 rounded-4 border bg-white d-flex align-items-center shadow-xs" style="border-color: #f1f5f9; transition: all 0.3s ease;">
                      <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6; font-size: 1.25rem;">
                        <i class="bi bi-person-fill"></i>
                      </div>
                      <div style="min-width: 0; flex-grow: 1;">
                        <h6 class="fw-bold text-truncate mb-0" style="color: #1e293b; font-size: 0.92rem;">{{ $sib['name'] ?? 'Sibling' }}</h6>
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">
                          GR: {{ $sib['grno'] ?? 'N/A' }} | Class: {{ $sib['class'] ?? 'N/A' }}
                        </small>
                      </div>
                      <div>
                        <a href="{{ route('students.view', $sib['id']) }}" class="btn btn-sm btn-light rounded-circle shadow-xs" style="width: 32px; height: 32px; padding: 0; line-height: 32px; text-align: center; border: 1px solid #dee2e6;">
                          <i class="bi bi-arrow-right"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <!-- 8. ATTENDANCE TAB -->
          <div class="tab-pane fade" id="pane-attendance" role="tabpanel" aria-labelledby="tab-attendance">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Monthly Attendance Statistics</h5>
            
            @php
              $totalGr = trim((string)$profile['grno']);
              $studentLogs = DB::table('attendances')
                ->where('student_id', (int)$profile['id'])
                ->orWhere('grno', $totalGr)
                ->orderBy('date', 'desc')
                ->get();
              $totalDays = count($studentLogs);
              $presents = $studentLogs->filter(fn($item) => strtolower($item->status) === 'present')->count();
              $absents = $studentLogs->filter(fn($item) => strtolower($item->status) === 'absent')->count();
              $leaves = $studentLogs->filter(fn($item) => strtolower($item->status) === 'leave')->count();
              $attendanceRate = $totalDays > 0 ? round(($presents / $totalDays) * 100) : 0;
            @endphp

            @if($totalDays === 0)
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-calendar3-range" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Attendance Logs Found</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">No daily attendance records have been registered or logged for this student in the database.</p>
              </div>
            @else
              <!-- Metrics Cards -->
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center bg-white border" style="border-color: #e2e8f0 !important;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Marked Days</span>
                    <h3 class="fw-bold mb-0" style="color: #1e293b; font-family: 'Outfit', sans-serif;">{{ $totalDays }}</h3>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center bg-white border" style="border-color: #e2e8f0 !important;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Present Rate</span>
                    <h3 class="fw-bold mb-0 text-success" style="font-family: 'Outfit', sans-serif;">{{ $attendanceRate }}%</h3>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 rounded-4 shadow-sm text-center bg-white border" style="border-color: #e2e8f0 !important;">
                    <span class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Absents / Leaves</span>
                    <h3 class="fw-bold mb-0 text-danger" style="font-family: 'Outfit', sans-serif;">{{ $absents }} / {{ $leaves }}</h3>
                  </div>
                </div>
              </div>

              <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #1e293b; font-size: 0.95rem;">Attendance Log Registry</h5>
              <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important; max-height: 350px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                      <th class="ps-3 py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Date</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Status</th>
                      <th class="py-3 pe-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($studentLogs as $log)
                      <tr>
                        <td class="ps-3 fw-semibold text-muted" style="font-size: 0.88rem;">
                          <i class="bi bi-calendar-event me-2 text-primary"></i> 
                          {{ \Carbon\Carbon::parse($log->date)->format('F d, Y') }}
                        </td>
                        <td>
                          @if(strtolower($log->status) === 'present')
                            <span class="badge badge-soft-success px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem; text-transform: uppercase;">Present</span>
                          @elseif(strtolower($log->status) === 'absent')
                            <span class="badge badge-soft-danger px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem; text-transform: uppercase;">Absent</span>
                          @else
                            <span class="badge badge-soft-info px-3 py-2 fw-bold" style="border-radius: 6px; font-size: 0.75rem; text-transform: uppercase;">{{ $log->status }}</span>
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

          <!-- 9. RECENT ENTRIES TAB -->
          <div class="tab-pane fade" id="pane-entries" role="tabpanel" aria-labelledby="tab-entries">
            <h5 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif; color: #1e293b; border-left: 4px solid #004ac6; padding-left: 10px;">Recent Results Entries</h5>
            
            @if(empty($entries))
              <div class="text-center py-5 rounded-3" style="background-color: #f8fafc; border: 1.5px dashed #e2e8f0;">
                <i class="bi bi-journal-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="fw-bold text-muted mt-3">No Exam Entries Found</h6>
                <p class="text-muted px-4" style="font-size: 0.85rem; max-width: 400px; margin: 8px auto 0 auto;">There are no examination results recently updated or recorded for this student.</p>
              </div>
            @else
              <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important;">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th class="ps-3 py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Term Exam</th>
                      <th class="py-3" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Subject Code</th>
                      <th class="py-3 pe-3 text-end" style="color: #475569; font-weight: 700; font-size: 0.82rem; text-transform: uppercase;">Last Updated</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($entries as $e)
                      <tr>
                        <td class="ps-3 fw-bold" style="color: #1e293b; font-size: 0.85rem;">{{ $e->term ?? 'N/A' }}</td>
                        <td>
                          <span class="badge rounded-pill bg-light text-primary border px-2.5 py-1 fw-bold" style="font-size: 0.78rem;">
                            {{ $e->subject ?? 'N/A' }}
                          </span>
                        </td>
                        <td class="pe-3 text-end fw-semibold text-muted" style="font-size: 0.85rem;">
                          <i class="bi bi-clock me-1 text-primary"></i> 
                          {{ $e->updated_at ? \Carbon\Carbon::parse($e->updated_at)->format('M d, Y | h:i A') : 'N/A' }}
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
  /* Custom Horizontal Scrolling Nav Bar Styling */
  .tab-scroller::-webkit-scrollbar {
    height: 4px;
  }
  .tab-scroller::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .tab-scroller::-webkit-scrollbar-track {
    background: #f1f5f9;
  }

  /* Custom Student Prominent Tabs */
  .custom-student-tab {
    border: none !important;
    border-bottom: 3px solid transparent !important;
    font-weight: 700 !important;
    color: #475569 !important; /* Prominent visible dark color for unselected tabs */
    font-size: 0.9rem !important;
    padding: 10px 18px !important;
    background: transparent !important;
    transition: all 0.3s ease !important;
    border-radius: 0 !important;
  }
  .custom-student-tab:hover {
    color: #004ac6 !important;
    background-color: rgba(0, 74, 198, 0.02) !important;
  }
  .custom-student-tab.active {
    color: #004ac6 !important;
    border-color: #004ac6 !important;
    font-weight: 800 !important;
    background: transparent !important;
  }

  /* Subject Card Hover Micro-Interactions */
  .card-subject-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 74, 198, 0.05) !important;
    border-color: rgba(0, 74, 198, 0.2) !important;
  }

  /* Custom badge soft-colors styling */
  .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
  .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
  .bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }
  .bg-primary-light { background-color: rgba(0, 74, 198, 0.08); }
  .bg-warning-light { background-color: rgba(254, 166, 25, 0.1); }

  .badge-soft-success {
    background-color: rgba(25, 135, 84, 0.08);
    color: #198754;
    border: 1px solid rgba(25, 135, 84, 0.2);
  }
  .badge-soft-danger {
    background-color: rgba(220, 53, 69, 0.08);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
  }
  .badge-soft-info {
    background-color: rgba(13, 202, 240, 0.08);
    color: #0dcaf0;
    border: 1px solid rgba(13, 202, 240, 0.2);
  }
  .badge-soft-secondary {
    background-color: rgba(108, 117, 125, 0.08);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.2);
  }

  /* Pulse dynamic animations */
  .animate-icon-box {
    transition: all 0.3s ease;
  }
  .animate-icon-box:hover {
    transform: scale(1.1);
    background-color: rgba(0, 74, 198, 0.1) !important;
  }
</style>
@endsection
