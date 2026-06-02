@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1><i class="bi bi-speedometer2 me-2"></i>School Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <!-- ========== TOP STATS ROW ========== -->
  <div class="row">
    <!-- Total Fees Collected -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass info-card revenue-card clickable-card" onclick="window.location.href='{{ route('reportsCollectiveFees') }}'">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-wallet2" style="font-size: 20px;"></i>
            </div>
            <span class="badge badge-soft-success">
              <i class="bi bi-graph-up-arrow me-1"></i> {{ $dashboardData['feesCollectionRate'] ?? 0 }}%
            </span>
          </div>
          <p class="text-muted small uppercase tracking-wider mb-1">Total Fees Collected</p>
          <h6 class="metric-value">Rs. {{ number_format($dashboardData['totalFeesCollected'] ?? 0) }}</h6>
          <div class="progress-container">
            <div class="progress-fill" style="width: {{ $dashboardData['feesCollectionRate'] ?? 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Fees -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass info-card sales-card clickable-card" onclick="window.location.href='{{ route('challan') }}'">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-hourglass-split" style="font-size: 20px;"></i>
            </div>
            <span class="text-muted small font-bold" style="font-weight: 600; color: #434655;">{{ $dashboardData['studentsWithPendingFees'] ?? 0 }} Students</span>
          </div>
          <p class="text-muted small uppercase tracking-wider mb-1">Pending Fees</p>
          <h6 class="metric-value">Rs. {{ number_format($dashboardData['pendingFees'] ?? 0) }}</h6>
          <p class="small text-danger mt-2 mb-0" style="font-weight: 500; font-size: 12px;">
            <i class="bi bi-exclamation-triangle me-1"></i> Outstanding Fees
          </p>
        </div>
      </div>
    </div>

    <!-- Total Exams -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass info-card customers-card clickable-card" onclick="window.location.href='{{ route('exams.index') }}'">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-journal-text" style="font-size: 20px;"></i>
            </div>
            <span class="badge badge-soft-primary">{{ $dashboardData['activeExams'] ?? 0 }} Active</span>
          </div>
          <p class="text-muted small uppercase tracking-wider mb-1">Total Exams</p>
          <h6 class="metric-value">{{ $dashboardData['totalExams'] ?? 0 }}</h6>
          <p class="small text-muted mt-2 mb-0" style="font-size: 12px;">Semester evaluations</p>
        </div>
      </div>
    </div>

    <!-- Average Performance -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass info-card sales-card clickable-card" onclick="window.location.href='{{ route('exam-reports.index') }}'">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-star-fill" style="font-size: 20px;"></i>
            </div>
            <span class="badge badge-soft-success">High</span>
          </div>
          <p class="text-muted small uppercase tracking-wider mb-1">Avg. Performance</p>
          <h6 class="metric-value">{{ $dashboardData['avgPerformance'] ?? 0 }}%</h6>
          <p class="small text-success mt-2 mb-0" style="font-weight: 500; font-size: 12px;">
            <i class="bi bi-arrow-up-short"></i> {{ $dashboardData['totalAttempts'] ?? 0 }} total attempts
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== ENTITY CARDS ROW ========== -->
  <div class="row">
    <!-- Classes -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass clickable-card shadow-sm" onclick="window.location.href='{{ route('classes') }}'" style="border: 1px solid rgba(195, 198, 215, 0.4) !important;">
        <div class="card-body d-flex align-items-center p-3">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3" style="background: rgba(0, 74, 198, 0.1) !important; color: #004ac6 !important; width: 48px; height: 48px;">
            <i class="bi bi-door-open" style="font-size: 22px;"></i>
          </div>
          <div>
            <h6 class="metric-value mb-0">{{ $dashboardData['totalClasses'] ?? 0 }}</h6>
            <p class="text-muted small mb-0">Classes Active</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Teachers -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass clickable-card shadow-sm" onclick="window.location.href='{{ route('teachers') }}'" style="border: 1px solid rgba(195, 198, 215, 0.4) !important;">
        <div class="card-body d-flex align-items-center p-3">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3" style="background: rgba(254, 166, 25, 0.1) !important; color: #855300 !important; width: 48px; height: 48px;">
            <i class="bi bi-person-badge" style="font-size: 22px;"></i>
          </div>
          <div>
            <h6 class="metric-value mb-0">{{ $dashboardData['totalTeachers'] ?? 0 }}</h6>
            <p class="text-muted small mb-0">Teachers Online</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Students -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass clickable-card shadow-sm" onclick="window.location.href='{{ route('students') }}'" style="border: 1px solid rgba(195, 198, 215, 0.4) !important;">
        <div class="card-body d-flex align-items-center p-3">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3" style="background: rgba(0, 98, 66, 0.1) !important; color: #006242 !important; width: 48px; height: 48px;">
            <i class="bi bi-people" style="font-size: 22px;"></i>
          </div>
          <div>
            <h6 class="metric-value mb-0">{{ $dashboardData['totalStudents'] ?? 0 }}</h6>
            <p class="text-muted small mb-0">Students Enrolled</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Schools -->
    <div class="col-xxl-3 col-md-6">
      <div class="card card-glass clickable-card shadow-sm" onclick="window.location.href='{{ route('schools') }}'" style="border: 1px solid rgba(195, 198, 215, 0.4) !important;">
        <div class="card-body d-flex align-items-center p-3">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3" style="background: rgba(0, 74, 198, 0.1) !important; color: #004ac6 !important; width: 48px; height: 48px;">
            <i class="bi bi-building" style="font-size: 22px;"></i>
          </div>
          <div>
            <h6 class="metric-value mb-0">{{ $dashboardData['totalSchools'] ?? 1 }}</h6>
            <p class="text-muted small mb-0">School Branches</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== DAILY ATTENDANCE SUMMARY ROW ========== -->
  <div class="row mb-3">
    <!-- Teacher Attendance Card -->
    <div class="col-lg-6 mb-3">
      <div class="card card-glass border-0 shadow-sm clickable-card" onclick="window.location.href='{{ route('attendance.reports.teachers') }}'" style="border-radius: 20px; overflow: hidden; background: #fff; border: 1.5px solid rgba(0, 74, 198, 0.08) !important;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title p-0 m-0" style="font-family: 'Outfit', sans-serif; font-weight: 700; color: #1e293b; font-size: 1.1rem; line-height: 1.2;">
              <i class="bi bi-calendar3-range text-primary me-2"></i> Teacher Attendance Summary
            </h5>
            <span class="badge bg-primary-light text-primary px-3 py-1.5 fw-bold" style="border-radius: 20px; font-size: 0.72rem;">
              This Month: {{ $dashboardData['teacherAttendanceMonthlyRate'] ?? 100 }}%
            </span>
          </div>

          <div class="row align-items-center">
            <div class="col-sm-5 text-center py-2">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle animate-icon-box" style="width: 90px; height: 90px; background: rgba(0, 74, 198, 0.04); border: 2.5px solid rgba(0, 74, 198, 0.15);">
                <div class="text-center">
                  <h3 class="fw-bold mb-0" style="color: #004ac6; font-family: 'Outfit', sans-serif; font-size: 1.45rem;">
                    @if(isset($dashboardData['teacherAttendanceTodayRate']) && $dashboardData['teacherAttendanceTodayRate'] !== null)
                      {{ $dashboardData['teacherAttendanceTodayRate'] }}%
                    @else
                      <span style="font-size: 0.75rem;" class="text-muted fw-bold">No Logs</span>
                    @endif
                  </h3>
                  <small class="text-muted fw-bold" style="font-size: 0.65rem; text-transform: uppercase;">Today</small>
                </div>
              </div>
            </div>
            
            <div class="col-sm-7">
              <div class="d-flex flex-column gap-2 text-start">
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small fw-semibold"><i class="bi bi-circle-fill text-success me-2" style="font-size: 0.5rem;"></i> Present Today:</span>
                  <span class="fw-bold" style="color: #1e293b;">{{ $dashboardData['teacherAttendanceActiveToday'] ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small fw-semibold"><i class="bi bi-circle-fill text-danger me-2" style="font-size: 0.5rem;"></i> Absent Today:</span>
                  <span class="fw-bold" style="color: #1e293b;">{{ $dashboardData['teacherAttendanceAbsentToday'] ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small fw-semibold"><i class="bi bi-circle-fill text-info me-2" style="font-size: 0.5rem;"></i> On Leave Today:</span>
                  <span class="fw-bold" style="color: #1e293b;">{{ $dashboardData['teacherAttendanceLeaveToday'] ?? 0 }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i> Click to view teacher monthly registry</small>
            <span class="text-primary fw-bold small" style="font-size: 0.8rem;">View Report <i class="bi bi-chevron-right ms-1"></i></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Student Attendance Card -->
    <div class="col-lg-6 mb-3">
      <div class="card card-glass border-0 shadow-sm clickable-card" onclick="window.location.href='{{ route('attendance.reports.students') }}'" style="border-radius: 20px; overflow: hidden; background: #fff; border: 1.5px solid rgba(25, 135, 84, 0.08) !important;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title p-0 m-0" style="font-family: 'Outfit', sans-serif; font-weight: 700; color: #1e293b; font-size: 1.1rem; line-height: 1.2;">
              <i class="bi bi-people-fill text-success me-2"></i> Student Attendance Summary
            </h5>
            <span class="badge bg-success-light text-success px-3 py-1.5 fw-bold" style="border-radius: 20px; font-size: 0.72rem;">
              Enrolled Term
            </span>
          </div>

          <div class="row align-items-center">
            <div class="col-sm-5 text-center py-2">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle animate-icon-box" style="width: 90px; height: 90px; background: rgba(25, 135, 84, 0.04); border: 2.5px solid rgba(25, 135, 84, 0.15);">
                <div class="text-center">
                  <h3 class="fw-bold mb-0 text-success" style="font-family: 'Outfit', sans-serif; font-size: 1.45rem;">
                    @php
                      $todayDateStr = date('Y-m-d');
                      try {
                        $sTotal = DB::table('attendances')->where('date', $todayDateStr)->count();
                        $sPresent = DB::table('attendances')->where('date', $todayDateStr)->whereIn(DB::raw('LOWER(status)'), ['present', 'p', 'active'])->count();
                        $sAbsent = DB::table('attendances')->where('date', $todayDateStr)->whereIn(DB::raw('LOWER(status)'), ['absent', 'a'])->count();
                        $sLeave = DB::table('attendances')->where('date', $todayDateStr)->whereIn(DB::raw('LOWER(status)'), ['leave', 'l'])->count();
                        $sRate = $sTotal > 0 ? round(($sPresent / $sTotal) * 100) : null;
                      } catch (\Exception $e) {
                        $sTotal = $sPresent = $sAbsent = $sLeave = 0;
                        $sRate = null;
                      }
                    @endphp
                    @if($sRate !== null)
                      {{ $sRate }}%
                    @else
                      <span style="font-size: 0.75rem;" class="text-muted fw-bold">No Logs</span>
                    @endif
                  </h3>
                  <small class="text-muted fw-bold" style="font-size: 0.65rem; text-transform: uppercase;">Today</small>
                </div>
              </div>
            </div>
            
            <div class="col-sm-7">
              <div class="d-flex flex-column gap-2 text-start">
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small fw-semibold"><i class="bi bi-circle-fill text-success me-2" style="font-size: 0.5rem;"></i> Present Today:</span>
                  <span class="fw-bold" style="color: #1e293b;">{{ $sPresent }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small fw-semibold"><i class="bi bi-circle-fill text-danger me-2" style="font-size: 0.5rem;"></i> Absent Today:</span>
                  <span class="fw-bold" style="color: #1e293b;">{{ $sAbsent }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small fw-semibold"><i class="bi bi-circle-fill text-info me-2" style="font-size: 0.5rem;"></i> On Leave Today:</span>
                  <span class="fw-bold" style="color: #1e293b;">{{ $sLeave }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i> Click to view student monthly registry</small>
            <span class="text-success fw-bold small" style="font-size: 0.8rem;">View Report <i class="bi bi-chevron-right ms-1"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== ANALYTICS ROW ========== -->
  <div class="row">
    <!-- Monthly trends chart -->
    <div class="col-lg-8">
      <div class="card card-glass">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-graph-up-arrow me-2"></i>Monthly Fees Collection Trends</h5>
          <div id="feesCollectionChart"></div>
        </div>
      </div>
    </div>

    <!-- Payment status breakdown -->
    <div class="col-lg-4">
      <div class="card card-glass">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-pie-chart me-2"></i>Payment Status</h5>
          <div id="paymentStatusChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Exam performance trends -->
    <div class="col-lg-8">
      <div class="card card-glass">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-bar-chart me-2"></i>Subject-wise Performance Analysis</h5>
          <div id="subjectPerformanceChart"></div>
        </div>
      </div>
    </div>

    <!-- Grade distribution -->
    <div class="col-lg-4">
      <div class="card card-glass">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-award me-2"></i>Grade Distribution</h5>
          <div id="gradeDistributionChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Class-wise fee collection -->
    <div class="col-lg-6">
      <div class="card card-glass">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-collection me-2"></i>Class-wise Fee Collection</h5>
          <div id="classWiseFeesChart"></div>
        </div>
      </div>
    </div>

    <!-- Monthly exam schedule -->
    <div class="col-lg-6">
      <div class="card card-glass">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-calendar3 me-2"></i>Monthly Exam Schedule</h5>
          <div id="monthlyExamChart"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== RECENT TRANSACTIONS ROW ========== -->
  <div class="row">
    <!-- Recent Fee Payments -->
    <div class="col-lg-6">
      <div class="card card-glass recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cash-stack me-2"></i>Recent Fee Payments</h5>
          
          @if(isset($dashboardData['recentFeePayments']) && $dashboardData['recentFeePayments']->count() > 0)
            <table class="table table-premium table-borderless datatable">
              <thead>
                <tr>
                  <th scope="col">Student</th>
                  <th scope="col">Class</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Status</th>
                  <th scope="col">Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dashboardData['recentFeePayments'] as $payment)
                <tr>
                  <td>{{ $payment->studentName ?? $payment->student_id }}</td>
                  <td><span class="badge badge-soft-primary">{{ $payment->class_name }}</span></td>
                  <td>Rs. {{ number_format($payment->total_fee) }}</td>
                  <td>
                    @if($payment->status == 'paid')
                      <span class="badge badge-soft-success">Paid</span>
                    @else
                      <span class="badge badge-soft-warning">Pending</span>
                    @endif
                  </td>
                  <td>{{ date('M d, Y', strtotime($payment->updated_at)) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="text-center py-4">
              <i class="bi bi-receipt display-4 text-muted"></i>
              <p class="text-muted mt-2">No recent fee payments found</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Recent Exam Results -->
    <div class="col-lg-6">
      <div class="card card-glass recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-trophy me-2"></i>Recent Exam Results</h5>
          
          @if(isset($dashboardData['recentExamResults']) && $dashboardData['recentExamResults']->count() > 0)
            <table class="table table-premium table-borderless datatable">
              <thead>
                <tr>
                  <th scope="col">Student</th>
                  <th scope="col">Exam</th>
                  <th scope="col">Score</th>
                  <th scope="col">Grade</th>
                  <th scope="col">Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dashboardData['recentExamResults'] as $result)
                <tr>
                  <td>{{ $result->studentName }}</td>
                  <td>{{ Str::limit($result->exam_name, 20) }}</td>
                  <td>{{ number_format($result->percentage, 1) }}%</td>
                  <td>
                    @php
                      $gradeClass = 'badge-soft-secondary';
                      if($result->grade == 'A+') $gradeClass = 'badge-soft-success';
                      elseif($result->grade == 'A') $gradeClass = 'badge-soft-primary';
                      elseif(in_array($result->grade, ['B+', 'B'])) $gradeClass = 'badge-soft-info';
                      elseif(in_array($result->grade, ['C+', 'C'])) $gradeClass = 'badge-soft-warning';
                      elseif($result->grade == 'D') $gradeClass = 'badge-soft-warning';
                      elseif($result->grade == 'F') $gradeClass = 'badge-soft-danger';
                    @endphp
                    <span class="badge {{ $gradeClass }}">{{ $result->grade ?? 'N/A' }}</span>
                  </td>
                  <td>{{ $result->end_time ? date('M d', strtotime($result->end_time)) : 'N/A' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="text-center py-4">
              <i class="bi bi-journal-x display-4 text-muted"></i>
              <p class="text-muted mt-2">No recent exam results found</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- ========== FLOATING ACTION BUTTON ========== -->
  <div class="fixed-bottom right-0 p-4" style="z-index: 1040; left: auto;">
    <button class="btn-floating" onclick="window.location.href='{{ route('exams.create') }}'" title="Create New Exam">
      <i class="bi bi-plus" style="font-size: 32px; color: white;"></i>
    </button>
  </div>
</section>

@endsection

@section('scripts')
<style>
/* Card title improvements */
.clickable-card .card-title i {
    font-size: 14px;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.clickable-card:hover .card-title i {
    opacity: 1;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // ========== FEES COLLECTION TRENDS CHART ==========
    const feesData = @json($dashboardData['monthlyFeeTrends'] ?? []);
    const feesCategories = feesData.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                           'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1] + ' ' + item.year;
    });
    const feesValues = feesData.map(item => parseInt(item.total));

    const feesChart = new ApexCharts(document.querySelector("#feesCollectionChart"), {
        series: [{
            name: 'Fees Collected',
            data: feesValues
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false }
        },
        colors: ['#004ac6'],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.2,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2.5 },
        xaxis: {
            categories: feesCategories,
            title: { text: 'Month' }
        },
        yaxis: {
            title: { text: 'Amount (Rs.)' },
            labels: {
                formatter: function(val) {
                    return 'Rs. ' + val.toLocaleString();
                }
            }
        }
    });
    feesChart.render();

    // ========== PAYMENT STATUS PIE CHART ==========
    const paymentData = @json($dashboardData['paymentStatusBreakdown'] ?? []);
    const paymentLabels = paymentData.map(item => item.status === 'paid' ? 'Paid' : 'Pending');
    const paymentValues = paymentData.map(item => parseInt(item.count));

    const paymentChart = new ApexCharts(document.querySelector("#paymentStatusChart"), {
        series: paymentValues,
        chart: {
            width: 380,
            type: 'pie'
        },
        labels: paymentLabels,
        colors: ['#004ac6', '#fea619'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    });
    paymentChart.render();

    // ========== SUBJECT PERFORMANCE BAR CHART ==========
    const subjectData = @json($dashboardData['subjectWisePerformance'] ?? []);
    const subjectNames = subjectData.map(item => item.subject_name);
    const subjectPerformances = subjectData.map(item => parseFloat(item.avg_percentage).toFixed(1));

    const subjectChart = new ApexCharts(document.querySelector("#subjectPerformanceChart"), {
        series: [{
            name: 'Average Performance',
            data: subjectPerformances
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        colors: ['#004ac6'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: subjectNames,
            title: { text: 'Subjects' }
        },
        yaxis: {
            title: { text: 'Average Percentage' },
            max: 100
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + "%"
                }
            }
        }
    });
    subjectChart.render();

    // ========== GRADE DISTRIBUTION DONUT CHART ==========
    const gradeData = @json($dashboardData['gradeDistribution'] ?? []);
    const gradeLabels = gradeData.map(item => item.grade || 'N/A');
    const gradeValues = gradeData.map(item => parseInt(item.count));

    const gradeChart = new ApexCharts(document.querySelector("#gradeDistributionChart"), {
        series: gradeValues,
        chart: {
            width: 380,
            type: 'donut'
        },
        labels: gradeLabels,
        colors: ['#006242', '#004ac6', '#2563eb', '#fea619', '#855300', '#ba1a1a'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    });
    gradeChart.render();

    // ========== CLASS-WISE FEES COLLECTION ==========
    const classData = @json($dashboardData['classWiseFees'] ?? []);
    const classNames = classData.map(item => item.class_name);
    const classAmounts = classData.map(item => parseInt(item.total_collected));

    const classChart = new ApexCharts(document.querySelector("#classWiseFeesChart"), {
        series: [{
            name: 'Fees Collected',
            data: classAmounts
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        colors: ['#2563eb'],
        plotOptions: {
            bar: {
                horizontal: true,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: classNames,
            title: { text: 'Amount (Rs.)' }
        },
        yaxis: {
            title: { text: 'Classes' }
        }
    });
    classChart.render();

    // ========== MONTHLY EXAM TRENDS ==========
    const examData = @json($dashboardData['monthlyExamTrends'] ?? []);
    const examCategories = examData.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                           'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1] + ' ' + item.year;
    });
    const examValues = examData.map(item => parseInt(item.total));

    const examChart = new ApexCharts(document.querySelector("#monthlyExamChart"), {
        series: [{
            name: 'Exams Conducted',
            data: examValues
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false }
        },
        colors: ['#004ac6'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: examCategories,
            title: { text: 'Month' }
        },
        yaxis: {
            title: { text: 'Number of Exams' }
        },
        markers: {
            size: 5,
            colors: ['#004ac6'],
            strokeColors: '#fff',
            strokeWidth: 2
        }
    });
    examChart.render();
});
</script>
@endsection
