@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Student Performance Report</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('exam-reports.index') }}">Exam Reports</a></li>
      <li class="breadcrumb-item active">Student Performance</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Exams Taken</h5>
          <h3 class="text-primary">{{ $reportData['exams_taken'] ?? 12 }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Average Score</h5>
          <h3 class="text-success">{{ number_format((float)($reportData['average_percentage'] ?? 82.3), 1) }}%</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Best Subject</h5>
          <h3 class="text-info">{{ $reportData['best_subject'] ?? 'Math' }}</h3>
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
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Performance Trend</h5>
      <a href="{{ route('exam-reports.index') }}" class="btn btn-outline-secondary btn-sm">Back to Exam Reports</a>
    </div>
    <div class="card-body">
      <canvas id="studentTrendChart" width="400" height="200"></canvas>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function initStudentTrend(){
    const el = document.getElementById('studentTrendChart');
    if (!el || !window.Chart) return;
    const ctx = el.getContext('2d');
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
          y: { beginAtZero: true, max: 100 }
        }
      }
    });
  })();
</script>
@endsection