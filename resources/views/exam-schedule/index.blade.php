@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Exam Schedule</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Exam Schedule</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <!-- Schedule Controls -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Filter by Class</label>
                                <select class="form-select" id="class_filter">
                                    <option value="">All Classes</option>
                                    @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}">{{ $class->className }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Filter by Subject</label>
                                <select class="form-select" id="subject_filter">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects ?? [] as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#scheduleExamModal">
                                    <i class="bi bi-plus-circle"></i> Schedule Exam
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar View -->
            <div class="card" id="calendar_view">
                <div class="card-header">
                    <h3 class="card-title">Exam Calendar</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="previousMonth()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="current_month" class="mx-3">{{ date('F Y') }}</span>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="nextMonth()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- List View -->
            <div class="card" id="list_view">
                <div class="card-header">
                    <h3 class="card-title">Scheduled Exams</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scheduledExams ?? [] as $exam)
                            <tr>
                                <td>{{ $exam->exam_name }}</td>
                                <td>{{ $exam->subject->subject_name ?? 'N/A' }}</td>
                                <td>{{ $exam->class_name ?? $exam->class_id ?? 'N/A' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}</small>
                                </td>
                                <td>{{ $exam->duration_minutes ?? $exam->duration ?? 'N/A' }} min</td>
                                <td>
                                    <span class="badge bg-{{ $exam->status === 'published' ? 'success' : ($exam->status === 'draft' ? 'warning' : 'info') }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" onclick="viewExamDetails({{ $exam->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editSchedule({{ $exam->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="cancelExam({{ $exam->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="p-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No exams scheduled yet. Click "Schedule Exam" to get started.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Exams Widget -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Upcoming Exams (Next 7 Days)</h5>
                        </div>
                        <div class="card-body">
                            @forelse($upcomingExams ?? [] as $exam)
                            <div class="exam-item mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">{{ $exam->exam_name }}</h6>
                                        <p class="mb-1 text-muted">{{ $exam->subject->subject_name ?? 'N/A' }} - {{ $exam->class_name ?? $exam->class_id ?? 'N/A' }}</p>
                                        <small class="text-info">{{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="badge bg-primary">{{ $exam->duration_minutes ?? $exam->duration ?? 'N/A' }} min</span>
                                    </div>
                                </div>
                            </div>
                            @if(!$loop->last)<hr>@endif
                            @empty
                            <p class="text-muted text-center">No upcoming exams in the next 7 days.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Schedule Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="description-block">
                                        <span class="description-percentage text-primary">{{ $todayExams ?? 0 }}</span>
                                        <span class="description-text">Today</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="description-block">
                                        <span class="description-percentage text-info">{{ $thisWeekExams ?? 0 }}</span>
                                        <span class="description-text">This Week</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="description-block">
                                        <span class="description-percentage text-success">{{ $thisMonthExams ?? 0 }}</span>
                                        <span class="description-text">This Month</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Exam Modal -->
<div class="modal fade" id="scheduleExamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Schedule Exam</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="scheduleExamForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="exam_id" class="form-label">Select Exam <span class="text-danger">*</span></label>
                        <select class="form-select" name="exam_id" id="exam_id" required>
                            <option value="">Choose an exam...</option>
                            @forelse($availableExams ?? [] as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->exam_name }} - {{ $exam->subject->subject_name ?? 'N/A' }}</option>
                            @empty
                            <option disabled>No draft exams available for scheduling</option>
                            @endforelse
                        </select>
                        @if(count($availableExams ?? []) === 0)
                        <small class="form-text text-warning">
                            <i class="bi bi-info-circle"></i> No draft exams available. Create exams first to schedule them.
                        </small>
                        @endif
                        <div class="invalid-feedback">Please select an exam to schedule.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="schedule_date" class="form-label">Exam Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="exam_date" id="schedule_date" required>
                                <div class="invalid-feedback">Please select a valid exam date.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="schedule_time" class="form-label">Exam Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="exam_time" id="schedule_time" required>
                                <div class="invalid-feedback">Please select a valid exam time.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="schedule_class" class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select" name="class_id" id="schedule_class" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}">{{ $class->className }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a class.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="room_number" class="form-label">Room Number</label>
                                <input type="text" class="form-control" name="room_number" id="room_number" placeholder="e.g., Room 101">
                                <small class="form-text text-muted">Optional: Specify the exam room</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="invigilator" class="form-label">Invigilator</label>
                        <select class="form-select" name="invigilator_id" id="invigilator">
                            <option value="">Select Invigilator</option>
                            @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Optional: Assign a teacher to supervise the exam</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="special_instructions" class="form-label">Special Instructions</label>
                        <textarea class="form-control" name="special_instructions" id="special_instructions" rows="3" placeholder="Any special instructions for this exam session..."></textarea>
                        <small class="form-text text-muted">Optional: Add any special notes or requirements</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_notifications" id="send_notifications" checked>
                            <label class="form-check-label" for="send_notifications">
                                Send notifications to students and parents
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Exam Schedule</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="editScheduleForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="schedule_id" id="edit_schedule_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_exam_name">Exam Name</label>
                        <input type="text" class="form-control" id="edit_exam_name" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_schedule_date">Exam Date</label>
                                <input type="date" class="form-control" name="exam_date" id="edit_schedule_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_schedule_time">Exam Time</label>
                                <input type="time" class="form-control" name="exam_time" id="edit_schedule_time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_room_number">Room Number</label>
                                <input type="text" class="form-control" name="room_number" id="edit_room_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_invigilator">Invigilator</label>
                                <select class="form-control" name="invigilator_id" id="edit_invigilator">
                                    <option value="">Select Invigilator</option>
                                    @foreach($teachers ?? [] as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_special_instructions">Special Instructions</label>
                        <textarea class="form-control" name="special_instructions" id="edit_special_instructions" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Exam Details Modal -->
<div class="modal fade" id="examDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Exam Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body" id="exam_details_content">
                <!-- Exam details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

<script>
let calendar;
let currentDate = new Date();

$(document).ready(function() {
    // Initialize calendar
    initializeCalendar();
    

    // Schedule exam form submission
    $('#scheduleExamForm').submit(function(e) {
        e.preventDefault();
        
        // Remove existing validation feedback
        $(this).find('.is-invalid').removeClass('is-invalid');
        
        // Basic client-side validation
        let isValid = true;
        const examId = $('#exam_id').val();
        const examDate = $('#schedule_date').val();
        const examTime = $('#schedule_time').val();
        const classId = $('#schedule_class').val();
        
        if (!examId) {
            $('#exam_id').addClass('is-invalid');
            isValid = false;
        }
        
        if (!examDate) {
            $('#schedule_date').addClass('is-invalid');
            isValid = false;
        }
        
        if (!examTime) {
            $('#schedule_time').addClass('is-invalid');
            isValid = false;
        }
        
        if (!classId) {
            $('#schedule_class').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        let formData = new FormData(this);
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="bi bi-spinner spin"></i> Scheduling...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("exam-schedule.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleExamModal'));
                modal.hide();
                
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Exam scheduled successfully!');
                } else {
                    alert('Exam scheduled successfully!');
                }
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors || {};
                let errorMessages = Object.values(errors).flat().join('<br>');
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessages || 'Failed to schedule exam');
                } else {
                    alert(errorMessages || 'Failed to schedule exam');
                }
                
                // Highlight invalid fields
                Object.keys(errors).forEach(field => {
                    $(`[name="${field}"]`).addClass('is-invalid');
                });
            },
            complete: function() {
                // Reset loading state
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Edit schedule form submission
    $('#editScheduleForm').submit(function(e) {
        e.preventDefault();
        
        let scheduleId = $('#edit_schedule_id').val();
        let formData = new FormData(this);
        
        $.ajax({
            url: `/exam-schedule/${scheduleId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editScheduleModal'));
                modal.hide();
                toastr.success('Schedule updated successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = Object.values(errors).flat().join('<br>');
                toastr.error(errorMessages);
            }
        });
    });
});

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    
    try {
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            @foreach($scheduledExams ?? [] as $exam)
            @if($exam->exam_date && $exam->exam_time)
            @php
                try {
                    $startDateTime = \Carbon\Carbon::parse($exam->exam_date . ' ' . $exam->exam_time);
                    $duration = $exam->duration_minutes ?? $exam->duration ?? 60;
                    $endDateTime = $startDateTime->copy()->addMinutes($duration);
                    $validEvent = true;
                } catch (Exception $e) {
                    $validEvent = false;
                }
            @endphp
            @if($validEvent)
            {
                id: '{{ $exam->id }}',
                title: '{{ addslashes($exam->exam_name) }}',
                start: '{{ $exam->exam_date }}T{{ $exam->exam_time }}',
                end: '{{ $endDateTime->format('Y-m-d\TH:i:s') }}',
                backgroundColor: '{{ $exam->status === "published" ? "#28a745" : ($exam->status === "draft" ? "#ffc107" : "#17a2b8") }}',
                borderColor: '{{ $exam->status === "published" ? "#28a745" : ($exam->status === "draft" ? "#ffc107" : "#17a2b8") }}',
                extendedProps: {
                    subject: '{{ addslashes($exam->subject->subject_name ?? "N/A") }}',
                    class: '{{ addslashes($exam->class_name ?? $exam->class_id ?? "N/A") }}',
                    duration: '{{ $exam->duration_minutes ?? $exam->duration ?? 60 }}',
                    status: '{{ $exam->status }}'
                }
            },
            @endif
            @endif
            @endforeach
        ],
        eventClick: function(info) {
            viewExamDetails(info.event.id);
        },
        dateClick: function(info) {
            $('#schedule_date').val(info.dateStr);
            const modal = new bootstrap.Modal(document.getElementById('scheduleExamModal'));
            modal.show();
        },
        eventMouseEnter: function(info) {
            // Tooltip functionality can be added here
        }
    });
    
    calendar.render();
    } catch (error) {
        console.error('Calendar initialization error:', error);
        document.getElementById('calendar').innerHTML = '<div class="alert alert-warning">Calendar could not be loaded. Please refresh the page.</div>';
    }
}

function previousMonth() {
    calendar.prev();
    updateMonthDisplay();
}

function nextMonth() {
    calendar.next();
    updateMonthDisplay();
}

function updateMonthDisplay() {
    const date = calendar.getDate();
    $('#current_month').text(date.toLocaleString('default', { month: 'long', year: 'numeric' }));
}

function viewExamDetails(examId) {
    $.ajax({
        url: `/exams/${examId}`,
        method: 'GET',
        success: function(exam) {
            let content = `
                <div class="exam-details">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>${exam.exam_name}</h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="badge bg-${exam.status === 'published' ? 'success' : (exam.status === 'draft' ? 'warning' : 'info')}">
                                ${exam.status.charAt(0).toUpperCase() + exam.status.slice(1)}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Subject:</strong> ${exam.subject ? exam.subject.name : 'N/A'}</p>
                            <p><strong>Class:</strong> ${exam.class}</p>
                            <p><strong>Total Marks:</strong> ${exam.total_marks}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> ${new Date(exam.exam_date).toLocaleDateString()}</p>
                            <p><strong>Time:</strong> ${exam.exam_time}</p>
                            <p><strong>Duration:</strong> ${exam.duration} minutes</p>
                        </div>
                    </div>
                    
                    ${exam.instructions ? `
                        <div class="mt-3">
                            <h6>Instructions:</h6>
                            <div class="p-3" style="background: #f8f9fa; border-radius: 5px;">
                                ${exam.instructions}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            
            $('#exam_details_content').html(content);
            const modal = new bootstrap.Modal(document.getElementById('examDetailsModal'));
            modal.show();
        },
        error: function() {
            toastr.error('Failed to load exam details');
        }
    });
}

function editSchedule(examId) {
    $.ajax({
        url: `/exam-schedule/${examId}`,
        method: 'GET',
        success: function(schedule) {
            $('#edit_schedule_id').val(schedule.id);
            $('#edit_exam_name').val(schedule.exam_name);
            $('#edit_schedule_date').val(schedule.exam_date ? schedule.exam_date.substring(0, 10) : '');
            // Handle time format which might include seconds that HTML time input dislikes
            $('#edit_schedule_time').val(schedule.exam_time ? schedule.exam_time.substring(11, 16) : '');
            $('#edit_room_number').val(schedule.room_number);
            $('#edit_invigilator').val(schedule.invigilator_id || schedule.teacher_id);
            $('#edit_special_instructions').val(schedule.instructions);
            
            const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            modal.show();
        },
        error: function() {
            toastr.error('Failed to load schedule details');
        }
    });
}

function cancelExam(examId) {
    if (confirm('Are you sure you want to cancel this exam? This action cannot be undone.')) {
        $.ajax({
            url: `/exam-schedule/${examId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                toastr.success('Exam cancelled successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function() {
                toastr.error('Failed to cancel exam');
            }
        });
    }
}

function applyFilters() {
    let classFilter = $('#class_filter').val();
    let subjectFilter = $('#subject_filter').val();
    
    // Filter calendar events
    if (calendar) {
        let events = calendar.getEvents();
        events.forEach(event => {
            let show = true;
            
            if (classFilter && event.extendedProps.class != classFilter) {
                show = false;
            }
            
            if (subjectFilter && event.extendedProps.subject_id != subjectFilter) {
                show = false;
            }
            
            if (show) {
                event.setProp('display', 'block');
            } else {
                event.setProp('display', 'none');
            }
        });
    }
    
    // Filter list view
    filterListView(classFilter, subjectFilter);
}

function filterListView(classFilter, subjectFilter) {
    $('#list_view tbody tr').each(function() {
        let row = $(this);
        let show = true;
        
            if (classFilter) {
                let rowClass = row.find('td:nth-child(3)').text().trim();
                // Handle both 'Class X' and just the class number
                let classNum = rowClass.replace(/^(Class\s*)?/, '').trim();
                if (classNum != classFilter) {
                    show = false;
                }
            }
        
        if (show) {
            row.show();
        } else {
            row.hide();
        }
    });
}

// Apply filters when filter values change
$('#class_filter, #subject_filter').change(function() {
    applyFilters();
});

// Set minimum date for scheduling (today)
$('#schedule_date').attr('min', new Date().toISOString().split('T')[0]);
$('#edit_schedule_date').attr('min', new Date().toISOString().split('T')[0]);
</script>

<style>
#calendar {
    padding: 20px;
}

.fc-event {
    cursor: pointer;
}

.fc-event:hover {
    opacity: 0.8;
}

.exam-item {
    padding: 10px;
    border-left: 4px solid #17a2b8;
    background: #f8f9fa;
    border-radius: 0 5px 5px 0;
}

.description-block {
    margin: 0;
}

.description-percentage {
    color: #999;
    display: block;
    font-size: 24px;
    font-weight: 600;
}

.description-text {
    font-size: 14px;
    display: block;
    color: #999;
    text-transform: uppercase;
}

.fc-toolbar {
    margin-bottom: 1em;
}

.fc-button-group > .fc-button {
    background: #007bff;
    border-color: #007bff;
}

.fc-button-group > .fc-button:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.fc-daygrid-event {
    font-size: 0.8em;
    padding: 2px 4px;
}

.fc-event-title {
    font-weight: 600;
}

.fc-event-time {
    font-style: italic;
}

/* Custom spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}

/* Form improvements */
.form-select:focus,
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.invalid-feedback {
    display: block;
}

.form-group .form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Card improvements */
.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-bottom: none;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Table improvements */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}
</style>
@endsection
