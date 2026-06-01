@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Edit Question</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('question-bank.index') }}">Question Bank</a></li>
            <li class="breadcrumb-item active">Edit Question</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Question</h3>
                    <div class="card-tools">
                        <a href="{{ route('question-bank.show', $question->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View Question
                        </a>
                        <a href="{{ route('question-bank.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bank
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('question-bank.update', $question->id) }}" method="POST" id="editQuestionForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject_id">Subject <span class="text-danger">*</span></label>
                                    <select class="form-control" name="subject_id" id="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $question->subject_id == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class_level">Class Level <span class="text-danger">*</span></label>
                                    <select class="form-control" name="class_level" id="class_level" required>
                                        <option value="">Select Class</option>
                                        @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $question->class_level == $i ? 'selected' : '' }}>
                                            Class {{ $i }}
                                        </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="question_type">Question Type <span class="text-danger">*</span></label>
                                    <select class="form-control" name="question_type" id="question_type" required>
                                        <option value="">Select Type</option>
                                        <option value="mcq" {{ $question->question_type === 'mcq' ? 'selected' : '' }}>Multiple Choice (MCQ)</option>
                                        <option value="short" {{ $question->question_type === 'short' ? 'selected' : '' }}>Short Answer</option>
                                        <option value="long" {{ $question->question_type === 'long' ? 'selected' : '' }}>Long Answer</option>
                                        <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True/False</option>
                                        <option value="fill_blank" {{ $question->question_type === 'fill_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="difficulty_level">Difficulty <span class="text-danger">*</span></label>
                                    <select class="form-control" name="difficulty_level" id="difficulty_level" required>
                                        <option value="">Select Difficulty</option>
                                        <option value="easy" {{ $question->difficulty_level === 'easy' ? 'selected' : '' }}>Easy</option>
                                        <option value="medium" {{ $question->difficulty_level === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="hard" {{ $question->difficulty_level === 'hard' ? 'selected' : '' }}>Hard</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="marks">Default Marks <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="marks" id="marks" 
                                           value="{{ old('marks', $question->default_marks ?? $question->marks) }}" 
                                           min="1" max="100" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="question_text">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="question_text" id="question_text" rows="5" required>{{ old('question_text', $question->question_text) }}</textarea>
                        </div>

                        <!-- MCQ Options -->
                        <div id="mcq_options" style="{{ $question->question_type === 'mcq' ? 'display: block;' : 'display: none;' }}">
                            <label>Answer Options <span class="text-danger">*</span></label>
                            <div id="options_container">
                                @if($question->question_type === 'mcq' && $question->mcqOptions->count() > 0)
                                    @foreach($question->mcqOptions as $index => $option)
                                    <div class="option-item mb-2">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input type="radio" name="correct_option" value="{{ $index }}" 
                                                       {{ $option->is_correct ? 'checked' : '' }}>
                                            </div>
                                            <input type="text" class="form-control" name="options[]" 
                                                   value="{{ $option->option_text }}" placeholder="Option {{ $index + 1 }}">
                                            @if($index > 1)
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
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
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-success" onclick="addOption()">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                        </div>

                        <!-- Answer field for other question types -->
                        <div class="form-group" id="answer_field" style="{{ $question->question_type !== 'mcq' ? 'display: block;' : 'display: none;' }}">
                            <label for="correct_answer">Answer/Explanation</label>
                            <textarea class="form-control" name="correct_answer" id="correct_answer" rows="4">{{ old('correct_answer', $question->correct_answer) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="topic">Topic (optional)</label>
                                    <input type="text" class="form-control" name="topic" id="topic" 
                                           value="{{ old('topic', $question->topic) }}" placeholder="Enter topic">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="chapter">Chapter (optional)</label>
                                    <input type="text" class="form-control" name="chapter" id="chapter" 
                                           value="{{ old('chapter', $question->chapter) }}" placeholder="Enter chapter">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="explanation">Explanation (optional)</label>
                            <textarea class="form-control" name="explanation" id="explanation" rows="3" 
                                      placeholder="Provide detailed explanation for the answer">{{ old('explanation', $question->explanation) }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="tags">Tags (optional)</label>
                            <input type="text" class="form-control" name="tags" id="tags" 
                                   value="{{ old('tags', $question->tags) }}" 
                                   placeholder="Enter tags separated by commas">
                            <small class="form-text text-muted">Separate multiple tags with commas</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Question
                        </button>
                        <a href="{{ route('question-bank.show', $question->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
let optionCount = {{ $question->question_type === 'mcq' && $question->mcqOptions ? $question->mcqOptions->count() : 2 }};

$(document).ready(function() {
    // Handle question type change
    $('#question_type').change(function() {
        handleQuestionTypeChange(this.value);
    });
});

function handleQuestionTypeChange(type) {
    if (type === 'mcq') {
        $('#mcq_options').show();
        $('#answer_field').hide();
    } else {
        $('#mcq_options').hide();
        $('#answer_field').show();
    }
}

function addOption() {
    const container = $('#options_container');
    const newOption = `
        <div class="option-item mb-2">
            <div class="input-group">
                <div class="input-group-text">
                    <input type="radio" name="correct_option" value="${optionCount}">
                </div>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount + 1}">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.append(newOption);
    optionCount++;
}

function removeOption(button) {
    $(button).closest('.option-item').remove();
}
</script>
@endsection
