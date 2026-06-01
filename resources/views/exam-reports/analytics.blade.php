@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Exam Analytics</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('exam-reports.index') }}">Exam Reports</a></li>
      <li class="breadcrumb-item active">Analytics</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-md-3">
      <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $stats['results_count'] ?? 0 }}</h3>
          <p>Total Results</p>
        </div>
        <div class="icon"><i class="bi bi-graph-up"></i></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="small-box bg-success">
        <div class="inner">
          <h3>{{ $stats['passed'] ?? 0 }}</h3>
          <p>Passed</p>
        </div>
        <div class="icon"><i class="bi bi-check-circle"></i></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>{{ $stats['failed'] ?? 0 }}</h3>
          <p>Failed</p>
        </div>
        <div class="icon"><i class="bi bi-x-circle"></i></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="small-box bg-secondary">
        <div class="inner">
          <h3>{{ $stats['absent'] ?? 0 }}</h3>
          <p>Absent</p>
        </div>
        <div class="icon"><i class="bi bi-person-dash"></i></div>
      </div>
    </div>
  </div>
</section>
@endsection