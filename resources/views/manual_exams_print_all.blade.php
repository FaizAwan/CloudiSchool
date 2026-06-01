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
    <title>All Student Reports - {{ $term ?? 'Manual Exams' }}</title>
    <style>
        @page { 
            size: A4 portrait; 
            margin: 10mm; 
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.3;
        }
        
        .report-card {
            page-break-after: always;
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            background: white;
        }
        
        .report-card:last-child {
            page-break-after: avoid;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 12px;
            margin: 5px 0;
            color: #666;
        }
        
        .student-info {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            padding: 8px;
            margin-bottom: 15px;
        }
        
        .student-info .row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .student-info .col {
            flex: 1;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 15px;
        }
        
        .marks-table th,
        .marks-table td {
            border: 1px solid #000;
            padding: 4px;
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
            margin-bottom: 15px;
        }
        
        .attendance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 8px;
        }
        
        .attendance-item {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            background: #f8f9fa;
        }
        
        .signatures {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .signature-box {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000; background: rgba(255,255,255,0.95); padding: 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: 1px solid #e0e0e0;">
        <div style="margin-bottom: 10px; font-weight: bold; color: #333; font-size: 12px;">Print Options</div>
        <button onclick="window.print()" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; border: none; padding: 12px 18px; border-radius: 6px; cursor: pointer; margin-right: 8px; font-size: 14px; font-weight: 500; box-shadow: 0 2px 8px rgba(0,123,255,0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,123,255,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,123,255,0.3)'">
            🖨️ Print All
        </button>
        <button onclick="goBack()" style="background: linear-gradient(135deg, #6c757d, #545b62); color: white; border: none; padding: 12px 18px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; box-shadow: 0 2px 8px rgba(108,117,125,0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(108,117,125,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(108,117,125,0.3)'">
            ← Back
        </button>
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
                    <img src="{{ url('images/logo.jpeg') }}" alt="Logo" style="height: 60px; margin-bottom: 10px;">
                    <h1>{{ $schoolName ?? 'School' }}</h1>
                    <h2>{{ $term ?? 'Term' }} - Examination and Progress Report</h2>
                    <div style="font-size: 10px; color: #666;">
                        Academic Session: {{ $sessionValue ?: '2024-2025' }} | Date: {{ date('d-m-Y') }}
                    </div>
                </div>

                <div class="student-info">
                    <div class="row">
                        <div class="col">
                            <strong>STUDENT NAME:</strong> {{ $student->studentName }}
                        </div>
                        <div class="col">
                            <strong>FATHER'S NAME:</strong> {{ $student->father_name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <strong>CLASS:</strong> {{ $student->class_id }}
                        </div>
                        <div class="col">
                            <strong>SECTION:</strong> {{ $student->section ?? 'N/A' }}
                        </div>
                        <div class="col">
                            <strong>GR NO:</strong> {{ $student->grno }}
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <strong style="font-size: 12px;">Subjects and Assessment</strong>
                </div>

                <table class="marks-table">
                    <thead>
                        <tr>
                            <th class="subject">Subject</th>
                            <th>Total Marks</th>
                            <th>Obtained Marks</th>
                            <th>Percentage</th>
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
                                    @if($overallPercentage >= 85) Excellent Performance
                                    @elseif($overallPercentage >= 70) Good Performance
                                    @elseif($overallPercentage >= 50) Satisfactory
                                    @else Needs Significant Improvement
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="attendance-section">
                    <strong style="font-size: 12px; margin-bottom: 8px; display: block;">Attendance Summary</strong>
                    <div class="attendance-grid">
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

                @if(!empty($studentData['improvement_studies']) || !empty($studentData['overall_grade']))
                    <div style="margin-bottom: 15px; border-top: 1px solid #ccc; padding-top: 10px;">
                        @if(!empty($studentData['improvement_studies']))
                            <div style="margin-bottom: 8px;">
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
                    <div class="signature-box">
                        <div style="height: 30px;"></div>
                        Class Teacher's Signature
                    </div>
                    <div class="signature-box">
                        <div style="height: 30px;"></div>
                        Principal's Signature
                    </div>
                    <div class="signature-box">
                        <div style="height: 30px;"></div>
                        Parent/Guardian's Signature
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
            <p><a href="{{ url('/manual-exams') }}">← Back to Manual Exams</a></p>
        </div>
    @endif
</body>
</html>