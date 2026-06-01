@extends('layouts.app')

@section('styles')
<style>
.form-wizard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
}

.wizard-step {
    transition: all 0.3s ease;
}

.wizard-step.active {
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    border-radius: 25px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.form-floating > label {
    color: #6c757d;
}

.badge {
    border-radius: 20px;
    padding: 8px 15px;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.question-preview {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.step-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content-center;
    font-weight: bold;
    margin: 0 auto;
}

.step-indicator.completed {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.step-indicator.active {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
}

.step-indicator.pending {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #dee2e6;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: fadeInUp 0.6s ease-out;
}
</style>
@endsection

@section('content')
<div class="pagetitle">
    <h1>Create New Exam</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Exam Details</h5>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('exams.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="exam_name" class="form-label">Exam Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('exam_name') is-invalid @enderror" 
                                       id="exam_name" name="exam_name" value="{{ old('exam_name') }}" 
                                       placeholder="e.g. Class 5 English Monthly Test" required>
                                @error('exam_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="exam_type_id" class="form-label">Exam Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('exam_type_id') is-invalid @enderror" 
                                        id="exam_type_id" name="exam_type_id" required>
                                    <option value="">Select Exam Type</option>
                                    @foreach($examTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->exam_type_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('exam_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" 
                                        id="subject_id" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }} ({{ $subject->subject_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->className }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="teacher_id" class="form-label">Assigned Teacher</label>
                                <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                        id="teacher_id" name="teacher_id">
                                    <option value="">Select Teacher (Optional)</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->teacher_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="exam_date" class="form-label">Exam Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('exam_date') is-invalid @enderror" 
                                       id="exam_date" name="exam_date" value="{{ old('exam_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('exam_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="exam_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('exam_time') is-invalid @enderror" 
                                       id="exam_time" name="exam_time" value="{{ old('exam_time') }}" required>
                                @error('exam_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" 
                                       min="15" max="300" required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_marks') is-invalid @enderror" 
                                       id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}" 
                                       min="1" max="1000" required>
                                @error('total_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="passing_marks" class="form-label">Passing Marks <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('passing_marks') is-invalid @enderror" 
                                       id="passing_marks" name="passing_marks" value="{{ old('passing_marks', 33) }}" 
                                       min="1" required>
                                @error('passing_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="instructions" class="form-label">Instructions for Students</label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                          id="instructions" name="instructions" rows="4" 
                                          placeholder="Enter exam instructions...">{{ old('instructions') }}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_submit" name="auto_submit" value="1" 
                                           {{ old('auto_submit', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_submit">
                                        Auto-submit exam when time expires
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_results" name="show_results" value="1" 
                                           {{ old('show_results') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_results">
                                        Show results to students immediately after submission
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="randomize_questions" name="randomize_questions" value="1" 
                                           {{ old('randomize_questions') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="randomize_questions">
                                        Randomize question order for each student
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-save"></i> Create Exam
                                </button>
                                <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Tips</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Creating an Exam</h6>
                        <ul class="mb-0 small">
                            <li>Fill all required fields marked with *</li>
                            <li>Set exam date at least 1 day in advance</li>
                            <li>Passing marks should be 33% of total marks</li>
                            <li>Duration should be appropriate for question count</li>
                            <li>After creating, add questions to publish the exam</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <h6><i class="bi bi-lightbulb"></i> Best Practices</h6>
                        <ul class="mb-0 small">
                            <li>Use clear and descriptive exam names</li>
                            <li>Provide detailed instructions for students</li>
                            <li>Enable auto-submit to prevent timing issues</li>
                            <li>Consider randomizing questions for fairness</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Ensure dropdowns are working properly
document.addEventListener('DOMContentLoaded', function() {
    // Debugging: Log dropdown values
    console.log('Exam Types count:', document.querySelectorAll('#exam_type_id option').length - 1);
    console.log('Subjects count:', document.querySelectorAll('#subject_id option').length - 1);
    console.log('Classes count:', document.querySelectorAll('#class_id option').length - 1);
    console.log('Teachers count:', document.querySelectorAll('#teacher_id option').length - 1);
    
    // Add change event listeners to dropdowns for debugging
    document.getElementById('exam_type_id').addEventListener('change', function() {
        console.log('Exam type selected:', this.value, this.options[this.selectedIndex].text);
    });
    
    document.getElementById('subject_id').addEventListener('change', function() {
        console.log('Subject selected:', this.value, this.options[this.selectedIndex].text);
    });
    
    document.getElementById('class_id').addEventListener('change', function() {
        console.log('Class selected:', this.value, this.options[this.selectedIndex].text);
    });
    
    // Auto-calculate passing marks when total marks change
    document.getElementById('total_marks').addEventListener('input', function() {
        const totalMarks = parseInt(this.value);
        if (totalMarks > 0) {
            const passingMarks = Math.ceil(totalMarks * 0.33); // 33% passing
            document.getElementById('passing_marks').value = passingMarks;
        }
    });
    
    // Validate passing marks doesn't exceed total marks
    document.getElementById('passing_marks').addEventListener('input', function() {
        const totalMarks = parseInt(document.getElementById('total_marks').value);
        const passingMarks = parseInt(this.value);
        
        if (passingMarks > totalMarks) {
            this.setCustomValidity('Passing marks cannot exceed total marks');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const examType = document.getElementById('exam_type_id').value;
        const subject = document.getElementById('subject_id').value;
        const classId = document.getElementById('class_id').value;
        
        if (!examType) {
            alert('Please select an exam type');
            e.preventDefault();
            return false;
        }
        
        if (!subject) {
            alert('Please select a subject');
            e.preventDefault();
            return false;
        }
        
        if (!classId) {
            alert('Please select a class');
            e.preventDefault();
            return false;
        }
        
        console.log('Form submitted with:', { examType, subject, classId });
    });
});
</script>
@endsection
