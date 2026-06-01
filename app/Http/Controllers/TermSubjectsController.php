<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class TermSubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Render the wizard page (lightweight; data is fetched via AJAX)
    public function index(Request $request)
    {
        $classes = DB::table('classes')->orderBy('className')->get();
        return view('term_subjects_wizard', [
            'classes' => $classes,
        ]);
    }

    // Fetch subjects and available terms for a class
    public function fetch(Request $request)
    {
        $classId = (int) $request->input('class_id');
        if (!$classId) {
            return response()->json(['error' => 'Missing class_id'], 422);
        }

        // Validate class exists
        $classExists = DB::table('classes')->where('id', $classId)->exists();
        if (!$classExists) {
            return response()->json(['error' => 'Class not found'], 404);
        }

        // Build terms list (from exam_terms when available)
        $terms = [];
        try {
            if (Schema::hasTable('exam_terms')) {
                $query = DB::table('exam_terms');
                if (Schema::hasColumn('exam_terms', 'is_active')) {
                    $query->where('is_active', true);
                }
                if (Schema::hasColumn('exam_terms', 'sort_order')) {
                    $query->orderBy('sort_order');
                } else {
                    $query->orderBy('term_name');
                }
                $rows = $query->get();

                // Normalize and de-duplicate similar terms (e.g., "Mid Term" vs "Mid Term Exams")
                $canon = [];
                foreach ($rows as $r) {
                    $tname = trim((string)$r->term_name);
                    $key = strtolower($tname);
                    $key = str_replace(['  ', '   '], ' ', $key);
                    $key = preg_replace('/\s+/', ' ', $key);
                    // Canonical mapping for mid term variants
                    if ($key === 'mid term exams' || $key === 'mid-term exams' || $key === 'midterm exams') {
                        $key = 'mid term';
                        $tname = 'Mid Term';
                    }
                    if (!array_key_exists($key, $canon)) {
                        // Preferred labels for certain canonical terms
                        $label = isset($r->display_name) ? $r->display_name : $tname;
                        if ($key === 'mid term') { $label = 'Mid Term Examinations'; $tname = 'Mid Term Exams'; }
                        if ($key === '1st bi-monthly') { $label = '1st Bi-Monthly Examination'; }
                        if ($key === '2nd bi-monthly') { $label = '2nd Bi-Monthly Examination'; }
                        if ($key === 'grand test - mid term exams' || $key === 'grand test - mid term examinations') { $label = 'Grand Test - Mid Term Examinations'; $tname = 'Grand Test - Mid Term Exams'; $key = 'grand test - mid term exams'; }
                        $canon[$key] = [ 'name' => $tname, 'label' => $label ];
                    }
                }
                $terms = array_values($canon);
            }
        } catch (\Throwable $e) {
            // ignore
        }
        if (empty($terms)) {
            $terms = [
                ['name' => 'Mid Term Exams', 'label' => 'Mid Term Examinations'],
                ['name' => 'Final Term', 'label' => 'Final Term'],
            ];
        }

        // Load subjects for class
        if (!Schema::hasTable('subjects')) {
            return response()->json(['terms' => $terms, 'rows' => []]);
        }

        $subjects = DB::table('subjects')
            ->select('id','subject_name','subject_code','class_id','term','total_marks','passing_marks','term_marks','status','sort_order')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('subject_name')
            ->get();

        // Group by subject_name
        $groups = [];
        foreach ($subjects as $s) {
            $key = $s->subject_name; // keep exact case for display
            if (!isset($groups[$key])) { $groups[$key] = []; }
            $groups[$key][] = $s;
        }

        $rows = [];
        foreach ($groups as $name => $list) {
            // Determine base record: prefer term NULL; else first by sort_order then id
            $base = null; $byTerm = [];
            foreach ($list as $s) {
                if ($s->term === null && $base === null) { $base = $s; }
                if (!empty($s->term)) { $byTerm[$s->term] = $s; }
            }
            if ($base === null) {
                // pick first as base to anchor JSON updates
                $base = collect($list)->sortBy([['sort_order','asc']])->first();
            }

            // Prepare term-wise values with precedence: explicit term record > JSON override > base marks
            $termValues = [];
            $json = [];
            if (!empty($base->term_marks)) {
                $decoded = json_decode($base->term_marks, true);
                if (is_array($decoded)) { $json = $decoded; }
            }
foreach ($terms as $t) {
                $tname = $t['name'];
                if (isset($byTerm[$tname])) {
                    $termValues[$tname] = [
                        'total_marks' => (float) $byTerm[$tname]->total_marks,
                        'passing_marks' => (float) $byTerm[$tname]->passing_marks,
                        'enabled' => true,
                        'source' => 'record',
                    ];
                } elseif (isset($json[$tname]) && is_array($json[$tname])) {
                    $termValues[$tname] = [
                        'total_marks' => isset($json[$tname]['total_marks']) ? (float)$json[$tname]['total_marks'] : (float)$base->total_marks,
                        'passing_marks' => isset($json[$tname]['passing_marks']) ? (float)$json[$tname]['passing_marks'] : (float)$base->passing_marks,
                        'enabled' => array_key_exists('enabled', $json[$tname]) ? (bool)$json[$tname]['enabled'] : true,
                        'source' => 'json',
                    ];
                } else {
                    $termValues[$tname] = [
                        'total_marks' => (float) $base->total_marks,
                        'passing_marks' => (float) $base->passing_marks,
                        'enabled' => true,
                        'source' => 'fallback',
                    ];
                }
            }

            $rows[] = [
                'subject_id' => (int) $base->id,
                'subject_name' => $name,
                'subject_code' => $base->subject_code,
                'base_total_marks' => (float) $base->total_marks,
                'base_passing_marks' => (float) $base->passing_marks,
                'terms' => $termValues,
            ];
        }

        return response()->json([
            'terms' => $terms,
            'rows' => $rows,
        ]);
    }

    
    // Save term-wise marks in bulk (JSON-only safe mode)
    public function save(Request $request)
    {
        // Debug logging
        \Log::info('TermSubjects Save Request', [
            'request_data' => $request->all(),
            'php_version' => PHP_VERSION
        ]);
        
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|integer|exists:classes,id',
            'rows' => 'required|array|min:1',
            'rows.*.subject_id' => 'required|integer|exists:subjects,id',
            'rows.*.terms' => 'required|array|min:1',
            'rows.*.terms.*.total_marks' => 'required|numeric|min:0.001|max:1000',
            'rows.*.terms.*.passing_marks' => 'required|numeric|min:0.001',
            'rows.*.terms.*.enabled' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            \Log::error('TermSubjects Validation Failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $classId = (int) $request->input('class_id');
        $rows = $request->input('rows');

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            \Log::info('Starting TermSubjects Save Transaction', [
                'class_id' => $classId,
                'rows_count' => count($rows)
            ]);
            
            foreach ($rows as $row) {
                $subjectId = (int) $row['subject_id'];
                $terms = $row['terms'];

                // Fetch the base subject and verify class
                $base = DB::table('subjects')->where('id', $subjectId)->first();
                if (!$base) {
                    \Log::warning('Subject not found', ['subject_id' => $subjectId]);
                    continue;
                }
                if ((int)$base->class_id !== $classId) {
                    \Log::warning('Subject class mismatch', [
                        'subject_id' => $subjectId,
                        'subject_class' => $base->class_id,
                        'requested_class' => $classId
                    ]);
                    continue;
                }

                // Merge existing JSON with incoming values
                $existing = [];
                if (!empty($base->term_marks)) {
                    $decoded = json_decode($base->term_marks, true);
                    if (is_array($decoded)) { $existing = $decoded; }
                }

                foreach ($terms as $termName => $vals) {
                    $tTotal = isset($vals['total_marks']) ? (float)$vals['total_marks'] : null;
                    $tPass = isset($vals['passing_marks']) ? (float)$vals['passing_marks'] : null;
                    if ($tTotal !== null && $tPass !== null) {
                        if (!isset($existing[$termName]) || !is_array($existing[$termName])) {
                            $existing[$termName] = [];
                        }
                        $existing[$termName]['total_marks'] = $tTotal;
                        // Ensure passing <= total
                        $existing[$termName]['passing_marks'] = min($tPass, $tTotal);
                        if (array_key_exists('enabled', $vals)) {
                            $existing[$termName]['enabled'] = (bool)$vals['enabled'];
                        }
                    }
                }

                $updateData = [
                    'term_marks' => json_encode($existing),
                    'updated_at' => now(),
                ];
                
                \Log::info('Updating subject', [
                    'subject_id' => $subjectId,
                    'update_data' => $updateData
                ]);
                
                $updated = DB::table('subjects')->where('id', $subjectId)->update($updateData);
                if ($updated) {
                    $updatedCount++;
                }
            }

            DB::commit();
            \Log::info('TermSubjects Save Complete', [
                'updated_count' => $updatedCount
            ]);
            return response()->json(['success' => true, 'updated_count' => $updatedCount]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('TermSubjects Save Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}