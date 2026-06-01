<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view($id)
    {
        $subject = Subject::with('class')->find($id);
        if (!$subject) {
            abort(404);
        }
        // Optional: exam count if exams table exists
        $examCount = 0;
        if (Schema::hasTable('exams') && method_exists($subject, 'exams')) {
            try { $examCount = $subject->exams()->count(); } catch (\Throwable $e) { $examCount = 0; }
        }
        return view('subjects.view', compact('subject', 'examCount'));
    }

    public function index(Request $request)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $subjects = collect();
        $uniqueSubjects = collect();
        $examCounts = [];
        $availableTerms = $this->getAvailableTerms();

        if (Schema::hasTable('subjects')) {
            $query = Subject::with('class');
            if (!empty($tenantId) && Schema::hasColumn('subjects','tenant_id')) {
                $query->where('tenant_id', $tenantId);
            }
            
            // Apply class filter if provided
            $selectedClassId = $request->get('class_id');
            if ($selectedClassId) {
                $query->where('class_id', $selectedClassId);
            }
            
            // Apply subject filter if provided
            $selectedSubjectName = $request->get('subject_name');
            if ($selectedSubjectName) {
                $query->where('subject_name', $selectedSubjectName);
            }
            
            // Apply term filter if provided
            $selectedTerm = $request->get('term');
            if ($selectedTerm) {
                if ($selectedTerm === 'general') {
                    $query->whereNull('term');
                } else {
                    $query->where('term', $selectedTerm);
                }
            }
            
            // Manual ordering by sort_order, then subject_name as tiebreaker
            $subjects = $query->orderBy('sort_order', 'asc')->orderBy('subject_name', 'asc')->get();

            // Pre-compute exam counts safely (exams table may not exist)
            if (Schema::hasTable('exams')) {
                $countsQuery = \App\Models\Exam::selectRaw('subject_id, COUNT(*) as cnt')
                    ->whereNotNull('subject_id');
                if (!empty($tenantId) && Schema::hasColumn('exams','tenant_id')) {
                    $countsQuery->where('tenant_id',$tenantId);
                }
                $counts = $countsQuery->groupBy('subject_id')->pluck('cnt', 'subject_id');
                foreach ($subjects as $s) {
                    $examCounts[$s->id] = (int)($counts[$s->id] ?? 0);
                }
            } else {
                foreach ($subjects as $s) {
                    $examCounts[$s->id] = 0;
                }
            }
            
            // Get unique subject names for the subject filter dropdown
            $uniqueSubjectsQuery = Subject::select('subject_name')->distinct();
            if (!empty($tenantId) && Schema::hasColumn('subjects','tenant_id')) {
                $uniqueSubjectsQuery->where('tenant_id', $tenantId);
            }
            $uniqueSubjects = $uniqueSubjectsQuery->orderBy('subject_name')->pluck('subject_name');
        } else {
            $subjects = collect();
            $uniqueSubjects = collect();
            $examCounts = [];
            $selectedClassId = $request->get('class_id');
            $selectedSubjectName = $request->get('subject_name');
            $selectedTerm = $request->get('term');
        }

        $classes = DB::table('classes')
            ->when($tenantId, fn($q)=>$q->where('tenant_id',$tenantId))
            ->orderBy('className')
            ->get();
        
        return view('subjects.index', compact('subjects', 'classes', 'selectedClassId', 'selectedSubjectName', 'selectedTerm', 'uniqueSubjects', 'examCounts', 'availableTerms'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $subjectCodeRule = ['nullable','string','max:20'];
        if (!empty($tenantId)) {
            $subjectCodeRule[] = Rule::unique('subjects','subject_code')->where(fn($q)=>$q->where('tenant_id',$tenantId));
        } else {
            $subjectCodeRule[] = Rule::unique('subjects','subject_code');
        }
        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|max:100',
            'subject_code' => $subjectCodeRule,
            'class_id' => 'required|exists:classes,id',
            'term' => 'nullable|string|max:50',
            'total_marks' => 'required|numeric|min:0.001|max:1000',
            'passing_marks' => 'required|numeric|min:0.001|lte:total_marks',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0'
        ], [
            'subject_name.unique' => 'This subject already exists for the selected class.',
            'subject_code.unique' => 'Subject code already exists in your school.'
        ]);

        if ($validator->fails()) {
            if (!$request->expectsJson()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Explicitly fetch school_id from class if user record doesn't have it
        $schoolId = auth()->user()->school_id ?? null;
        if (!$schoolId || $schoolId == 0) {
            $class = DB::table('classes')->where('id', $request->class_id)->first();
            if ($class) { $schoolId = $class->school_id; }
        }

        // Prevent duplicate subjects for the same class
        $exists = Subject::where('class_id', $request->class_id)
            ->where('subject_name', trim($request->subject_name))
            ->exists();
        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'This subject already exists for the selected class.'], 422);
            }
            return redirect()->back()->withErrors(['subject_name' => 'This subject already exists for the selected class.'])->withInput();
        }

        $subject = Subject::create([
            'tenant_id' => $tenantId,
            'school_id' => $schoolId,
            'subject_name' => trim($request->subject_name),
            'subject_code' => trim((string)$request->subject_code) ?: null,
            'class_id' => $request->class_id,
            'term' => $request->term ?: null,
            'total_marks' => (float) $request->total_marks,
            'passing_marks' => (float) $request->passing_marks,
            'status' => $request->status,
            'sort_order' => (int) $request->input('sort_order', 0)
        ]);

        if (!$request->expectsJson()) {
            return redirect()->route('subjects.index')->with('success', "Subject '{$subject->subject_name}' created successfully.");
        }
        return response()->json(['success' => true, 'subject' => $subject]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $updateSubjectCodeRule = ['nullable','string','max:20'];
        if (!empty($tenantId)) {
            $updateSubjectCodeRule[] = Rule::unique('subjects','subject_code')
                ->where(fn($q)=>$q->where('tenant_id',$tenantId))
                ->ignore($subject->id);
        } else {
            $updateSubjectCodeRule[] = Rule::unique('subjects','subject_code')->ignore($subject->id);
        }
        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|max:100',
            'subject_code' => $updateSubjectCodeRule,
            'class_id' => 'required|exists:classes,id',
            'term' => 'nullable|string|max:50',
            'total_marks' => 'required|numeric|min:0.001|max:1000',
            'passing_marks' => 'required|numeric|min:0.001|lte:total_marks',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            if (!$request->expectsJson()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Explicitly fetch school_id from class 
        $schoolId = auth()->user()->school_id ?? null;
        if (!$schoolId || $schoolId == 0) {
            $class = DB::table('classes')->where('id', $request->class_id)->first();
            if ($class) { $schoolId = $class->school_id; }
        }

        $subject->update([
            'tenant_id' => $tenantId,
            'school_id' => $schoolId,
            'subject_name' => trim($request->input('subject_name')),
            'subject_code' => trim((string)$request->input('subject_code')) ?: null,
            'class_id' => (int) $request->input('class_id'),
            'term' => $request->input('term') ?: null,
            'total_marks' => (float) $request->input('total_marks'),
            'passing_marks' => (float) $request->input('passing_marks'),
            'status' => $request->input('status'),
            'sort_order' => (int) $request->input('sort_order', $subject->sort_order ?? 0),
        ]);

        if (!$request->expectsJson()) {
            return redirect()->route('subjects.index')->with('success', "Subject '{$subject->subject_name}' updated successfully.");
        }
        return response()->json(['success' => true, 'subject' => $subject]);
    }

    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            // Check if subject is used in any exams (only if exams table exists)
            if (Schema::hasTable('exams')) {
                if ($subject->exams()->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Cannot delete subject that is used in exams. Please remove all related exams first.'
                    ], 400);
                }
            }

            // Check if subject is used in manual exam entries
            $manualExamCount = \DB::table('manual_exam_entries')
                ->where('data', 'LIKE', '%"' . $subject->subject_name . '"%')
                ->count();
            
            if ($manualExamCount > 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot delete subject that is used in manual exam entries. Please remove related entries first.'
                ], 400);
            }

            $subjectName = $subject->subject_name;
            $subject->delete();
            
            \Log::info("Subject deleted successfully: {$subjectName} (ID: {$id})", [
                'user_id' => auth()->id(),
                'subject_id' => $id
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => "Subject '{$subjectName}' deleted successfully"
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Failed to delete subject (ID: {$id}): " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'subject_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete subject: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjects' => 'required|array|min:1',
            'subjects.*.subject_name' => 'required|string|max:100',
            'subjects.*.subject_code' => 'nullable|string|max:20',
            'subjects.*.class_id' => 'required|exists:classes,id',
            'subjects.*.term' => 'nullable|string|max:50',
            'subjects.*.total_marks' => 'required|numeric|min:0.001|max:1000',
            'subjects.*.passing_marks' => 'required|numeric|min:0.001',
            'subjects.*.status' => 'required|in:active,inactive',
            'subjects.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            if (!$request->expectsJson()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rows = [];
        $now = now();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        foreach ($request->input('subjects') as $row) {
            $name = trim((string)($row['subject_name'] ?? ''));
            $code = trim((string)($row['subject_code'] ?? ''));
            $classId = (int)($row['class_id'] ?? 0);
            $total = (float)($row['total_marks'] ?? 0);
            $passing = (float)($row['passing_marks'] ?? 0);
            $sort = (int)($row['sort_order'] ?? 0);
            if ($classId <= 0 || $name === '') { continue; }
            if ($total <= 0) { $total = 100.0; }
            if ($passing <= 0) { $passing = round($total * 0.5, 3); }
            if ($passing > $total) { $passing = $total; }
            $rows[] = [
                'subject_name' => $name,
                'subject_code' => $code ?: null,
                'class_id' => $classId,
                'term' => !empty($row['term']) ? $row['term'] : null,
                'total_marks' => $total,
                'passing_marks' => $passing,
                'status' => ($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                'sort_order' => $sort,
                'tenant_id' => $tenantId,
                'school_id' => $schoolId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($rows)) {
            return response()->json(['success' => false, 'error' => 'No valid rows to insert.'], 422);
        }

        // De-duplicate within the incoming payload based on class_id + subject_name (case-insensitive)
        $unique = [];
        foreach ($rows as $r) {
            $key = $r['class_id'] . '|' . mb_strtolower($r['subject_name']);
            $unique[$key] = $r; // last one wins
        }
        $rows = array_values($unique);

        // Insert row-by-row via Eloquent to ensure tenant scoping and events
        $ok = 0; $failed = []; $ignored = 0;
        foreach ($rows as $r) {
            try {
                $exists = Subject::query()
                    ->where('class_id', (int)$r['class_id'])
                    ->where('subject_name', $r['subject_name'])
                    ->exists();
                if ($exists) { $ignored++; continue; }
                Subject::create([
                    'subject_name' => $r['subject_name'],
                    'subject_code' => $r['subject_code'],
                    'class_id' => (int)$r['class_id'],
                    'term' => $r['term'],
                    'total_marks' => (float)$r['total_marks'],
                    'passing_marks' => (float)$r['passing_marks'],
                    'status' => $r['status'],
                    'sort_order' => (int)$r['sort_order'],
                ]);
                $ok++;
            } catch (\Throwable $ex) {
                $failed[] = [
                    'subject_name' => $r['subject_name'],
                    'class_id' => $r['class_id'],
                    'error' => $ex->getMessage(),
                ];
            }
        }
        $resp = ['success' => true, 'inserted' => $ok, 'ignored' => $ignored, 'failed' => $failed];
        if (!$request->expectsJson()) {
            return redirect()->route('subjects.index')->with('success', "$ok subject(s) inserted. ");
        }
        return response()->json($resp);
    }

    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjects' => 'required|array|min:1',
            'subjects.*.id' => 'required|integer|exists:subjects,id',
            'subjects.*.subject_name' => 'required|string|max:100',
            'subjects.*.subject_code' => 'nullable|string|max:20',
            'subjects.*.class_id' => 'required|exists:classes,id',
            'subjects.*.term' => 'nullable|string|max:50',
            'subjects.*.total_marks' => 'required|numeric|min:0.001|max:1000',
            // Use a simpler rule and enforce lte manually per row for compatibility
            'subjects.*.passing_marks' => 'required|numeric|min:0.001',
            'subjects.*.status' => 'required|in:active,inactive',
            'subjects.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            if (!$request->expectsJson()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updated = 0;
        foreach ($request->input('subjects') as $row) {
            $id = (int)($row['id'] ?? 0);
            if (!$id) { continue; }
            $subject = Subject::find($id);
            if (!$subject) { continue; }
            $total = (float)($row['total_marks'] ?? $subject->total_marks);
            $passing = (float)($row['passing_marks'] ?? $subject->passing_marks);
            if ($passing > $total) { $passing = $total; }
            $subject->update([
                'subject_name' => $row['subject_name'] ?? $subject->subject_name,
                'subject_code' => $row['subject_code'] ?? $subject->subject_code,
                'class_id' => (int)($row['class_id'] ?? $subject->class_id),
                'term' => isset($row['term']) ? ($row['term'] ?: null) : $subject->term,
                'total_marks' => $total,
                'passing_marks' => $passing,
                'status' => $row['status'] ?? $subject->status,
                'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : ($subject->sort_order ?? 0),
            ]);
            $updated++;
        }

        if (!$request->expectsJson()) {
            return redirect()->route('subjects.index')->with('success', "$updated subject(s) updated successfully.");
        }
        return response()->json(['success' => true, 'updated' => $updated]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'error' => 'No subjects selected'], 422);
        }
        $deleted = [];
        $failed = [];
        foreach ($ids as $id) {
            try {
                $subject = Subject::find($id);
                if (!$subject) { $failed[] = ['id'=>$id,'reason'=>'Not found']; continue; }
                // Check constraints
                if (Schema::hasTable('exams') && method_exists($subject, 'exams')) {
                    if ($subject->exams()->count() > 0) {
                        $failed[] = ['id'=>$id,'reason'=>'Used in exams'];
                        continue;
                    }
                }
                $manualExamCount = DB::table('manual_exam_entries')
                    ->where('data', 'LIKE', '%"' . $subject->subject_name . '"%')
                    ->count();
                if ($manualExamCount > 0) {
                    $failed[] = ['id'=>$id,'reason'=>'Used in manual exam entries'];
                    continue;
                }
                $name = $subject->subject_name;
                $subject->delete();
                $deleted[] = ['id'=>$id,'name'=>$name];
            } catch (\Throwable $e) {
                $failed[] = ['id'=>$id,'reason'=>$e->getMessage()];
            }
        }
        return response()->json(['success'=>true,'deleted'=>$deleted,'failed'=>$failed]);
    }
    
    public function byClass($id)
    {
        $classId = (int) $id;
        if ($classId <= 0) {
            return response()->json(['success'=>false,'error'=>'Invalid class id'], 422);
        }
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $subjects = Subject::with('class')
            ->where('class_id', $classId)
            ->when(!empty($tenantId) && Schema::hasColumn('subjects','tenant_id'), function($q) use ($tenantId){ $q->where('tenant_id',$tenantId); })
            ->orderBy('sort_order', 'asc')
            ->orderBy('subject_name', 'asc')
            ->get();
        return response()->json(['success'=>true,'subjects'=>$subjects]);
    }
    
    private function getAvailableTerms(): array
    {
        try {
            $query = DB::table('exam_terms');
            
            if (Schema::hasColumn('exam_terms', 'is_active')) {
                $query->where('is_active', true);
            }
            
            if (Schema::hasColumn('exam_terms', 'sort_order')) {
                $query->orderBy('sort_order');
            } else {
                $query->orderBy('term_name');
            }
            
            $allTerms = $query->get();
            $availableTerms = ['general' => 'General (All Terms)'];
            
            foreach ($allTerms as $term) {
                $displayName = isset($term->display_name) ? $term->display_name : $term->term_name;
                $availableTerms[$term->term_name] = $displayName;
            }
            
        } catch (\Exception $e) {
            $availableTerms = [
                'general' => 'General (All Terms)',
                'Mid Term' => 'Mid Term',
                'Final Term' => 'Final Term',
                'Grand Test - Mid Term Exams' => 'Grand Test - Mid Term Exams'
            ];
        }
        
        return $availableTerms;
    }
}
