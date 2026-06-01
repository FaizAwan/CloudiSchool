@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Exam Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Exams</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Stats Row -->
        @if(auth()->user()->role != 'student')
        <div class="col-12 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-primary-light text-primary p-3 d-inline-block mb-3">
                                <i class="bi bi-send fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $exams->where('status', 'published')->count() }}</h4>
                            <p class="text-muted mb-0 small text-uppercase fw-bold">Published</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-warning-light text-warning p-3 d-inline-block mb-3">
                                <i class="bi bi-pencil fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $exams->where('status', 'draft')->count() }}</h4>
                            <p class="text-muted mb-0 small text-uppercase fw-bold">Drafts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-success-light text-success p-3 d-inline-block mb-3">
                                <i class="bi bi-check2-circle fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $exams->where('status', 'completed')->count() }}</h4>
                            <p class="text-muted mb-0 small text-uppercase fw-bold">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-info-light text-info p-3 d-inline-block mb-3">
                                <i class="bi bi-collection fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $exams->count() }}</h4>
                            <p class="text-muted mb-0 small text-uppercase fw-bold">Total Exams</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <h5 class="card-title mb-0">All Exams</h5>
                    @if(auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                    <a href="{{ route('exams.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create Exam
                    </a>
                    @endif
                </div>

                <div class="card-body pt-4">
                    <!-- Filters -->
                    <form method="GET" class="mb-4 bg-light p-4 rounded-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select border-0 shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Type</label>
                                <select name="exam_type" class="form-select border-0 shadow-sm">
                                    <option value="">All Types</option>
                                    @foreach($examTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('exam_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->exam_type_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Subject</label>
                                <select name="subject" class="form-select border-0 shadow-sm">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Exam Details</th>
                                    <th>Subject & Type</th>
                                    <th>Date & Time</th>
                                    <th>Marks</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $exam)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $exam->exam_name }}</div>
                                        <small class="text-muted"><i class="bi bi-question-circle me-1"></i>{{ $exam->total_questions }} Questions</small>
                                        <div class="small fw-bold text-dark mt-1">{{ $exam->class_name }}</div>
                                    </td>
                                    <td>
                                        <div class="badge bg-info-light text-info mb-1">{{ $exam->examType->exam_type_name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $exam->subject->subject_name ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'Not Set' }}</div>
                                        <div class="small text-muted">{{ $exam->exam_time ? $exam->exam_time->format('h:i A') : '' }}</div>
                                        <div class="small text-muted"><i class="bi bi-hourglass-split me-1"></i>{{ $exam->formatted_duration }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark p-2 border">{{ $exam->total_marks }} Marks</span>
                                    </td>
                                    <td>
                                        @if($exam->status == 'published')
                                        <span class="badge bg-success rounded-pill px-3">Published</span>
                                        @elseif($exam->status == 'draft')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Draft</span>
                                        @elseif($exam->status == 'completed')
                                        <span class="badge bg-primary rounded-pill px-3">Completed</span>
                                        @else
                                        <span class="badge bg-secondary rounded-pill px-3">{{ ucfirst($exam->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin' ||
                                            (auth()->user()->role == 'teacher' && $exam->teacher_id == auth()->id()))
                                            <a href="{{ route('exams.edit', $exam->id) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endif

                                            @if($exam->status != 'draft' && $exam->attempts()->count() > 0)
                                            <a href="{{ route('exams.results', $exam->id) }}" class="btn btn-sm btn-outline-info rounded-pill" title="Results">
                                                <i class="bi bi-trophy"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bi bi-journal-x display-1 text-muted opacity-25"></i>
                                        </div>
                                        <h5 class="text-muted">No exams found matching your criteria</h5>
                                        @if(auth()->user()->role != 'student')
                                        <a href="{{ route('exams.create') }}" class="btn btn-primary mt-3 rounded-pill px-4">Create First Exam</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($exams->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $exams->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection