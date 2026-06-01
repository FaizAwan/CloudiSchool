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
  <div class="row">

    <!-- ========== FEES STATISTICS CARDS ========== -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card revenue-card clickable-card" onclick="window.location.href='{{ route('reportsCollectiveFees') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Total Fees Collected <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-currency-dollar text-success"></i>
            </div>
            <div class="ps-3">
              <h6>Rs. {{ number_format($dashboardData['totalFeesCollected'] ?? 0) }}</h6>
              <span class="text-success small pt-1 fw-bold">{{ $dashboardData['feesCollectionRate'] ?? 0 }}%</span>
              <span class="text-muted small pt-2 ps-1">collection rate</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card clickable-card" onclick="window.location.href='{{ route('challan') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Pending Fees <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-clock-history text-warning"></i>
            </div>
            <div class="ps-3">
              <h6>Rs. {{ number_format($dashboardData['pendingFees'] ?? 0) }}</h6>
              <span class="text-warning small pt-1 fw-bold">{{ $dashboardData['studentsWithPendingFees'] ?? 0 }}</span>
              <span class="text-muted small pt-2 ps-1">students pending</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== EXAM STATISTICS CARDS ========== -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card clickable-card" onclick="window.location.href='{{ route('exams.index') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Total Exams <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-journal-text text-primary"></i>
            </div>
            <div class="ps-3">
              <h6>{{ $dashboardData['totalExams'] ?? 0 }}</h6>
              <span class="text-primary small pt-1 fw-bold">{{ $dashboardData['activeExams'] ?? 0 }}</span>
              <span class="text-muted small pt-2 ps-1">active</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card clickable-card" onclick="window.location.href='{{ route('exam-reports.index') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Average Performance <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-graph-up text-info"></i>
            </div>
            <div class="ps-3">
              <h6>{{ $dashboardData['avgPerformance'] ?? 0 }}%</h6>
              <span class="text-info small pt-1 fw-bold">{{ $dashboardData['totalAttempts'] ?? 0 }}</span>
              <span class="text-muted small pt-2 ps-1">total attempts</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- ========== CLASSES CARD ========== -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card clickable-card" onclick="window.location.href='{{ route('classes') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Classes <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-mortarboard text-primary"></i>
            </div>
            <div class="ps-3">
              <h6>{{ $dashboardData['totalClasses'] ?? 0 }}</h6>
              <span class="text-primary small pt-1 fw-bold">{{ $dashboardData['studentsInClasses'] ?? 0 }}</span>
              <span class="text-muted small pt-2 ps-1">total students</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== TEACHERS CARD ========== -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card revenue-card clickable-card" onclick="window.location.href='{{ route('teachers') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Teachers <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-person-workspace text-success"></i>
            </div>
            <div class="ps-3">
              <h6>{{ $dashboardData['totalTeachers'] ?? 0 }}</h6>
              <span class="text-success small pt-1 fw-bold">{{ $dashboardData['activeTeachers'] ?? 0 }}</span>
              <span class="text-muted small pt-2 ps-1">active</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== STUDENTS CARD ========== -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card clickable-card" onclick="window.location.href='{{ route('students') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Students <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-people text-info"></i>
            </div>
            <div class="ps-3">
              <h6>{{ $dashboardData['totalStudents'] ?? 0 }}</h6>
              <span class="text-info small pt-1 fw-bold">{{ $dashboardData['activeStudents'] ?? 0 }}</span>
              <span class="text-muted small pt-2 ps-1">active</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== SCHOOLS CARD ========== -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card clickable-card" onclick="window.location.href='{{ route('schools') }}'" style="cursor: pointer;">
        <div class="card-body">
          <h5 class="card-title">Schools <i class="bi bi-arrow-right-short"></i></h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-building text-warning"></i>
            </div>
            <div class="ps-3">
              <h6>{{ $dashboardData['totalSchools'] ?? 1 }}</h6>
              <span class="text-warning small pt-1 fw-bold">{{ $dashboardData['schoolsActive'] ?? 1 }}</span>
              <span class="text-muted small pt-2 ps-1">active</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- ========== FEES COLLECTION TRENDS CHART ========== -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-graph-up-arrow me-2"></i>Monthly Fees Collection Trends</h5>
          <div id="feesCollectionChart"></div>
        </div>
      </div>
    </div>

    <!-- ========== PAYMENT STATUS BREAKDOWN ========== -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-pie-chart me-2"></i>Payment Status</h5>
          <div id="paymentStatusChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- ========== EXAM PERFORMANCE TRENDS ========== -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-bar-chart me-2"></i>Subject-wise Performance Analysis</h5>
          <div id="subjectPerformanceChart"></div>
        </div>
      </div>
    </div>

    <!-- ========== GRADE DISTRIBUTION ========== -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-award me-2"></i>Grade Distribution</h5>
          <div id="gradeDistributionChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- ========== CLASS-WISE FEE COLLECTION ========== -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-collection me-2"></i>Class-wise Fee Collection</h5>
          <div id="classWiseFeesChart"></div>
        </div>
      </div>
    </div>

    <!-- ========== MONTHLY EXAM TRENDS ========== -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-calendar3 me-2"></i>Monthly Exam Schedule</h5>
          <div id="monthlyExamChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- ========== RECENT FEE PAYMENTS TABLE ========== -->
    <div class="col-lg-6">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cash-stack me-2"></i>Recent Fee Payments</h5>
          
          @if(isset($dashboardData['recentFeePayments']) && $dashboardData['recentFeePayments']->count() > 0)
            <table class="table table-borderless datatable">
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
                  <td><span class="badge bg-light text-dark">{{ $payment->class_name }}</span></td>
                  <td>Rs. {{ number_format($payment->total_fee) }}</td>
                  <td>
                    @if($payment->status == 'paid')
                      <span class="badge bg-success">Paid</span>
                    @else
                      <span class="badge bg-warning">Pending</span>
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

    <!-- ========== RECENT EXAM RESULTS TABLE ========== -->
    <div class="col-lg-6">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-trophy me-2"></i>Recent Exam Results</h5>
          
          @if(isset($dashboardData['recentExamResults']) && $dashboardData['recentExamResults']->count() > 0)
            <table class="table table-borderless datatable">
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
                      $gradeClass = 'bg-secondary';
                      if($result->grade == 'A+') $gradeClass = 'bg-success';
                      elseif($result->grade == 'A') $gradeClass = 'bg-primary';
                      elseif(in_array($result->grade, ['B+', 'B'])) $gradeClass = 'bg-info';
                      elseif(in_array($result->grade, ['C+', 'C'])) $gradeClass = 'bg-warning';
                      elseif($result->grade == 'D') $gradeClass = 'bg-warning';
                      elseif($result->grade == 'F') $gradeClass = 'bg-danger';
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

</section>

@endsection

@section('scripts')
<style>
/* Clickable card styles */
.clickable-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.clickable-card:hover .card-title {
    color: #007bff;
}

.clickable-card:hover .card-icon {
    animation: bounce 0.6s;
}

@keyframes bounce {
    0%, 20%, 60%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    80% {
        transform: translateY(-5px);
    }
}

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
        colors: ['#28a745'],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.4,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
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
        colors: ['#28a745', '#ffc107'],
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
        colors: ['#007bff'],
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
        colors: ['#28a745', '#007bff', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545'],
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
        colors: ['#20c997'],
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
        colors: ['#6f42c1'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: examCategories,
            title: { text: 'Month' }
        },
        yaxis: {
            title: { text: 'Number of Exams' }
        },
        markers: {
            size: 6,
            colors: ['#6f42c1'],
            strokeColors: '#fff',
            strokeWidth: 2
        }
    });
    examChart.render();
});
</script>
@endsection
