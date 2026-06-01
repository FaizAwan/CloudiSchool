@extends('layouts.app')

@section('content')
<style>
  /* Consolidated Professional Styling */
  .text-left {
    text-align: left !important;
  }

  /* Aggressive Compact View - No Horizontal Scroll */
  body {
    overflow-x: auto !important;
  }

  .container.my-4 {
    max-width: 100% !important;
    width: 100% !important;
    padding: 5px !important;
    margin: 0 !important;
  }

  .card {
    overflow: hidden;
    margin: 0 !important;
  }

  .card-header {
    padding: 8px 10px !important;
    font-size: 14px !important;
  }

  .card-body {
    padding: 8px !important;
  }

  /* Compact alerts */
  .alert {
    padding: 6px 10px !important;
    margin-bottom: 8px !important;
    font-size: 11px !important;
  }

  .alert .badge {
    font-size: 9px !important;
    padding: 2px 4px !important;
  }

  /* Compact filters */
  .row.mb-3 {
    margin-bottom: 8px !important;
  }

  .col-md-4 {
    padding-left: 3px !important;
    padding-right: 3px !important;
  }

  .form-label {
    margin-bottom: 3px !important;
    font-size: 11px !important;
  }

  .form-control,
  .form-select {
    padding: 4px 6px !important;
    font-size: 11px !important;
    height: auto !important;
  }

  /* Compact tabs */
  .nav-tabs {
    margin-bottom: 8px !important;
  }

  .nav-tabs .nav-link {
    padding: 6px 10px !important;
    font-size: 11px !important;
  }

  .tab-content {
    margin-top: 0 !important;
  }

  .mt-3 {
    margin-top: 8px !important;
  }

  .mb-3 {
    margin-bottom: 8px !important;
  }

  /* Hyper-compact table */
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 8px !important;
    padding: 0 !important;
    max-width: 100%;
  }

  #marksEntryTable {
    margin: 0 !important;
    font-size: 9px !important;
    width: 100% !important;
  }

  #marksEntryTable th,
  #marksEntryTable td {
    padding: 2px 1px !important;
    white-space: nowrap;
    font-size: 9px !important;
    line-height: 1.2 !important;
    border: 1px solid #dee2e6 !important;
  }

  #marksEntryTable th {
    padding: 3px 2px !important;
  }

  #marksEntryTable input[type="number"],
  #marksEntryTable input[type="text"] {
    width: 38px !important;
    min-width: 35px !important;
    max-width: 42px !important;
    font-size: 8px !important;
    padding: 1px 2px !important;
    margin: 0 !important;
    height: 20px !important;
  }

  /* Specific sizes for regularity fields */
  #marksEntryTable input[placeholder="Days"],
  #marksEntryTable input[placeholder="Present"],
  #marksEntryTable input[placeholder="Absent"] {
    width: 32px !important;
    min-width: 30px !important;
  }

  #marksEntryTable th.reg-dy,
  #marksEntryTable th.reg-p,
  #marksEntryTable th.reg-a {
    width: 8px !important;
    text-align: center;
    padding: 0 !important;
    font-size: 6px !important;
  }

  #marksEntryTable td.reg-td {
    width: 8px !important;
    padding: 0 !important;
    overflow: hidden !important;
  }

  #marksEntryTable input[name*="[total_working_days]"],
  #marksEntryTable input[name*="[total_present]"],
  #marksEntryTable input[name*="[total_absent]"] {
    width: 8px !important;
    height: 12px !important;
    font-size: 6px !important;
    line-height: 1 !important;
    text-align: center;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
  }

  #marksEntryTable input[placeholder="Studies"] {
    width: 50px !important;
    min-width: 45px !important;
  }

  #marksEntryTable input[placeholder="Grade"] {
    width: 20px !important;
  }

  #marksEntryTable th small {
    font-size: 8px !important;
    display: block;
  }

  /* Compact buttons */
  .btn-sm {
    font-size: 10px !important;
    padding: 4px 6px !important;
    line-height: 1.2 !important;
  }

  .btn-group {
    gap: 2px !important;
  }

  .d-flex {
    gap: 4px !important;
  }

  .btn.btn-primary {
    font-size: 11px !important;
    padding: 5px 10px !important;
  }

  #marksEntryTable .btn-info {
    font-size: 8px !important;
    padding: 2px 4px !important;
  }

  /* Student Report - Compact */
  #studentReport {
    max-width: 100%;
    overflow-x: auto;
    padding: 8px !important;
  }

  #studentReport .table {
    font-size: 10px !important;
    margin-bottom: 8px !important;
  }

  #studentReport .table th,
  #studentReport .table td {
    padding: 3px 4px !important;
    font-size: 10px !important;
  }

  #studentReport h6 {
    font-size: 12px !important;
    margin-bottom: 6px !important;
  }

  #studentReport .border {
    padding: 6px !important;
  }

  #studentReport .row {
    margin-left: -3px !important;
    margin-right: -3px !important;
  }

  #studentReport .col,
  #studentReport [class*="col-"] {
    padding-left: 3px !important;
    padding-right: 3px !important;
  }

  /* Form in report tab */
  #reportSearchForm .form-control,
  #reportSearchForm .form-label {
    font-size: 11px !important;
  }

  #reportSearchForm label {
    margin-bottom: 2px !important;
  }

  /* Extremely compact for all screen sizes */
  @media (min-width: 768px) {
    .container.my-4 {
      max-width: 100% !important;
    }
  }

  @media (max-width: 1600px) {
    #marksEntryTable {
      font-size: 8px !important;
    }

    #marksEntryTable th,
    #marksEntryTable td {
      padding: 1px !important;
      font-size: 8px !important;
    }

    #marksEntryTable input[type="number"],
    #marksEntryTable input[type="text"] {
      width: 40px !important;
      min-width: 38px !important;
      max-width: 45px !important;
      font-size: 8px !important;
      padding: 1px 2px !important;
      height: 20px !important;
    }
  }

  @media (max-width: 1400px) {
    #marksEntryTable th small {
      font-size: 7px !important;
    }

    #marksEntryTable input[type="number"],
    #marksEntryTable input[type="text"] {
      width: 38px !important;
      min-width: 35px !important;
    }
  }

  @media (max-width: 1200px) {
    .col-md-4 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 6px !important;
    }
  }

  /* Text truncation for long names */
  #marksEntryTable td.text-start {
    min-width: 280px !important;
    max-width: none !important;
    width: 280px !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: nowrap !important;
    font-size: 8px !important;
  }

  /* Reduce space in improvement section */
  .subjects-improvement-section {
    padding: 8px !important;
    margin: 6px 0 !important;
  }

  .subjects-improvement-section h6 {
    margin-bottom: 6px !important;
    font-size: 11px !important;
  }

  /* Old student meta styles - keep for compatibility */
  .student-meta {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px 12px;
  }

  .meta-item {
    display: grid;
    grid-template-columns: 110px 1fr;
    align-items: center;
  }

  .meta-label {
    color: #6b7280;
  }

  /* New prominent student metadata styles */
  .student-meta-new {
    border: 2px solid #007bff !important;
    background-color: #f8f9fa !important;
    border-radius: 8px;
  }

  .meta-item-prominent {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 32px;
  }

  .meta-label-prominent {
    font-weight: 700;
    font-size: 14px;
    color: #2c3e50;
    min-width: 120px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .meta-value-prominent {
    font-weight: 600;
    font-size: 15px;
    color: #1a1a1a;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  /* Dynamic subjects table styling */
  .table th {
    background-color: #f8f9fa;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    vertical-align: middle;
    border: 1px solid #dee2e6;
    padding: 8px 4px;
  }

  .table td {
    text-align: center;
    vertical-align: middle;
    border: 1px solid #dee2e6;
    padding: 4px;
  }

  .table input[type="number"],
  .table input[type="text"] {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 2px 4px;
    text-align: center;
    font-size: 12px;
  }

  .table input[type="number"]:focus,
  .table input[type="text"]:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    outline: 0;
  }

  /* Color-coded marks input styles */
  .marks-input-fail {
    border-color: #dc3545 !important;
    background-color: #f8d7da !important;
    color: #721c24 !important;
    font-weight: bold !important;
  }

  .marks-input-pass {
    border-color: #28a745 !important;
    background-color: #d4edda !important;
    color: #155724 !important;
    font-weight: bold !important;
  }

  .marks-input-exceed {
    border-color: #dc3545 !important;
    background-color: #f8d7da !important;
    color: #721c24 !important;
    font-weight: bold !important;
    animation: shake 0.5s ease-in-out;
  }

  @keyframes shake {

    0%,
    100% {
      transform: translateX(0);
    }

    25% {
      transform: translateX(-2px);
    }

    75% {
      transform: translateX(2px);
    }
  }

  .table .text-start {
    text-align: left !important;
    font-weight: 500;
  }

  /* Professional Absent Subject Styles */
  .disabled-absent {
    background-color: #f8f9fa !important;
    border-color: #6c757d !important;
    color: #6c757d !important;
    cursor: not-allowed !important;
    text-decoration: line-through;
  }

  .absent-checkbox {
    transform: scale(1.2);
    cursor: pointer;
  }

  .absent-checkbox:checked {
    background-color: #dc3545;
    border-color: #dc3545;
  }

  .absent-checkbox:checked+.form-check-label {
    color: #dc3545;
    font-weight: bold;
  }

  .absent-indicator {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: bold;
    display: inline-block;
  }

  .improvement-critical {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    display: inline-block;
    margin: 2px;
  }

  .improvement-high {
    background: linear-gradient(135deg, #fd7e14, #e96b00);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    display: inline-block;
    margin: 2px;
  }

  .improvement-medium {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #212529;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    display: inline-block;
    margin: 2px;
  }

  .subjects-improvement-section {
    background: linear-gradient(135deg, #fff3cd, #fce4a6);
    border: 2px solid #ffc107;
    border-radius: 10px;
    padding: 15px;
    margin: 10px 0;
  }

  .subjects-improvement-section h6 {
    color: #856404;
    font-weight: bold;
    margin-bottom: 10px;
  }

  @media print {
    body * {
      visibility: hidden;
    }

    #studentReport,
    #studentReport * {
      visibility: visible;
    }

    #studentReport {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }

    @page {
      size: A4 portrait;
      margin: 3mm;
    }

    #studentReport {
      width: 204mm !important;
      padding: 2mm !important;
      border: none !important;
      margin: 0 !important;
    }

    .container,
    .container-fluid,
    .row,
    [class^="col-"],
    [class*=" col-"] {
      padding-left: 0 !important;
      padding-right: 0 !important;
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    .row {
      --bs-gutter-x: 0 !important;
    }

    #studentReport,
    #studentReport * {
      font-size: 9px !important;
      line-height: 1.1 !important;
    }

    #studentReport table th,
    #studentReport table td {
      padding: 2px 3px !important;
      font-size: 8px !important;
    }

    #studentReport .section-title {
      margin: 3px 0 2px 0 !important;
      font-size: 10px !important;
    }

    #studentReport .border {
      border-width: 1px !important;
    }

    #studentReport .mt-3,
    #studentReport .mt-2,
    #studentReport .mb-3,
    #studentReport .mb-2 {
      margin: 2px 0 !important;
    }

    #studentReport .pt-3,
    #studentReport .pb-3 {
      padding: 2px 0 !important;
    }

    #studentReport .p-2,
    #studentReport .p-3 {
      padding: 2px !important;
    }

    #studentReport img {
      max-width: 70px !important;
      max-height: 80px !important;
    }

    .nav,
    .btn,
    form[action$="manual-exams"],
    .nav-tabs,
    .edit-only {
      display: none !important;
    }

    .student-meta-new {
      border: 1px solid #000 !important;
      background-color: #f8f9fa !important;
      margin: 2px 0 !important;
      padding: 3px !important;
    }

    .student-meta-new strong {
      font-weight: 700 !important;
      font-size: 8px !important;
      color: #000 !important;
    }

    .student-meta-new .col-6 {
      font-size: 8px !important;
      line-height: 1.2 !important;
    }

    #studentReport h6,
    #studentReport .section-title {
      font-size: 9px !important;
      margin: 1px 0 !important;
    }

    #studentReport .row.g-3 {
      margin: 2px 0 !important;
    }

    #studentReport .row.g-3 .col {
      padding: 0 2px !important;
    }

    #studentReport .row.g-3 .border {
      min-height: 25px !important;
      padding: 1px !important;
    }

    #studentReport .row.g-2 .col {
      padding: 0 1px !important;
    }

    #studentReport .row.g-2 .border {
      padding: 1px !important;
      font-size: 7px !important;
    }

    #studentReport .row.g-2 .col-6 .border {
      padding: 2px !important;
      font-size: 7px !important;
    }

    #studentReport .mt-4 {
      margin-top: 3px !important;
    }
  }
</style>
<div class="container my-4">
  <div class="card">
    <div class="card-header" style="background: #1488CC; color:#fff; font-weight:bold;">Manual Exam Management</div>
    <div class="card-body">
      @if (session('success'))
      <div id="alert-success" class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if (session('error'))
      <div id="alert-error" class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if (session('message'))
      <div id="alert-message" class="alert alert-primary">{{ session('message') }}</div>
      @endif

      <form method="GET" action="{{ request()->getBaseUrl() }}/manual-exams" id="examFiltersForm">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">Filter by Class:</label>
            <select class="form-control" name="class_id" id="filterClass">
              <option value="">-- Select Class --</option>
              @foreach(($classesForTeacher ?? []) as $c)
              <option value="{{ $c->id }}" {{ ($selectedClassId ?? null)==$c->id ? 'selected' : '' }}>{{ $c->className }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Filter by Section:</label>
            <select class="form-control" name="section" id="filterSection">
              <option value="">-- All Sections --</option>
              @if(!empty($availableSections))
              @foreach($availableSections as $section)
              <option value="{{ $section }}" {{ ($selectedSection ?? '')===$section ? 'selected' : '' }}>{{ $section }}</option>
              @endforeach
              @else
              @foreach(($allSections ?? []) as $section)
              <option value="{{ $section }}" {{ ($selectedSection ?? '')===$section ? 'selected' : '' }}>{{ $section }}</option>
              @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Filter by Term:</label>
            <select class="form-control" name="term" id="filterTerm">
              @foreach(($availableTerms ?? []) as $termValue => $termLabel)
              <option value="{{ $termValue }}" {{ ($term ?? '')===$termValue ? 'selected' : '' }}>{{ $termLabel }}</option>
              @endforeach
            </select>
          </div>
        </div>
        @if(!$selectedClassId)
        <div class="alert alert-info">Select a class to load students.</div>
        @endif
        <input type="hidden" name="tab" value="{{ request()->get('tab', (($selectedStudentId ?? 0) ? 'report' : 'entry')) }}" />
      </form>

      @php
      $isReportTab = (request()->get('tab') === 'report') || (($selectedStudentId ?? 0) > 0);
      // Helper function to format numbers cleanly (remove unnecessary decimal zeros)
      function formatNumber($number) {
      return $number == floor($number) ? intval($number) : rtrim(rtrim(number_format($number, 3), '0'), '.');
      }

      // Helper function to abbreviate subject names
      function abbreviateSubject($subjectName) {
      $abbreviations = [
      'English' => 'EN',
      'Urdu' => 'UD',
      'Tarjatul Quran' => 'TQ',
      'Tarjuma Tul Quran' => 'TQ',
      'Mathematics' => 'MT',
      'Maths' => 'MT',
      'Science' => 'SC',
      'General Science' => 'GS',
      'Physics' => 'PH',
      'Chemistry' => 'CH',
      'Biology' => 'BI',
      'Computer Science' => 'CS',
      'Computer' => 'CM',
      'Social Studies' => 'SS',
      'Islamiat' => 'IS',
      'Islamic Studies' => 'IS',
      'Pakistan Studies' => 'PS',
      'General Knowledge' => 'GK',
      'History' => 'HI',
      'Geography' => 'GE',
      'Civics' => 'CV',
      'Economics' => 'EC',
      'Drawing' => 'DR',
      'Art' => 'AR',
      'Nazra' => 'NZ',
      'Qaida' => 'QD',
      'Arabic' => 'AB',
      ];

      // Check if exact match exists
      if (isset($abbreviations[$subjectName])) {
      return $abbreviations[$subjectName];
      }

      // Try case-insensitive match
      foreach ($abbreviations as $full => $abbr) {
      if (strcasecmp($subjectName, $full) === 0) {
      return $abbr;
      }
      }

      // If no match, create abbreviation from first letters
      $words = preg_split('/[\s\-]+/', $subjectName);
      if (count($words) > 1) {
      return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
      }

      // Single word - take first 2-3 letters
      return strtoupper(substr($subjectName, 0, 2));
      }
      @endphp

      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link {{ $isReportTab ? '' : 'active' }}" data-bs-toggle="tab" data-bs-target="#tab-entry" type="button" role="tab">Marks Entry</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link {{ $isReportTab ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-report" type="button" role="tab">Student-wise Report</button>
        </li>
      </ul>
      <div class="tab-content mt-3">
        <div class="tab-pane fade {{ $isReportTab ? '' : 'show active' }}" id="tab-entry" role="tabpanel">

          <form method="POST" action="{{ request()->getBaseUrl() }}/manual-exams">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClassId ?? '' }}" />
            <input type="hidden" name="section" value="{{ $selectedSection ?? '' }}" />
            <input type="hidden" name="session" value="{{ $sessionValue ?? '' }}" />
            <input type="hidden" name="term" value="{{ $term ?? '' }}" />

            @if($classSubjectsForEntry->isNotEmpty())
            <div class="alert alert-info mb-3">
              <i class="bi bi-info-circle"></i>
              <strong>Subjects for {{ $term ?? 'this term' }}:</strong>
              @foreach($classSubjectsForEntry as $subject)
              <span class="badge bg-primary ms-1">{{ $subject->subject_name }} ({{ formatNumber($subject->total_marks) }})</span>
              @endforeach
              @if($term && $term === 'Grand Test - Mid Term Exams')
              <span class="badge bg-success ms-2">Term-Specific (50 marks each)</span>
              @endif
              <br><small class="text-muted">Enter marks for each subject. Input max values are set according to selected term.</small>
              <br><small class="text-muted">
                <strong>Color Guide:</strong>
                <span class="marks-input-pass px-1 rounded">Green = ≥50% (Pass)</span> |
                <span class="marks-input-fail px-1 rounded">Red = <50% (Fail)</span>
              </small>
            </div>
            @endif

            <!-- Subject selection (client-side filter; does not alter database subjects) -->
            @if($classSubjectsForEntry->isNotEmpty())
            <div class="card mb-2" style="border-color:#e2e8f0;">
              <div class="card-body p-2">
                <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                  <strong style="font-size:12px;">Subjects to enter:</strong>
                  <div id="subjectFilterList" class="d-flex align-items-center flex-wrap" style="gap:8px;">
                    @foreach($classSubjectsForEntry as $subject)
                    @php $sfKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name)); @endphp
                    <label class="form-check-label" style="font-size:11px;">
                      <input class="form-check-input me-1 subject-filter-checkbox" type="checkbox" value="{{ $sfKey }}" checked>
                      {{ $subject->subject_name }}
                    </label>
                    @endforeach
                  </div>
                  <div class="ms-auto d-flex" style="gap:6px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAllSubjects">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearAllSubjects">Clear</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btnApplySubjectFilter">Apply</button>
                  </div>
                </div>
                <small class="text-muted">This only hides/shows columns for quick data entry. To add/remove subjects at system level, use the Subjects page.</small>
              </div>
            </div>
            @endif

            <div class="table-responsive">
              @if($classSubjectsForEntry->isNotEmpty() && $students->isNotEmpty())
              <div class="table-responsive">
                <table id="marksEntryTable" class="table table-bordered table-sm align-middle text-center w-100 my-3 compact">
                  <thead>
                    <tr>
                      <th rowspan="2" style="min-width: 80px; font-size: 8px;">GR-Student</th>
                      @foreach($classSubjectsForEntry as $subject)
                      @php $headerKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name)); @endphp
                      <th class="subject-col" data-subject-key="{{ $headerKey }}" title="{{ $subject->subject_name }} (Total: {{ formatNumber($subject->total_marks) }}, Pass: {{ formatNumber($subject->passing_marks) }})" style="min-width: 24px; font-size: 8px;">
                        {{ abbreviateSubject($subject->subject_name) }}
                        <br><small style="font-size: 7px;">({{ formatNumber($subject->total_marks) }})</small>
                      </th>
                      @endforeach
                      <th colspan="3" style="font-size: 7px;">REG</th>
                      <th style="font-size: 7px; min-width: 36px;">Improve</th>
                      <th style="font-size: 7px; min-width: 30px;">Behavior</th>
                      <th rowspan="2" style="font-size: 7px; min-width: 28px;">Grade</th>
                    </tr>
                    <tr>
                      @foreach($classSubjectsForEntry as $subject)
                      <th style="width: 38px; font-size: 7px;">Marks</th>
                      @endforeach
                      <th class="reg-dy" style="font-size: 6px; padding:0; margin:0;" title="Days">DY</th>
                      <th class="reg-p" style="font-size: 6px;" title="Present">P</th>
                      <th class="reg-a" style="font-size: 6px;" title="Absent">A</th>
                      <th style="font-size: 6px;">Studies</th>
                      <th style="font-size: 6px;">Attr</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(($students ?? []) as $stu)
                    @php $vals = $existingByStudentId[$stu->grno] ?? []; @endphp
                    <tr>
                      <td class="text-start" title="{{ $stu->grno }} - {{ $stu->studentName }}">{{ $stu->grno }}-{{ $stu->studentName }}</td>
                      @foreach($classSubjectsForEntry as $subject)
                      @php
                      $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name));
                      @endphp
                      <!-- Marks Input Cell -->
                      <td data-subject-key="{{ $fieldKey }}">
                        <input
                          name="entries[{{ $stu->grno }}][{{ $fieldKey }}]"
                          value="{{ $vals[$fieldKey] ?? '' }}"
                          type="text"
                          inputmode="decimal"
                          maxlength="8"
                          data-type="marks-or-absent"
                          max="{{ $subject->total_marks }}"
                          placeholder="/{{ formatNumber($subject->total_marks) }}"
                          title="{{ $subject->subject_name }} - Max: {{ formatNumber($subject->total_marks) }}, Pass: {{ formatNumber($subject->passing_marks) }} | Enter number or 'A' for Absent"
                          class="marks-input" />
                      </td>
                      @endforeach
                      <!-- Regularity Fields -->
                      <td class="reg-td reg-dy-td"><input name="entries[{{ $stu->grno }}][total_working_days]" value="{{ $vals['total_working_days'] ?? '' }}" type="number" min="0" step="0.001" placeholder="DY" title="Working Days" /></td>
                      <td class="reg-td reg-p-td"><input name="entries[{{ $stu->grno }}][total_present]" value="{{ $vals['total_present'] ?? '' }}" type="number" min="0" step="0.001" placeholder="P" title="Present" /></td>
                      <td class="reg-td reg-a-td"><input name="entries[{{ $stu->grno }}][total_absent]" value="{{ $vals['total_absent'] ?? '' }}" type="number" min="0" step="0.001" placeholder="A" title="Absent" /></td>
                      <td><input name="entries[{{ $stu->grno }}][improvement_studies]" value="{{ $vals['improvement_studies'] ?? '' }}" type="text" placeholder="Studies" /></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#attributesModal" data-stu-id="{{ $stu->grno }}" data-stu-name="{{ $stu->grno }} - {{ $stu->studentName }}" title="Set Behavior Attributes (1-5 scale)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        @php
                        $behaviorData = $vals['behavior_attributes'] ?? [];
                        $behaviorSummary = '';
                        if (is_array($behaviorData) && !empty($behaviorData)) {
                        $avgScore = array_sum($behaviorData) / count($behaviorData);
                        if ($avgScore >= 4.5) $behaviorSummary = 'Excellent';
                        elseif ($avgScore >= 3.5) $behaviorSummary = 'Very Good';
                        elseif ($avgScore >= 2.5) $behaviorSummary = 'Good';
                        elseif ($avgScore >= 1.5) $behaviorSummary = 'Satisfactory';
                        else $behaviorSummary = 'Needs Improvement';
                        }
                        @endphp
                        @if($behaviorSummary)
                        <br><small class="text-muted">{{ $behaviorSummary }}</small>
                        @endif
                        <!-- Hidden input to store behavior data -->
                        <input type="hidden" name="entries[{{ $stu->grno }}][behavior_attributes]" id="behaviorData_{{ $stu->grno }}" value="{{ json_encode($behaviorData) }}" />
                      </td>
                      <td><input name="entries[{{ $stu->grno }}][overall_grade]" value="{{ $vals['overall_grade'] ?? '' }}" type="text" placeholder="Grade" /></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center py-5 my-4" style="background: #f8f9fa; border-radius: 12px; border: 2px dashed #dee2e6;">
                <div class="mb-3">
                  <i class="bi bi-journal-x display-1 text-muted"></i>
                </div>
                <h3 class="text-muted">No Records found</h3>
                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                  We couldn't find any data for the selected class, section, or term.
                  Please ensure that subjects are assigned to the class and students are enrolled.
                </p>
                <div class="d-flex justify-content-center gap-2">
                  <a href="{{ route('subjects.index') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-book"></i> Manage Subjects
                  </a>
                  <a href="{{ route('students') }}" class="btn btn-primary px-4">
                    <i class="bi bi-person-plus"></i> View Students
                  </a>
                </div>
                @if($selectedClassId)
                <div class="mt-4">
                  <span class="badge bg-light text-dark border p-2">
                    <i class="bi bi-info-circle me-1"></i> Debug Info: Class ID {{ $selectedClassId }} | Term: {{ $term }}
                  </span>
                </div>
                @endif
              </div>
              @endif
            </div>

            @if($classSubjectsForEntry->isNotEmpty())
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printEntrySheet()">
                  <i class="bi bi-printer"></i> Print Entry Sheet
                </button>
                <a class="btn btn-outline-success btn-sm" href="{{ url('/manual-exams/export-csv') }}?class_id={{ $selectedClassId }}&section={{ urlencode($selectedSection ?? '') }}&session={{ urlencode($sessionValue ?? '') }}&term={{ urlencode($term ?? '') }}">
                  <i class="bi bi-download"></i> Export CSV
                </a>
                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                  <i class="bi bi-upload"></i> Import CSV
                </button>
              </div>
              <button type="button" class="btn btn-warning me-2" onclick="document.getElementById('sheetUpload').click()">
                <i class="bi bi-camera"></i> Upload Marks Sheet (AI)
              </button>
              <input type="file" id="sheetUpload" class="d-none" accept="image/*" onchange="uploadAndAutofill(this)">
              <button class="btn btn-primary" type="submit" onclick="return validateAndSaveEntries()">Save Entries</button>
            </div>
            @endif
          </form>
        </div>

        <div class="tab-pane fade {{ $isReportTab ? 'show active' : '' }}" id="tab-report" role="tabpanel">
          <form class="row g-2 mb-3" method="GET" action="{{ request()->getBaseUrl() }}/manual-exams" id="reportSearchForm">
            <input type="hidden" name="teacher_id" value="" />
            <input type="hidden" name="class_id" value="{{ $selectedClassId ?? '' }}" />
            <input type="hidden" name="section" value="{{ $selectedSection ?? '' }}" />
            <input type="hidden" name="session" value="{{ $sessionValue ?? '' }}" />
            <input type="hidden" name="term" value="{{ $term ?? '' }}" />
            <input type="hidden" name="tab" value="report" />
            <div class="col-md-4">
              <label>Student</label>
              <select name="student_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Select Student --</option>
                @foreach(($students ?? []) as $s)
                <option value="{{ $s->grno }}" {{ ($selectedStudentId ?? 0)==$s->grno ? 'selected' : '' }}>{{ $s->grno }} - {{ $s->studentName }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label>Search by GR No (All Classes/Sections)</label>
              <div class="input-group">
                <input type="text" name="grno" id="grnoInput" class="form-control" value="{{ request()->get('grno', ($selectedStudentId ?? '')) }}" placeholder="Enter GR No" list="grnoDatalist" />
                <button class="btn btn-outline-secondary" type="submit">Search</button>
              </div>
              <datalist id="grnoDatalist"></datalist>
              <small class="text-muted">Type any GR No to load the student's report below</small>
            </div>
          </form>



          @if(request()->get('grno') && empty($selectedStudent))
          <div class="alert alert-warning">No student found with GR No: {{ request()->get('grno') }}</div>
          @endif

          @if($selectedStudent)
          @php
          // Dynamic report title based on term
          $reportTitleText = 'Examination and Progress Report';
          if (stripos($term ?? '', 'final') !== false) {
          $reportTitleText = 'Final Term Examination and Progress Report';
          } elseif (stripos($term ?? '', 'mid') !== false) {
          $reportTitleText = 'Mid Term Examination and Progress Report';
          } elseif (stripos($term ?? '', 'bi-monthly') !== false || stripos($term ?? '', 'bimonthly') !== false) {
          $reportTitleText = 'Bi-Monthly Examination and Progress Report';
          } elseif (stripos($term ?? '', 'grand test') !== false) {
          $reportTitleText = 'Grand Test Examination and Progress Report';
          } else {
          $reportTitleText = ($term ?? 'Term') . ' - Examination and Progress Report';
          }
          @endphp
          <div class="d-flex justify-content-end mb-2 gap-2">
            <a class="btn btn-outline-primary btn-sm" href="{{ url('/manual-exams/print-all') }}?class_id={{ $selectedClassId }}&section={{ urlencode($selectedSection ?? '') }}&session={{ urlencode($sessionValue ?? '') }}&term={{ urlencode($term ?? '') }}" target="_blank">
              <i class="bi bi-printer"></i> Print All Reports
            </a>
            <a class="btn btn-outline-danger btn-sm" href="{{ url('/manual-exams/print-all/pdf') }}?class_id={{ $selectedClassId }}&section={{ urlencode($selectedSection ?? '') }}&session={{ urlencode($sessionValue ?? '') }}&term={{ urlencode($term ?? '') }}" target="_blank">
              <i class="bi bi-file-earmark-pdf"></i> Download PDF
            </a>
            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
              <i class="bi bi-printer"></i> Print This Report
            </button>
          </div>
          <div id="studentReport" class="page container-fluid" style="border:1px solid #111; padding:16px; background:#fff;">
            <div class="row">
              <div class="col-1">
                <!-- School Logo -->
                <img src="{{ asset('cloudiSchool.png') }}"
                  alt="School Logo"
                  style="width: 60px; height: 60px; object-fit: contain;"
                  onerror="this.src='https://ui-avatars.com/api/?name=School&background=0D8ABC&color=fff'" />
              </div>
              <div class="col-7">
                <div style="font-weight:700; text-transform:uppercase; font-size:1.2em; margin-bottom:8px;">{{ config('app.name', 'Super School') }}</div>
                <div style="font-weight:600; margin-bottom:4px;">{{ $reportTitleText }}</div>
                <div class="text-muted">Academic Session: {{ '2025 - 2026' }} | Term: {{ $term ?? 'N/A' }}</div>
              </div>
              <div class="col-4 text-end">
                <div style="border: 2px solid #333; width: 120px; height: 140px; margin-left: auto; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                  @php
                  $studentPhoto = !empty($selectedStudent->profile_image) ? asset('storage/' . $selectedStudent->profile_image) : '';
                  $placeholderSvg = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTEwIiBoZWlnaHQ9IjEzMCIgdmlld0JveD0iMCAwIDExMCAxMzAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjExMCIgaGVpZ2h0PSIxMzAiIGZpbGw9IiNFOUVDRUYiLz48Y2lyY2xlIGN4PSI1NSIgY3k9IjQ1IiByPSIyMCIgZmlsbD0iIzZDNzU3RCIvPjxwYXRoIGQ9Ik0yMCA5MEMyMCA4MCAzNSA3MCA1NSA3MEM3NSA3MCA5MCA4MCA5MCA5MFYxMzBIMjBWOTBaIiBmaWxsPSIjNkM3NTdEIi8+PC9zdmc+';
                  @endphp
                  <img src="{{ $studentPhoto ?: $placeholderSvg }}"
                    alt="Student Photo"
                    style="width: 110px; height: 130px; object-fit: cover;"
                    onerror="this.src='{{ $placeholderSvg }}'" />
                </div>
              </div>
            </div>

            <div class="student-meta-new border p-2 mt-2" style="background-color: #f8f9fa;">
              <!-- First Row: Student Name and Father's Name -->
              <div class="row mb-2">
                <div class="col-6">
                  <strong>STUDENT NAME:</strong> {{ $selectedStudent->studentName }}
                </div>
                <div class="col-6">
                  <strong>FATHER'S NAME:</strong> {{ $selectedStudent->father_name ?? 'N/A' }}
                </div>
              </div>
              <!-- Second Row: Class, Section and GR No -->
              <div class="row">
                <div class="col-4">
                  <strong>CLASS:</strong> {{ $selectedStudent->class_id }}
                </div>
                <div class="col-4">
                  <strong>SECTION:</strong> {{ $selectedStudent->section ?? 'N/A' }}
                </div>
                <div class="col-4">
                  <strong>GR NO:</strong> {{ $selectedStudent->grno }}
                </div>
              </div>
            </div>

            <div class="mt-3">
              <div style="font-weight:700; margin-bottom:6px;">Subjects and Assessment</div>
              <table class="table table-bordered table-sm">
                <thead class="table-light">
                  <tr>
                    <th class="text-left">Subject</th>
                    <th class="text-center">Total Marks</th>
                    <th class="text-center">Obtained Marks</th>
                    <th class="text-center">Percentage</th>
                    <th class="text-center">Grade</th>
                    <th class="text-center">Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $vals = $existingByStudentId[$selectedStudentId] ?? [];
                  // Use the same term-specific subjects that we computed for the entry form
                  $classSubjects = [];
                  if ($classSubjectsForEntry && $classSubjectsForEntry->count() > 0) {
                  $classSubjects = $classSubjectsForEntry->keyBy('subject_name');
                  }
                  @endphp
                  @php $sumTotalMarks = 0; $sumObtained = 0; @endphp
                  @foreach(($reportSubjects ?? []) as $label => $key)
                  @php
                  $score = $vals[$key] ?? '';
                  $totalMarks = isset($classSubjects[$label]) ? (float)$classSubjects[$label]->total_marks : 100.0;
                  $passingMarks = isset($classSubjects[$label]) ? (float)$classSubjects[$label]->passing_marks : ($totalMarks / 2);
                  // Accumulate totals
                  $sumTotalMarks += (is_numeric($totalMarks) ? (float)$totalMarks : 0);
                  $sumObtained += (is_numeric($score) ? (float)$score : 0);
                  $percentage = 0; // Initialize percentage variable
                  $grade = '';
                  $gradeComment = '';

                  if (is_numeric($score) && $totalMarks > 0) {
                  $percentage = ((float) $score / $totalMarks) * 100;
                  $isPassingScore = (float)$score >= $passingMarks;

                  if ($percentage >= 95) {
                  $grade = 'A++';
                  $gradeComment = 'Exceptional';
                  } elseif ($percentage >= 90) {
                  $grade = 'A+';
                  $gradeComment = 'Outstanding';
                  } elseif ($percentage >= 85) {
                  $grade = 'A';
                  $gradeComment = 'Excellent';
                  } elseif ($percentage >= 80) {
                  $grade = 'B++';
                  $gradeComment = 'Very Good';
                  } elseif ($percentage >= 75) {
                  $grade = 'B+';
                  $gradeComment = 'Good';
                  } elseif ($percentage >= 70) {
                  $grade = 'B';
                  $gradeComment = 'Fairly Good';
                  } elseif ($percentage >= 60) {
                  $grade = 'C';
                  $gradeComment = 'Above Average';
                  } elseif ($isPassingScore) {
                  $grade = 'D';
                  $gradeComment = 'Pass';
                  } else {
                  $grade = 'F';
                  $gradeComment = 'Fail';
                  }
                  }
                  @endphp
                  <tr>
                    <td class="text-left">{{ $label }}</td>
                    <td class="text-center">{{ formatNumber($totalMarks) }}</td>
                    <td class="text-center">
                      @if(is_string($score ?? null) && strtoupper(trim($score)) === 'A')
                      A
                      @elseif(isset($score) && $score !== '' && is_numeric($score))
                      {{ formatNumber((float)$score) }}
                      @else
                      -
                      @endif
                    </td>
                    <td class="text-center">
                      @if($score && is_numeric($score) && $totalMarks > 0)
                      {{ number_format($percentage, 1) }}%
                      @else
                      -
                      @endif
                    </td>
                    <td class="text-center" style="font-weight: bold; color: {{ $percentage >= 85 ? '#28a745' : ($percentage >= 70 ? '#ffc107' : ($percentage >= 50 ? '#fd7e14' : '#dc3545')) }};">
                      @if(is_string($score ?? null) && strtoupper(trim($score)) === 'A')
                      -
                      @else
                      {{ $grade ?: '-' }}
                      @endif
                    </td>
                    <td class="text-center" style="font-size: 0.9em; color: #6c757d;">
                      @if(is_string($score ?? null) && strtoupper(trim($score)) === 'A')
                      Absent
                      @else
                      {{ $gradeComment ?: '-' }}
                      @endif
                    </td>
                  </tr>
                  @endforeach
                  @php
                  // Totals row calculation
                  $overallTotal = (float) $sumTotalMarks;
                  $overallObtained = (float) $sumObtained;
                  $overallPct = $overallTotal > 0 ? ($overallObtained / $overallTotal) * 100 : 0;
                  $overallGrade = '';
                  $overallRemark = '';
                  if ($overallPct >= 95) { $overallGrade = 'A++'; $overallRemark = 'Exceptional'; }
                  elseif ($overallPct >= 90) { $overallGrade = 'A+'; $overallRemark = 'Outstanding'; }
                  elseif ($overallPct >= 85) { $overallGrade = 'A'; $overallRemark = 'Excellent'; }
                  elseif ($overallPct >= 80) { $overallGrade = 'B++'; $overallRemark = 'Very Good'; }
                  elseif ($overallPct >= 75) { $overallGrade = 'B+'; $overallRemark = 'Good'; }
                  elseif ($overallPct >= 70) { $overallGrade = 'B'; $overallRemark = 'Fairly Good'; }
                  elseif ($overallPct >= 60) { $overallGrade = 'C'; $overallRemark = 'Above Average'; }
                  elseif ($overallPct >= 50) { $overallGrade = 'D'; $overallRemark = 'Average'; }
                  else { $overallGrade = 'F'; $overallRemark = 'Unsatisfactory'; }
                  @endphp
                  <tr class="table-secondary">
                    <td class="text-left" style="font-weight:700;">Total</td>
                    <td class="text-center" style="font-weight:700;">{{ formatNumber($sumTotalMarks) }}</td>
                    <td class="text-center" style="font-weight:700;">{{ formatNumber($sumObtained) }}</td>
                    <td class="text-center" style="font-weight:700;">{{ number_format($overallPct, 1) }}%</td>
                    <td class="text-center" style="font-weight:700;">{{ $overallGrade }}</td>
                    <td class="text-center" style="font-weight:700; color: #6c757d;">{{ $overallRemark }}</td>
                  </tr>
                </tbody>
              </table>

              @php $vals = $existingByStudentId[$selectedStudentId] ?? []; @endphp
              @if(!empty($vals))
              <div class="mt-2">
                <div style="font-weight:700; margin-bottom:6px;">Attributes (from Marks Entry)</div>
                <div class="row g-2">
                  @if(!empty($vals['improvement_studies']))
                  <div class="col-12"><strong>Improvement Required:</strong> {{ $vals['improvement_studies'] }}</div>
                  @endif
                  @if(!empty($vals['overall_grade']))
                  <div class="col-12"><strong>Overall Grade:</strong> {{ $vals['overall_grade'] }}</div>
                  @endif
                </div>
              </div>
              @endif
            </div>



            @php
            // Get attendance data from the marks entry fields
            $vals = $existingByStudentId[$selectedStudentId] ?? [];
            $displayWorkingDays = $vals['total_working_days'] ?? $attWorkingDays ?? 0;
            $displayPresent = $vals['total_present'] ?? $attPresent ?? 0;
            $displayAbsent = $vals['total_absent'] ?? $attAbsent ?? 0;

            // Determine source and calculate percentage
            $attendanceSource = 'System Attendance';
            $attendancePercentage = 0;

            if (isset($vals['total_working_days']) && $vals['total_working_days'] > 0) {
            $attendanceSource = 'Manual Entry (Marks Table)';
            $attendancePercentage = round(($displayPresent / $displayWorkingDays) * 100, 1);
            } elseif ($displayWorkingDays > 0) {
            $attendancePercentage = round(($displayPresent / $displayWorkingDays) * 100, 1);
            }
            @endphp

            <div class="mb-2">
              <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-1" style="font-weight: 700;">Attendance Summary</h6>
                <small class="text-muted">{{ $attendanceSource }}</small>
              </div>
            </div>

            <div class="row g-2 mt-1">
              <div class="col">
                <div class="border p-2 text-center" style="background-color: #e3f2fd;">Working Days: {{ $displayWorkingDays }}</div>
              </div>
              <div class="col">
                <div class="border p-2 text-center" style="background-color: #e8f5e8;">Number of Days Present: {{ $displayPresent }}</div>
              </div>
              <div class="col">
                <div class="border p-2 text-center" style="background-color: #ffebee;">Number of Days Absent: {{ $displayAbsent }}</div>
              </div>
              <div class="col">
                <div class="border p-2 text-center"
                  style="background-color: 
                             @if($attendancePercentage >= 90) #d4edda
                             @elseif($attendancePercentage >= 75) #fff3cd 
                             @else #f8d7da @endif; 
                             font-weight: bold;">
                  Attendance: {{ $attendancePercentage }}%
                </div>
              </div>
            </div>

            <div class="mt-2">
              <form method="POST" action="#" onsubmit="return saveReportExtras(event);">
                @csrf
                <input type="hidden" id="extras_student_id" value="{{ $selectedStudentId }}" />
                <input type="hidden" id="extras_term" value="{{ $term }}" />
                <input type="hidden" id="extras_session" value="{{ $sessionValue }}" />
                <!-- Display Behavior Attributes from Modal -->
                @php
                $behaviorAttributesFromTable = $behaviorAttributesFromTable ?? [];
                $behaviorAttributes = !empty($behaviorAttributesFromTable)
                ? $behaviorAttributesFromTable
                : ($valsForSelected['behavior_attributes'] ?? []);
                $attributeLabels = [
                'classwork' => 'Class Work',
                'homework' => 'Home Work',
                'writing_neatness' => 'Writing and Neatness in Work',
                'assignment_submission' => 'Completion and Submission of Assignment on Time',
                'turnout' => 'Turn Out (Uniform, Shoes, Hair and Nails)',
                'books_condition' => 'Condition of Books, Copies, Pockets, Diary and Bag',
                'personal_hygiene' => 'Personal Hygiene',
                'regularity' => 'Regularity',
                'punctuality' => 'Punctuality',
                'speaking_behaving' => 'Way of Speaking and Behaving'
                ];
                @endphp
                @php $isMidOrFinal = preg_match('/\b(mid|final)\b/i', $term ?? '') === 1; @endphp
                @if($isMidOrFinal && !empty($behaviorAttributes) && is_array($behaviorAttributes))
                <div class="mb-3">
                  <label class="mb-1" style="font-weight:700;">Behavior Attributes Assessment (1-5 Scale)</label>
                  <table class="table table-bordered table-sm">
                    <thead class="table-light">
                      <tr>
                        <th class="text-left">Attributes</th>
                        <th class="text-center">Score</th>
                        <th class="text-center">Rating</th>
                        <th class="text-center">Grade</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($behaviorAttributes as $key => $value)
                      @php
                      $label = $attributeLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                      $score = (int) $value;
                      $rating = '';
                      $gradeColor = '#6c757d';

                      if ($score === 5) {
                      $rating = 'Excellent';
                      $gradeColor = '#28a745';
                      } elseif ($score === 4) {
                      $rating = 'Very Good';
                      $gradeColor = '#20c997';
                      } elseif ($score === 3) {
                      $rating = 'Good';
                      $gradeColor = '#ffc107';
                      } elseif ($score === 2) {
                      $rating = 'Satisfactory';
                      $gradeColor = '#fd7e14';
                      } elseif ($score === 1) {
                      $rating = 'Unsatisfactory';
                      $gradeColor = '#dc3545';
                      }
                      @endphp
                      <tr>
                        <td class="text-left">{{ $label }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $score }}/5</td>
                        <td class="text-center" style="color: {{ $gradeColor }}; font-weight: bold;">{{ $rating }}</td>
                        <td class="text-center">
                          @if($score >= 4)
                          <span class="badge bg-success">A</span>
                          @elseif($score >= 3)
                          <span class="badge bg-warning">B</span>
                          @elseif($score >= 2)
                          <span class="badge bg-info">C</span>
                          @else
                          <span class="badge bg-danger">D</span>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                      <tr class="table-info">
                        <td class="text-left" style="font-weight: bold;">Overall Average</td>
                        @php
                        $totalScore = array_sum($behaviorAttributes);
                        $totalAttributes = count($behaviorAttributes);
                        $averageScore = $totalAttributes > 0 ? round($totalScore / $totalAttributes, 1) : 0;

                        $overallRating = '';
                        $overallColor = '#6c757d';
                        if ($averageScore >= 4.5) {
                        $overallRating = 'Excellent';
                        $overallColor = '#28a745';
                        } elseif ($averageScore >= 3.5) {
                        $overallRating = 'Very Good';
                        $overallColor = '#20c997';
                        } elseif ($averageScore >= 2.5) {
                        $overallRating = 'Good';
                        $overallColor = '#ffc107';
                        } elseif ($averageScore >= 1.5) {
                        $overallRating = 'Satisfactory';
                        $overallColor = '#fd7e14';
                        } else {
                        $overallRating = 'Needs Improvement';
                        $overallColor = '#dc3545';
                        }
                        @endphp
                        <td class="text-center" style="font-weight: bold; font-size: 1.1em;">{{ $averageScore }}/5</td>
                        <td class="text-center" style="color: {{ $overallColor }}; font-weight: bold; font-size: 1.1em;">{{ $overallRating }}</td>
                        <td class="text-center">
                          @if($averageScore >= 4)
                          <span class="badge bg-success" style="font-size: 1.1em;">A</span>
                          @elseif($averageScore >= 3)
                          <span class="badge bg-warning" style="font-size: 1.1em;">B</span>
                          @elseif($averageScore >= 2)
                          <span class="badge bg-info" style="font-size: 1.1em;">C</span>
                          @else
                          <span class="badge bg-danger" style="font-size: 1.1em;">D</span>
                          @endif
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  @if(!empty($behaviorOverallAverage))
                  <div class="mt-2">
                    <div class="border p-2" style="background-color:#f0f4ff; border-color:#3b82f6 !important;">
                      <strong style="font-size:13px; color:#1f2937;">Stored Overall Average (Behavior):</strong>
                      @php
                      $avg5 = (float)$behaviorOverallAverage;
                      $avgPct = ($avg5 / 5) * 100;
                      @endphp
                      <span class="badge bg-primary ms-2" style="font-size:12px;">{{ number_format($avg5,2) }}/5</span>
                      <span class="badge bg-info text-dark ms-1" style="font-size:12px;">{{ number_format($avgPct,0) }}%</span>
                    </div>
                  </div>
                  @endif
                </div>
                @endif

                <!-- Position in Class and Overall Grade Section -->
                @php
                // Calculate overall grade: 80% subjects + 20% behavior attributes
                $subjectsAverage = 0.0;
                $subjectsCount = 0;
                $subjectsTotalPercentage = 0.0;

                // Calculate subjects average percentage using sum of obtained/sum of total method
                // This matches the controller logic for consistency
                $sumTotalMarks = 0.0;
                $sumObtainedMarks = 0.0;

                foreach(($reportSubjects ?? []) as $label => $key) {
                $score = $vals[$key] ?? '';
                if (is_numeric($score)) {
                $scoreFloat = (float) $score;
                // Only include subjects with marks > 0
                if ($scoreFloat > 0) {
                // Get total marks for this subject
                $totalMarks = 100.0; // Default
                if ($classSubjectsForEntry && $classSubjectsForEntry->count() > 0) {
                $subjectData = $classSubjectsForEntry->firstWhere('subject_name', $label);
                if ($subjectData && isset($subjectData->total_marks) && $subjectData->total_marks > 0) {
                $totalMarks = (float) $subjectData->total_marks;
                }
                }

                $sumTotalMarks += $totalMarks;
                $sumObtainedMarks += $scoreFloat;
                $subjectsCount++;
                }
                }
                }

                // Calculate subjects percentage using sum method (matches controller)
                if ($sumTotalMarks > 0 && $subjectsCount > 0) {
                $subjectsAverage = ($sumObtainedMarks / $sumTotalMarks) * 100.0;
                }

                // Calculate behavior attributes average (convert from 5-point scale to percentage)
                $behaviorAverage = 0.0;
                $behaviorAttributesToUse = [];

                // Prefer behavior attributes from dedicated table (same as controller logic)
                if (!empty($behaviorAttributesFromTable) && is_array($behaviorAttributesFromTable)) {
                $behaviorAttributesToUse = $behaviorAttributesFromTable;
                } elseif (!empty($behaviorAttributes) && is_array($behaviorAttributes)) {
                $behaviorAttributesToUse = $behaviorAttributes;
                } elseif (isset($valsForSelected['behavior_attributes'])) {
                // Handle JSON string from existing entries
                $attr = $valsForSelected['behavior_attributes'];
                if (is_string($attr)) {
                $decoded = json_decode($attr, true);
                if (is_array($decoded)) {
                $behaviorAttributesToUse = $decoded;
                }
                } elseif (is_array($attr)) {
                $behaviorAttributesToUse = $attr;
                }
                }

                if (!empty($behaviorAttributesToUse)) {
                // Filter out invalid values and calculate average
                $validScores = [];
                foreach ($behaviorAttributesToUse as $score) {
                $scoreInt = (int) $score;
                if ($scoreInt >= 1 && $scoreInt <= 5) {
                  $validScores[]=$scoreInt;
                  }
                  }

                  if (!empty($validScores)) {
                  $behaviorAverageScore=array_sum($validScores) / count($validScores);
                  $behaviorAverage=($behaviorAverageScore / 5.0) * 100.0; // Convert 5-point scale to percentage
                  }
                  }

                  // Determine subject grade based on subjects average
                  $subjectGrade='' ;
                  $subjectColor='#6c757d' ;
                  if ($subjectsAverage>= 95) {
                  $subjectGrade = 'A++'; $subjectColor = '#28a745';
                  } elseif ($subjectsAverage >= 90) {
                  $subjectGrade = 'A+'; $subjectColor = '#28a745';
                  } elseif ($subjectsAverage >= 85) {
                  $subjectGrade = 'A'; $subjectColor = '#28a745';
                  } elseif ($subjectsAverage >= 80) {
                  $subjectGrade = 'B++'; $subjectColor = '#20c997';
                  } elseif ($subjectsAverage >= 75) {
                  $subjectGrade = 'B+'; $subjectColor = '#20c997';
                  } elseif ($subjectsAverage >= 70) {
                  $subjectGrade = 'B'; $subjectColor = '#ffc107';
                  } elseif ($subjectsAverage >= 60) {
                  $subjectGrade = 'C'; $subjectColor = '#fd7e14';
                  } elseif ($subjectsAverage >= 50) {
                  $subjectGrade = 'D'; $subjectColor = '#fd7e14';
                  } else {
                  $subjectGrade = 'F'; $subjectColor = '#dc3545';
                  }

                  // Determine assessment grade based on behavior average (for Mid/Final terms only)
                  $assessmentGrade = '';
                  $assessmentColor = '#6c757d';
                  if ($isMidOrFinal && $behaviorAverage > 0) {
                  if ($behaviorAverage >= 95) {
                  $assessmentGrade = 'A++'; $assessmentColor = '#28a745';
                  } elseif ($behaviorAverage >= 90) {
                  $assessmentGrade = 'A+'; $assessmentColor = '#28a745';
                  } elseif ($behaviorAverage >= 85) {
                  $assessmentGrade = 'A'; $assessmentColor = '#28a745';
                  } elseif ($behaviorAverage >= 80) {
                  $assessmentGrade = 'B++'; $assessmentColor = '#20c997';
                  } elseif ($behaviorAverage >= 75) {
                  $assessmentGrade = 'B+'; $assessmentColor = '#20c997';
                  } elseif ($behaviorAverage >= 70) {
                  $assessmentGrade = 'B'; $assessmentColor = '#ffc107';
                  } elseif ($behaviorAverage >= 60) {
                  $assessmentGrade = 'C'; $assessmentColor = '#fd7e14';
                  } elseif ($behaviorAverage >= 50) {
                  $assessmentGrade = 'D'; $assessmentColor = '#fd7e14';
                  } else {
                  $assessmentGrade = 'F'; $assessmentColor = '#dc3545';
                  }
                  }

                  // Display format for overall grade section
                  $overallDisplayText = '';
                  $overallColor = $subjectColor; // Default to subject color

                  if ($isMidOrFinal && !empty($assessmentGrade)) {
                  $overallDisplayText = "Subjects: {$subjectGrade} | Assessment: {$assessmentGrade}";
                  } else {
                  $overallDisplayText = "Subjects: {$subjectGrade}";
                  }

                  // Calculate percentage for principal remarks (same logic used for position ranking)
                  $percentageForRemarks = 0.0;
                  if ($isMidOrFinal) {
                  // For Mid/Final terms: use 80-20 calculation for principal remarks
                  $percentageForRemarks = ($subjectsAverage * 0.8) + ($behaviorAverage * 0.2);
                  } else {
                  // For other exams: use subjects percentage only
                  $percentageForRemarks = $subjectsAverage;
                  }

                  // Position in class computed in controller
                  $positionDisplay = is_numeric($positionInClass) ? ($positionInClass . ' of ' . $classStrength) : 'N/A';

                  // Compute auto principal remark based on calculated percentage using principal_remarks table
                  $autoPrincipalRemark = '';
                  try {
                  $rule = DB::table('principal_remarks')
                  ->where('is_active', true)
                  ->where('percentage_min', '<=', $percentageForRemarks)
                    ->where('percentage_max', '>=', $percentageForRemarks)
                    ->orderBy('sort_order')
                    ->orderBy('percentage_min', 'desc')
                    ->first();
                    if ($rule) { $autoPrincipalRemark = $rule->remark; }
                    } catch (Exception $e) { $autoPrincipalRemark = ''; }
                    @endphp

                    <!-- Position in Class and Overall Grade Display -->
                    <div class="mb-3">
                      <div class="row g-2">
                        <div class="col-6">
                          <div class="border p-2" style="background-color: #e8f5e8; border-color: #28a745 !important;">
                            <strong style="font-size: 14px; color: #2c3e50;">Position in Class:</strong>
                            <span style="font-size: 16px; font-weight: bold; color: #28a745;">{{ $positionDisplay }}</span>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="border p-2" style="background-color: #f8f9fa; border-color: {{ $overallColor }} !important;">
                            <div class="d-flex justify-content-between align-items-center">
                              <div style="flex: 1;">
                                <strong style="font-size: 14px; color: #2c3e50;">Overall Grade:</strong>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 600;">
                                  @if($isMidOrFinal && !empty($assessmentGrade))
                                  <span style="color: {{ $subjectColor }};">Subjects: {{ $subjectGrade }}</span><br>
                                  <span style="color: {{ $assessmentColor }};">Assessment: {{ $assessmentGrade }}</span>
                                  @else
                                  <span style="color: {{ $subjectColor }};">Subjects: {{ $subjectGrade }}</span>
                                  @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-12">
                          <small class="text-muted" style="font-size: 11px;">
                            @if($isMidOrFinal && !empty($assessmentGrade))
                            <em>Overall Grade shows individual grades: Subjects ({{ number_format($subjectsAverage, 1) }}% = {{ $subjectGrade }}) and Assessment ({{ number_format($behaviorAverage, 1) }}% = {{ $assessmentGrade }})</em>
                            @else
                            <em>Overall Grade shows: Subjects ({{ number_format($subjectsAverage, 1) }}% = {{ $subjectGrade }})</em>
                            @endif
                          </small>
                        </div>
                      </div>
                    </div>

                    <!-- Display sections for print report -->
                    @php
                    // Use professional subjects to improve data from controller
                    $improvementSubjects = [];
                    $absentSubjects = $absentSubjectsData ?? [];

                    if (!empty($subjectsToImproveData) && is_array($subjectsToImproveData)) {
                    foreach ($subjectsToImproveData as $improvementData) {
                    $subject = $improvementData['subject_name'] ?? '';
                    $reason = $improvementData['reason'] ?? '';
                    $priority = $improvementData['priority'] ?? 'medium';
                    $percentage = $improvementData['percentage'] ?? 0;

                    if ($subject) {
                    $displayText = $subject;
                    if ($percentage > 0) {
                    $displayText .= ' (' . number_format($percentage, 1) . '%)';
                    }
                    if ($reason) {
                    $displayText .= ' - ' . $reason;
                    }
                    $improvementSubjects[] = [
                    'text' => $displayText,
                    'priority' => $priority,
                    'subject' => $subject,
                    'reason' => $reason
                    ];
                    }
                    }
                    } else {
                    // Fallback: Build improvement list using old logic for backward compatibility
                    foreach(($reportSubjects ?? []) as $label => $key) {
                    $score = $vals[$key] ?? '';
                    if ($score !== '' && is_numeric($score) && (float)$score > 0) {
                    $totalMarks = 100.0;
                    if ($classSubjectsForEntry && $classSubjectsForEntry->count() > 0) {
                    $subjectData = $classSubjectsForEntry->firstWhere('subject_name', $label);
                    if ($subjectData && isset($subjectData->total_marks)) {
                    $totalMarks = (float)$subjectData->total_marks;
                    }
                    }

                    $pct = ($totalMarks > 0) ? ((float)$score / (float)$totalMarks) * 100.0 : 0.0;

                    if ($pct < 60.0) {
                      $priority=$pct < 40 ? 'critical' : ($pct < 50 ? 'high' : 'medium' );
                      $improvementSubjects[]=[ 'text'=> $label . ' (' . number_format($pct, 1) . '%)',
                      'priority' => $priority,
                      'subject' => $label,
                      'reason' => 'Below 60%'
                      ];
                      }
                      }
                      }
                      }
                      // Attributes improvement: score 1
                      $attributeLabels = [
                      'classwork' => 'Class Work',
                      'homework' => 'Home Work',
                      'writing_neatness' => 'Writing and Neatness in Work',
                      'assignment_submission' => 'Completion and Submission of Assignment on Time',
                      'turnout' => 'Turn Out (Uniform, Shoes, Hair and Nails)',
                      'books_condition' => 'Condition of Books, Copies, Pockets, Diary and Bag',
                      'personal_hygiene' => 'Personal Hygiene',
                      'regularity' => 'Regularity',
                      'punctuality' => 'Punctuality',
                      'speaking_behaving' => 'Way of Speaking and Behaving'
                      ];
                      $improvementAttributes = [];
                      if (!empty($behaviorAttributes) && is_array($behaviorAttributes)) {
                      foreach($behaviorAttributes as $k => $v){
                      $score = (int)$v;
                      if ($score === 1) {
                      $improvementAttributes[] = $attributeLabels[$k] ?? ucwords(str_replace('_',' ',$k));
                      }
                      }
                      }
                      @endphp

                      <!-- Professional Subjects to Improve Section -->
                      <div class="subjects-improvement-section">
                        <h6 style="font-weight: 700; color: #856404; margin-bottom: 15px;">
                          <i class="bi bi-exclamation-triangle-fill me-2"></i>Subjects Requiring Improvement
                        </h6>

                        @if(count($improvementSubjects) > 0)
                        <div class="row g-3">
                          <div class="col-md-8">
                            <div style="font-weight:600; margin-bottom: 8px; color: #856404;">Academic Performance Issues:</div>
                            @foreach($improvementSubjects as $item)
                            <span class="improvement-{{ $item['priority'] }}">
                              {{ $item['subject'] }}
                              @if($item['reason'] === 'Absent from examination')
                              <i class="bi bi-person-x ms-1" title="Absent"></i>
                              @elseif(strpos($item['reason'], 'Critical') !== false)
                              <i class="bi bi-exclamation-triangle-fill ms-1" title="Critical"></i>
                              @elseif(strpos($item['reason'], 'Below passing') !== false)
                              <i class="bi bi-arrow-down-circle ms-1" title="Below Pass"></i>
                              @endif
                              <br><small>{{ $item['reason'] }}</small>
                            </span>
                            @endforeach
                            <div class="mt-2">
                              <small class="text-muted">
                                <strong>{{ count($improvementSubjects) }}</strong> subject(s) require focused attention
                              </small>
                            </div>
                          </div>
                          <div class="col-md-4">
                            @if(!empty($absentSubjects))
                            <div style="font-weight:600; margin-bottom: 8px; color: #dc3545;">Absent Subjects:</div>
                            @foreach($absentSubjects as $absentSubject)
                            <span class="absent-indicator">{{ $absentSubject }}</span><br>
                            @endforeach
                            @endif
                          </div>
                        </div>
                        @else
                        <div class="text-center py-3">
                          <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #28a745;"></i>
                          <div style="font-weight: 600; color: #28a745; margin-top: 8px;">
                            Excellent Performance!
                          </div>
                          <small class="text-muted">All subjects are above the improvement threshold</small>
                        </div>
                        @endif
                      </div>
                      <div class="col-md-6">
                        <div class="border p-2" style="background-color: #fde2e2; border-color: #dc3545 !important; min-height: 60px;">
                          <div style="font-weight:700;">Attributes</div>
                          @if(count($improvementAttributes) > 0)
                          <ul class="mb-0" style="padding-left:18px;">
                            @foreach($improvementAttributes as $a)
                            <li>{{ $a }}</li>
                            @endforeach
                          </ul>
                          @else
                          <span class="text-muted">None</span>
                          @endif
                        </div>
                      </div>
            </div>
          </div>

          @php
          // Fallback: derive principal remarks from saved entries JSON if not explicitly provided
          if (!isset($reportPrincipalRemarks) || $reportPrincipalRemarks === null || $reportPrincipalRemarks === '') {
          $vals = $existingByStudentId[$selectedStudentId] ?? [];
          $reportPrincipalRemarks = $vals['principal_remarks'] ?? '';
          }
          // If still empty, use auto computed remark based on overall %
          if (empty($reportPrincipalRemarks)) {
          $reportPrincipalRemarks = $autoPrincipalRemark ?? '';
          }
          @endphp

          @if(!empty($reportPrincipalRemarks))
          <div class="mb-3">
            <h6 class="mb-1" style="font-weight: 700; color: #2c3e50;">Principal's Remarks</h6>
            <div class="border p-2" style="background-color: #d1ecf1; border-color: #17a2b8 !important; min-height: 60px;">
              <p class="mb-0" style="font-size: 14px; line-height: 1.4;">{{ $reportPrincipalRemarks }}</p>
            </div>
          </div>
          @endif

          <!-- Form fields for editing (hidden on print) -->
          <div class="edit-only" style="display: block;">
            <input type="hidden" id="auto_principal_remarks" value="{{ $autoPrincipalRemark ?? '' }}" />
            <div class="mb-2">
              <label>Principal's Remarks</label>
              <textarea id="principal_remarks" class="form-control" rows="2">{{ $reportPrincipalRemarks ?? '' }}</textarea>
            </div>
          </div>
          <div class="text-end">
            <button class="btn btn-outline-primary btn-sm" type="submit">Save Notes</button>
          </div>
          </form>
        </div>

        <div class="row g-3 mt-3">
          <div class="col">
            <div class="border p-3" style="min-height:64px; background-color: #f8f9fa;"></div>
            <div class="text-center text-muted small" style="margin-top:6px; font-weight: 600;">Class Teacher's Signature</div>
          </div>
          <div class="col">
            <div class="border p-3" style="min-height:64px; background-color: #f8f9fa;"></div>
            <div class="text-center text-muted small" style="margin-top:6px; font-weight: 600;">Principal's Signature</div>
          </div>
          <div class="col">
            <div class="border p-3" style="min-height:64px; background-color: #f8f9fa;"></div>
            <div class="text-center text-muted small" style="margin-top:6px; font-weight: 600;">Parent/Guardian's Signature</div>
          </div>
        </div>

        <!-- Date and Official Seal Row -->
        <div class="row mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
          <div class="col-6">
            <div class="d-flex align-items-center">
              <span style="font-weight: 600; color: #2c3e50; margin-right: 10px;">Date:</span>
              <div style="border-bottom: 2px solid #000; width: 150px; height: 20px;"></div>
            </div>
          </div>
          <div class="col-6 text-end">
            <div class="d-flex align-items-center justify-content-end">
              <span style="font-weight: 600; color: #2c3e50; margin-right: 10px;">OFFICIAL SEAL:</span>
              <div style="border-bottom: 2px solid #000; width: 150px; height: 20px;"></div>
            </div>
          </div>
        </div>
      </div>
      @else
      <div class="alert alert-info">Select a student to view printable report.</div>
      @endif
    </div>
  </div>

</div>
</div>
</div>

<!-- Attributes Modal -->
<div class="modal fade" id="attributesModal" tabindex="-1" aria-labelledby="attributesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attributesModalLabel">Set Behavior Attributes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong>Scoring Guide:</strong><br>
          5 = Excellent | 4 = Very Good | 3 = Good | 2 = Satisfactory | 1 = Unsatisfactory
        </div>
        <p>Student: <strong id="modalStudentName"></strong></p>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Attributes</th>
                <th class="text-center">Score (1-5)</th>
                <th class="text-center">Rating</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-left">Class Work</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_classwork" onchange="updateRating('classwork')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_classwork" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Home Work</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_homework" onchange="updateRating('homework')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_homework" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Writing and Neatness in Work</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_writing_neatness" onchange="updateRating('writing_neatness')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_writing_neatness" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Completion and Submission of Assignment on Time</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_assignment_submission" onchange="updateRating('assignment_submission')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_assignment_submission" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Turn Out (Uniform, Shoes, Hair and Nails)</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_turnout" onchange="updateRating('turnout')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_turnout" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Condition of Books, Copies, Pockets, Diary and Bag</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_books_condition" onchange="updateRating('books_condition')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_books_condition" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Personal Hygiene</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_personal_hygiene" onchange="updateRating('personal_hygiene')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_personal_hygiene" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Regularity</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_regularity" onchange="updateRating('regularity')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_regularity" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Punctuality</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_punctuality" onchange="updateRating('punctuality')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_punctuality" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
              <tr>
                <td class="text-left">Way of Speaking and Behaving</td>
                <td class="text-center">
                  <select class="form-control form-control-sm" id="attr_speaking_behaving" onchange="updateRating('speaking_behaving')" style="width:80px; margin:auto;">
                    <option value="">-</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                  </select>
                </td>
                <td class="text-center" id="rating_speaking_behaving" style="color: #6c757d; font-weight: normal;">-</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="row mt-3">
          <div class="col-md-6">
            <strong>Overall Average:</strong> <span id="overallAverage">-</span>
          </div>
          <div class="col-md-6 text-end">
            <strong>Overall Rating:</strong> <span id="overallRating">-</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveAttributes()">Save Attributes</button>
      </div>
    </div>
  </div>
</div>



<!-- CSV Import Modal -->
<div class="modal fade" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importCsvModalLabel">Import CSV File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('manual-exams.import-csv') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClassId ?? '' }}" />
        <input type="hidden" name="section" value="{{ $selectedSection ?? '' }}" />
        <input type="hidden" name="session" value="{{ $sessionValue ?? '' }}" />
        <input type="hidden" name="term" value="{{ $term ?? '' }}" />

        <div class="modal-body">
          <div class="mb-3">
            <label for="csv_file" class="form-label">Select CSV File</label>
            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
            <div class="form-text">
              Upload a CSV file with student marks. The file should include headers: GR_No, Student_Name, Class, Section, and subject columns.
            </div>
          </div>

          <div class="alert alert-info">
            <h6>CSV Format Instructions:</h6>
            <ul class="mb-0">
              <li>First row should contain column headers</li>
              <li>Required columns: GR_No, Student_Name, Class, Section</li>
              <li>Subject columns should match the format: "Subject_Name_(Total_Marks)"</li>
              <li>Additional columns: Working_Days, Present, Absent, Improvement_Studies, Overall_Grade</li>
              <li>You can export a CSV first to see the correct format</li>
            </ul>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Import CSV
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
<script>
  $(document).ready(function() {
    // === 1. Filters and Dynamic Loading ===
    const filterClass = $('#filterClass');
    const filterTerm = $('#filterTerm');
    const filterSection = $('#filterSection');
    const examFiltersForm = $('#examFiltersForm');

    function loadSections(classId) {
      if (!classId) {
        filterSection.empty().append('<option value="">-- All Sections --</option>');
        return;
      }
      $.ajax({
        url: '{{ url("/manual-exams/sections") }}/' + encodeURIComponent(classId),
        method: 'GET',
        success: function(sections) {
          const current = '{{ $selectedSection ?? '
          ' }}';
          filterSection.empty().append('<option value="">-- All Sections --</option>');
          $.each(sections, function(index, s) {
            filterSection.append(`<option ${current === s ? 'selected' : ''} value="${s}">${s}</option>`);
          });
        },
        error: function(xhr, status, error) {
          console.error('Error fetching sections:', error);
        }
      });
    }

    function updateSubjectsInfo(classId, term) {
      if (!classId || !term) return;
      const subjectsInfoPanel = $('.alert.alert-info');
      if (subjectsInfoPanel.length === 0) return;

      fetch(`{{ request()->getBaseUrl() }}/manual-exams/subjects-by-term?class_id=${classId}&term=${encodeURIComponent(term)}`)
        .then(r => r.json())
        .then(subjects => {
          if (subjects && subjects.length > 0) {
            const badgesHtml = subjects.map(s =>
              `<span class="badge bg-primary ms-1">${s.subject_name} (${parseFloat(s.total_marks)})</span>`
            ).join('');

            subjectsInfoPanel.first().html(`
                        <i class="bi bi-info-circle"></i>
                        <strong>Subjects for this class and term:</strong> 
                        ${badgesHtml}
                        <br><small class="text-muted">Enter marks for each subject. Max values set automatically.</small>
                    `);
            updateInputMaxValues(subjects);
          }
        })
        .catch(err => console.warn('Could not load subjects:', err));
    }

    function updateInputMaxValues(subjects) {
      subjects.forEach(s => {
        const fieldKey = s.subject_name.toLowerCase().replace(/[\s\(\)\.&\-]/g, match => (match === ' ' || match === '-') ? '_' : '');
        const inputs = $(`input[name*="[${fieldKey}]"]`);
        inputs.each(function() {
          const total = parseFloat(s.total_marks);
          const pass = parseFloat(s.passing_marks || (total / 2));
          $(this).attr('max', total).attr('data-passing-marks', pass)
            .attr('placeholder', `/${total}`)
            .attr('title', `${s.subject_name} - Max: ${total}, Pass: ${pass}`);
        });
      });
    }

    filterClass.on('change', function() {
      const val = $(this).val();
      loadSections(val);
      if (filterTerm.val()) updateSubjectsInfo(val, filterTerm.val());
      setTimeout(() => examFiltersForm.submit(), 200);
    });

    filterTerm.on('change', () => examFiltersForm.submit());
    filterSection.on('change', () => examFiltersForm.submit());

    // === 2. Behavior Attributes Logic ===
    let currentStudentId = null;

    function ratingForScore(v) {
      switch (parseInt(v)) {
        case 5:
          return 'Excellent';
        case 4:
          return 'Very Good';
        case 3:
          return 'Good';
        case 2:
          return 'Satisfactory';
        case 1:
          return 'Unsatisfactory';
        default:
          return '-';
      }
    }

    window.updateRating = function(attr) {
      const select = $('#attr_' + attr);
      const ratingEl = $('#rating_' + attr);
      if (!select.length || !ratingEl.length) return;
      const val = select.val();
      const colors = {
        5: '#28a745',
        4: '#20c997',
        3: '#ffc107',
        2: '#fd7e14',
        1: '#dc3545'
      };
      ratingEl.text(ratingForScore(val) || '-').css({
        'color': colors[val] || '#6c757d',
        'font-weight': val ? 'bold' : 'normal'
      });
      calculateOverall();
    };

    function calculateOverall() {
      const attrs = ['classwork', 'homework', 'writing_neatness', 'assignment_submission', 'turnout', 'books_condition', 'personal_hygiene', 'regularity', 'punctuality', 'speaking_behaving'];
      let total = 0,
        count = 0;
      attrs.forEach(a => {
        const v = parseInt($('#attr_' + a).val());
        if (!isNaN(v)) {
          total += v;
          count++;
        }
      });
      const avgEl = $('#overallAverage'),
        ratEl = $('#overallRating');
      if (count === 0) {
        avgEl.text('-');
        ratEl.text('-').css('color', '#6c757d');
        return;
      }
      const avg = total / count;
      avgEl.text(avg.toFixed(1));
      let rating = 'Needs Improvement',
        color = '#dc3545';
      if (avg >= 4.5) {
        rating = 'Excellent';
        color = '#28a745';
      } else if (avg >= 3.5) {
        rating = 'Very Good';
        color = '#20c997';
      } else if (avg >= 2.5) {
        rating = 'Good';
        color = '#ffc107';
      } else if (avg >= 1.5) {
        rating = 'Satisfactory';
        color = '#fd7e14';
      }
      ratEl.text(`${rating} (${(avg/5*100).toFixed(0)}%)`).css({
        'color': color,
        'font-weight': 'bold'
      });
    }

    const attrModal = $('#attributesModal');
    if (attrModal.length) {
      attrModal.on('show.bs.modal', function(e) {
        const btn = $(e.relatedTarget);
        currentStudentId = btn.data('stu-id');
        $('#modalStudentName').text(btn.data('stu-name') || '');
        const dataRaw = $('#behaviorData_' + currentStudentId).val();
        let data = {};
        try {
          data = JSON.parse(dataRaw || '{}');
        } catch (_) {}
        ['classwork', 'homework', 'writing_neatness', 'assignment_submission', 'turnout', 'books_condition', 'personal_hygiene', 'regularity', 'punctuality', 'speaking_behaving'].forEach(a => {
          $(`#attr_${a}`).val(data[a] || '');
          window.updateRating(a);
        });
      });
    }

    window.saveAttributes = function() {
      const data = {};
      ['classwork', 'homework', 'writing_neatness', 'assignment_submission', 'turnout', 'books_condition', 'personal_hygiene', 'regularity', 'punctuality', 'speaking_behaving'].forEach(a => {
        const v = parseInt($(`#attr_${a}`).val());
        if (!isNaN(v)) data[a] = v;
      });
      const hidden = $('#behaviorData_' + currentStudentId);
      hidden.val(JSON.stringify(data)).attr('name', `entries[${currentStudentId}][behavior_attributes]`);

      // Instant AJAX save
      fetch('{{ request()->getBaseUrl() }}/manual-exams/attributes-save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          student_id: currentStudentId,
          class_id: $('input[name="class_id"]').val(),
          section: $('input[name="section"]').val(),
          session: $('input[name="session"]').val(),
          term: $('input[name="term"]').val(),
          attributes: data
        })
      });
      updateBehaviorSummary(currentStudentId, data);
      recalcOverallGradeForStudent(currentStudentId);
      bootstrap.Modal.getInstance(attrModal[0]).hide();
    };

    function updateBehaviorSummary(sid, data) {
      const btn = $(`button[data-stu-id="${sid}"]`);
      if (!btn.length) return;
      const vals = Object.values(data).map(v => parseInt(v)).filter(v => !isNaN(v));
      if (!vals.length) return;
      const avg = vals.reduce((a, b) => a + b, 0) / vals.length;
      let s = 'Needs Improvement';
      if (avg >= 4.5) s = 'Excellent';
      else if (avg >= 3.5) s = 'Very Good';
      else if (avg >= 2.5) s = 'Good';
      else if (avg >= 1.5) s = 'Satisfactory';
      let sm = btn.parent().find('small.text-muted');
      if (!sm.length) {
        btn.parent().append('<br><small class="text-muted"></small>');
        sm = btn.parent().find('small.text-muted');
      }
      sm.text(s);
    }

    // === 3. Grading and Calculations ===
    function mapPercentToGrade(p) {
      if (p >= 95) return 'A++';
      if (p >= 90) return 'A+';
      if (p >= 85) return 'A';
      if (p >= 80) return 'B++';
      if (p >= 75) return 'B+';
      if (p >= 70) return 'B';
      if (p >= 60) return 'C';
      if (p >= 50) return 'D';
      return 'F';
    }

    window.recalcOverallGradeForStudent = function(sid) {
      const row = $(`#behaviorData_${sid}`).closest('tr');
      if (!row.length) return;
      let obtained = 0,
        total = 0;
      row.find('input.marks-input[max]').each(function() {
        const m = parseFloat($(this).attr('max')),
          v = parseFloat($(this).val());
        if (m > 0) {
          total += m;
          if (v > 0) obtained += v;
        }
      });
      const subjPct = total > 0 ? (obtained / total) * 100 : 0;
      let behPct = 0,
        behAvail = false;
      try {
        const data = JSON.parse($(`#behaviorData_${sid}`).val() || '{}');
        const vals = Object.values(data).map(v => parseInt(v)).filter(v => !isNaN(v));
        if (vals.length) {
          behPct = (vals.reduce((a, b) => a + b, 0) / vals.length) / 5 * 100;
          behAvail = true;
        }
      } catch (_) {}
      const finalPct = behAvail ? (subjPct * 0.8 + behPct * 0.2) : subjPct;
      row.find('input[name*="overall_grade"]').val(mapPercentToGrade(finalPct));
    };

    // Marks color coding and reactive update
    $(document).on('input', 'input.marks-input', function() {
      const inp = $(this),
        val = parseFloat(inp.val()),
        max = parseFloat(inp.attr('max'));
      const pass = parseFloat(inp.data('passing-marks')) || (max / 2);
      inp.removeClass('marks-input-pass marks-input-fail marks-input-exceed disabled-absent');
      if (inp.val().toUpperCase() === 'A') inp.addClass('disabled-absent');
      else if (val > max) inp.addClass('marks-input-exceed');
      else if (!isNaN(val)) inp.addClass(val >= pass ? 'marks-input-pass' : 'marks-input-fail');

      const sid = inp.attr('name').match(/entries\[(\d+)\]/)?.[1];
      if (sid) recalcOverallGradeForStudent(sid);
    });

    // Attendance Calculations
    $(document).on('input', 'input[name*="total_working_days"], input[name*="total_present"]', function() {
      const row = $(this).closest('tr'),
        wInput = row.find('input[name*="total_working_days"]'),
        pInput = row.find('input[name*="total_present"]'),
        aInput = row.find('input[name*="total_absent"]');
      const w = parseInt(wInput.val()) || 0,
        p = parseInt(pInput.val()) || 0;
      if (w > 0) {
        aInput.val(Math.max(0, w - p));
        const pct = (p / w) * 100;
        pInput.css('background-color', pct >= 90 ? '#d4edda' : (pct >= 75 ? '#fff3cd' : '#f8d7da'));
      }
    });

    // === 4. Subject Column Filter ===
    (function() {
      const storageKey = `manualExamSubjectFilter:${{{ $selectedClassId ?? 0 }}}:{{ $term ?? '' }}:{{ $selectedSection ?? '' }}`;
      const table = $('#marksEntryTable');
      if (!table.length) return;

      function applyFilter() {
        const keys = new Set($('.subject-filter-checkbox:checked').map((_, cb) => cb.value).get());
        localStorage.setItem(storageKey, JSON.stringify([...keys]));
        const headerCells = table.find('thead tr:first th');
        headerCells.each((idx, th) => {
          const k = $(th).data('subject-key');
          if (k) {
            const show = keys.has(k);
            table.find(`tr`).each((_, r) => $(r.cells[idx]).toggle(show));
          }
        });
      }

      $('#btnApplySubjectFilter').on('click', applyFilter);
      $('#btnSelectAllSubjects').on('click', () => {
        $('.subject-filter-checkbox').prop('checked', true);
      });
      $('#btnClearAllSubjects').on('click', () => {
        $('.subject-filter-checkbox').prop('checked', false);
      });

      // Restore
      try {
        const saved = JSON.parse(localStorage.getItem(storageKey));
        if (saved) {
          $('.subject-filter-checkbox').each((_, cb) => cb.checked = saved.includes(cb.value));
          applyFilter();
        }
      } catch (_) {}
    })();

    // === 5. Utility and Printing ===
    window.printReport = function() {
      const win = window.open('', '', 'height=600,width=800');
      win.document.write(`<html><head><title>Report</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{padding:20px;font-size:10px;}.no-print{display:none !important;}</style></head><body>${$('#studentReport').html()}</body></html>`);
      win.document.close();
      win.focus();
      setTimeout(() => {
        win.print();
        win.close();
      }, 500);
    };

    window.printEntrySheet = function() {
      const clone = $('#marksEntryTable').clone();
      clone.find('input, select').each(function() {
        $(this).replaceWith(`<span>${$(this).val() || ''}</span>`);
      });
      const win = window.open('', '', 'height=600,width=800');
      win.document.write(`<html><head><title>Entry Sheet</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{padding:20px; font-size:9px;} table{width:100%; border-collapse:collapse;} th,td{border:1px solid #ddd; padding:4px; text-align:center;}</style></head><body>${clone[0].outerHTML}</body></html>`);
      win.document.close();
      win.focus();
      setTimeout(() => {
        win.print();
        win.close();
      }, 500);
    };

    // Save report extras
    window.saveReportExtras = function(e) {
      e.preventDefault();
      const data = {
        student_id: $('#extras_student_id').val(),
        term: $('#extras_term').val(),
        session: $('#extras_session').val(),
        improvement_required: $('#improvement_required').val(),
        principal_remarks: $('#principal_remarks').val() || $('#auto_principal_remarks').val(),
        class_id: '{{ $selectedClassId }}',
        teacher_id: '{{ $selectedTeacherId ?? ($teacher->id ?? 0) }}'
      };
      fetch('{{ request()->getBaseUrl() }}/manual-exams/report-extras', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
      }).then(() => {
        alert('Saved!');
        window.location.reload();
      });
    };

    // Form validation
    window.validateAndSaveEntries = function() {
      let hasData = false;
      $('#marksEntryTable tbody tr').each(function() {
        if ($(this).find('input.marks-input').filter((_, el) => $(el).val()).length > 0) hasData = true;
      });
      if (!hasData) {
        alert('Enter some data!');
        return false;
      }
      return confirm('Save all entries?');
    };

    // Enter Key Navigation
    $(document).on('keydown', '#marksEntryTable input', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const td = $(this).closest('td'),
          tr = $(this).closest('tr');
        const colIdx = tr.find('td').index(td);
        const nextRow = e.shiftKey ? tr.prev() : tr.next();
        const nextInp = nextRow.find('td').eq(colIdx).find('input,select');
        if (nextInp.length) {
          nextInp.focus().select();
        }
      }
    });

    // AI Upload
    window.uploadAndAutofill = function(input) {
      if (!input.files?.[0]) return;
      const fd = new FormData();
      fd.append('sheet_image', input.files[0]);
      const subHeaders = $('#marksEntryTable thead th.subject-col').map((_, th) => $(th).attr('title')?.split('(')[0].trim() || $(th).text().trim()).get();
      fd.append('class', '{{ $selectedClassId }}');
      fd.append('section', '{{ $selectedSection }}');
      fd.append('term', '{{ $term }}');
      subHeaders.forEach((s, i) => fd.append(`subjects[${i}]`, s));

      const btn = $('button[onclick*="sheetUpload"]');
      const orig = btn.html();
      btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Extracting...');

      fetch('{{ route("manual-exams.upload-sheet") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: fd
      }).then(r => r.json()).then(res => {
        btn.prop('disabled', false).html(orig);
        if (!res.success) {
          alert(res.error);
          return;
        }
        let count = 0;
        const subMap = {};
        $('#marksEntryTable thead th.subject-col').each((idx, th) => {
          subMap[$(th).attr('title').split('(')[0].trim().toLowerCase()] = idx;
        });

        res.data.forEach(rec => {
          $('#marksEntryTable tbody tr').each(function() {
            if ($(this).find('td:first').text().split('-')[0].trim() == rec.grno) {
              const inps = $(this).find('input.marks-input');
              Object.entries(rec.marks).forEach(([sName, score]) => {
                const idx = subMap[sName.toLowerCase().trim()];
                if (idx !== undefined && inps[idx]) {
                  $(inps[idx]).val(score).trigger('input');
                  count++;
                }
              });
            }
          });
        });
        alert(`Filled ${count} entries.`);
      }).catch(() => {
        btn.prop('disabled', false).html(orig);
        alert('Failed.');
      });
    };

    // Alert timeout
    setTimeout(() => $('#alert-message').fadeOut(), 4000);

    // Initializations
    if (filterClass.val()) loadSections(filterClass.val());
    $('.marks-input').trigger('input');
  });

  // GR NO Autocomplete
  (function() {
    const grInput = document.getElementById('grnoInput');
    const dl = document.getElementById('grnoDatalist');
    if (!grInput) return;
    grInput.addEventListener('input', function() {
      const q = this.value.trim();
      if (q.length < 1) return;
      fetch(`{{ request()->getBaseUrl() }}/manual-exams/grno-search?q=${encodeURIComponent(q)}`)
        .then(r => r.json()).then(list => {
          dl.innerHTML = '';
          list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.grno;
            opt.label = `${item.grno} - ${item.name}`;
            dl.appendChild(opt);
          });
        });
    });
  })();
</script>
@endsection
@endsection