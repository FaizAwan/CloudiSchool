@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Exam Results</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                        <li class="breadcrumb-item active">Results</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Result Summary Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">{{ $exam->exam_name }} - Result Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Student</span>
                                    <span class="info-box-number">{{ $student->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-trophy"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Score</span>
                                    <span class="info-box-number">{{ $result->marks_obtained }}/{{ $exam->total_marks }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon {{ $result->percentage >= $exam->passing_marks ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas fa-percent"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Percentage</span>
                                    <span class="info-box-number">{{ number_format($result->percentage, 2) }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon {{ $result->status === 'passed' ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas fa-{{ $result->status === 'passed' ? 'check' : 'times' }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">{{ ucfirst($result->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Exam Type:</strong> {{ $exam->examType->exam_type_name ?? ($exam->examType->name ?? 'N/A') }}</p>
                            <p><strong>Subject:</strong> {{ $exam->subject->subject_name ?? 'N/A' }}</p>
                            <p><strong>Class:</strong> {{ $exam->class }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Exam Date:</strong> {{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}</p>
                            <p><strong>Duration:</strong> {{ $exam->duration }} minutes</p>
                            <p><strong>Time Taken:</strong> {{ gmdate('i:s', $attempt->time_taken ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Analysis -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Question-wise Performance</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Answer Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="description-block border-right">
                                        <span class="description-percentage text-success">{{ $correctAnswers }}</span>
                                        <span class="description-text">Correct</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="description-block border-right">
                                        <span class="description-percentage text-danger">{{ $incorrectAnswers }}</span>
                                        <span class="description-text">Incorrect</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="description-block">
                                        <span class="description-percentage text-warning">{{ $skippedAnswers }}</span>
                                        <span class="description-text">Skipped</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Answer Review -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Answer Review</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" onclick="printResults()">
                            <i class="fas fa-print"></i> Print Results
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($questions as $index => $question)
                    <div class="question-review mb-4">
                        <div class="question-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-1">Question {{ $index + 1 }}</h5>
                                </div>
                                <div class="col-md-4 text-right">
                                    @php
                                        $studentAnswer = $answers->where('question_id', $question->id)->first();
                                        $isCorrect = false;
                                        $marksAwarded = 0;
                                        
                                        if ($studentAnswer) {
                                            if ($question->question_type === 'mcq') {
                                                $correctOption = $question->mcqOptions->where('is_correct', true)->first();
                                                $isCorrect = $studentAnswer->selected_option_id == $correctOption->id;
                                            } elseif ($question->question_type === 'true_false') {
                                                $isCorrect = strtolower($studentAnswer->answer_text) === strtolower($question->correct_answer);
                                            }
                                            
                                            if ($isCorrect) {
                                                $marksAwarded = $question->marks;
                                            }
                                        }
                                    @endphp
                                    
                                    <span class="badge {{ $isCorrect ? 'badge-success' : ($studentAnswer ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $marksAwarded }}/{{ $question->marks }} marks
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="question-content">
                            <div class="question-text mb-3">
                                <strong>Question:</strong> {!! $question->question_text !!}
                            </div>
                            
                            @if($question->question_type === 'mcq')
                                <div class="mcq-review">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Options:</h6>
                                            <ul class="list-group">
                                                @foreach($question->mcqOptions as $optionIndex => $option)
                                                <li class="list-group-item 
                                                    {{ $option->is_correct ? 'list-group-item-success' : '' }}
                                                    {{ $studentAnswer && $studentAnswer->selected_option_id == $option->id && !$option->is_correct ? 'list-group-item-danger' : '' }}">
                                                    <div class="d-flex justify-content-between">
                                                        <span>{{ chr(65 + $optionIndex) }}. {{ $option->option_text }}</span>
                                                        <span>
                                                            @if($option->is_correct)
                                                                <i class="fas fa-check text-success"></i> Correct
                                                            @endif
                                                            @if($studentAnswer && $studentAnswer->selected_option_id == $option->id)
                                                                <i class="fas fa-arrow-left text-primary"></i> Your Answer
                                                            @endif
                                                        </span>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Your Answer:</h6>
                                            @if($studentAnswer)
                                                @php
                                                    $selectedOption = $question->mcqOptions->where('id', $studentAnswer->selected_option_id)->first();
                                                @endphp
                                                <div class="alert {{ $isCorrect ? 'alert-success' : 'alert-danger' }}">
                                                    {{ $selectedOption ? chr(65 + $question->mcqOptions->search($selectedOption)) . '. ' . $selectedOption->option_text : 'No answer selected' }}
                                                </div>
                                            @else
                                                <div class="alert alert-warning">No answer provided</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @elseif($question->question_type === 'true_false')
                                <div class="true-false-review">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Correct Answer:</h6>
                                            <div class="alert alert-success">{{ $question->correct_answer }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Your Answer:</h6>
                                            @if($studentAnswer)
                                                <div class="alert {{ $isCorrect ? 'alert-success' : 'alert-danger' }}">
                                                    {{ $studentAnswer->answer_text }}
                                                </div>
                                            @else
                                                <div class="alert alert-warning">No answer provided</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-answer-review">
                                    @if($studentAnswer)
                                        <h6>Your Answer:</h6>
                                        <div class="answer-box p-3 mb-3" style="background: #f8f9fa; border-radius: 5px;">
                                            {!! nl2br(e($studentAnswer->answer_text)) !!}
                                        </div>
                                    @else
                                        <div class="alert alert-warning">No answer provided</div>
                                    @endif
                                    
                                    @if($question->correct_answer)
                                        <h6>Model Answer:</h6>
                                        <div class="model-answer p-3" style="background: #e8f5e8; border-radius: 5px;">
                                            {!! nl2br(e($question->correct_answer)) !!}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        @if(!$loop->last)
                        <hr>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize performance chart
    initPerformanceChart();
});

function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Sample data - this should come from the controller
    const questionNumbers = [
        @foreach($questions as $index => $question)
            '{{ $index + 1 }}'{{ !$loop->last ? ',' : '' }}
        @endforeach
    ];
    
    const marksData = [
        @foreach($questions as $question)
            @php
                $studentAnswer = $answers->where('question_id', $question->id)->first();
                $marksAwarded = 0;
                
                if ($studentAnswer) {
                    if ($question->question_type === 'mcq') {
                        $correctOption = $question->mcqOptions->where('is_correct', true)->first();
                        if ($correctOption && $studentAnswer->selected_option_id == $correctOption->id) {
                            $marksAwarded = $question->marks;
                        }
                    } elseif ($question->question_type === 'true_false') {
                        if (strtolower($studentAnswer->answer_text) === strtolower($question->correct_answer)) {
                            $marksAwarded = $question->marks;
                        }
                    }
                    // For short and long answers, manual evaluation is needed
                }
            @endphp
            {{ $marksAwarded }}{{ !$loop->last ? ',' : '' }}
        @endforeach
    ];
    
    const totalMarksData = [
        @foreach($questions as $question)
            {{ $question->marks }}{{ !$loop->last ? ',' : '' }}
        @endforeach
    ];
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: questionNumbers,
            datasets: [{
                label: 'Marks Obtained',
                data: marksData,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Total Marks',
                data: totalMarksData,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                type: 'line'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Question-wise Performance'
                }
            }
        }
    });
}

function printResults() {
    window.print();
}
</script>

<style>
@media print {
    .content-wrapper {
        margin-left: 0 !important;
    }
    
    .main-sidebar,
    .main-header,
    .content-header .breadcrumb,
    .card-tools,
    .btn {
        display: none !important;
    }
    
    .content-wrapper,
    .card {
        box-shadow: none !important;
        border: none !important;
    }
}

.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.description-block {
    margin: 0;
}

.description-percentage {
    color: #999;
    display: block;
    font-size: 24px;
    font-weight: 600;
}

.description-text {
    font-size: 14px;
    display: block;
    color: #999;
    text-transform: uppercase;
}

.border-right {
    border-right: 1px solid #f4f4f4;
}

.question-review {
    border-left: 4px solid #17a2b8;
    padding-left: 15px;
}

.answer-box {
    max-height: 200px;
    overflow-y: auto;
}

.model-answer {
    max-height: 200px;
    overflow-y: auto;
}
</style>
@endsection
