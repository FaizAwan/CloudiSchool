@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Add Question to Bank</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('question-bank.index') }}">Question Bank</a></li>
            <li class="breadcrumb-item active">Add Question</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add New Question to Bank</h5>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('question-bank.store') }}" method="POST" id="questionForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">Subject *</label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" name="subject_id" id="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="class_level" class="form-label">Class Level *</label>
                                <select class="form-select @error('class_level') is-invalid @enderror" name="class_level" id="class_level" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_level') == $class->id ? 'selected' : '' }}>
                                            {{ $class->className }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="question_type" class="form-label">Question Type *</label>
                                <select class="form-select @error('question_type') is-invalid @enderror" name="question_type" id="question_type" required>
                                    <option value="">Select Type</option>
                                    <option value="mcq" {{ old('question_type') == 'mcq' ? 'selected' : '' }}>Multiple Choice (MCQ)</option>
                                    <option value="short" {{ old('question_type') == 'short' ? 'selected' : '' }}>Short Answer</option>
                                    <option value="long" {{ old('question_type') == 'long' ? 'selected' : '' }}>Long Answer</option>
                                    <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                                    <option value="fill_blank" {{ old('question_type') == 'fill_blank' ? 'selected' : '' }}>Fill in Blank</option>
                                </select>
                                @error('question_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="difficulty_level" class="form-label">Difficulty Level *</label>
                                <select class="form-select @error('difficulty_level') is-invalid @enderror" name="difficulty_level" id="difficulty_level" required>
                                    <option value="">Select Difficulty</option>
                                    <option value="easy" {{ old('difficulty_level') == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty_level') == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                @error('difficulty_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="marks" class="form-label">Marks *</label>
                                <input type="number" class="form-control @error('marks') is-invalid @enderror" 
                                       name="marks" id="marks" min="1" max="100" value="{{ old('marks', 1) }}" required>
                                @error('marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text *</label>
                            <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                      name="question_text" id="question_text" rows="4" required>{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- MCQ Options Section -->
                        <div id="mcq_options" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Answer Options *</label>
                                <div id="options_container">
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="option-item mb-2">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input type="radio" name="correct_option" value="{{ $i }}" 
                                                           {{ old('correct_option') == $i ? 'checked' : '' }}>
                                                </div>
                                                <input type="text" class="form-control" name="options[]" 
                                                       placeholder="Option {{ chr(65 + $i) }}" 
                                                       value="{{ old('options.' . $i) }}">
                                                @if($i >= 2)
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption()" id="add_option_btn">
                                    <i class="bi bi-plus"></i> Add Option
                                </button>
                                @error('options')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('correct_option')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="explanation" class="form-label">Explanation (Optional)</label>
                            <textarea class="form-control @error('explanation') is-invalid @enderror" 
                                      name="explanation" id="explanation" rows="3" 
                                      placeholder="Provide explanation or solution">{{ old('explanation') }}</textarea>
                            @error('explanation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags (Optional)</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   name="tags" id="tags" 
                                   placeholder="Enter tags separated by commas (e.g., algebra, equations, chapter1)" 
                                   value="{{ old('tags') }}">
                            <div class="form-text">Tags help in categorizing and searching questions</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Add to Bank
                            </button>
                            <a href="{{ route('question-bank.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    let optionCount = 4;

    document.getElementById('question_type').addEventListener('change', function() {
        const mcqOptions = document.getElementById('mcq_options');
        if (this.value === 'mcq') {
            mcqOptions.style.display = 'block';
            // Make options required
            document.querySelectorAll('input[name="options[]"]').forEach(input => {
                input.required = true;
            });
        } else {
            mcqOptions.style.display = 'none';
            // Remove required attribute from options
            document.querySelectorAll('input[name="options[]"]').forEach(input => {
                input.required = false;
            });
        }
    });

    function addOption() {
        if (optionCount >= 6) {
            alert('Maximum 6 options allowed');
            return;
        }

        const container = document.getElementById('options_container');
        const newOption = document.createElement('div');
        newOption.className = 'option-item mb-2';
        newOption.innerHTML = `
            <div class="input-group">
                <div class="input-group-text">
                    <input type="radio" name="correct_option" value="${optionCount}">
                </div>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${String.fromCharCode(65 + optionCount)}" required>
                <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
        container.appendChild(newOption);
        optionCount++;

        if (optionCount >= 6) {
            document.getElementById('add_option_btn').style.display = 'none';
        }
    }

    function removeOption(button) {
        const optionItem = button.closest('.option-item');
        optionItem.remove();
        optionCount--;
        
        document.getElementById('add_option_btn').style.display = 'block';
    }

    // Initialize form based on old input
    document.addEventListener('DOMContentLoaded', function() {
        const questionType = document.getElementById('question_type').value;
        if (questionType === 'mcq') {
            document.getElementById('mcq_options').style.display = 'block';
        }
    });

    // Form validation
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        const questionType = document.getElementById('question_type').value;
        
        if (questionType === 'mcq') {
            const options = document.querySelectorAll('input[name="options[]"]');
            const correctOption = document.querySelector('input[name="correct_option"]:checked');
            
            let filledOptions = 0;
            options.forEach(option => {
                if (option.value.trim()) {
                    filledOptions++;
                }
            });
            
            if (filledOptions < 2) {
                e.preventDefault();
                alert('Please provide at least 2 options for MCQ questions');
                return false;
            }
            
            if (!correctOption) {
                e.preventDefault();
                alert('Please select the correct answer');
                return false;
            }
        }
    });
</script>
@endsection
