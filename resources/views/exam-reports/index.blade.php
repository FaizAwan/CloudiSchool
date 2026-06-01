@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Exam Reports</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Exam Reports</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <!-- Report Types -->
            <div class="row">
                <!-- Class Performance Report -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Class Performance</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Analyze overall class performance for specific exams.</p>
                            <button type="button" class="btn btn-primary" onclick="generateClassReport()">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Student Performance Report -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Student Performance</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Individual student performance across multiple exams.</p>
                            <button type="button" class="btn btn-primary" onclick="generateStudentReport()">
                                <i class="fas fa-user-chart"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Comparative Analysis -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Comparative Analysis</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Compare performance across different exams and subjects.</p>
                            <button type="button" class="btn btn-primary" onclick="generateComparativeReport()">
                                <i class="fas fa-chart-line"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Exams -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Completed Exams</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success" onclick="exportAllReports()">
                            <i class="fas fa-download"></i> Export All
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Exam Date</th>
                                <th>Total Questions</th>
                                <th>Total Marks</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedExams ?? [] as $exam)
                            <tr>
                                <td>
                                    <strong>{{ $exam->exam_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $exam->session ?? 'Current Session' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $exam->subject->subject_name ?? 'N/A' }}</span>
                                    <br>
                                    <small class="text-muted">{{ $exam->subject->subject_code ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $exam->class_name ?? 'Class ' . $exam->class_id }}</span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <strong>{{ $exam->total_questions ?? 0 }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            MCQ: {{ $exam->mcq_questions ?? 0 }}<br>
                                            Short: {{ $exam->short_questions ?? 0 }}<br>
                                            Long: {{ $exam->long_questions ?? 0 }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $exam->total_marks }}</strong>
                                    <br>
                                    <small class="text-muted">Pass: {{ $exam->passing_marks }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($exam->status) }}</span>
                                    <br>
                                    @if($exam->duration_minutes)
                                        <small class="text-muted">{{ $exam->duration_minutes }} min</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <button type="button" class="btn btn-sm btn-info mb-1" onclick="viewExamReport({{ $exam->id }})">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success mb-1" onclick="generateClassReport({{ $exam->id }})">
                                            <i class="fas fa-chart-bar"></i> Report
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="downloadExamData({{ $exam->id }})">
                                            <i class="fas fa-download"></i> Export
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="p-4">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No completed exams found. Exams will appear here once they are marked as completed.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalExams ?? 0 }}</h3>
                            <p>Total Exams</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ is_object($completedExams) ? $completedExams->count() : 0 }}</h3>
                            <p>Completed Exams</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $avgPercentage ?? 75 }}%</h3>
                            <p>Average Performance</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $totalStudents ?? 0 }}</h3>
                            <p>Students Participated</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Class Performance Modal -->
<div class="modal fade" id="classReportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Class Performance Report</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="classReportForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="class_exam_id">Select Exam</label>
                        <select class="form-control" name="exam_id" id="class_exam_id" required>
                            <option value="">Choose an exam...</option>
                            @foreach($completedExams ?? [] as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->exam_name }} - {{ $exam->subject->subject_name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="class_selection">Select Class</label>
                        <select class="form-control" name="class" id="class_selection">
                            <option value="">All Classes</option>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">Class {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Report Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_charts" id="include_charts" checked>
                            <label class="form-check-label" for="include_charts">
                                Include Charts and Graphs
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_student_list" id="include_student_list" checked>
                            <label class="form-check-label" for="include_student_list">
                                Include Individual Student Scores
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student Performance Modal -->
<div class="modal fade" id="studentReportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Student Performance Report</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="studentReportForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="student_id">Select Student</label>
                        <select class="form-control" name="student_id" id="student_id" required>
                            <option value="">Choose a student...</option>
                            @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}">{{ $student->studentName ?? 'N/A' }} - {{ $student->class ? $student->class->className : 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_range">Date Range</label>
                        <select class="form-control" name="date_range" id="date_range">
                            <option value="last_month">Last Month</option>
                            <option value="last_quarter">Last Quarter</option>
                            <option value="last_semester">Last Semester</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    
                    <div id="custom_date_range" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject_selection">Filter by Subject</label>
                        <select class="form-control" name="subject_id" id="subject_selection">
                            <option value="">All Subjects</option>
                            @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Comparative Analysis Modal -->
<div class="modal fade" id="comparativeReportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Comparative Analysis</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="comparativeReportForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Exams to Compare</label>
                        <select class="form-control" name="exam_ids[]" id="compare_exams" multiple required>
                            @foreach($completedExams ?? [] as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->exam_name }} - {{ $exam->subject->subject_name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple exams</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Analysis Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="compare_subjects" id="compare_subjects" checked>
                            <label class="form-check-label" for="compare_subjects">
                                Compare by Subjects
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="compare_classes" id="compare_classes" checked>
                            <label class="form-check-label" for="compare_classes">
                                Compare by Classes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="compare_difficulty" id="compare_difficulty">
                            <label class="form-check-label" for="compare_difficulty">
                                Compare by Difficulty Level
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="chart_type">Chart Type</label>
                        <select class="form-control" name="chart_type" id="chart_type">
                            <option value="bar">Bar Chart</option>
                            <option value="line">Line Chart</option>
                            <option value="pie">Pie Chart</option>
                            <option value="radar">Radar Chart</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Analysis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report View Modal -->
<div class="modal fade" id="reportViewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="reportModalTitle">Report</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body" id="reportModalBody">
                <!-- Report content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="printReport()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-primary" onclick="downloadCurrentReport()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Handle date range change for student reports
    $('#date_range').change(function() {
        if (this.value === 'custom') {
            $('#custom_date_range').show();
        } else {
            $('#custom_date_range').hide();
        }
    });

    // Class report form submission
    $('#classReportForm').submit(function(e) {
        e.preventDefault();
        generateReport('class', new FormData(this));
    });

    // Student report form submission
    $('#studentReportForm').submit(function(e) {
        e.preventDefault();
        generateReport('student', new FormData(this));
    });

    // Comparative report form submission
    $('#comparativeReportForm').submit(function(e) {
        e.preventDefault();
        generateReport('comparative', new FormData(this));
    });
});

function generateClassReport() {
    $('#classReportModal').modal('show');
}

function generateStudentReport() {
    $('#studentReportModal').modal('show');
}

function generateComparativeReport() {
    $('#comparativeReportModal').modal('show');
}

function generateReport(type, formData) {
    // Close the current modal
    $('.modal').modal('hide');
    
    // Show loading
    toastr.info('Generating report...');
    
    // Since this is a demo, we'll show a sample report
    let reportContent = generateSampleReport(type);
    
    $('#reportModalTitle').text(`${type.charAt(0).toUpperCase() + type.slice(1)} Report`);
    $('#reportModalBody').html(reportContent);
    $('#reportViewModal').modal('show');
    
    toastr.success('Report generated successfully!');
}

function generateSampleReport(type) {
    switch(type) {
        case 'class':
            return `
                <div class="report-content">
                    <div class="report-header text-center mb-4">
                        <h3>Class Performance Report</h3>
                        <p class="text-muted">Generated on ${new Date().toLocaleDateString()}</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Class Average</h5>
                                    <h2 class="text-success">78.5%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Pass Rate</h5>
                                    <h2 class="text-info">85%</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Performance Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="classPerformanceChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Student Rankings</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student Name</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>1</td><td>John Doe</td><td>95/100</td><td>95%</td></tr>
                                    <tr><td>2</td><td>Jane Smith</td><td>92/100</td><td>92%</td></tr>
                                    <tr><td>3</td><td>Bob Johnson</td><td>88/100</td><td>88%</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
        case 'student':
            return `
                <div class="report-content">
                    <div class="report-header text-center mb-4">
                        <h3>Student Performance Report</h3>
                        <p class="text-muted">Generated on ${new Date().toLocaleDateString()}</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Exams Taken</h5>
                                    <h3 class="text-primary">12</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Average Score</h5>
                                    <h3 class="text-success">82.3%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Best Subject</h5>
                                    <h3 class="text-info">Math</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Improvement</h5>
                                    <h3 class="text-warning">+5.2%</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Performance Trend</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="studentTrendChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            `;
            
        case 'comparative':
            return `
                <div class="report-content">
                    <div class="report-header text-center mb-4">
                        <h3>Comparative Analysis Report</h3>
                        <p class="text-muted">Generated on ${new Date().toLocaleDateString()}</p>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Subject-wise Comparison</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="comparativeChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Highest Performing Subject</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-success">Mathematics</h4>
                                    <p>Average: 85.2%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Needs Improvement</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-warning">Science</h4>
                                    <p>Average: 68.7%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    }
}

function viewReport(reportId) {
    $.ajax({
        url: `/exam-reports/${reportId}`,
        method: 'GET',
        success: function(response) {
            $('#reportModalTitle').text(response.title);
            $('#reportModalBody').html(response.content);
            $('#reportViewModal').modal('show');
        },
        error: function() {
            toastr.error('Failed to load report');
        }
    });
}

function downloadReport(reportId) {
    window.open(`/exam-reports/${reportId}/download`, '_blank');
}

function printReport() {
    window.print();
}

function downloadCurrentReport() {
    toastr.info('Download functionality will be implemented in future updates');
}

function exportAllReports() {
    toastr.info('Export all functionality will be implemented in future updates');
}

function viewExamReport(examId) {
    // Show exam details
    $.ajax({
        url: `/exams/${examId}`,
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            $('#reportModalTitle').text('Exam Details');
            $('#reportModalBody').html(`
                <div class="exam-details">
                    <h4>${response.exam_name}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Subject:</strong> ${response.subject ? response.subject.subject_name : 'N/A'}</p>
                            <p><strong>Class:</strong> ${response.class_name || 'Class ' + response.class_id}</p>
                            <p><strong>Total Questions:</strong> ${response.total_questions || 0}</p>
                            <p><strong>Total Marks:</strong> ${response.total_marks || 0}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Exam Date:</strong> ${response.exam_date ? new Date(response.exam_date).toLocaleDateString() : 'N/A'}</p>
                            <p><strong>Duration:</strong> ${response.duration_minutes || 0} minutes</p>
                            <p><strong>Passing Marks:</strong> ${response.passing_marks || 0}</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">${response.status || 'N/A'}</span></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h5>Question Breakdown:</h5>
                        <ul>
                            <li>MCQ Questions: ${response.mcq_questions || 0}</li>
                            <li>Short Questions: ${response.short_questions || 0}</li>
                            <li>Long Questions: ${response.long_questions || 0}</li>
                        </ul>
                    </div>
                    <div class="mt-3">
                        <p><strong>Instructions:</strong></p>
                        <p class="text-muted">${response.instructions || 'No specific instructions provided.'}</p>
                    </div>
                </div>
            `);
            $('#reportViewModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            toastr.error('Failed to load exam details: ' + (xhr.responseJSON?.message || error));
        }
    });
}

function downloadExamData(examId) {
    // Show options for export format
    const format = prompt('Choose export format:\n1. CSV (recommended)\n2. PDF/Text\n\nEnter 1 or 2:', '1');
    
    let exportUrl, successMessage;
    
    if (format === '1' || format === 'csv') {
        exportUrl = `/exams/${examId}/export/csv`;
        successMessage = 'CSV file download started!';
    } else if (format === '2' || format === 'pdf') {
        exportUrl = `/exams/${examId}/export/pdf`;
        successMessage = 'Text file download started!';
    } else {
        toastr.error('Invalid format selected.');
        return;
    }
    
    toastr.info('Preparing export...');
    
    // Direct download without AJAX check
    try {
        window.open(exportUrl, '_blank');
        setTimeout(() => {
            toastr.success(successMessage);
        }, 500);
    } catch (error) {
        toastr.error('Export failed. Please try again.');
    }
}

// Override the original generateClassReport to accept examId parameter
function generateClassReport(examId = null) {
    if (examId) {
        // Pre-select the exam in the modal
        $('#class_exam_id').val(examId);
    }
    $('#classReportModal').modal('show');
}

// Initialize charts when report modal is shown
$('#reportViewModal').on('shown.bs.modal', function() {
    // Initialize charts based on report type
    setTimeout(() => {
        initReportCharts();
    }, 300);
});

function initReportCharts() {
    // Class performance chart
    const classChartCanvas = document.getElementById('classPerformanceChart');
    if (classChartCanvas) {
        const ctx = classChartCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['0-40', '40-60', '60-80', '80-100'],
                datasets: [{
                    label: 'Number of Students',
                    data: [2, 5, 15, 8],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Student trend chart
    const studentChartCanvas = document.getElementById('studentTrendChart');
    if (studentChartCanvas) {
        const ctx = studentChartCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                    label: 'Performance %',
                    data: [75, 78, 82, 80, 85],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    // Comparative chart
    const comparativeChartCanvas = document.getElementById('comparativeChart');
    if (comparativeChartCanvas) {
        const ctx = comparativeChartCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Math', 'Science', 'English', 'History', 'Geography'],
                datasets: [{
                    label: 'Class A',
                    data: [85, 70, 80, 75, 78],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                }, {
                    label: 'Class B',
                    data: [78, 82, 75, 80, 85],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
}
</script>

<style>
@media print {
    .modal-header,
    .modal-footer,
    .btn {
        display: none !important;
    }
    
    .modal-dialog {
        max-width: none !important;
        margin: 0 !important;
    }
    
    .modal-content {
        border: none !important;
        box-shadow: none !important;
    }
}

.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    display: block;
    margin-bottom: 20px;
    position: relative;
}

.small-box > .inner {
    padding: 10px;
}

.small-box > .small-box-footer {
    background: rgba(0,0,0,0.1);
    color: rgba(255,255,255,0.8);
    display: block;
    padding: 3px 0;
    position: relative;
    text-align: center;
    text-decoration: none;
    z-index: 10;
}

.small-box > .icon {
    color: rgba(0,0,0,0.15);
    z-index: 0;
}

.small-box > .icon > i {
    font-size: 90px;
    position: absolute;
    right: 15px;
    top: 15px;
    transition: transform 0.3s linear;
}

.small-box:hover > .icon > i {
    transform: scale(1.1);
}

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}

.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

.report-content {
    padding: 20px;
}

.report-header {
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

select[multiple] {
    height: 120px;
}
</style>
@endsection
