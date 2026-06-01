@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ $exam->exam_name }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item active">{{ $exam->exam_name }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Exam Info Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Exam Details</h5>
                    <div>
                        @if($canEdit && $exam->status == 'draft')
                            <a href="{{ route('exams.edit', $exam->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('exams.toggle-status', $exam->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" 
                                        {{ $exam->questions()->count() == 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle"></i> Publish
                                </button>
                            </form>
                        @elseif($canEdit && $exam->status == 'published')
                            <form method="POST" action="{{ route('exams.toggle-status', $exam->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pause-circle"></i> Unpublish
                                </button>
                            </form>
                        @endif
                        
                        @if($exam->status != 'draft' && isset($statistics) && $statistics['completed_attempts'] > 0)
                            <a href="{{ route('exams.results', $exam->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-trophy"></i> View Results
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Exam Type:</strong></td>
                                    <td>{{ $exam->examType->exam_type_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $exam->subject->subject_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Class:</strong></td>
                                    <td>{{ $exam->class_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teacher:</strong></td>
                                    <td>{{ $exam->teacher->teacherName ?? 'Not Assigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Date & Time:</strong></td>
                                    <td>
                                        {{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'Not Set' }}<br>
                                        <small class="text-muted">{{ $exam->exam_time ? $exam->exam_time->format('h:i A') : 'Not Set' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $exam->formatted_duration }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Marks:</strong></td>
                                    <td>{{ $exam->total_marks }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Passing Marks:</strong></td>
                                    <td>{{ $exam->passing_marks }} ({{ $exam->passing_percentage }}%)</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($exam->instructions)
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Instructions</h6>
                            <p class="mb-0">{{ $exam->instructions }}</p>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <h6>Status & Settings</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge {{ $exam->status == 'published' ? 'bg-success' : ($exam->status == 'draft' ? 'bg-warning' : 'bg-primary') }} fs-6">
                                    {{ ucfirst($exam->status) }}
                                </span>
                                @if($exam->auto_submit)
                                    <span class="badge bg-info fs-6">Auto Submit</span>
                                @endif
                                @if($exam->show_results)
                                    <span class="badge bg-success fs-6">Show Results</span>
                                @endif
                                @if($exam->randomize_questions)
                                    <span class="badge bg-secondary fs-6">Randomized</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Statistics Card -->
            @if(isset($statistics))
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Exam Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">{{ $statistics['total_attempts'] }}</h4>
                                <p class="mb-0">Total Attempts</p>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ $statistics['completed_attempts'] }}</h4>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                        <hr>
                        @if($statistics['completed_attempts'] > 0)
                            <div class="text-center">
                                <h5 class="text-info">{{ number_format($statistics['average_percentage'], 1) }}%</h5>
                                <p class="mb-0">Average Score</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Student View -->
            @if(auth()->user()->role == 'student')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Take Exam</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($attempt)
                            @if($attempt->isStarted())
                                <div class="alert alert-warning">
                                    <i class="bi bi-clock"></i> Exam in progress
                                </div>
                                <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-primary">
                                    <i class="bi bi-play-circle"></i> Continue Exam
                                </a>
                            @elseif($attempt->isSubmitted())
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> Exam completed
                                </div>
                                <a href="{{ route('student.exams.result', $exam->id) }}" class="btn btn-info">
                                    <i class="bi bi-trophy"></i> View Results
                                </a>
                            @endif
                        @else
                            @if($isAvailable)
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Exam is available
                                </div>
                                <form method="POST" action="{{ route('student.exams.start', $exam->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-play-circle"></i> Start Exam
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-clock"></i> Exam not yet available
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Questions Section -->
    @if($canEdit || auth()->user()->role == 'superadmin')
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Exam Questions ({{ $exam->questions()->count() }})</h5>
                        @if($canEdit)
                            <a href="{{ route('exam-questions.create', $exam->id) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Question
                            </a>
                        @endif
                    </div>
                    
                    <div class="card-body">
                        @if($exam->questions()->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Q#</th>
                                            <th>Question</th>
                                            <th>Type</th>
                                            <th>Marks</th>
                                            <th>Difficulty</th>
                                            <th>Status</th>
                                            @if($canEdit)
                                                <th>Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exam->questions()->orderBy('question_number')->get() as $question)
                                            <tr>
                                                <td><strong>{{ $question->question_number }}</strong></td>
                                                <td>
                                                    <div style="max-width: 300px;">
                                                        {{ Str::limit($question->question_text, 80) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($question->question_type) }}</span>
                                                </td>
                                                <td>{{ $question->marks }}</td>
                                                <td>
                                                    <span class="badge 
                                                        {{ $question->difficulty_level == 'easy' ? 'bg-success' : 
                                                           ($question->difficulty_level == 'medium' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ ucfirst($question->difficulty_level) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $question->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($question->status) }}
                                                    </span>
                                                </td>
                                                @if($canEdit)
                                                    <td>
                                                        <a href="{{ route('exam-questions.edit', [$exam->id, $question->id]) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('exam-questions.destroy', [$exam->id, $question->id]) }}" 
                                                              style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to delete this question?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-question-circle display-4 text-muted"></i>
                                <h5 class="text-muted mt-3">No Questions Added Yet</h5>
                                <p class="text-muted">Add questions to make this exam available to students</p>
                                @if($canEdit)
                                    <a href="{{ route('exam-questions.create', $exam->id) }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Add First Question
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('{{ session('success') }}', 'success');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('{{ session('error') }}', 'danger');
        });
    </script>
@endif

<script>
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
@endif
