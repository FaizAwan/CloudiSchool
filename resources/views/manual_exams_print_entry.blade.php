@extends('layouts.app')

@section('title', 'Manual Exam Marks Entry Sheet')

@section('content')
<style>
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    body {
        font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
        background-color: #f6f9ff;
    }

    .main-container-report {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        padding: 40px;
        margin: 20px auto;
        max-width: 100%;
        border: 1px solid #eef2f7;
    }

    .header-section {
        text-align: center;
        margin-bottom: 30px;
        position: relative;
    }

    .header-section h1 {
        font-size: 26px;
        font-weight: 800;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .header-section .subtitle {
        color: #7f8c8d;
        font-size: 14px;
        font-weight: 500;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .info-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #764ba2;
    }

    .info-card label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #a0aec0;
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .info-card .value {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
    }

    .info-card.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .info-card.primary label { color: rgba(255,255,255,0.7); }
    .info-card.primary .value { color: white; }

    .marks-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11px;
        margin-bottom: 30px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .marks-table th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        padding: 12px 8px;
        text-align: center;
        border-bottom: 2px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
    }

    .marks-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        text-align: center;
        color: #1e293b;
    }

    .student-name-col {
        text-align: left !important;
        font-weight: 600;
        background: #fff;
        position: sticky;
        left: 0;
        z-index: 10;
        min-width: 200px;
        border-right: 2px solid #e2e8f0 !important;
    }

    .marks-cell {
        min-width: 45px;
        background-color: #fff;
    }

    .subject-header {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        height: 120px;
        padding: 10px 5px !important;
    }

    .footer-signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 50px;
        gap: 30px;
    }

    .sig-box {
        flex: 1;
        text-align: center;
        padding-top: 15px;
        border-top: 2px solid #e2e8f0;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }

    .instructions-panel {
        background: #fffcf0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 5px solid #fbbf24;
    }

    .instructions-panel h4 {
        font-size: 14px;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 10px;
    }

    .no-data-card {
        padding: 80px 40px;
        text-align: center;
        background: #fff;
        border-radius: 20px;
        border: 2px dashed #e2e8f0;
    }

    .floating-actions {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        display: flex;
        gap: 10px;
    }

    .btn-glass {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border-radius: 50px;
        padding: 12px 25px;
        font-weight: 700;
        color: #2d3436;
        transition: all 0.3s ease;
    }

    .btn-glass:hover {
        transform: translateY(-5px);
        background: #fff;
    }

    @media print {
        .no-print, .floating-actions, .pagetitle, #header, #sidebar, .btn, .breadcrumb {
            display: none !important;
        }
        
        body { background: white !important; margin: 0; padding: 0; }
        .main-container-report { box-shadow: none !important; border: none !important; margin: 0; padding: 0; }
        .marks-table th { background: #eee !important; color: black !important; -webkit-print-color-adjust: exact; }
        .info-card { background: #f9f9f9 !important; border: 1px solid #ddd !important; }
        .info-card.primary { background: #eee !important; color: black !important; }
        .info-card.primary label, .info-card.primary .value { color: black !important; }
        .instructions-panel { border: 1px solid #eee !important; background: white !important; }
    }
</style>

<div class="floating-actions no-print">
    <button onclick="goBack()" class="btn btn-glass shadow">
        <i class="bi bi-arrow-left me-2"></i> Back
    </button>
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow">
        <i class="bi bi-printer me-2"></i> Print Report
    </button>
</div>

<div class="main-container-report no-print mb-4" style="padding: 20px;">
    <form method="GET" action="{{ request()->getBaseUrl() }}/manual-exams/print-entry" id="examFiltersForm">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Filter by Class:</label>
                <select class="form-select form-select-sm" name="class_id" id="filterClass" onchange="this.form.submit()">
                    <option value="">-- Select Class --</option>
                    @foreach(($classesForTeacher ?? []) as $c)
                    <option value="{{ $c->id }}" {{ ($selectedClassId ?? null)==$c->id ? 'selected' : '' }}>{{ $c->className }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Filter by Section:</label>
                <select class="form-select form-select-sm" name="section" id="filterSection" onchange="this.form.submit()">
                    <option value="">-- All Sections --</option>
                    @if(!empty($availableSections))
                    @foreach($availableSections as $sec)
                    <option value="{{ $sec }}" {{ ($selectedSection ?? '')===$sec ? 'selected' : '' }}>{{ $sec }}</option>
                    @endforeach
                    @else
                    @foreach(($allSections ?? []) as $sec)
                    <option value="{{ $sec }}" {{ ($selectedSection ?? '')===$sec ? 'selected' : '' }}>{{ $sec }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Filter by Term:</label>
                <select class="form-select form-select-sm" name="term" id="filterTerm" onchange="this.form.submit()">
                    @foreach(($availableTerms ?? []) as $termValue => $termLabel)
                    <option value="{{ $termValue }}" {{ ($term ?? '')===$termValue ? 'selected' : '' }}>{{ $termLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                    <i class="bi bi-filter me-1"></i> Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

<div class="main-container-report">
    <div class="header-section">
        <h1>Manual Exam Marks Entry Sheet</h1>
        <div class="subtitle">Academic Assessment System &bullet; CloudiSchool</div>
    </div>

    <div class="info-grid">
        <div class="info-card primary">
            <label>Current Class</label>
            <div class="value">
                @if($selectedClassId)
                {{ DB::table('classes')->where('id', $selectedClassId)->value('className') ?? 'N/A' }}
                @else
                N/A
                @endif
            </div>
        </div>
        <div class="info-card">
            <label>Section</label>
            <div class="value">{{ $selectedSection ?: 'All Sections' }}</div>
        </div>
        <div class="info-card">
            <label>Exam Term</label>
            <div class="value">{{ $term ?? 'N/A' }}</div>
        </div>
        <div class="info-card">
            <label>Academic Session</label>
            <div class="value">{{ $sessionValue ?: '2024-2025' }}</div>
        </div>
        <div class="info-card">
            <label>Generated Date</label>
            <div class="value">{{ date('d M, Y') }}</div>
        </div>
    </div>

    @if($classSubjectsForEntry->isNotEmpty() && $students->isNotEmpty())
    <div class="table-responsive">
        <table class="marks-table">
            <thead>
                <tr>
                    <th rowspan="2" class="student-name-col">Student Identity</th>
                    @foreach($classSubjectsForEntry as $subject)
                    <th rowspan="2" class="subject-header">
                        <div style="margin-bottom: 8px;">{{ $subject->subject_name }}</div>
                        <span class="badge bg-white text-dark rounded-pill">{{ $subject->total_marks ?? 100 }}</span>
                    </th>
                    @endforeach
                    <th colspan="3">Attendance</th>
                    <th rowspan="2">Imp. Studies</th>
                    <th rowspan="2">Grade</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <th>W.D</th>
                    <th>Pres</th>
                    <th>Abs</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                @php $existingData = $existingByStudentId[$student->grno] ?? []; @endphp
                <tr>
                    <td class="student-name-col">
                        <div class="text-primary small fw-bold">#{{ $student->grno }}</div>
                        <div class="text-dark">{{ $student->studentName }}</div>
                    </td>
                    @foreach($classSubjectsForEntry as $subject)
                    @php
                    $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name));
                    $mark = $existingData[$fieldKey] ?? '';
                    @endphp
                    <td class="marks-cell">
                        <span class="text-muted fw-bold">{{ $mark ?: '---' }}</span>
                    </td>
                    @endforeach
                    <td>{{ $existingData['total_working_days'] ?? '---' }}</td>
                    <td>{{ $existingData['total_present'] ?? '---' }}</td>
                    <td>{{ $existingData['total_absent'] ?? '---' }}</td>
                    <td>{{ $existingData['improvement_studies'] ?? '' }}</td>
                    <td><span class="fw-bold">{{ $existingData['overall_grade'] ?? '' }}</span></td>
                    <td style="width: 100px;"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="instructions-panel">
        <h4><i class="bi bi-file-earmark-check me-2"></i>Assessment Instructions</h4>
        <div class="row small">
            <div class="col-md-6">
                <ul class="mb-0">
                    <li>Entry should be made in blue/black ink only.</li>
                    <li>Passing criteria: 40% (C grade) for all subjects.</li>
                    <li>Manual corrections must be countersigned.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="mb-0">
                    <li>Working Days (W.D) should reflect the entire term.</li>
                    <li>Leave 'Remarks' for unique student observations.</li>
                    <li>Ensure GR numbers match student profiles.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-signatures">
        <div class="sig-box text-uppercase">Class Teacher</div>
        <div class="sig-box text-uppercase">Exam Coordinator</div>
        <div class="sig-box text-uppercase">Head of Institution</div>
    </div>

    @else
    <div class="no-data-card">
        <div class="display-1 text-muted mb-4"><i class="bi bi-clipboard-x"></i></div>
        <h2 class="fw-bold text-dark mb-3">No Records Found</h2>
        <p class="text-muted mb-5">We couldn't find any student data matching your current class and section selection.<br>Please use the filters to load specific records.</p>
        <a href="{{ url('/manual-exams') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
            <i class="bi bi-arrow-repeat me-2"></i> Return to Filters
        </a>
    </div>
    @endif
</div>

<script>
    function goBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '{{ url("/manual-exams") }}';
        }
    }
</script>
@endsection
on