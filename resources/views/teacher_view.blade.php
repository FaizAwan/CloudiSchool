@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1><i class="bi bi-person-vcard me-2"></i>Teacher Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('teachers') }}">Teachers</a></li>
      <li class="breadcrumb-item active">{{ $teacher->teacherName }}</li>
    </ol>
  </nav>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $teacher->teacherName }}</h5>
    <a href="{{ route('teachers') }}" class="btn btn-sm btn-secondary">Back</a>
  </div>
  <div class="card-body">
    <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Info</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="timetable-tab" data-bs-toggle="tab" data-bs-target="#timetable" type="button" role="tab">Timetable</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">Attendance</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">Exams</button>
      </li>
    </ul>
    <div class="tab-content pt-3" id="teacherTabsContent">
      <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
        <div class="row g-3">
          <div class="col-md-6"><strong>Name:</strong> {{ $teacher->teacherName }}</div>
          <div class="col-md-6"><strong>Email:</strong> {{ $teacher->email }}</div>
          <div class="col-md-6"><strong>Phone:</strong> {{ $teacher->phone }}</div>
          <div class="col-md-6"><strong>School:</strong> {{ $teacher->schoolName }}</div>
          <div class="col-md-6"><strong>Class:</strong> {{ $teacher->classNameFromJoin ?? $teacher->className }}</div>
          <div class="col-md-12"><strong>Address:</strong> {{ $teacher->address ?? 'N/A' }}</div>
        </div>
      </div>
      <div class="tab-pane fade" id="timetable" role="tabpanel" aria-labelledby="timetable-tab">
        <div class="text-muted">No timetable data available.</div>
      </div>
      <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
        <div class="text-muted">No attendance data available.</div>
      </div>
      <div class="tab-pane fade" id="exams" role="tabpanel" aria-labelledby="exams-tab">
        <div class="text-muted">No exams data available.</div>
      </div>
    </div>
  </div>
</div>
@endsection
