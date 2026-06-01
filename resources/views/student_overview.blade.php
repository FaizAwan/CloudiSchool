@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1><i class="bi bi-person-badge me-2"></i> Student Overview</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('students') }}">Students</a></li>
      <li class="breadcrumb-item active">View</li>
    </ol>
  </nav>
</div>

<div class="d-flex justify-content-end mb-2">
  <a href="{{ route('home') }}" class="btn btn-sm btn-secondary"><i class="bi bi-house"></i> Back to Home</a>
</div>

<style>
.overview-card .card-body { padding: 10px !important; }
.overview-grid .card { margin-bottom: 10px; }
.small-list { max-height: 220px; overflow: auto; }
</style>

<ul class="nav nav-tabs" id="studentTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="tab-profile" data-bs-toggle="tab" data-bs-target="#pane-profile" type="button" role="tab">Profile</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-results" data-bs-toggle="tab" data-bs-target="#pane-results" type="button" role="tab">Results</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-behavior" data-bs-toggle="tab" data-bs-target="#pane-behavior" type="button" role="tab">Behavior</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-subjects" data-bs-toggle="tab" data-bs-target="#pane-subjects" type="button" role="tab">Subjects</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-fees" data-bs-toggle="tab" data-bs-target="#pane-fees" type="button" role="tab">Fees</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-online" data-bs-toggle="tab" data-bs-target="#pane-online" type="button" role="tab">Online Exams</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-siblings" data-bs-toggle="tab" data-bs-target="#pane-siblings" type="button" role="tab">Siblings</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-attendance" data-bs-toggle="tab" data-bs-target="#pane-attendance" type="button" role="tab">Attendance</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-entries" data-bs-toggle="tab" data-bs-target="#pane-entries" type="button" role="tab">Recent Entries</button>
  </li>
</ul>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="pane-profile" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Profile</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-12"><strong>Name:</strong> {{ $profile['name'] ?? '' }}</div>
          <div class="col-6"><strong>GR No:</strong> {{ $profile['grno'] ?? '' }}</div>
          <div class="col-6"><strong>Gender:</strong> {{ $profile['gender'] ?? '' }}</div>
          <div class="col-6"><strong>Class:</strong> {{ $profile['class'] ?? '' }}</div>
          <div class="col-6"><strong>Section:</strong> {{ $profile['section'] ?? '' }}</div>
          <div class="col-6"><strong>Session:</strong> {{ $profile['session'] ?? '' }}</div>
          <div class="col-6"><strong>Status:</strong> {{ ucfirst($profile['status'] ?? '') }}</div>
          <div class="col-6"><strong>DOB:</strong> {{ $profile['dob'] ?? '' }}</div>
          <div class="col-6"><strong>Age:</strong> {{ isset($profile['age']) ? ($profile['age'].' yrs') : '' }}</div>
          <div class="col-12"><strong>School:</strong> {{ $profile['school'] ?? '' }}</div>
          <div class="col-12"><strong>Parent:</strong> {{ $profile['parent'] ?? '' }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-results" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Results (Manual Exams)</div>
      <div class="card-body">
        Total Entries: {{ $results['count'] ?? 0 }}
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-behavior" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Behavior Assessments</div>
      <div class="card-body">
        Total Assessments: {{ $behavior['count'] ?? 0 }}
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-subjects" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Subjects</div>
      <div class="card-body small-list">
        @if(!empty($subjects))
          <ul class="mb-0">
            @foreach($subjects as $s)
              <li>{{ $s }}</li>
            @endforeach
          </ul>
        @else
          <div class="text-muted">No subjects found</div>
        @endif
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-fees" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Fees / Challans</div>
      <div class="card-body">
        <div>Total Records: {{ $fees['count'] ?? 0 }}</div>
        @if(isset($fees['paid']) || isset($fees['pending']))
          <div>Paid: {{ $fees['paid'] ?? 0 }} | Pending: {{ $fees['pending'] ?? 0 }}</div>
        @endif
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-online" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Online Exams</div>
      <div class="card-body">
        Total Records: {{ $online['count'] ?? 0 }}
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-siblings" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Siblings</div>
      <div class="card-body small-list">
        @if(!empty($siblings))
          <ul class="mb-0">
            @foreach($siblings as $sib)
              <li>{{ $sib['grno'] ?? '' }} - {{ $sib['name'] ?? '' }} @if(!empty($sib['class'])) ({{ $sib['class'] }}) @endif</li>
            @endforeach
          </ul>
        @else
          <div class="text-muted">No siblings</div>
        @endif
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-attendance" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Attendance</div>
      <div class="card-body">
        @php
          $tot = $attendance['totals'] ?? [];
          $rec = $attendance['recent'] ?? [];
          $fmt = function($arr){
            $keys=['present','absent','leave','late'];
            return implode(' | ', array_map(function($k) use ($arr){ return ucfirst($k).': '.($arr[$k] ?? 0); }, $keys));
          };
        @endphp
        <div>Totals: {{ $fmt($tot) }}</div>
        <div class="text-muted">Last 30d: {{ $fmt($rec) }}</div>
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="pane-entries" role="tabpanel">
    <div class="card overview-card">
      <div class="card-header">Recent Results Entries</div>
      <div class="card-body small-list">
        @php $entries = $results['entries'] ?? []; @endphp
        @if(!empty($entries))
          <table class="table table-sm table-bordered">
            <thead class="table-light"><tr><th>Term</th><th>Subject</th><th>Updated</th></tr></thead>
            <tbody>
            @foreach($entries as $e)
              <tr>
                <td>{{ $e->term ?? '' }}</td>
                <td>{{ $e->subject ?? '' }}</td>
                <td>{{ $e->updated_at ?? '' }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        @else
          <div class="text-muted">No recent results</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
