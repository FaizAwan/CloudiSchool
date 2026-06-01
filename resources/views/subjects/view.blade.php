@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Subject Details</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
      <li class="breadcrumb-item active">View</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ $subject->subject_name }}</h5>
          <div>
            <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="text-muted d-block">Subject Code</label>
              <div class="fw-semibold">{{ $subject->subject_code ?: '—' }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Class</label>
              <div class="fw-semibold">{{ $subject->class->className ?? $subject->class_id }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Term</label>
              <div>
                @if($subject->term)
                  <span class="badge bg-warning">{{ $subject->term }}</span>
                @else
                  <span class="badge bg-secondary">General (All Terms)</span>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Status</label>
              <div>
                <span class="badge {{ $subject->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($subject->status) }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Total Marks</label>
              <div class="fw-semibold">{{ rtrim(rtrim(number_format((float)$subject->total_marks, 3, '.', ''), '0'), '.') }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Passing Marks</label>
              <div class="fw-semibold">{{ rtrim(rtrim(number_format((float)$subject->passing_marks, 3, '.', ''), '0'), '.') }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Sort Order</label>
              <div class="fw-semibold">{{ $subject->sort_order ?? 0 }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Linked Exams</label>
              <div class="fw-semibold">{{ $examCount ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
