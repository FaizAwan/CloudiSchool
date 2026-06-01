@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Question Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('question-bank.index') }}">Question Bank</a></li>
            <li class="breadcrumb-item active">Question Details</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Question Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('question-bank.edit', $question->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Question
                        </a>
                        <a href="{{ route('question-bank.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bank
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Subject:</strong> 
                            <span class="badge badge-info">{{ $question->subject->subject_name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Class Level:</strong> 
                            <span class="badge badge-secondary">Class {{ $question->class_level ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Question Type:</strong> 
                            <span class="badge badge-primary">{{ strtoupper($question->question_type) }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Difficulty:</strong> 
                            <span class="badge badge-{{ $question->difficulty_level === 'easy' ? 'success' : ($question->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($question->difficulty_level) }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Default Marks:</strong> 
                            <span class="badge badge-dark">{{ $question->default_marks ?? $question->marks }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Created By:</strong> {{ $question->creator->name ?? 'Unknown' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Usage Count:</strong> 
                            <span class="badge badge-success">{{ $question->usage_count ?? 0 }} times</span>
                        </div>
                    </div>
                    
                    @if($question->topic || $question->chapter)
                    <div class="row mb-3">
                        @if($question->topic)
                        <div class="col-md-6">
                            <strong>Topic:</strong> {{ $question->topic }}
                        </div>
                        @endif
                        @if($question->chapter)
                        <div class="col-md-6">
                            <strong>Chapter:</strong> {{ $question->chapter }}
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Question</h5>
                        </div>
                        <div class="card-body">
                            <div class="question-text" style="background: #f8f9fa; padding: 20px; border-radius: 5px; font-size: 16px; line-height: 1.6;">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>
                        </div>
                    </div>
                    
                    @if($question->question_type === 'mcq' && $question->mcqOptions && $question->mcqOptions->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Answer Options</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($question->mcqOptions as $index => $option)
                                <div class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : '' }}">
                                    <strong>{{ chr(65 + $index) }}.</strong> {{ $option->option_text }}
                                    @if($option->is_correct)
                                        <span class="badge badge-success float-right">Correct Answer</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($question->correct_answer)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Answer/Explanation</h5>
                        </div>
                        <div class="card-body">
                            <div class="answer-text" style="background: #e8f5e8; padding: 15px; border-radius: 5px;">
                                {!! nl2br(e($question->correct_answer)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($question->explanation)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Explanation</h5>
                        </div>
                        <div class="card-body">
                            <div class="explanation-text">
                                {!! nl2br(e($question->explanation)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($question->tags)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tags</h5>
                        </div>
                        <div class="card-body">
                            @foreach(explode(',', $question->tags) as $tag)
                                <span class="badge badge-secondary mr-1">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Created: {{ $question->created_at ? $question->created_at->format('M d, Y g:i A') : 'Unknown' }}
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                Last Updated: {{ $question->updated_at ? $question->updated_at->format('M d, Y g:i A') : 'Unknown' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
