@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header" style="background: #1488CC; color:#fff; font-weight:bold;">
            <i class="bi bi-clipboard-check"></i> Principal Remarks Management
        </div>
        <div class="card-body">
            @if (session('success'))
                <div id="alert-success" class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div id="alert-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Form -->
            <form method="GET" action="{{ url('/principal-remarks') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Class:</label>
                        <select class="form-control" name="class_id">
                            <option value="">-- All Classes --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                    {{ $class->className }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Section:</label>
                        <select class="form-control" name="section">
                            <option value="">-- All Sections --</option>
                            @foreach($sections as $section)
                                <option value="{{ $section }}" {{ $selectedSection == $section ? 'selected' : '' }}>
                                    {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Term:</label>
                        <input type="text" class="form-control" name="term" value="{{ $term }}" placeholder="e.g., Mid Term">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ url('/principal-remarks') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Results Table -->
            @if(count($items) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Student ID</th>
                                <th width="20%">Student Name</th>
                                <th width="10%">Class</th>
                                <th width="8%">Section</th>
                                <th width="12%">Term</th>
                                <th width="30%">Principal Remarks</th>
                                <th width="10%">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td><strong>{{ $item['student_id'] }}</strong></td>
                                    <td>{{ $item['student_name'] }}</td>
                                    <td>{{ $item['class'] }}</td>
                                    <td>{{ $item['section'] ?: 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item['term'] }}</span>
                                    </td>
                                    <td>
                                        <div style="max-height: 100px; overflow-y: auto;">
                                            {{ $item['remarks'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($item['updated_at'])->format('M d, Y H:i') }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>{{ count($items) }}</strong> student(s) found with principal remarks.
                        @if($selectedClassId || $selectedSection || $term)
                            <br><small class="text-muted">
                                Filtered by: 
                                @if($selectedClassId) Class, @endif
                                @if($selectedSection) Section, @endif
                                @if($term) Term @endif
                            </small>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>No records found!</strong>
                    @if($selectedClassId || $selectedSection || $term)
                        Try adjusting your filter criteria.
                    @else
                        No students have principal remarks entered yet.
                    @endif
                </div>
            @endif

            <!-- Help Section -->
            <div class="mt-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-question-circle"></i> How to Add Principal Remarks
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li>Go to <a href="{{ url('/manual-exams') }}" class="text-decoration-none">Manual Exams</a></li>
                            <li>Select a class, section, and term</li>
                            <li>Switch to the "Student-wise Report" tab</li>
                            <li>Search for a student by GR No or select from the dropdown</li>
                            <li>Scroll down to the "Principal's Remarks" section</li>
                            <li>Enter remarks and click "Save Notes"</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-color: #dee2e6;
    }
    
    .table td {
        vertical-align: middle;
        border-color: #dee2e6;
    }
    
    .badge {
        font-size: 0.85em;
    }
    
    .alert {
        border-radius: 8px;
    }
    
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
</style>
@endsection