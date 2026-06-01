@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1><i class="bi bi-people me-2"></i>Parent Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('parents') }}">Parents</a></li>
      <li class="breadcrumb-item active">{{ $parent->parentName }}</li>
    </ol>
  </nav>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $parent->parentName }}</h5>
    <a href="{{ route('parents') }}" class="btn btn-sm btn-secondary">Back</a>
  </div>
  <div class="card-body">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#p-info" type="button" role="tab">Info</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#p-children" type="button" role="tab">Children</button></li>
    </ul>
    <div class="tab-content pt-3">
      <div class="tab-pane fade show active" id="p-info" role="tabpanel">
        <div class="row g-2">
          <div class="col-md-6"><strong>Name:</strong> {{ $parent->parentName }}</div>
          <div class="col-md-6"><strong>Employee:</strong> {{ $parent->is_commandercityschool_employee }}</div>
          <div class="col-md-6"><strong>Phone:</strong> {{ $parent->phone }}</div>
          <div class="col-md-6"><strong>School:</strong> {{ $parent->schoolName ?? 'N/A' }}</div>
          <div class="col-md-12"><strong>Address:</strong> {{ $parent->address }}</div>
        </div>
      </div>
      <div class="tab-pane fade" id="p-children" role="tabpanel">
        @if(count($children))
        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>GR No</th>
                <th>Name</th>
                <th>Class</th>
                <th>Session</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($children as $i=>$ch)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $ch->grno }}</td>
                <td>{{ $ch->studentName }}</td>
                <td>{{ $ch->className }}</td>
                <td>{{ $ch->session }}</td>
                <td><span class="badge bg-{{ $ch->status=='active'?'success':'warning' }}">{{ ucfirst($ch->status) }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
          <div class="text-muted">No children found.</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
