<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Barryvdh\DomPDF\Facade as Pdf;

class ManualExamsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Ensure required tables exist
        $this->ensureTable();

        $term = $request->input('term', 'Mid Term');
        $selectedClassId = (int) $request->input('class_id', 0);
        $selectedSection = $request->input('section', '');
        $selectedSubject = null; // subject filter removed
        $selectedStudentId = (int) $request->input('student_id', 0);
        $sessionValue = $request->input('session', 'April 2024 to March 2025');
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        // New: GR.No global search support (ignores class/section filters)
        $searchGrno = trim((string)$request->input('grno', ''));
        if ($searchGrno !== '') {
            $found = DB::table('students')->where('grno', $searchGrno)->first();
            if ($found) {
                // Use class_id directly from students table
                $selectedClassId = (int) $found->class_id;
                $selectedStudentId = (int) $found->grno;
                // Do not constrain by section when searching by GR.No (search across sections)
                $selectedSection = '';
            }
        }

        // Get all classes for the dropdown
        $classesForTeacher = DB::table('classes')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->orderBy('className')->get();
        
        // Get all sections for initial dropdown population
        $allSections = DB::table('students')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->select('section')
            ->distinct()
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->orderBy('section')
            ->pluck('section')
            ->toArray();

        // Default to first class if none selected
        if (!$selectedClassId && $classesForTeacher->isNotEmpty()) {
            $selectedClassId = (int) $classesForTeacher->first()->id;
        }

        $students = collect([]);
        $classSubjectsForEntry = collect([]);
        $subjectsForTeacher = [];
        $existingByStudentId = [];
        $selectedStudent = null;
        $availableSections = [];
        $allSections = [];
        $reportSubjects = [];
        $valsForSelected = [];
        $behaviorAttributesFromTable = [];
        $behaviorOverallAverage = null;
        $subjectsToImproveData = [];
        $absentSubjectsData = [];
        
        // Ensure exam_terms table exists
        $this->ensureExamTermsTable();
        
        // Get available terms for the selected class
        $availableTerms = $this->getAvailableTerms($selectedClassId);

        if ($selectedClassId) {
            // Get the class name for the selected class
            $selectedClassName = DB::table('classes')->where('id', $selectedClassId)->value('className');
            
            // Get all available sections for this class
            if ($selectedClassName) {
                $availableSections = DB::table('students')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                    ->select('section')
                    ->where('class_id', $selectedClassId)
                    ->distinct()
                    ->whereNotNull('section')
                    ->where('section', '!=', '')
                    ->orderBy('section')
                    ->pluck('section')
                    ->toArray();
            }
            
            // Get students for selected class and section
            $studentsQuery = DB::table('students')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId);
                
            // Add section filter if selected
            if (!empty($selectedSection)) {
                $studentsQuery->where('section', $selectedSection);
            }
            
            $studentsRaw = $studentsQuery
                ->orderByRaw('CAST(grno AS UNSIGNED) ASC')
                ->get();

            // Use raw student data directly (no mapping needed)
            $students = $studentsRaw;

            // Get subjects for the selected class and term
            if (Schema::hasTable('subjects')) {
                // Get subjects applicable to the selected term (term = selected OR general NULL)
                $allSubjects = DB::table('subjects')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                    ->where('class_id', $selectedClassId)
                    ->where('status', 'active')
                    ->where(function($q) use ($term) {
                        $q->whereNull('term')->orWhere('term', $term);
                    })
                    ->orderBy('sort_order')
                    ->orderBy('subject_name')
                    ->get();
                
                // Group subjects by name to handle duplicates properly
                $subjectsByName = [];
                foreach ($allSubjects as $subject) {
                    $name = $subject->subject_name;
                    
                    // Prioritize term-specific subjects over general subjects
                    if (!isset($subjectsByName[$name])) {
                        $subjectsByName[$name] = $subject;
                    } else {
                        // If we already have a subject with this name, prefer term-specific
                        if ($subject->term === $term && $subjectsByName[$name]->term !== $term) {
                            $subjectsByName[$name] = $subject;
                        }
                        // If both are general or both are term-specific, keep the first one
                    }
                }
                
                // Convert back to collection and process term-specific marks
                $processedSubjects = collect([]);
                foreach ($subjectsByName as $subject) {
                    $processedSubject = clone $subject;
                    
                    // Check if term_marks column exists and has term-specific configuration
                    $enabledForTerm = true;
                    if (Schema::hasColumn('subjects', 'term_marks') && !empty($subject->term_marks)) {
                        $termMarks = json_decode($subject->term_marks, true);
                        if (is_array($termMarks) && isset($termMarks[$term])) {
                            $termConfig = $termMarks[$term];
                            // Allow hiding subjects for specific term via JSON flag
                            if (array_key_exists('enabled', $termConfig) && $termConfig['enabled'] === false) {
                                $enabledForTerm = false;
                            }
                            // Override total_marks and passing_marks for this term
                            if (isset($termConfig['total_marks'])) {
                                $processedSubject->total_marks = $termConfig['total_marks'];
                            }
                            if (isset($termConfig['passing_marks'])) {
                                $processedSubject->passing_marks = $termConfig['passing_marks'];
                            }
                        }
                    }
                    
                    if ($enabledForTerm) {
                        $processedSubjects->push($processedSubject);
                    }
                }
                
                // Sort the final subjects
                $classSubjectsForEntry = $processedSubjects->sortBy(['sort_order', 'subject_name'])->values();
                $subjectsForTeacher = $classSubjectsForEntry->pluck('subject_name')->toArray();
                
                // Build reportSubjects mapping label => key used in entries JSON
                foreach ($classSubjectsForEntry as $subj) {
                    $label = $subj->subject_name;
                    $key = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $label));
                    $reportSubjects[$label] = $key;
                }
            }

            // Get existing exam entries for this class and term (subject aggregated as 'all')
            if (Schema::hasTable('manual_exam_entries')) {
                $existingEntries = DB::table('manual_exam_entries')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                    ->where('class_id', $selectedClassId)
                    ->where('term', $term)
                    ->where(function($q){
                        $q->where('subject', 'all')
                          ->orWhereNull('subject')
                          ->orWhere('subject', '');
                    })
                    ->get();

                foreach ($existingEntries as $row) {
                    $existingByStudentId[$row->student_id] = json_decode($row->data, true) ?: [];
                }
            }

            // Get selected student details if requested
            if ($selectedStudentId) {
                $selectedStudent = DB::table('students')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                    ->where('grno', $selectedStudentId)
                    ->first();
                // Values for the selected student to simplify view logic
                $valsForSelected = $existingByStudentId[$selectedStudentId] ?? [];
                
                // Get stored subjects to improve data
                $subjectsToImproveData = [];
                $absentSubjectsData = [];
                if (Schema::hasTable('manual_exam_entries')) {
                    $entryRow = DB::table('manual_exam_entries')
                        ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                        ->where('class_id', $selectedClassId)
                        ->where('student_id', $selectedStudentId)
                        ->where('term', $term)
                        ->where('subject', 'all')
                        ->first();
                    if ($entryRow) {
                        $subjectsToImproveData = json_decode($entryRow->subjects_to_improve ?? '[]', true) ?: [];
                        $absentSubjectsData = json_decode($entryRow->absent_subjects ?? '[]', true) ?: [];
                    }
                }

                // Fetch behavior attributes from dedicated table (preferred source)
                $behaviorAttributesFromTable = [];
                $behaviorOverallAverage = null;
                if (Schema::hasTable('student_behavior_attributes')) {
                    $row = DB::table('student_behavior_attributes')
                        ->where('student_id', $selectedStudentId)
                        ->where('class_id', $selectedClassId)
                        ->when(!empty($selectedSection), function($q) use ($selectedSection){ $q->where('section', $selectedSection); })
                        ->when(!empty($sessionValue), function($q) use ($sessionValue){ $q->where('session', $sessionValue); })
                        ->where('term', $term)
                        ->first();
                    if ($row) {
                        $decoded = json_decode($row->attributes ?? '[]', true);
                        if (is_array($decoded)) { $behaviorAttributesFromTable = $decoded; }
                        $behaviorOverallAverage = $row->overall_average ?? null;
                    }
                }
            }
        }

        // Fallback: if no subjects loaded from table, infer from saved keys
        if (empty($reportSubjects) && !empty($valsForSelected)) {
            $candidateMap = [
                'English' => 'english',
                'Islamic Studies' => 'islamic_studies',
                'Mathematics' => 'mathematics',
                'Science' => 'science',
                'Urdu' => 'urdu',
            ];
            foreach ($candidateMap as $label => $key) {
                if (array_key_exists($key, $valsForSelected)) {
                    $reportSubjects[$label] = $key;
                }
            }
            // If still empty, grab any numeric-looking subject keys
            if (empty($reportSubjects)) {
                foreach (array_keys($valsForSelected) as $key) {
                    if (in_array($key, ['total_working_days','total_present','total_absent','improvement_studies','behavior_attributes','overall_grade'])) {
                        continue;
                    }
                    $label = ucwords(str_replace('_', ' ', $key));
                    $reportSubjects[$label] = $key;
                }
            }
        }

        // Compute Position in Class (within selected class and optional section)
        $positionInClass = 'N/A';
        $classStrength = 0;
        $selectedStudentOverallPercent = null;
        
        if (!empty($selectedClassId) && isset($students) && $students && $students->count() > 0) {
            $studentIds = $students->pluck('grno')->map(function($v){ return (int)$v; })->all();
            $classStrength = count($studentIds);
            
            // Build subject total marks map to avoid repeated queries
            $subjectTotals = [];
            if ($classSubjectsForEntry && $classSubjectsForEntry->count() > 0) {
                foreach ($classSubjectsForEntry as $subj) {
                    $subjectTotals[$subj->subject_name] = max(1, (int) ($subj->total_marks ?? 100));
                }
            }
            
            // Prefetch behavior attributes from table for all students in this scope
            $behaviorRows = [];
            if (Schema::hasTable('student_behavior_attributes') && !empty($studentIds)) {
                $rows = DB::table('student_behavior_attributes')
                    ->where('class_id', $selectedClassId)
                    ->when(!empty($selectedSection), function($q) use ($selectedSection){ $q->where('section', $selectedSection); })
                    ->when(!empty($sessionValue), function($q) use ($sessionValue){ $q->where('session', $sessionValue); })
                    ->where('term', $term)
                    ->whereIn('student_id', $studentIds)
                    ->get();
                foreach ($rows as $r) { $behaviorRows[(int)$r->student_id] = $r; }
            }
            
            $overallMap = [];
            foreach ($studentIds as $sid) {
                $vals = $existingByStudentId[$sid] ?? [];
                // Subjects percentage based on sum of obtained over sum of total (consistent grade %)
                $sumTotalMarks = 0.0; $sumObtained = 0.0;
                foreach (($reportSubjects ?? []) as $label => $key) {
                    $totalMarks = (int)($subjectTotals[$label] ?? 100);
                    $totalMarks = $totalMarks > 0 ? $totalMarks : 100;
                    $sumTotalMarks += $totalMarks;
                    $score = $vals[$key] ?? '';
                    if (is_numeric($score) && (float)$score > 0) {
                        $sumObtained += (float)$score;
                    }
                }
                $subjectsAvg = $sumTotalMarks > 0 ? (($sumObtained / $sumTotalMarks) * 100.0) : 0.0;
                
                // Behavior average percentage (prefer table)
                $behaviorPct = 0.0;
                if (isset($behaviorRows[$sid])) {
                    $arr = json_decode($behaviorRows[$sid]->attributes ?? '[]', true);
                    if (is_array($arr) && count($arr) > 0) {
                        $avg5 = array_sum($arr) / count($arr);
                        $behaviorPct = ($avg5 / 5.0) * 100.0;
                    }
                } else {
                    $arr = $vals['behavior_attributes'] ?? [];
                    if (is_string($arr)) { $arr = json_decode($arr, true) ?: []; }
                    if (is_array($arr) && count($arr) > 0) {
                        $avg5 = array_sum($arr) / count($arr);
                        $behaviorPct = ($avg5 / 5.0) * 100.0;
                    }
                }
                
                // Apply 80-20 calculation only for Mid Term and Final Term examinations
                // For other exam types, use 100% subjects
                $isMidOrFinal = preg_match('/\b(mid|final)\b/i', $term ?? '') === 1;
                if ($isMidOrFinal) {
                    $overall = ($subjectsAvg * 0.8) + ($behaviorPct * 0.2);
                } else {
                    $overall = $subjectsAvg; // 100% subjects for other exam types
                }
                $overallMap[$sid] = $overall;
            }
            
            if (!empty($overallMap) && $selectedStudentId) {
                // Rank descending by overall percentage
                arsort($overallMap, SORT_NUMERIC);
                $rank = 0; $lastVal = null; $positionByStudent = [];
                foreach ($overallMap as $sid => $val) {
                    if ($lastVal === null || $val < $lastVal) { $rank++; $lastVal = $val; }
                    $positionByStudent[$sid] = $rank;
                }
                $positionInClass = $positionByStudent[$selectedStudentId] ?? 'N/A';
                $selectedStudentOverallPercent = $overallMap[$selectedStudentId] ?? null;
            }
        }
        
        return view('manual_exams', compact(
            'classesForTeacher',
            'selectedClassId',
            'selectedSection',
            'students',
            'classSubjectsForEntry',
            'subjectsForTeacher',
            'selectedSubject',
            'existingByStudentId',
            'term',
            'selectedStudent',
            'selectedStudentId',
            'sessionValue',
            'availableTerms',
            'availableSections',
            'allSections',
            'reportSubjects',
            'valsForSelected',
            'behaviorAttributesFromTable',
            'behaviorOverallAverage',
            'positionInClass',
            'classStrength',
            'selectedStudentOverallPercent',
            'subjectsToImproveData',
            'absentSubjectsData'
        ));
    }

    public function store(Request $request)
    {
        try {
            $this->ensureTable();
            $this->ensureBehaviorAttributesTable();

            $classId = (int) $request->input('class_id');
            $section = (string) $request->input('section', '');
            $sessionVal = (string) $request->input('session', '');
            $subject = 'all'; // store aggregated marks under a fixed subject key
            $term = $request->input('term', 'Mid Term');
            $studentData = $request->input('entries', []);

            DB::beginTransaction();

            // Get class subjects for improvement calculation
            $classSubjects = [];
            if (Schema::hasTable('subjects')) {
                $classSubjects = DB::table('subjects')
                    ->where('class_id', $classId)
                    ->where('status', 'active')
                    ->get();
            }

            foreach ($studentData as $studentGrno => $marks) {
                // Save marks regardless of empty or zero values
                // Only skip if marks array is completely empty
                if (!empty($marks)) {
                    // Normalize lowercase 'a' to uppercase 'A' for absent entries
                    foreach ($marks as $k => $v) {
                        if (is_string($v) && strtolower(trim($v)) === 'a') { $marks[$k] = 'A'; }
                    }
                    // Process absent subjects from checkbox data
                    $absentSubjects = $this->processAbsentSubjects($marks);
                    
                    // Calculate subjects requiring improvement
                    $subjectsToImprove = $this->calculateSubjectsToImprove($marks, $classSubjects, $absentSubjects);
                    
                    DB::table('manual_exam_entries')->updateOrInsert([
                        'class_id' => $classId,
                        'student_id' => $studentGrno, // Using grno as student_id (string format like 'STU001')
                        'subject' => $subject,
                        'term' => $term,
                        'tenant_id' => auth()->check() ? (auth()->user()->tenant_id ?? null) : null,
                    ], [
                        'data' => json_encode($marks),
                        'absent_subjects' => !empty($absentSubjects) ? json_encode($absentSubjects) : null,
                        'subjects_to_improve' => !empty($subjectsToImprove) ? json_encode($subjectsToImprove) : null,
                        'updated_at' => now(),
                    ]);
                }

                // Persist behavior attributes into dedicated table if provided
                if (isset($marks['behavior_attributes'])) {
                    $behaviorRaw = $marks['behavior_attributes'];
                    $behavior = [];
                    if (is_string($behaviorRaw)) {
                        $decoded = json_decode($behaviorRaw, true);
                        if (is_array($decoded)) { $behavior = $decoded; }
                    } elseif (is_array($behaviorRaw)) {
                        $behavior = $behaviorRaw;
                    }

                    // Normalize: keep only numeric 1-5 values
                    $normalized = [];
                    foreach ($behavior as $k => $v) {
                        if ($v === '' || $v === null) { continue; }
                        $iv = (int) $v;
                        if ($iv >= 1 && $iv <= 5) { $normalized[$k] = $iv; }
                    }

                    // Compute overall average if any
                    $overallAvg = null;
                    if (!empty($normalized)) {
                        $overallAvg = round(array_sum($normalized) / count($normalized), 2);
                    }

                    // Upsert into student_behavior_attributes keyed by student/class/section/session/term
                    if (!empty($normalized)) {
                        $exists = DB::table('student_behavior_attributes')
                            ->where('student_id', $studentGrno)
                            ->where('class_id', $classId)
                            ->where('section', $section)
                            ->where('session', $sessionVal)
                            ->where('term', $term)
                            ->exists();
                        if ($exists) {
                            DB::table('student_behavior_attributes')
                                ->where('student_id', $studentGrno)
                                ->where('class_id', $classId)
                                ->where('section', $section)
                                ->where('session', $sessionVal)
                                ->where('term', $term)
                                ->update([
                                    'attributes' => json_encode($normalized),
                                    'overall_average' => $overallAvg,
                                    'updated_at' => now(),
                                ]);
                        } else {
                            DB::table('student_behavior_attributes')->insert([
                                'student_id' => $studentGrno,
                                'class_id' => $classId,
                                'section' => $section,
                                'session' => $sessionVal,
                                'term' => $term,
                                'attributes' => json_encode($normalized),
                                'overall_average' => $overallAvg,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Exam marks saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save marks: ' . $e->getMessage());
        }
    }

    // Get students by class for AJAX calls
    public function getStudentsByClass($classId)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $students = DB::table('students')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->where('class_id', $classId)
            ->orderByRaw('CAST(grno AS UNSIGNED) ASC')
            ->get();

        return response()->json($students);
    }

    // Search students globally by GR.No (for autocomplete)
    public function searchByGrno(Request $request)
    {
        $q = trim((string)$request->input('q', ''));
        if ($q === '') {
            return response()->json([]);
        }
        // Exact-match search (no partials). If user types '4', only grno exactly '4' will appear
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $rows = DB::table('students')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->select('grno', 'studentName as name', 'class_id as class', 'section')
            ->where('grno', $q)
            ->orderByRaw('CAST(grno AS UNSIGNED) ASC')
            ->limit(20)
            ->get();
        return response()->json($rows);
    }
    
    // Get sections by class for dependent dropdown
    public function getSectionsByClassForExams($classId)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $sections = DB::table('students')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->select('section')
            ->where('class_id', $classId)
            ->distinct()
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->orderBy('section')
            ->pluck('section');
            
        return response()->json($sections);
    }
    
    // Get students by class and section for AJAX calls
    public function getStudentsByClassAndSection($classId, $section = null)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $query = DB::table('students')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->where('class_id', $classId);
            
        if ($section && $section !== 'all') {
            $query->where('section', $section);
        }
        
        $students = $query
            ->orderByRaw('CAST(grno AS UNSIGNED) ASC')
            ->get();

        return response()->json($students);
    }

    // Get subjects by class (active only) for dependent dropdown
    public function getSubjectsByClassForExams($classId)
    {
        if (!Schema::hasTable('subjects')) {
            return response()->json([]);
        }
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $subjects = DB::table('subjects')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->select('id', 'subject_name', 'subject_code')
            ->where('class_id', (int)$classId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('subject_name')
            ->get();
        return response()->json($subjects);
    }
    
    // Get subjects by class and term with term-specific marks
    public function getSubjectsByClassAndTerm(Request $request)
    {
        $classId = (int) $request->input('class_id');
        $term = $request->input('term', '');
        
        if (!Schema::hasTable('subjects') || !$classId) {
            return response()->json([]);
        }
        
        // Get all subjects for this class
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $allSubjects = DB::table('subjects')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->select('id', 'subject_name', 'subject_code', 'total_marks', 'passing_marks', 'term_marks', 'term', 'sort_order')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->where(function($q) use ($term) {
                $q->whereNull('term')->orWhere('term', $term);
            })
            ->orderBy('sort_order')
            ->orderBy('subject_name')
            ->get();
        
        // Group subjects by name to handle duplicates properly
        $subjectsByName = [];
        foreach ($allSubjects as $subject) {
            $name = $subject->subject_name;
            
            // Prioritize term-specific subjects over general subjects
            if (!isset($subjectsByName[$name])) {
                $subjectsByName[$name] = $subject;
            } else {
                // If we already have a subject with this name, prefer term-specific
                if ($subject->term === $term && $subjectsByName[$name]->term !== $term) {
                    $subjectsByName[$name] = $subject;
                }
                // If both are general or both are term-specific, keep the first one
            }
        }
        
        // Process subjects to apply term-specific marks if available
        $processedSubjects = [];
        foreach ($subjectsByName as $subject) {
            $processedSubject = (array) $subject;
            
            // Check if term_marks column exists and has term-specific configuration
            $enabledForTerm = true;
            if (Schema::hasColumn('subjects', 'term_marks') && !empty($subject->term_marks)) {
                $termMarks = json_decode($subject->term_marks, true);
                if (is_array($termMarks) && isset($termMarks[$term])) {
                    $termConfig = $termMarks[$term];
                    // Allow hiding via JSON flag
                    if (array_key_exists('enabled', $termConfig) && $termConfig['enabled'] === false) {
                        $enabledForTerm = false;
                    }
                    // Override total_marks and passing_marks for this term
                    if (isset($termConfig['total_marks'])) {
                        $processedSubject['total_marks'] = (float) $termConfig['total_marks'];
                    }
                    if (isset($termConfig['passing_marks'])) {
                        $processedSubject['passing_marks'] = (float) $termConfig['passing_marks'];
                    }
                }
            }
            
            if (!$enabledForTerm) { continue; }
            
            // Ensure marks are properly formatted as floats
            $processedSubject['total_marks'] = (float) $processedSubject['total_marks'];
            $processedSubject['passing_marks'] = (float) $processedSubject['passing_marks'];
            
            // Remove term_marks and other unnecessary fields from response
            unset($processedSubject['term_marks'], $processedSubject['term'], $processedSubject['sort_order']);
            $processedSubjects[] = $processedSubject;
        }
        
        // Sort by original sort order
        usort($processedSubjects, function($a, $b) {
            return strcmp($a['subject_name'], $b['subject_name']);
        });
        
        return response()->json($processedSubjects);
    }

    // Get student report
    public function principalRemarks(Request $request)
    {
        $this->ensureTable();
        $selectedClassId = (int) $request->input('class_id', 0);
        $selectedSection = $request->input('section', '');
        $term = $request->input('term', '');
        $sessionValue = $request->input('session', '');

        // Classes list for filter
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $classes = DB::table('classes')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->orderBy('className')->get();
        // Sections list (optional)
        $sections = DB::table('students')
            ->select('section')
            ->whereNotNull('section')->where('section','!=','')
            ->distinct()->orderBy('section')->pluck('section')->toArray();

        // Base query for aggregated entries
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $q = DB::table('manual_exam_entries')->where('subject','all')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); });
        if ($selectedClassId) $q->where('class_id', $selectedClassId);
        if (!empty($term)) $q->where('term', $term);
        $rows = $q->orderByDesc('updated_at')->get();

        // Index students and classes
        $studentIds = $rows->pluck('student_id')->unique()->map(fn($v)=>(int)$v)->all();
        $studentsMap = [];
        if (!empty($studentIds)) {
            $st = DB::table('students')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->whereIn('grno', $studentIds);
            if (!empty($selectedSection)) $st->where('section',$selectedSection);
            foreach ($st->get() as $s) { $studentsMap[(int)$s->grno] = $s; }
        }
        $classNames = DB::table('classes')->pluck('className','id');

        $items = [];
        foreach ($rows as $r) {
            if (!isset($studentsMap[(int)$r->student_id])) continue; // respect section filter
            $data = json_decode($r->data ?? '{}', true) ?: [];
            $remarks = trim((string)($data['principal_remarks'] ?? ''));
            if ($remarks === '') continue;
            $stu = $studentsMap[(int)$r->student_id] ?? null;
            $items[] = [
                'student_id' => $r->student_id,
                'student_name' => $stu ? ($stu->studentName ?? $r->student_id) : $r->student_id,
                'class' => $classNames[$r->class_id] ?? ('Class '.$r->class_id),
                'section' => $stu->section ?? '',
                'term' => $r->term,
                'remarks' => $remarks,
                'updated_at' => $r->updated_at,
            ];
        }

        return view('principal_remarks', [
            'classes' => $classes,
            'sections' => $sections,
            'selectedClassId' => $selectedClassId,
            'selectedSection' => $selectedSection,
            'term' => $term,
            'sessionValue' => $sessionValue,
            'items' => $items,
        ]);
    }

    public function printAll(Request $request)
    {
        // Expect class_id, section, term, session
        $this->ensureTable();

        $selectedClassId = (int) $request->input('class_id', 0);
        $selectedSection = $request->input('section', '');
        $term = $request->input('term', 'Mid Term');
        $sessionValue = $request->input('session', '');

        // Fetch students list for class & section
        $students = collect([]);
        if ($selectedClassId) {
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            $studentsQuery = DB::table('students')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId);
            if (!empty($selectedSection)) {
                $studentsQuery->where('section', $selectedSection);
            }
            $students = $studentsQuery->orderByRaw('CAST(grno AS UNSIGNED) ASC')->get();
        }

        // Subjects - Use same logic as index method for term-specific filtering
        $classSubjectsForEntry = collect([]);
        $reportSubjects = [];
        if ($selectedClassId && Schema::hasTable('subjects')) {
            // Get subjects applicable to the selected term (term = selected OR general NULL)
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            $allSubjects = DB::table('subjects')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId)
                ->where('status', 'active')
                ->where(function($q) use ($term) {
                    $q->whereNull('term')->orWhere('term', $term);
                })
                ->orderBy('sort_order')
                ->orderBy('subject_name')
                ->get();
            
            // Group subjects by name to handle duplicates properly
            $subjectsByName = [];
            foreach ($allSubjects as $subject) {
                $name = $subject->subject_name;
                
                // Prioritize term-specific subjects over general subjects
                if (!isset($subjectsByName[$name])) {
                    $subjectsByName[$name] = $subject;
                } else {
                    // If we already have a subject with this name, prefer term-specific
                    if ($subject->term === $term && $subjectsByName[$name]->term !== $term) {
                        $subjectsByName[$name] = $subject;
                    }
                    // If both are general or both are term-specific, keep the first one
                }
            }
            
            // Convert back to collection and process term-specific marks
            $processedSubjects = collect([]);
            foreach ($subjectsByName as $subject) {
                $processedSubject = clone $subject;
                
                // Check if term_marks column exists and has term-specific configuration
                $enabledForTerm = true;
                if (Schema::hasColumn('subjects', 'term_marks') && !empty($subject->term_marks)) {
                    $termMarks = json_decode($subject->term_marks, true);
                    if (is_array($termMarks) && isset($termMarks[$term])) {
                        $termConfig = $termMarks[$term];
                        // Allow hiding subjects for specific term via JSON flag
                        if (array_key_exists('enabled', $termConfig) && $termConfig['enabled'] === false) {
                            $enabledForTerm = false;
                        }
                        // Override total_marks and passing_marks for this term
                        if (isset($termConfig['total_marks'])) {
                            $processedSubject->total_marks = $termConfig['total_marks'];
                        }
                        if (isset($termConfig['passing_marks'])) {
                            $processedSubject->passing_marks = $termConfig['passing_marks'];
                        }
                    }
                }
                
                if ($enabledForTerm) {
                    $processedSubjects->push($processedSubject);
                }
            }
            
            // Sort the final subjects
            $classSubjectsForEntry = $processedSubjects->sortBy(['sort_order', 'subject_name'])->values();
            
            // Build reportSubjects mapping label => key used in entries JSON
            foreach ($classSubjectsForEntry as $subj) {
                $label = $subj->subject_name;
                $key = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $label));
                $reportSubjects[$label] = $key;
            }
        }

        // Existing entries mapping
        $existingByStudentId = [];
        if (Schema::hasTable('manual_exam_entries') && $selectedClassId) {
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            $existingEntries = DB::table('manual_exam_entries')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId)
                ->where('term', $term)
                ->where(function($q){ $q->where('subject','all')->orWhereNull('subject')->orWhere('subject',''); })
                ->get();
            foreach ($existingEntries as $row) {
                $existingByStudentId[$row->student_id] = json_decode($row->data, true) ?: [];
            }
        }

        return view('manual_exams_print_all', compact(
            'students', 'classSubjectsForEntry', 'existingByStudentId', 'reportSubjects',
            'selectedClassId', 'selectedSection', 'sessionValue', 'term'
        ));
    }

    public function printAllPdf(Request $request)
    {
        // Reuse the same data preparation as printAll
        $this->ensureTable();

        $selectedClassId = (int) $request->input('class_id', 0);
        $selectedSection = $request->input('section', '');
        $term = $request->input('term', 'Mid Term');
        $sessionValue = $request->input('session', '');

        $students = collect([]);
        if ($selectedClassId) {
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            $studentsQuery = DB::table('students')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId);
            if (!empty($selectedSection)) {
                $studentsQuery->where('section', $selectedSection);
            }
            $students = $studentsQuery->orderByRaw('CAST(grno AS UNSIGNED) ASC')->get();
        }

        $classSubjectsForEntry = collect([]);
        $reportSubjects = [];
        if ($selectedClassId && \Illuminate\Support\Facades\Schema::hasTable('subjects')) {
            $allSubjects = DB::table('subjects')
                ->where('class_id', $selectedClassId)
                ->where('status', 'active')
                ->where(function($q) use ($term) {
                    $q->whereNull('term')->orWhere('term', $term);
                })
                ->orderBy('sort_order')
                ->orderBy('subject_name')
                ->get();

            $subjectsByName = [];
            foreach ($allSubjects as $subject) {
                $name = $subject->subject_name;
                if (!isset($subjectsByName[$name])) {
                    $subjectsByName[$name] = $subject;
                } else {
                    if ($subject->term === $term && $subjectsByName[$name]->term !== $term) {
                        $subjectsByName[$name] = $subject;
                    }
                }
            }

            $processedSubjects = collect([]);
            foreach ($subjectsByName as $subject) {
                $processedSubject = clone $subject;
                $enabledForTerm = true;
                if (\Illuminate\Support\Facades\Schema::hasColumn('subjects', 'term_marks') && !empty($subject->term_marks)) {
                    $termMarks = json_decode($subject->term_marks, true);
                    if (is_array($termMarks) && isset($termMarks[$term])) {
                        $termConfig = $termMarks[$term];
                        if (array_key_exists('enabled', $termConfig) && $termConfig['enabled'] === false) {
                            $enabledForTerm = false;
                        }
                        if (isset($termConfig['total_marks'])) {
                            $processedSubject->total_marks = $termConfig['total_marks'];
                        }
                        if (isset($termConfig['passing_marks'])) {
                            $processedSubject->passing_marks = $termConfig['passing_marks'];
                        }
                    }
                }
                if ($enabledForTerm) {
                    $processedSubjects->push($processedSubject);
                }
            }

            $classSubjectsForEntry = $processedSubjects->sortBy(['sort_order', 'subject_name'])->values();
            foreach ($classSubjectsForEntry as $subj) {
                $label = $subj->subject_name;
                $key = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $label));
                $reportSubjects[$label] = $key;
            }
        }

        $existingByStudentId = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('manual_exam_entries') && $selectedClassId) {
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            $existingEntries = DB::table('manual_exam_entries')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId)
                ->where('term', $term)
                ->where(function($q){ $q->where('subject','all')->orWhereNull('subject')->orWhere('subject',''); })
                ->get();
            foreach ($existingEntries as $row) {
                $existingByStudentId[$row->student_id] = json_decode($row->data, true) ?: [];
            }
        }

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true])
            ->setPaper('a4', 'portrait')
            ->loadView('manual_exams_print_all_pdf', compact(
                'students', 'classSubjectsForEntry', 'existingByStudentId', 'reportSubjects',
                'selectedClassId', 'selectedSection', 'sessionValue', 'term'
            ));

        $filename = 'manual_exams_print_all_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    public function printEntry(Request $request)
    {
        $this->ensureTable();
        $selectedClassId = (int) $request->input('class_id', 0);
        $selectedSection = $request->input('section', '');
        $term = $request->input('term', 'Mid Term');
        $sessionValue = $request->input('session', '');

        // Students
        $students = collect([]);
        if ($selectedClassId) {
            $studentsQuery = DB::table('students')
                ->where('class_id', $selectedClassId);
            if (!empty($selectedSection)) $studentsQuery->where('section', $selectedSection);
            $students = $studentsQuery->orderByRaw('CAST(grno AS UNSIGNED) ASC')->get();
        }

        // Subjects - Use same logic as index method for term-specific filtering
        $classSubjectsForEntry = collect([]);
        $reportSubjects = [];
        if ($selectedClassId && Schema::hasTable('subjects')) {
            // Get subjects applicable to the selected term (term = selected OR general NULL)
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            $allSubjects = DB::table('subjects')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_id', $selectedClassId)
                ->where('status', 'active')
                ->where(function($q) use ($term) {
                    $q->whereNull('term')->orWhere('term', $term);
                })
                ->orderBy('sort_order')
                ->orderBy('subject_name')
                ->get();
            
            // Group subjects by name to handle duplicates properly
            $subjectsByName = [];
            foreach ($allSubjects as $subject) {
                $name = $subject->subject_name;
                
                // Prioritize term-specific subjects over general subjects
                if (!isset($subjectsByName[$name])) {
                    $subjectsByName[$name] = $subject;
                } else {
                    // If we already have a subject with this name, prefer term-specific
                    if ($subject->term === $term && $subjectsByName[$name]->term !== $term) {
                        $subjectsByName[$name] = $subject;
                    }
                    // If both are general or both are term-specific, keep the first one
                }
            }
            
            // Convert back to collection and process term-specific marks
            $processedSubjects = collect([]);
            foreach ($subjectsByName as $subject) {
                $processedSubject = clone $subject;
                
                // Check if term_marks column exists and has term-specific configuration
                $enabledForTerm = true;
                if (Schema::hasColumn('subjects', 'term_marks') && !empty($subject->term_marks)) {
                    $termMarks = json_decode($subject->term_marks, true);
                    if (is_array($termMarks) && isset($termMarks[$term])) {
                        $termConfig = $termMarks[$term];
                        // Allow hiding subjects for specific term via JSON flag
                        if (array_key_exists('enabled', $termConfig) && $termConfig['enabled'] === false) {
                            $enabledForTerm = false;
                        }
                        // Override total_marks and passing_marks for this term
                        if (isset($termConfig['total_marks'])) {
                            $processedSubject->total_marks = $termConfig['total_marks'];
                        }
                        if (isset($termConfig['passing_marks'])) {
                            $processedSubject->passing_marks = $termConfig['passing_marks'];
                        }
                    }
                }
                
                if ($enabledForTerm) {
                    $processedSubjects->push($processedSubject);
                }
            }
            
            // Sort the final subjects
            $classSubjectsForEntry = $processedSubjects->sortBy(['sort_order', 'subject_name'])->values();
            
            // Build reportSubjects mapping label => key used in entries JSON
            foreach ($classSubjectsForEntry as $subj) {
                $label = $subj->subject_name;
                $key = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $label));
                $reportSubjects[$label] = $key;
            }
        }

        // Existing entries
        $existingByStudentId = [];
        if (Schema::hasTable('manual_exam_entries') && $selectedClassId) {
            $existingEntries = DB::table('manual_exam_entries')
                ->where('class_id', $selectedClassId)
                ->where('term', $term)
                ->where(function($q){ $q->where('subject','all')->orWhereNull('subject')->orWhere('subject',''); })
                ->get();
            foreach ($existingEntries as $row) {
                $existingByStudentId[$row->student_id] = json_decode($row->data, true) ?: [];
            }
        }

        return view('manual_exams_print_entry', compact(
            'students','classSubjectsForEntry','existingByStudentId','reportSubjects','selectedClassId','selectedSection','sessionValue','term'
        ));
    }

    public function getStudentReport(Request $request)
    {
        $studentGrno = $request->input('student_id');
        $classId = $request->input('class_id');
        $section = $request->input('section', '');
        $term = $request->input('term', 'Mid Term');

        // Build student query with optional section filter
        $studentQuery = DB::table('students')->where('grno', $studentGrno);
        
        // Add section filter if provided
        if (!empty($section)) {
            $studentQuery->where('section', $section);
        }
        
        $student = $studentQuery->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Get all subjects for this class
        $subjects = [];
        if (Schema::hasTable('subjects')) {
            $subjects = DB::table('subjects')
                ->where('class_id', $classId)
                ->where('status', 'active')
                ->pluck('subject_name')
                ->toArray();
        }

        // Get marks for all subjects
        $marks = [];
        if (Schema::hasTable('manual_exam_entries')) {
            $entries = DB::table('manual_exam_entries')
                ->where('class_id', $classId)
                ->where('student_id', $studentGrno)
                ->where('term', $term)
                ->get();

            foreach ($entries as $entry) {
                $marks[$entry->subject] = json_decode($entry->data, true) ?: [];
            }
        }

        return response()->json([
            'student' => [
                'id' => $student->grno,
                'name' => $student->studentName,
                'father_name' => $student->father_name ?? '',
                'class' => $student->class_id,
                'section' => $student->section
            ],
            'subjects' => $subjects,
            'marks' => $marks,
            'term' => $term
        ]);
    }

    private function ensureTable()
    {
        if (!Schema::hasTable('manual_exam_entries')) {
            Schema::create('manual_exam_entries', function ($table) {
                $table->id();
                $table->integer('class_id');
                $table->string('student_id', 50); // Will store grno (string format like 'STU001')
                $table->string('subject');
                $table->string('term')->default('Mid Term');
                $table->json('data')->nullable();
                // Optional JSONs to store derived info (added defensively)
                $table->json('absent_subjects')->nullable();
                $table->json('subjects_to_improve')->nullable();
                $table->timestamps();
                
                $table->unique(['class_id', 'student_id', 'subject', 'term']);
            });
        }

        // Ensure the table has the subject column
        if (!Schema::hasColumn('manual_exam_entries', 'subject')) {
            Schema::table('manual_exam_entries', function ($table) {
                $table->string('subject')->after('student_id');
            });
        }
        // Ensure optional helper columns exist (used by store logic)
        if (!Schema::hasColumn('manual_exam_entries', 'absent_subjects')) {
            Schema::table('manual_exam_entries', function ($table) {
                $table->json('absent_subjects')->nullable()->after('data');
            });
        }
        if (!Schema::hasColumn('manual_exam_entries', 'subjects_to_improve')) {
            Schema::table('manual_exam_entries', function ($table) {
                $table->json('subjects_to_improve')->nullable()->after('absent_subjects');
            });
        }
    }
    
    /**
     * Calculate subjects requiring improvement based on marks and attendance
     * Professional algorithm considering multiple factors
     */
    private function calculateSubjectsToImprove($studentMarks, $classSubjects, $absentSubjects = [])
    {
        $improvementSubjects = [];
        
        foreach ($classSubjects as $subject) {
            $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name));
            $obtainedMark = $studentMarks[$fieldKey] ?? null;
            $totalMarks = (float)($subject->total_marks ?? 100);
            $passingMarks = (float)($subject->passing_marks ?? ($totalMarks * 0.5));
            
            $needsImprovement = false;
            $reason = '';
            $priority = 'medium';
            
            // Check if absent in this subject
            if (in_array($subject->subject_name, $absentSubjects)) {
                $needsImprovement = true;
                $reason = 'Absent from examination';
                $priority = 'high';
            }
            // Check if marks are below passing threshold
            elseif ($obtainedMark !== null && is_numeric($obtainedMark)) {
                $percentage = ($obtainedMark / $totalMarks) * 100;
                
                if ($percentage < 40) {
                    $needsImprovement = true;
                    $reason = 'Critical - Below 40%';
                    $priority = 'critical';
                } elseif ($percentage < 50) {
                    $needsImprovement = true;
                    $reason = 'Below passing marks';
                    $priority = 'high';
                } elseif ($percentage < 60) {
                    $needsImprovement = true;
                    $reason = 'Marginal performance';
                    $priority = 'medium';
                }
            }
            // Check if no marks entered (different from absent)
            elseif ($obtainedMark === null || $obtainedMark === '') {
                $needsImprovement = true;
                $reason = 'No marks recorded';
                $priority = 'high';
            }
            
            if ($needsImprovement) {
                $improvementSubjects[] = [
                    'subject_name' => $subject->subject_name,
                    'obtained_marks' => $obtainedMark,
                    'total_marks' => $totalMarks,
                    'percentage' => $obtainedMark && is_numeric($obtainedMark) ? round(($obtainedMark / $totalMarks) * 100, 1) : 0,
                    'reason' => $reason,
                    'priority' => $priority,
                    'passing_marks' => $passingMarks
                ];
            }
        }
        
        // Sort by priority: critical > high > medium
        usort($improvementSubjects, function($a, $b) {
            $priorityOrder = ['critical' => 3, 'high' => 2, 'medium' => 1];
            return ($priorityOrder[$b['priority']] ?? 0) - ($priorityOrder[$a['priority']] ?? 0);
        });
        
        return $improvementSubjects;
    }
    
    /**
     * Format absent subjects array from request data
     */
    private function processAbsentSubjects($requestData)
    {
        $absentSubjects = [];
        
        foreach ($requestData as $key => $value) {
            // Case 1: explicit checkbox pattern (backward compatibility)
            if (strpos($key, '_absent') !== false && $value === 'on') {
                $subjectKey = str_replace('_absent', '', $key);
                $subjectName = ucwords(str_replace('_', ' ', $subjectKey));
                $absentSubjects[] = $subjectName;
                continue;
            }
            // Case 2: value entered as 'A' (or 'a') in marks field
            if (is_string($value) && strtoupper(trim($value)) === 'A') {
                // Exclude known non-subject fields
                if (in_array($key, ['total_working_days','total_present','total_absent','improvement_studies','overall_grade','behavior_attributes'])) {
                    continue;
                }
                // Convert field key back to subject name (same logic used elsewhere)
                $subjectName = ucwords(str_replace('_', ' ', $key));
                $absentSubjects[] = $subjectName;
            }
        }
        
        return array_values(array_unique($absentSubjects));
    }

    private function ensureBehaviorAttributesTable(): void
    {
        if (!Schema::hasTable('student_behavior_attributes')) {
            Schema::create('student_behavior_attributes', function (Blueprint $table) {
                $table->id();
                $table->string('student_id', 50); // grno (string format like 'STU001')
                $table->integer('class_id');
                $table->string('section', 50)->nullable();
                $table->string('session', 128)->nullable();
                $table->string('term', 64);
                $table->json('attributes'); // behavior attributes map
                $table->decimal('overall_average', 5, 2)->nullable();
                $table->timestamps();
                $table->unique(['student_id','class_id','section','session','term'], 'uniq_student_behavior_scope');
                $table->index(['class_id','section','term']);
            });
        } else {
            // Add missing columns defensively
            if (!Schema::hasColumn('student_behavior_attributes', 'overall_average')) {
                Schema::table('student_behavior_attributes', function (Blueprint $table) {
                    $table->decimal('overall_average', 5, 2)->nullable()->after('attributes');
                });
            }
        }
    }

    // CSV Export functionality
    public function exportCsv(Request $request)
    {
        $classId = (int) $request->input('class_id');
        $section = $request->input('section', '');
        $term = $request->input('term', 'Mid Term');
        $sessionValue = $request->input('session', '');

        if (!$classId) {
            return redirect()->back()->with('error', 'Class ID is required for CSV export.');
        }

        // Get students
        $studentsQuery = DB::table('students')->where('class_id', $classId);
        if (!empty($section)) {
            $studentsQuery->where('section', $section);
        }
        $students = $studentsQuery->orderByRaw('CAST(grno AS UNSIGNED) ASC')->get();

        // Get subjects
        $subjects = [];
        if (Schema::hasTable('subjects')) {
            $subjects = DB::table('subjects')
                ->where('class_id', $classId)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('subject_name')
                ->get();
        }

        // Get existing entries
        $existingByStudentId = [];
        if (Schema::hasTable('manual_exam_entries')) {
            $existingEntries = DB::table('manual_exam_entries')
                ->where('class_id', $classId)
                ->where('term', $term)
                ->get();
            foreach ($existingEntries as $row) {
                $existingByStudentId[$row->student_id] = json_decode($row->data, true) ?: [];
            }
        }

        // Generate CSV content
        $csvData = [];
        $headers = ['GR_No', 'Student_Name', 'Class', 'Section'];
        
        // Add subject headers
        foreach ($subjects as $subject) {
            $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name));
            $headers[] = $subject->subject_name . '_(' . $subject->total_marks . ')';
        }
        
        $headers = array_merge($headers, ['Working_Days', 'Present', 'Absent', 'Improvement_Studies', 'Overall_Grade']);
        $csvData[] = $headers;

        // Add student data
        foreach ($students as $student) {
            $vals = $existingByStudentId[$student->grno] ?? [];
            $row = [
                $student->grno,
                $student->studentName,
                $student->class_id,
                $student->section ?? ''
            ];
            
            // Add subject marks
            foreach ($subjects as $subject) {
                $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subject->subject_name));
                $row[] = $vals[$fieldKey] ?? '';
            }
            
            $row = array_merge($row, [
                $vals['total_working_days'] ?? '',
                $vals['total_present'] ?? '',
                $vals['total_absent'] ?? '',
                $vals['improvement_studies'] ?? '',
                $vals['overall_grade'] ?? ''
            ]);
            
            $csvData[] = $row;
        }

        // Create CSV file
        $fileName = 'manual_exam_marks_' . $classId . '_' . $term . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // CSV Import functionality
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
            'class_id' => 'required|integer',
            'term' => 'required|string'
        ]);

        $classId = (int) $request->input('class_id');
        $term = $request->input('term');
        $section = $request->input('section', '');
        $sessionVal = $request->input('session', '');

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file));
            
            if (empty($csvData)) {
                return redirect()->back()->with('error', 'CSV file is empty.');
            }

            $headers = array_shift($csvData); // Remove header row
            $importedCount = 0;

            DB::beginTransaction();

            foreach ($csvData as $row) {
                if (count($row) < count($headers)) {
                    continue; // Skip incomplete rows
                }

                $studentData = array_combine($headers, $row);
                $grno = $studentData['GR_No'] ?? '';

                if (empty($grno)) {
                    continue;
                }

                // Build marks data
                $marksData = [];
                foreach ($studentData as $key => $value) {
                    if ($key === 'GR_No' || $key === 'Student_Name' || $key === 'Class' || $key === 'Section') {
                        continue;
                    }
                    
                    // Convert subject names back to field keys
                    if (strpos($key, '_(') !== false) {
                        $subjectName = preg_replace('/\s*\([^)]*\)$/', '', $key);
                        $fieldKey = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subjectName));
                        $marksData[$fieldKey] = $value;
                    } else {
                        $fieldKey = strtolower($key);
                        $marksData[$fieldKey] = $value;
                    }
                }
                
                // Normalize lowercase 'a' to uppercase 'A' in marks
                foreach ($marksData as $mk => $mv) {
                    if (is_string($mv) && strtolower(trim($mv)) === 'a') { $marksData[$mk] = 'A'; }
                }
                
                // Save to database
                if (!empty($marksData)) {
                    DB::table('manual_exam_entries')->updateOrInsert([
                        'class_id' => $classId,
                        'student_id' => $grno,
                        'subject' => 'all',
                        'term' => $term,
                    ], [
                        'data' => json_encode($marksData),
                        'updated_at' => now(),
                    ]);
                    $importedCount++;
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "CSV imported successfully! {$importedCount} student records processed.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }

    public function saveAttributesAjax(Request $request)
    {
        try {
            $this->ensureTable();
            $this->ensureBehaviorAttributesTable();

            $studentId = (int) $request->input('student_id');
            $classId = (int) $request->input('class_id');
            $section = (string) $request->input('section', '');
            $sessionVal = (string) $request->input('session', '');
            $term = (string) $request->input('term', 'Mid Term');
            $attributes = $request->input('attributes', []);

            if (!$studentId || !$classId) {
                return response()->json(['ok' => false, 'error' => 'Missing student_id or class_id'], 422);
            }

            // Normalize attributes map
            $normalized = [];
            if (is_string($attributes)) {
                $decoded = json_decode($attributes, true);
                if (is_array($decoded)) { $attributes = $decoded; }
            }
            if (is_array($attributes)) {
                foreach ($attributes as $k => $v) {
                    if ($v === '' || $v === null) continue;
                    $iv = (int) $v;
                    if ($iv >= 1 && $iv <= 5) $normalized[$k] = $iv;
                }
            }
            $overallAvg = !empty($normalized) ? round(array_sum($normalized) / count($normalized), 2) : null;

            // Upsert into student_behavior_attributes
            if (!empty($normalized)) {
                $exists = DB::table('student_behavior_attributes')
                    ->where('student_id', $studentId)
                    ->where('class_id', $classId)
                    ->where('section', $section)
                    ->where('session', $sessionVal)
                    ->where('term', $term)
                    ->exists();
                if ($exists) {
                    DB::table('student_behavior_attributes')
                        ->where('student_id', $studentId)
                        ->where('class_id', $classId)
                        ->where('section', $section)
                        ->where('session', $sessionVal)
                        ->where('term', $term)
                        ->update([
                            'attributes' => json_encode($normalized),
                            'overall_average' => $overallAvg,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('student_behavior_attributes')->insert([
                        'student_id' => $studentId,
                        'class_id' => $classId,
                        'section' => $section,
                        'session' => $sessionVal,
                        'term' => $term,
                        'attributes' => json_encode($normalized),
                        'overall_average' => $overallAvg,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Also mirror into manual_exam_entries JSON under subject 'all'
            $existing = DB::table('manual_exam_entries')
                ->where('class_id', $classId)
                ->where('student_id', $studentId)
                ->where('subject', 'all')
                ->where('term', $term)
                ->first();
            $data = [];
            if ($existing) { $data = json_decode($existing->data, true) ?: []; }
            $data['behavior_attributes'] = $normalized;
            DB::table('manual_exam_entries')->updateOrInsert([
                'class_id' => $classId,
                'student_id' => $studentId,
                'subject' => 'all',
                'term' => $term,
            ], [
                'data' => json_encode($data),
                'updated_at' => now(),
                'created_at' => $existing ? $existing->created_at : now(),
            ]);

            return response()->json(['ok' => true, 'saved' => count($normalized), 'overall_average' => $overallAvg]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function aiAnalyze(Request $request, \App\Services\OpenAIService $aiService)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'section' => 'required|string',
            'subjects' => 'required|array',
            'students' => 'required|array',
        ]);

        $analysis = $aiService->analyzeMarks($data);

        return response()->json(['analysis' => $analysis]);
    }

    public function uploadSheet(Request $request, \App\Services\OpenAIService $aiService)
    {
        $request->validate([
            'sheet_image' => 'required|image|max:10240', // Max 10MB
            'class' => 'nullable|string',
            'section' => 'nullable|string',
            'subjects' => 'nullable|array',
            'term' => 'nullable|string',
        ]);

        $path = $request->file('sheet_image')->path();
        $context = [
            'class' => $request->input('class'),
            'section' => $request->input('section'),
            'subjects' => $request->input('subjects'),
            'term' => $request->input('term'),
        ];
        
        $result = $aiService->extractDataFromImage($path, $context);

        if (is_string($result)) {
            return response()->json(['success' => false, 'error' => $result], 500);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    private function ensureExamTermsTable(): void
    {
        if (!Schema::hasTable('exam_terms')) {
            Schema::create('exam_terms', function (Blueprint $table) {
                $table->id();
                $table->string('term_name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->json('applicable_classes')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['is_active', 'sort_order']);
            });
        } else {
            // Check if basic required column exists, if not recreate table
            if (!Schema::hasColumn('exam_terms', 'term_name')) {
                // Drop and recreate the table with correct structure
                Schema::dropIfExists('exam_terms');
                Schema::create('exam_terms', function (Blueprint $table) {
                    $table->id();
                    $table->string('term_name')->unique();
                    $table->string('display_name');
                    $table->text('description')->nullable();
                    $table->json('applicable_classes')->nullable();
                    $table->integer('sort_order')->default(0);
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                    $table->index(['is_active', 'sort_order']);
                });
            } else {
                // Check and add missing columns (add them at the end to avoid dependency issues)
                if (!Schema::hasColumn('exam_terms', 'display_name')) {
                    Schema::table('exam_terms', function (Blueprint $table) {
                        $table->string('display_name');
                    });
                }
                if (!Schema::hasColumn('exam_terms', 'description')) {
                    Schema::table('exam_terms', function (Blueprint $table) {
                        $table->text('description')->nullable();
                    });
                }
                if (!Schema::hasColumn('exam_terms', 'applicable_classes')) {
                    Schema::table('exam_terms', function (Blueprint $table) {
                        $table->json('applicable_classes')->nullable();
                    });
                }
                if (!Schema::hasColumn('exam_terms', 'sort_order')) {
                    Schema::table('exam_terms', function (Blueprint $table) {
                        $table->integer('sort_order')->default(0);
                    });
                }
                if (!Schema::hasColumn('exam_terms', 'is_active')) {
                    Schema::table('exam_terms', function (Blueprint $table) {
                        $table->boolean('is_active')->default(true);
                    });
                }
            }
        }
        
        // Insert default terms if table is empty
        $existingTerms = DB::table('exam_terms')->count();
        if ($existingTerms === 0) {
            DB::table('exam_terms')->insert([
                [
                    'term_name' => '1st Bi-Monthly',
                    'display_name' => '1st Bi-Monthly Examination',
                    'description' => 'First bi-monthly examination',
                    'applicable_classes' => json_encode(['pre-nursery', 'nursery', 'prep', '1', '2', '3', '4', '5', '6', '7', '8']),
                    'sort_order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'term_name' => 'Mid Term',
                    'display_name' => 'Mid Term Examination',
                    'description' => 'Mid term examination',
                    'applicable_classes' => json_encode(['pre-nursery', 'nursery', 'prep', '1', '2', '3', '4']),
                    'sort_order' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'term_name' => 'Grand Test - Mid Term Exams',
                    'display_name' => 'Grand Test - Mid Term Examinations',
                    'description' => 'Grand test for mid term examinations',
                    'applicable_classes' => json_encode(['5', '6', '7', '8']),
                    'sort_order' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'term_name' => 'Mid Term Exams',
                    'display_name' => 'Mid Term Examinations',
                    'description' => 'Mid term examinations for higher classes',
                    'applicable_classes' => json_encode(['5', '6', '7', '8']),
                    'sort_order' => 4,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'term_name' => '2nd Bi-Monthly',
                    'display_name' => '2nd Bi-Monthly Examination',
                    'description' => 'Second bi-monthly examination',
                    'applicable_classes' => json_encode(['pre-nursery', 'nursery', 'prep', '1', '2', '3', '4', '5', '6', '7', '8']),
                    'sort_order' => 5,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'term_name' => 'Grand Test - Final Term',
                    'display_name' => 'Grand Test - Final Term',
                    'description' => 'Grand test for final term',
                    'applicable_classes' => json_encode(['5', '6', '7', '8']),
                    'sort_order' => 6,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'term_name' => 'Final Term',
                    'display_name' => 'Final Term Examination',
                    'description' => 'Final term examination',
                    'applicable_classes' => json_encode(['pre-nursery', 'nursery', 'prep', '1', '2', '3', '4', '5', '6', '7', '8']),
                    'sort_order' => 7,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }
    
    private function getAvailableTerms($classId = null): array
    {
        // Get current class name to determine available terms
        $currentClassName = null;
        if ($classId) {
            $currentClassName = DB::table('classes')->where('id', $classId)->value('className');
        }

        // Extract class level from class name
        $classLevel = $this->extractClassLevel($currentClassName);

        // Get terms from database
        try {
            $query = DB::table('exam_terms');
            
            // Only add conditions if columns exist
            if (Schema::hasColumn('exam_terms', 'is_active')) {
                $query->where('is_active', true);
            }
            
            if (Schema::hasColumn('exam_terms', 'sort_order')) {
                $query->orderBy('sort_order');
            } else {
                $query->orderBy('term_name');
            }
            
            $allTerms = $query->get();
        } catch (\Exception $e) {
            $allTerms = collect([]);
        }

        $availableTerms = [];
        
        foreach ($allTerms as $term) {
            $applicableClasses = [];
            $displayName = $term->term_name;
            
            // Handle applicable_classes column if it exists
            if (isset($term->applicable_classes)) {
                $applicableClasses = json_decode($term->applicable_classes, true) ?: [];
            }
            
            // Handle display_name column if it exists
            if (isset($term->display_name)) {
                $displayName = $term->display_name;
            }
            
            // If no class selected, show all terms
            if (!$classLevel) {
                $availableTerms[$term->term_name] = $displayName;
                continue;
            }
            
            // If no applicable classes defined, show for all classes
            if (empty($applicableClasses)) {
                $availableTerms[$term->term_name] = $displayName;
            } elseif (in_array($classLevel, $applicableClasses)) {
                // Check if current class level is in applicable classes
                $availableTerms[$term->term_name] = $displayName;
            }
        }

        // Fallback to default terms if no database terms found
        if (empty($availableTerms)) {
            $availableTerms = ['Mid Term' => 'Mid Term', 'Final Term' => 'Final Term'];
        }

        return $availableTerms;
    }
    
    private function extractClassLevel($className): ?string
    {
        if (!$className) {
            return null;
        }

        $className = strtolower(trim($className));
        
        // Extract class level
        if (strpos($className, 'pre-nursery') !== false || strpos($className, 'prenursery') !== false) {
            return 'pre-nursery';
        } elseif (strpos($className, 'nursery') !== false) {
            return 'nursery';
        } elseif (strpos($className, 'prep') !== false) {
            return 'prep';
        } elseif (preg_match('/(\d+)/', $className, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}
?>
