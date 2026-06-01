@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Question Bank</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Question Bank</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <!-- Filters Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subject</label>
                                <select class="form-control" id="subject_filter">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Question Type</label>
                                <select class="form-control" id="type_filter">
                                    <option value="">All Types</option>
                                    <option value="mcq">Multiple Choice</option>
                                    <option value="short">Short Answer</option>
                                    <option value="long">Long Answer</option>
                                    <option value="true_false">True/False</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Class</label>
                                <select class="form-control" id="class_filter">
                                    <option value="">All Classes</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">Class {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Question Bank</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addToBankModal">
                            <i class="bi bi-plus-circle"></i> Add to Bank
                        </button>
                        <button type="button" class="btn btn-info" onclick="bulkImport()">
                            <i class="bi bi-upload"></i> Bulk Import
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap" id="questionsTable">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Marks</th>
                                <th>Usage Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="questionsTableBody">
                            @forelse($questionBankItems as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="question-checkbox" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <div class="question-preview">
                                        {!! Str::limit($item->question_text, 80) !!}
                                        @if($item->question_type === 'mcq')
                                            <div class="mt-1">
                                                <small class="text-muted">{{ $item->mcqOptions->count() }} options</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ strtoupper($item->question_type) }}</span>
                                </td>
                                <td>{{ $item->subject->subject_name ?? 'N/A' }}</td>
                                <td>{{ $item->class_level ?? 'N/A' }}</td>
                                <td>{{ $item->default_marks }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $item->usage_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" onclick="viewBankQuestion({{ $item->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editBankQuestion({{ $item->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addToExam({{ $item->id }})">
                                        <i class="fas fa-plus"></i> Use
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteBankQuestion({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="p-4">
                                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No questions in the bank yet. Add questions to get started.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($questionBankItems->hasPages())
                <div class="card-footer">
                    {{ $questionBankItems->links() }}
                </div>
                @endif
            </div>
            
            <!-- Bulk Actions -->
            <div class="card" id="bulkActionsCard" style="display: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Selected:</strong> <span id="selectedCount">0</span> questions</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-sm btn-success" onclick="bulkAddToExam()">
                                <i class="fas fa-plus"></i> Add to Exam
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>

<!-- Add to Bank Modal -->
<div class="modal fade" id="addToBankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Question to Bank</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="addToBankForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject_id">Subject</label>
                                <select class="form-control" name="subject_id" id="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_level">Class Level</label>
                                <select class="form-control" name="class_level" id="class_level">
                                    <option value="">Select Class</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">Class {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bank_question_type">Question Type</label>
                        <select class="form-control" name="question_type" id="bank_question_type" required>
                            <option value="">Select Type</option>
                            <option value="mcq">Multiple Choice (MCQ)</option>
                            <option value="short">Short Answer</option>
                            <option value="long">Long Answer</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bank_question_text">Question</label>
                        <textarea class="form-control" name="question_text" id="bank_question_text" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="default_marks">Default Marks</label>
                        <input type="number" class="form-control" name="marks" id="default_marks" min="1" value="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="difficulty_level">Difficulty Level</label>
                        <select class="form-control" name="difficulty_level" id="difficulty_level" required>
                            <option value="">Select Difficulty</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <!-- MCQ Options for Bank -->
                    <div id="bank_mcq_options" style="display: none;">
                        <label>Options</label>
                        <div id="bank_options_container">
                            <div class="option-item mb-2">
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="0">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 1">
                                </div>
                            </div>
                            <div class="option-item mb-2">
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="1">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 2">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" onclick="addBankOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <!-- Answer field for other question types -->
                    <div class="form-group" id="bank_answer_field" style="display: none;">
                        <label for="bank_correct_answer">Answer/Explanation</label>
                        <textarea class="form-control" name="correct_answer" id="bank_correct_answer" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="topic">Topic (optional)</label>
                                <input type="text" class="form-control" name="topic" id="topic" placeholder="Enter topic">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chapter">Chapter (optional)</label>
                                <input type="text" class="form-control" name="chapter" id="chapter" placeholder="Enter chapter">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="explanation">Explanation (optional)</label>
                        <textarea class="form-control" name="explanation" id="explanation" rows="3" placeholder="Provide detailed explanation for the answer"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Bank Question Modal -->
<div class="modal fade" id="viewBankQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Question Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body" id="bank_question_details">
                <!-- Question details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add to Exam Modal -->
<div class="modal fade" id="addToExamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add to Exam</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="addToExamForm">
                @csrf
                <input type="hidden" name="question_bank_id" id="selected_question_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="target_exam_id">Select Exam</label>
                        <select class="form-control" name="exam_id" id="target_exam_id" required>
                            <option value="">Choose an exam...</option>
                            @foreach($availableExams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->exam_name }} - {{ $exam->subject->subject_name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="question_marks">Marks for this Question</label>
                        <input type="number" class="form-control" name="marks" id="question_marks" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let bankOptionCount = 2;
let selectedQuestions = [];

$(document).ready(function() {
    // Handle question type change for bank
    $('#bank_question_type').change(function() {
        handleBankQuestionTypeChange(this.value);
    });

    // Handle checkbox selection
    $('#select_all').change(function() {
        $('.question-checkbox').prop('checked', this.checked);
        updateSelectedQuestions();
    });

    $('.question-checkbox').change(function() {
        updateSelectedQuestions();
    });

    // Add to bank form submission
    $('#addToBankForm').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        // Debug: Log form data
        console.log('Form data being sent:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Validate MCQ questions
        const questionType = $('#bank_question_type').val();
        if (questionType === 'mcq') {
            const options = $('input[name="options[]"]').filter(function() {
                return this.value.trim() !== '';
            });
            
            if (options.length < 2) {
                toastr.error('MCQ questions must have at least 2 options.');
                return false;
            }
            
            const selectedCorrect = $('input[name="correct_option"]:checked');
            if (selectedCorrect.length === 0) {
                toastr.error('Please select the correct answer for MCQ question.');
                return false;
            }
        }
        
        $.ajax({
            url: '{{ route("question-bank.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addToBankModal'));
                modal.hide();
                toastr.success('Question added to bank successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                console.log('AJAX Error:', xhr);
                console.log('Response JSON:', xhr.responseJSON);
                console.log('Response Text:', xhr.responseText);
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    console.log('Validation errors:', errors);
                    let errorMessages = Object.values(errors).flat().join('<br>');
                    toastr.error('Validation errors: <br>' + errorMessages, 'Form Validation Failed', {
                        timeOut: 10000,
                        escapeHtml: false
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error('Error: ' + xhr.responseJSON.error);
                } else if (xhr.status === 500) {
                    toastr.error('Server error. Please check if the database tables exist.');
                } else {
                    toastr.error('Request failed: ' + xhr.status + ' ' + xhr.statusText + '<br>Response: ' + xhr.responseText, 'Request Error', {
                        timeOut: 10000,
                        escapeHtml: false
                    });
                }
            }
        });
    });

    // Add to exam form submission
    $('#addToExamForm').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("question-bank.add-to-exam") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addToExamModal'));
                modal.hide();
                toastr.success('Question added to exam successfully!');
            },
            error: function(xhr) {
                console.log('Add to Exam AJAX Error:', xhr);
                console.log('Response JSON:', xhr.responseJSON);
                console.log('Response Text:', xhr.responseText);
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = Object.values(errors).flat().join('<br>');
                    toastr.error('Validation errors: <br>' + errorMessages, 'Form Validation Failed', {
                        timeOut: 10000,
                        escapeHtml: false
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error('Error: ' + xhr.responseJSON.error);
                } else if (xhr.status === 500) {
                    toastr.error('Server error occurred while adding question to exam.');
                } else {
                    toastr.error('Request failed: ' + xhr.status + ' ' + xhr.statusText + '<br>Response: ' + xhr.responseText, 'Request Error', {
                        timeOut: 10000,
                        escapeHtml: false
                    });
                }
            }
        });
    });
});

function handleBankQuestionTypeChange(type) {
    if (type === 'mcq') {
        $('#bank_mcq_options').show();
        $('#bank_answer_field').hide();
    } else {
        $('#bank_mcq_options').hide();
        $('#bank_answer_field').show();
    }
}

function addBankOption() {
    const container = $('#bank_options_container');
    const newOption = `
        <div class="option-item mb-2">
            <div class="input-group">
                <div class="input-group-text">
                    <input type="radio" name="correct_option" value="${bankOptionCount}">
                </div>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${bankOptionCount + 1}">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeBankOption(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.append(newOption);
    bankOptionCount++;
}

function removeBankOption(button) {
    $(button).closest('.option-item').remove();
}

function viewBankQuestion(questionId) {
    // Use AJAX for modal view
    $.ajax({
        url: `{{ route('question-bank.index') }}/${questionId}`,
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(question) {
            let content = `
                <div class="question-details">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Subject:</strong> ${question.subject ? question.subject.subject_name : 'N/A'}
                        </div>
                        <div class="col-md-6">
                            <strong>Class:</strong> Class ${question.class_level || 'N/A'}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Type:</strong> ${question.question_type.toUpperCase()}
                        </div>
                        <div class="col-md-6">
                            <strong>Marks:</strong> ${question.marks || question.default_marks || 'N/A'}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Difficulty:</strong> <span class="badge badge-info">${question.difficulty_level ? question.difficulty_level.charAt(0).toUpperCase() + question.difficulty_level.slice(1) : 'N/A'}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Usage:</strong> <span class="badge badge-secondary">${question.usage_count || 0} times</span>
                        </div>
                    </div>
                    
                    <h5>Question:</h5>
                    <div class="question-text mb-3 p-3" style="background: #f8f9fa; border-radius: 5px;">
                        ${question.question_text}
                    </div>
            `;
            
            if (question.question_type === 'mcq' && question.mcq_options && question.mcq_options.length > 0) {
                content += `
                    <h5>Options:</h5>
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
                    <h5>Answer/Explanation:</h5>
                    <div class="answer-text p-3" style="background: #e8f5e8; border-radius: 5px;">
                        ${question.correct_answer}
                    </div>
                `;
            }
            
            if (question.explanation) {
                content += `
                    <h5 class="mt-3">Explanation:</h5>
                    <div class="p-3" style="background: #f0f8ff; border-radius: 5px;">
                        ${question.explanation}
                    </div>
                `;
            }
            
            if (question.topic || question.chapter) {
                content += `<div class="row mt-3">`;
                if (question.topic) {
                    content += `<div class="col-md-6"><strong>Topic:</strong> ${question.topic}</div>`;
                }
                if (question.chapter) {
                    content += `<div class="col-md-6"><strong>Chapter:</strong> ${question.chapter}</div>`;
                }
                content += `</div>`;
            }
            
            content += `</div>`;
            
            $('#bank_question_details').html(content);
            const modal = new bootstrap.Modal(document.getElementById('viewBankQuestionModal'));
            modal.show();
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', xhr);
            toastr.error('Failed to load question details. Error: ' + error);
        }
    });
}

function editBankQuestion(questionId) {
    window.location.href = `{{ route('question-bank.index') }}/${questionId}/edit`;
}

function addToExam(questionId) {
    $('#selected_question_id').val(questionId);
    const modal = new bootstrap.Modal(document.getElementById('addToExamModal'));
    modal.show();
}

function deleteBankQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question from the bank?')) {
        $.ajax({
            url: `/question-bank/${questionId}`,
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

function updateSelectedQuestions() {
    selectedQuestions = [];
    $('.question-checkbox:checked').each(function() {
        selectedQuestions.push($(this).val());
    });
    
    $('#selectedCount').text(selectedQuestions.length);
    
    if (selectedQuestions.length > 0) {
        $('#bulkActionsCard').show();
    } else {
        $('#bulkActionsCard').hide();
    }
}

function bulkAddToExam() {
    if (selectedQuestions.length === 0) {
        toastr.warning('Please select questions first');
        return;
    }
    
    toastr.info('Bulk add to exam functionality will be implemented in future updates');
}

function bulkDelete() {
    if (selectedQuestions.length === 0) {
        toastr.warning('Please select questions first');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedQuestions.length} selected questions?`)) {
        $.ajax({
            url: '{{ route("question-bank.bulk-delete") }}',
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',
                question_ids: selectedQuestions
            },
            success: function() {
                toastr.success('Selected questions deleted successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function() {
                toastr.error('Failed to delete questions');
            }
        });
    }
}

function bulkImport() {
    toastr.info('Bulk import functionality will be implemented in future updates');
}

function applyFilters() {
    let subject = $('#subject_filter').val();
    let type = $('#type_filter').val();
    let classLevel = $('#class_filter').val();
    
    let url = '{{ route("question-bank.index") }}?';
    let params = [];
    
    if (subject) params.push('subject_id=' + encodeURIComponent(subject));
    if (type) params.push('question_type=' + encodeURIComponent(type));
    if (classLevel) params.push('class_level=' + encodeURIComponent(classLevel));
    
    window.location.href = url + params.join('&');
}
</script>
@endsection
