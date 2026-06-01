@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Questions - {{ $exam->exam_name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                        <li class="breadcrumb-item active">Questions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Exam Info Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Exam Type:</strong> {{ $exam->examType->exam_type_name ?? ($exam->examType->name ?? 'N/A (ID: ' . ($exam->exam_type_id ?? 'None') . ')') }}</p>
                            <p><strong>Subject:</strong> {{ $exam->subject->subject_name ?? 'N/A' }}</p>
                            <p><strong>Class:</strong> {{ $exam->class ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Duration:</strong> {{ $exam->duration }} minutes</p>
                            <p><strong>Total Marks:</strong> {{ $exam->total_marks }}</p>
                            <p><strong>Total Questions:</strong> {{ $questions->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Questions</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                        <button type="button" class="btn btn-info" onclick="importFromBank()">
                            <i class="fas fa-download"></i> Import from Question Bank
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($questions->count() > 0)
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="question-preview">
                                        {!! Str::limit($question->question_text, 100) !!}
                                        @if($question->question_type === 'mcq')
                                            <div class="mt-1">
                                                <small class="text-muted">Options: {{ $question->mcqOptions->count() }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ strtoupper($question->question_type) }}</span>
                                </td>
                                <td>{{ $question->marks }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" onclick="viewQuestion({{ $question->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editQuestion({{ $question->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteQuestion({{ $question->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center p-4">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No questions added yet. Click "Add Question" to get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Question</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addQuestionForm">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="question_type">Question Type</label>
                        <select class="form-control" name="question_type" id="question_type" required>
                            <option value="">Select Type</option>
                            <option value="mcq">Multiple Choice (MCQ)</option>
                            <option value="short">Short Answer</option>
                            <option value="long">Long Answer</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="question_text">Question</label>
                        <textarea class="form-control" name="question_text" id="question_text" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="marks">Marks</label>
                        <input type="number" class="form-control" name="marks" id="marks" min="1" required>
                    </div>

                    <!-- MCQ Options (shown only for MCQ type) -->
                    <div id="mcq_options" style="display: none;">
                        <label>Options</label>
                        <div id="options_container">
                            <div class="option-item mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_option" value="0">
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 1">
                                </div>
                            </div>
                            <div class="option-item mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_option" value="1">
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 2">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" onclick="addOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <!-- Answer field for other question types -->
                    <div class="form-group" id="answer_field" style="display: none;">
                        <label for="correct_answer">Answer/Explanation</label>
                        <textarea class="form-control" name="correct_answer" id="correct_answer" rows="3"></textarea>
                        <small class="form-text text-muted">For True/False: Enter 'True' or 'False'</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Question</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editQuestionForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="question_id" id="edit_question_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_question_type">Question Type</label>
                        <select class="form-control" name="question_type" id="edit_question_type" required>
                            <option value="mcq">Multiple Choice (MCQ)</option>
                            <option value="short">Short Answer</option>
                            <option value="long">Long Answer</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_question_text">Question</label>
                        <textarea class="form-control" name="question_text" id="edit_question_text" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_marks">Marks</label>
                        <input type="number" class="form-control" name="marks" id="edit_marks" min="1" required>
                    </div>

                    <!-- Edit MCQ Options -->
                    <div id="edit_mcq_options" style="display: none;">
                        <label>Options</label>
                        <div id="edit_options_container">
                            <!-- Options will be loaded dynamically -->
                        </div>
                        <button type="button" class="btn btn-sm btn-success" onclick="addEditOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <!-- Edit Answer field -->
                    <div class="form-group" id="edit_answer_field" style="display: none;">
                        <label for="edit_correct_answer">Answer/Explanation</label>
                        <textarea class="form-control" name="correct_answer" id="edit_correct_answer" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Question Modal -->
<div class="modal fade" id="viewQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Question Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="question_details">
                <!-- Question details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Import from Question Bank Modal -->
<div class="modal fade" id="importQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import from Question Bank</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-info">This feature will allow importing questions from the question bank. Currently under development.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let optionCount = 2;

$(document).ready(function() {
    // Handle question type change
    $('#question_type').change(function() {
        handleQuestionTypeChange(this.value, 'add');
    });

    $('#edit_question_type').change(function() {
        handleQuestionTypeChange(this.value, 'edit');
    });

    // Add question form submission
    $('#addQuestionForm').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("exam-questions.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                (bootstrap.Modal.getInstance(document.getElementById('addQuestionModal')) || new bootstrap.Modal(document.getElementById('addQuestionModal'))).hide();
                toastr.success('Question added successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = Object.values(errors).flat().join('<br>');
                toastr.error(errorMessages);
            }
        });
    });

    // Edit question form submission
    $('#editQuestionForm').submit(function(e) {
        e.preventDefault();
        
        let questionId = $('#edit_question_id').val();
        let formData = new FormData(this);
        
        $.ajax({
            url: `/exam-questions/${questionId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editQuestionModal').modal('hide');
                toastr.success('Question updated successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = Object.values(errors).flat().join('<br>');
                toastr.error(errorMessages);
            }
        });
    });
});

function handleQuestionTypeChange(type, mode) {
    const prefix = mode === 'edit' ? 'edit_' : '';
    
    if (type === 'mcq') {
        $(`#${prefix}mcq_options`).show();
        $(`#${prefix}answer_field`).hide();
    } else {
        $(`#${prefix}mcq_options`).hide();
        $(`#${prefix}answer_field`).show();
    }
}

function addOption() {
    const container = $('#options_container');
    const newOption = `
        <div class="option-item mb-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="radio" name="correct_option" value="${optionCount}">
                    </div>
                </div>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount + 1}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(newOption);
    optionCount++;
}

function removeOption(button) {
    $(button).closest('.option-item').remove();
}

function viewQuestion(questionId) {
    $.ajax({
        url: `/exam-questions/${questionId}`,
        method: 'GET',
        success: function(question) {
            let content = `
                <div class="question-details">
                    <h5>Question:</h5>
                    <div class="question-text mb-3">${question.question_text}</div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Type:</strong> ${question.question_type.toUpperCase()}
                        </div>
                        <div class="col-md-6">
                            <strong>Marks:</strong> ${question.marks}
                        </div>
                    </div>
            `;
            
            if (question.question_type === 'mcq' && question.mcq_options) {
                content += `
                    <h5 class="mt-3">Options:</h5>
                    <ul class="list-group">
                `;
                question.mcq_options.forEach((option, index) => {
                    const isCorrect = option.is_correct ? ' (Correct Answer)' : '';
                    content += `
                        <li class="list-group-item ${option.is_correct ? 'list-group-item-success' : ''}">
                            ${String.fromCharCode(65 + index)}. ${option.option_text}${isCorrect}
                        </li>
                    `;
                });
                content += `</ul>`;
            } else if (question.correct_answer) {
                content += `
                    <h5 class="mt-3">Answer:</h5>
                    <div class="answer-text">${question.correct_answer}</div>
                `;
            }
            
            content += `</div>`;
            
            $('#question_details').html(content);
            (new bootstrap.Modal(document.getElementById('viewQuestionModal'))).show();
        },
        error: function() {
            toastr.error('Failed to load question details');
        }
    });
}

function editQuestion(questionId) {
    $.ajax({
        url: `/exam-questions/${questionId}`,
        method: 'GET',
        success: function(question) {
            $('#edit_question_id').val(question.id);
            $('#edit_question_type').val(question.question_type);
            $('#edit_question_text').val(question.question_text);
            $('#edit_marks').val(question.marks);
            
            handleQuestionTypeChange(question.question_type, 'edit');
            
            if (question.question_type === 'mcq' && question.mcq_options) {
                let optionsHtml = '';
                question.mcq_options.forEach((option, index) => {
                    optionsHtml += `
                        <div class="option-item mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="${index}" ${option.is_correct ? 'checked' : ''}>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="options[]" value="${option.option_text}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#edit_options_container').html(optionsHtml);
            } else {
                $('#edit_correct_answer').val(question.correct_answer);
            }
            
            (new bootstrap.Modal(document.getElementById('editQuestionModal'))).show();
        },
        error: function() {
            toastr.error('Failed to load question details');
        }
    });
}

function addEditOption() {
    const container = $('#edit_options_container');
    const currentOptions = container.find('.option-item').length;
    const newOption = `
        <div class="option-item mb-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="radio" name="correct_option" value="${currentOptions}">
                    </div>
                </div>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${currentOptions + 1}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(newOption);
}

function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: `/exam-questions/${questionId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                toastr.success('Question deleted successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function() {
                toastr.error('Failed to delete question');
            }
        });
    }
}

function importFromBank() {
    (new bootstrap.Modal(document.getElementById('importQuestionModal'))).show();
}
</script>
@endsection
