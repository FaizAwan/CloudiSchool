@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $exam->exam_name }}</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <div id="timer" class="badge badge-danger badge-lg">
                            <i class="fas fa-clock"></i>
                            <span id="time-remaining">{{ $exam->duration }}:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Questions Panel -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-0">
                                        Question <span id="current-question-number">1</span> of {{ $questions->count() }}
                                    </h5>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="badge badge-info">
                                        Marks: <span id="current-question-marks">{{ $questions->first()->marks ?? 0 }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="examForm">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                
                                @foreach($questions as $index => $question)
                                <div class="question-container" id="question-{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                                    <div class="question-text mb-4">
                                        <h6>{{ $index + 1 }}. {!! $question->question_text !!}</h6>
                                    </div>
                                    
                                    @if($question->question_type === 'mcq')
                                        <div class="mcq-options">
                                            @foreach($question->mcqOptions as $optionIndex => $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       id="q{{ $question->id }}_opt{{ $optionIndex }}" 
                                                       value="{{ $option->id }}">
                                                <label class="form-check-label" for="q{{ $question->id }}_opt{{ $optionIndex }}">
                                                    {{ chr(65 + $optionIndex) }}. {{ $option->option_text }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->question_type === 'true_false')
                                        <div class="true-false-options">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       id="q{{ $question->id }}_true" 
                                                       value="True">
                                                <label class="form-check-label" for="q{{ $question->id }}_true">
                                                    True
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       id="q{{ $question->id }}_false" 
                                                       value="False">
                                                <label class="form-check-label" for="q{{ $question->id }}_false">
                                                    False
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-answer">
                                            @if($question->question_type === 'short')
                                                <input type="text" class="form-control" 
                                                       name="answers[{{ $question->id }}]" 
                                                       placeholder="Enter your answer...">
                                            @else
                                                <textarea class="form-control" 
                                                          name="answers[{{ $question->id }}]" 
                                                          rows="6" 
                                                          placeholder="Enter your answer..."></textarea>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </form>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeQuestion(-1)" disabled>
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" id="nextBtn" onclick="changeQuestion(1)">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-warning mr-2" onclick="saveProgress()">
                                        <i class="fas fa-save"></i> Save Progress
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="submitExam()">
                                        <i class="fas fa-paper-plane"></i> Submit Exam
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question Navigation Panel -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Question Navigator</h5>
                        </div>
                        <div class="card-body">
                            <div class="question-grid">
                                @foreach($questions as $index => $question)
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-sm question-nav-btn mb-2" 
                                        data-question="{{ $index }}"
                                        onclick="goToQuestion({{ $index }})">
                                    {{ $index + 1 }}
                                </button>
                                @endforeach
                            </div>
                            
                            <hr>
                            
                            <div class="exam-progress">
                                <h6>Progress</h6>
                                <div class="progress mb-2">
                                    <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">
                                    <span id="answered-count">0</span> of {{ $questions->count() }} answered
                                </small>
                            </div>

                            <hr>

                            <div class="legend">
                                <h6>Legend</h6>
                                <div class="mb-1">
                                    <span class="btn btn-outline-secondary btn-sm">Not Visited</span>
                                </div>
                                <div class="mb-1">
                                    <span class="btn btn-warning btn-sm">Current</span>
                                </div>
                                <div class="mb-1">
                                    <span class="btn btn-success btn-sm">Answered</span>
                                </div>
                                <div class="mb-1">
                                    <span class="btn btn-info btn-sm">Visited</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Submit Exam</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Are you sure you want to submit your exam?</strong>
                </div>
                <p>Once submitted, you will not be able to make any changes.</p>
                <div class="submission-summary">
                    <p><strong>Total Questions:</strong> {{ $questions->count() }}</p>
                    <p><strong>Answered:</strong> <span id="final-answered-count">0</span></p>
                    <p><strong>Unanswered:</strong> <span id="final-unanswered-count">{{ $questions->count() }}</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmSubmit()">
                    Submit Exam
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentQuestion = 0;
let totalQuestions = {{ $questions->count() }};
let examDuration = {{ $exam->duration }} * 60; // Convert to seconds
let timeRemaining = examDuration;
let timer;
let questionStatus = new Array(totalQuestions).fill('not-visited');
let answers = {};

$(document).ready(function() {
    // Mark first question as current
    questionStatus[0] = 'current';
    updateQuestionNavigation();
    
    // Start timer
    startTimer();
    
    // Auto-save every 30 seconds
    setInterval(saveProgress, 30000);
    
    // Handle answer changes
    $('input[type="radio"], input[type="text"], textarea').on('change input', function() {
        saveAnswer();
        updateProgress();
    });
    
    // Prevent page refresh/close without warning
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'Are you sure you want to leave? Your progress will be saved automatically.';
        return 'Are you sure you want to leave? Your progress will be saved automatically.';
    });
});

function startTimer() {
    timer = setInterval(function() {
        timeRemaining--;
        updateTimerDisplay();
        
        if (timeRemaining <= 0) {
            clearInterval(timer);
            autoSubmitExam();
        }
    }, 1000);
}

function updateTimerDisplay() {
    let minutes = Math.floor(timeRemaining / 60);
    let seconds = timeRemaining % 60;
    
    $('#time-remaining').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
    
    // Change color based on time remaining
    if (timeRemaining < 300) { // Less than 5 minutes
        $('#timer').removeClass('badge-warning badge-danger').addClass('badge-danger');
    } else if (timeRemaining < 600) { // Less than 10 minutes
        $('#timer').removeClass('badge-info badge-danger').addClass('badge-warning');
    }
}

function changeQuestion(direction) {
    let newQuestion = currentQuestion + direction;
    
    if (newQuestion >= 0 && newQuestion < totalQuestions) {
        // Save current answer
        saveAnswer();
        
        // Update question status
        if (questionStatus[currentQuestion] === 'current') {
            questionStatus[currentQuestion] = hasAnswer(currentQuestion) ? 'answered' : 'visited';
        }
        
        // Hide current question
        $(`#question-${currentQuestion}`).hide();
        
        // Show new question
        currentQuestion = newQuestion;
        $(`#question-${currentQuestion}`).show();
        
        // Update status
        questionStatus[currentQuestion] = 'current';
        
        // Update UI
        updateQuestionNavigation();
        updateNavigationButtons();
        updateCurrentQuestionInfo();
    }
}

function goToQuestion(questionIndex) {
    if (questionIndex !== currentQuestion) {
        // Save current answer
        saveAnswer();
        
        // Update current question status
        if (questionStatus[currentQuestion] === 'current') {
            questionStatus[currentQuestion] = hasAnswer(currentQuestion) ? 'answered' : 'visited';
        }
        
        // Hide current question
        $(`#question-${currentQuestion}`).hide();
        
        // Show new question
        currentQuestion = questionIndex;
        $(`#question-${currentQuestion}`).show();
        
        // Update status
        questionStatus[currentQuestion] = 'current';
        
        // Update UI
        updateQuestionNavigation();
        updateNavigationButtons();
        updateCurrentQuestionInfo();
    }
}

function updateQuestionNavigation() {
    $('.question-nav-btn').each(function(index) {
        let btn = $(this);
        btn.removeClass('btn-outline-secondary btn-warning btn-success btn-info');
        
        switch(questionStatus[index]) {
            case 'current':
                btn.addClass('btn-warning');
                break;
            case 'answered':
                btn.addClass('btn-success');
                break;
            case 'visited':
                btn.addClass('btn-info');
                break;
            default:
                btn.addClass('btn-outline-secondary');
        }
    });
}

function updateNavigationButtons() {
    $('#prevBtn').prop('disabled', currentQuestion === 0);
    $('#nextBtn').prop('disabled', currentQuestion === totalQuestions - 1);
    
    if (currentQuestion === totalQuestions - 1) {
        $('#nextBtn').text('Finish').removeClass('btn-secondary').addClass('btn-success');
    } else {
        $('#nextBtn').html('Next <i class="fas fa-chevron-right"></i>').removeClass('btn-success').addClass('btn-secondary');
    }
}

function updateCurrentQuestionInfo() {
    $('#current-question-number').text(currentQuestion + 1);
    
    // Get current question marks
    let currentQuestionElement = $(`#question-${currentQuestion}`);
    let questionData = @json($questions->toArray());
    $('#current-question-marks').text(questionData[currentQuestion].marks);
}

function hasAnswer(questionIndex) {
    let questionElement = $(`#question-${questionIndex}`);
    
    // Check for radio button answers
    if (questionElement.find('input[type="radio"]:checked').length > 0) {
        return true;
    }
    
    // Check for text/textarea answers
    let textInputs = questionElement.find('input[type="text"], textarea');
    for (let input of textInputs) {
        if ($(input).val().trim() !== '') {
            return true;
        }
    }
    
    return false;
}

function saveAnswer() {
    let questionElement = $(`#question-${currentQuestion}`);
    let questionData = @json($questions->toArray());
    let questionId = questionData[currentQuestion].id;
    
    // Get answer based on question type
    let answer = '';
    
    // Radio button answers (MCQ, True/False)
    let checkedRadio = questionElement.find('input[type="radio"]:checked');
    if (checkedRadio.length > 0) {
        answer = checkedRadio.val();
    } else {
        // Text answers (Short, Long)
        let textInput = questionElement.find('input[type="text"], textarea');
        if (textInput.length > 0) {
            answer = textInput.val().trim();
        }
    }
    
    // Store answer
    answers[questionId] = answer;
    
    // Update question status
    if (answer !== '') {
        if (questionStatus[currentQuestion] === 'current') {
            questionStatus[currentQuestion] = 'answered';
        } else if (questionStatus[currentQuestion] === 'visited') {
            questionStatus[currentQuestion] = 'answered';
        }
    }
    
    updateQuestionNavigation();
    updateProgress();
}

function updateProgress() {
    let answeredCount = 0;
    
    for (let i = 0; i < totalQuestions; i++) {
        if (hasAnswer(i)) {
            answeredCount++;
        }
    }
    
    let progressPercentage = (answeredCount / totalQuestions) * 100;
    $('#progress-bar').css('width', progressPercentage + '%');
    $('#answered-count').text(answeredCount);
}

function saveProgress() {
    // Collect all answers
    let allAnswers = {};
    
    for (let i = 0; i < totalQuestions; i++) {
        if (hasAnswer(i)) {
            let questionElement = $(`#question-${i}`);
            let questionData = @json($questions->toArray());
            let questionId = questionData[i].id;
            
            let answer = '';
            let checkedRadio = questionElement.find('input[type="radio"]:checked');
            if (checkedRadio.length > 0) {
                answer = checkedRadio.val();
            } else {
                let textInput = questionElement.find('input[type="text"], textarea');
                if (textInput.length > 0) {
                    answer = textInput.val().trim();
                }
            }
            
            allAnswers[questionId] = answer;
        }
    }
    
    $.ajax({
        url: '{{ route("exam-attempts.save-progress") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            exam_id: {{ $exam->id }},
            answers: allAnswers
        },
        success: function(response) {
            toastr.success('Progress saved successfully!');
        },
        error: function() {
            toastr.error('Failed to save progress');
        }
    });
}

function submitExam() {
    // Update final counts
    updateProgress();
    let answeredCount = parseInt($('#answered-count').text());
    let unansweredCount = totalQuestions - answeredCount;
    
    $('#final-answered-count').text(answeredCount);
    $('#final-unanswered-count').text(unansweredCount);
    
    $('#submitConfirmModal').modal('show');
}

function confirmSubmit() {
    $('#submitConfirmModal').modal('hide');
    
    // Show loading
    toastr.info('Submitting exam...');
    
    // Collect all answers
    let allAnswers = {};
    
    for (let i = 0; i < totalQuestions; i++) {
        let questionElement = $(`#question-${i}`);
        let questionData = @json($questions->toArray());
        let questionId = questionData[i].id;
        
        let answer = '';
        let checkedRadio = questionElement.find('input[type="radio"]:checked');
        if (checkedRadio.length > 0) {
            answer = checkedRadio.val();
        } else {
            let textInput = questionElement.find('input[type="text"], textarea');
            if (textInput.length > 0) {
                answer = textInput.val().trim();
            }
        }
        
        if (answer !== '') {
            allAnswers[questionId] = answer;
        }
    }
    
    $.ajax({
        url: '{{ route("exam-attempts.submit") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            exam_id: {{ $exam->id }},
            answers: allAnswers,
            time_taken: examDuration - timeRemaining
        },
        success: function(response) {
            clearInterval(timer);
            toastr.success('Exam submitted successfully!');
            
            setTimeout(() => {
                window.location.href = `/exam-results/${response.attempt_id}`;
            }, 2000);
        },
        error: function() {
            toastr.error('Failed to submit exam');
        }
    });
}

function autoSubmitExam() {
    toastr.warning('Time is up! Auto-submitting exam...');
    confirmSubmit();
}

// Initialize
$(document).ready(function() {
    updateCurrentQuestionInfo();
    updateNavigationButtons();
});
</script>

<style>
.question-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 5px;
}

.question-nav-btn {
    width: 100%;
    aspect-ratio: 1;
}

#timer {
    font-size: 1.2em;
    padding: 8px 15px;
}

.question-text {
    font-size: 1.1em;
    line-height: 1.6;
}

.form-check {
    padding: 8px 15px;
    background: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #e9ecef;
}

.form-check:hover {
    background: #e9ecef;
}

.form-check-input:checked ~ .form-check-label {
    font-weight: bold;
    color: #007bff;
}

.progress {
    height: 25px;
}

.legend .btn {
    width: 100%;
    margin: 2px 0;
    font-size: 0.8em;
}
</style>
@endsection
