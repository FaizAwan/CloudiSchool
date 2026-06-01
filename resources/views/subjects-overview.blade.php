@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header" style="background: #1488CC; color:#fff; font-weight:bold;">
            📚 Pakistani Educational System - Subjects Overview
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Complete Pakistani Curriculum Subjects Added!</strong><br>
                This overview shows all subjects configured for each class according to the Pakistani educational system.
            </div>

            @foreach($classes as $class)
                @if(isset($subjectsByClass[$class->className]) && count($subjectsByClass[$class->className]) > 0)
                <div class="class-section mb-4">
                    <h5 class="border-bottom pb-2 mb-3" style="color: #2c3e50;">
                        <i class="bi bi-journal-bookmark-fill me-2"></i>
                        Class {{ $class->className }}
                        <span class="badge bg-primary ms-2">{{ count($subjectsByClass[$class->className]) }} subjects</span>
                    </h5>
                    
                    <div class="row g-2">
                        @foreach($subjectsByClass[$class->className] as $subject)
                            @php
                                // Determine subject category for color coding
                                $category = 'default';
                                $subjectName = strtolower($subject->subject_name);
                                
                                if (str_contains($subjectName, 'islamiyat') || str_contains($subjectName, 'tarjma') || 
                                    str_contains($subjectName, 'quran') || str_contains($subjectName, 'hadith') || 
                                    str_contains($subjectName, 'islamic') || str_contains($subjectName, 'seerat')) {
                                    $category = 'islamic';
                                } elseif (str_contains($subjectName, 'english') || str_contains($subjectName, 'urdu')) {
                                    $category = 'languages';
                                } elseif (str_contains($subjectName, 'mathematics') || str_contains($subjectName, 'physics') || 
                                         str_contains($subjectName, 'chemistry') || str_contains($subjectName, 'biology') || 
                                         str_contains($subjectName, 'science') || str_contains($subjectName, 'computer')) {
                                    $category = 'sciences';
                                } elseif (str_contains($subjectName, 'history') || str_contains($subjectName, 'geography') || 
                                         str_contains($subjectName, 'pakistan studies') || str_contains($subjectName, 'civics') || 
                                         str_contains($subjectName, 'social')) {
                                    $category = 'social';
                                } elseif (str_contains($subjectName, 'art') || str_contains($subjectName, 'drawing') || 
                                         str_contains($subjectName, 'physical') || str_contains($subjectName, 'health')) {
                                    $category = 'arts';
                                }
                            @endphp
                            <div class="col-md-4 col-lg-3">
                                <div class="subject-card p-2 border rounded h-100" 
                                     style="background: 
                                        @if($category === 'islamic') linear-gradient(135deg, #d4edda, #c3e6cb)
                                        @elseif($category === 'languages') linear-gradient(135deg, #cce5ff, #b3d7ff)
                                        @elseif($category === 'sciences') linear-gradient(135deg, #fff3cd, #fce4a6)
                                        @elseif($category === 'social') linear-gradient(135deg, #f8d7da, #f1b2b7)
                                        @elseif($category === 'arts') linear-gradient(135deg, #e2e3e5, #d6d8db)
                                        @else linear-gradient(135deg, #f8f9fa, #e9ecef)
                                        @endif;
                                        border-color: 
                                        @if($category === 'islamic') #28a745 !important
                                        @elseif($category === 'languages') #007bff !important
                                        @elseif($category === 'sciences') #ffc107 !important
                                        @elseif($category === 'social') #dc3545 !important
                                        @elseif($category === 'arts') #6c757d !important
                                        @else #dee2e6 !important
                                        @endif;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong style="font-size: 13px; color: #2c3e50;">{{ $subject->subject_name }}</strong>
                                        @if($category === 'islamic')
                                            <i class="bi bi-star-fill text-success"></i>
                                        @elseif($category === 'languages')
                                            <i class="bi bi-translate text-primary"></i>
                                        @elseif($category === 'sciences')
                                            <i class="bi bi-calculator text-warning"></i>
                                        @elseif($category === 'social')
                                            <i class="bi bi-globe text-danger"></i>
                                        @elseif($category === 'arts')
                                            <i class="bi bi-palette text-secondary"></i>
                                        @else
                                            <i class="bi bi-book text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        <div><i class="bi bi-award me-1"></i>Total: {{ $subject->total_marks }} marks</div>
                                        <div><i class="bi bi-check-circle me-1"></i>Pass: {{ $subject->passing_marks }} marks</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            <div class="mt-4">
                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle-fill me-2"></i>Subject Categories Added:</h6>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-star-fill text-success me-2"></i>
                                <strong>Islamic Studies:</strong> Islamiyat, Tarjma-tul-Quran, Quranic Studies, Hadith, Islamic Ethics, Seerat-un-Nabi
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-translate text-primary me-2"></i>
                                <strong>Languages:</strong> English, Urdu, Arabic, Persian
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calculator text-warning me-2"></i>
                                <strong>Sciences & Math:</strong> Mathematics, Physics, Chemistry, Biology, Computer Science, General Science
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-globe text-danger me-2"></i>
                                <strong>Social Studies:</strong> Pakistan Studies, Geography, History, Civics, Social Studies, Political Science
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-palette text-secondary me-2"></i>
                                <strong>Arts & Physical:</strong> Health & Physical Education, Fine Arts, Art & Drawing, Home Economics
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-book text-muted me-2"></i>
                                <strong>Others:</strong> Environmental Science, Life Skills, Information Technology, Agriculture, Business Studies
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ url('/manual-exams') }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square me-2"></i>Go to Manual Exams
                </a>
                <a href="{{ url('/subjects') }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-gear me-2"></i>Manage Subjects
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.subject-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.subject-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.class-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}
</style>
@endsection