<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-H3MS6X8GNJ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-H3MS6X8GNJ');
</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Student Reports PDF - {{ $term ?? 'Manual Exams' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.2;
        }
        
        .report-card {
            page-break-after: always;
            border: 2px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            background: white;
        }
        
        .report-card:last-child {
            page-break-after: avoid;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 14px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 11px;
            margin: 4px 0;
            color: #666;
        }
        
        .student-info {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            padding: 6px;
            margin-bottom: 12px;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }
        
        .info-col {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 12px;
        }
        
        .marks-table th,
        .marks-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        .marks-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        
        .marks-table td.subject {
            text-align: left;
            font-weight: bold;
        }
        
        .grade-a { color: #28a745; font-weight: bold; }
        .grade-b { color: #ffc107; font-weight: bold; }
        .grade-c { color: #fd7e14; font-weight: bold; }
        .grade-f { color: #dc3545; font-weight: bold; }
        
        .attendance-section {
            margin-bottom: 12px;
        }
        
        .attendance-grid {
            display: table;
            width: 100%;
            border-spacing: 0;
        }
        
        .attendance-row {
            display: table-row;
        }
        
        .attendance-item {
            display: table-cell;
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            background: #f8f9fa;
            width: 25%;
        }
        
        .signatures {
            margin-top: 15px;
            display: table;
            width: 100%;
        }
        
        .signature-row {
            display: table-row;
        }
        
        .signature-box {
            display: table-cell;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 9px;
            width: 33.33%;
            height: 25px;
        }
    </style>
</head>
<body>
    @if($students->isNotEmpty())
        @foreach($students as $student)
            @php
                $studentData = $existingByStudentId[$student->grno] ?? [];
                
                // Calculate grades and totals (include all subjects; treat 'A' as absent)
                $totalMarks = 0;
                $obtainedMarks = 0;
                $subjectResults = [];
                
                foreach($classSubjectsForEntry as $subject) {
                    $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name));
                    $mark = $studentData[$fieldKey] ?? '';
                    $totalForSubj = (float)($subject->total_marks ?? 100);
                    $totalMarks += $totalForSubj;
                    
                    $isAbsent = is_string($mark) && strtoupper(trim($mark)) === 'A';
                    $isNumeric = is_numeric($mark);
                    $obtained = $isNumeric ? (float)$mark : 0;
                    if ($isNumeric) { $obtainedMarks += $obtained; }
                    
                    $percentage = ($totalForSubj > 0 && $isNumeric) ? (($obtained / $totalForSubj) * 100) : null;
                    
                    $grade = '';
                    $gradeClass = '';
                    if ($percentage !== null) {
                        if ($percentage >= 85) { $grade = 'A'; $gradeClass = 'grade-a'; }
                        elseif ($percentage >= 70) { $grade = 'B'; $gradeClass = 'grade-b'; }
                        elseif ($percentage >= 50) { $grade = 'C'; $gradeClass = 'grade-c'; }
                        else { $grade = 'F'; $gradeClass = 'grade-f'; }
                    }
                    
                    $subjectResults[] = [
                        'name' => $subject->subject_name,
                        'total' => $totalForSubj,
                        'obtained' => $isAbsent ? 'A' : ($isNumeric ? (string)$mark : ''),
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'gradeClass' => $gradeClass,
                        'absent' => $isAbsent
                    ];
                }
                
                $overallPercentage = ($totalMarks > 0) ? (($obtainedMarks / $totalMarks) * 100) : 0;
            @endphp
            
            <div class="report-card">
                <div class="header">
                    <img src="{{ public_path('images/logo.jpeg') }}" alt="Logo" style="height: 50px; margin-bottom: 5px;">
                    <h1>{{ $schoolName ?? 'School' }}</h1>
                    <h2>{{ $term ?? 'Term' }} - Examination and Progress Report</h2>
                    <div style="font-size: 9px; color: #666;">
                        Academic Session: {{ $sessionValue ?: '2024-2025' }} | Date: {{ date('d-m-Y') }}
                    </div>
                </div>

                <div class="student-info">
                    <div class="info-row">
                        <div class="info-col">
                            <strong>STUDENT NAME:</strong> {{ $student->studentName }}
                        </div>
                        <div class="info-col">
                            <strong>FATHER'S NAME:</strong> {{ $student->father_name ?? 'N/A' }}
                        </div>
                        <div class="info-col">
                            <strong>GR NO:</strong> {{ $student->grno }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-col">
                            <strong>CLASS:</strong> {{ $student->class_id }}
                        </div>
                        <div class="info-col">
                            <strong>SECTION:</strong> {{ $student->section ?? 'N/A' }}
                        </div>
                        <div class="info-col">
                            <!-- Empty cell for alignment -->
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 11px;">Subjects and Assessment</strong>
                </div>

                <table class="marks-table">
                    <thead>
                        <tr>
                            <th class="subject">Subject</th>
                            <th>Total</th>
                            <th>Obtained</th>
                            <th>%</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjectResults as $result)
                            <tr>
                                <td class="subject">{{ $result['name'] }}</td>
                                <td>{{ $result['total'] }}</td>
                                <td><strong>{{ $result['obtained'] !== '' ? $result['obtained'] : '-' }}</strong></td>
                                <td>
                                    @if(!is_null($result['percentage']))
                                        {{ number_format($result['percentage'], 1) }}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="{{ $result['gradeClass'] }}">{{ $result['absent'] ? '-' : ($result['grade'] ?: '-') }}</td>
                                <td>
                                    @if($result['absent']) Absent
                                    @elseif(!is_null($result['percentage']) && $result['percentage'] >= 85) Excellent
                                    @elseif(!is_null($result['percentage']) && $result['percentage'] >= 70) Good
                                    @elseif(!is_null($result['percentage']) && $result['percentage'] >= 50) Satisfactory
                                    @elseif(!is_null($result['percentage'])) Needs Improvement
                                    @else -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        
                        @if(count($subjectResults) > 0)
                            <tr style="background-color: #e9ecef; font-weight: bold;">
                                <td class="subject">TOTAL</td>
                                <td>{{ $totalMarks }}</td>
                                <td>{{ $obtainedMarks }}</td>
                                <td>{{ number_format($overallPercentage, 1) }}%</td>
                                <td class="{{ $overallPercentage >= 85 ? 'grade-a' : ($overallPercentage >= 70 ? 'grade-b' : ($overallPercentage >= 50 ? 'grade-c' : 'grade-f')) }}">
                                    @if($overallPercentage >= 85) A
                                    @elseif($overallPercentage >= 70) B
                                    @elseif($overallPercentage >= 50) C
                                    @else F
                                    @endif
                                </td>
                                <td>
                                    @if($overallPercentage >= 85) Excellent
                                    @elseif($overallPercentage >= 70) Good
                                    @elseif($overallPercentage >= 50) Satisfactory
                                    @else Needs Improvement
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="attendance-section">
                    <strong style="font-size: 11px; margin-bottom: 6px; display: block;">Attendance Summary</strong>
                    <div class="attendance-grid">
                        <div class="attendance-row">
                            <div class="attendance-item">
                                <strong>Working Days</strong><br>
                                {{ $studentData['total_working_days'] ?? 'N/A' }}
                            </div>
                            <div class="attendance-item">
                                <strong>Present</strong><br>
                                {{ $studentData['total_present'] ?? 'N/A' }}
                            </div>
                            <div class="attendance-item">
                                <strong>Absent</strong><br>
                                {{ $studentData['total_absent'] ?? 'N/A' }}
                            </div>
                            <div class="attendance-item">
                                <strong>Attendance %</strong><br>
                                @php
                                    $workingDays = (int)($studentData['total_working_days'] ?? 0);
                                    $present = (int)($studentData['total_present'] ?? 0);
                                    $attendancePercentage = ($workingDays > 0) ? (($present / $workingDays) * 100) : 0;
                                @endphp
                                {{ $workingDays > 0 ? number_format($attendancePercentage, 1) . '%' : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($studentData['improvement_studies']) || !empty($studentData['overall_grade']))
                    <div style="margin-bottom: 12px; border-top: 1px solid #ccc; padding-top: 8px;">
                        @if(!empty($studentData['improvement_studies']))
                            <div style="margin-bottom: 6px;">
                                <strong>Improvement Required:</strong> {{ $studentData['improvement_studies'] }}
                            </div>
                        @endif
                        @if(!empty($studentData['overall_grade']))
                            <div>
                                <strong>Overall Grade:</strong> {{ $studentData['overall_grade'] }}
                            </div>
                        @endif
                    </div>
                @endif

                <div class="signatures">
                    <div class="signature-row">
                        <div class="signature-box">
                            Class Teacher's Signature
                        </div>
                        <div class="signature-box">
                            Principal's Signature
                        </div>
                        <div class="signature-box">
                            Parent/Guardian's Signature
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 50px; border: 2px dashed #ccc; color: #666;">
            <h3>No Student Reports Available</h3>
            <p>Please ensure:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>A class is selected</li>
                <li>Students are enrolled in the selected class</li>
                <li>Marks have been entered for students</li>
            </ul>
        </div>
    @endif
</body>
</html>