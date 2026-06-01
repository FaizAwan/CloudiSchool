<?php

namespace App\Http\Controllers;

use App\Models\teachers;
use Illuminate\Http\Request;
use DB;
use Hash;
use App\Models\School;
use App\Models\Classes;
use App\Models\Timetable;
use App\Models\TeacherAttendance;
use App\Models\Exam;
use App\Models\Subject;

class TeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;

        $classList = DB::table('classes')
            ->select('id', 'className', 'school_id')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when(!$tenantId && $schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->get();

        $schoolList = DB::table('schools')
            ->select('id', 'schoolName', 'address')
            ->when($tenantId, fn($q) => $q->where('id', $tenantId))
            ->when(!$tenantId && $schoolId, fn($q) => $q->where('id', $schoolId))
            ->get();

        $teacherList = DB::table('teachers')
            ->leftJoin('schools', 'teachers.school_id', '=', 'schools.id')
            ->leftJoin('classes', 'teachers.class_id', '=', 'classes.id')
            ->select(
                'teachers.id',
                'teachers.school_id',
                'teachers.class_id',
                'teachers.teacherName',
                'teachers.email',
                'teachers.phone',
                'schools.schoolName',
                'classes.className as classNameFromJoin'
            )
            ->where(function ($q) use ($tenantId, $schoolId) {
                if ($tenantId) {
                    $q->where('teachers.tenant_id', $tenantId);
                } elseif ($schoolId) {
                    $q->where('teachers.school_id', $schoolId);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->get();

        return view('teachers', Compact('classList', 'teacherList', 'schoolList'));
    }

    public function addTeacher(Request $request)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolIdUser = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $request->validate([
            'teacherName' => 'required|string|max:255',
            'teacherEmail' => 'required|email|max:255|unique:users,email',
            'teacherPassword' => 'required|string|min:6',
            'teacherPhoneNumber' => 'nullable|string|max:50',
            'school_id' => 'required|integer',
            'class_id' => 'nullable|integer',
        ]);

        if ($request->class_id) {

            $classRow = DB::table('classes')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('id', (int)$request->class_id)->first();
            $className = $classRow->className ?? '';
            $schoolId = $request->school_id ?: ($classRow->school_id ?? null);

            DB::table('teachers')->insert([
                'tenant_id' => ($tenantId ?: $schoolIdUser ?: $schoolId),
                'teacherName' => $request->teacherName,
                'teacher_name' => $request->teacherName,
                'email' => $request->teacherEmail,
                'phone' => $request->teacherPhoneNumber,
                'class_id' => $request->class_id,
                'className' => $className,
                'school_id' => $schoolId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {

            DB::table('teachers')->insert([
                'tenant_id' => ($tenantId ?: $schoolIdUser ?: (int)$request->school_id),
                'teacherName' => $request->teacherName,
                'teacher_name' => $request->teacherName,
                'email' => $request->teacherEmail,
                'phone' => $request->teacherPhoneNumber,
                'class_id' => $request->class_id,
                'className' => '',
                'school_id' => $request->school_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        DB::table('users')->insert([
            'tenant_id' => ($tenantId ?: $schoolIdUser ?: (int)$request->school_id),
            'school_id' => $request->school_id,
            'role' => 'teacher',
            'name' => $request->teacherName,
            'password' => Hash::make($request->teacherPassword),
            'email' => $request->teacherEmail,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('message', 'Teacher Added Successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $id = (int)$id;
        $teacher = DB::table('teachers')
            ->leftJoin('schools', 'teachers.school_id', '=', 'schools.id')
            ->leftJoin('classes', 'teachers.class_id', '=', 'classes.id')
            ->select('teachers.*', 'schools.schoolName', 'schools.address', 'classes.className as classNameFromJoin')
            ->where(function ($q) use ($tenantId, $schoolId) {
                if ($tenantId) {
                    $q->where('teachers.tenant_id', $tenantId);
                } elseif ($schoolId) {
                    $q->where('teachers.school_id', $schoolId);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->where('teachers.id', $id)
            ->first();
        if (!$teacher) {
            return redirect()->route('teachers')->with('errorMessage', 'Teacher not found');
        }
        // Placeholder datasets (extend later as needed)
        $timetable = [];
        $attendance = [];
        $exams = [];
        return view('teacher_view', compact('teacher', 'timetable', 'attendance', 'exams'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function edit(teachers $teachers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function updateTeacher(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'teacherName' => 'required|string|max:255',
            'teacherEmail' => 'required|email|max:255',
            'teacherPhoneNumber' => 'nullable|string|max:50',
            'school_id' => 'required|integer',
            'class_id' => 'nullable|integer',
        ]);
        try {
            $className = '';
            if ($request->class_id) {
                $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
                try {
                    if (function_exists('tenant') && tenant()) {
                        $tenantId = $tenantId ?: (string) tenant('id');
                    }
                } catch (\Throwable $e) {
                }
                $cls = DB::table('classes')
                    ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                    ->where('id', (int)$request->class_id)
                    ->first();
                $className = $cls ? $cls->className : '';
            }
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            try {
                if (function_exists('tenant') && tenant()) {
                    $tenantId = $tenantId ?: (string) tenant('id');
                }
            } catch (\Throwable $e) {
            }
            $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
            DB::table('teachers')
                ->where(function ($q) use ($tenantId, $schoolId) {
                    if ($tenantId) {
                        $q->where('tenant_id', $tenantId);
                    } elseif ($schoolId) {
                        $q->where('school_id', $schoolId);
                    } else {
                        $q->whereRaw('1=0');
                    }
                })
                ->where('id', (int)$request->id)
                ->update([
                    'teacherName' => $request->teacherName,
                    'teacher_name' => $request->teacherName,
                    'email' => $request->teacherEmail,
                    'phone' => $request->teacherPhoneNumber,
                    'school_id' => (int)$request->school_id,
                    'class_id' => $request->class_id ? (int)$request->class_id : null,
                    'className' => $className,
                    'updated_at' => now(),
                ]);
            return redirect()->back()->with('message', 'Teacher updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('errorMessage', 'Failed to update teacher: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function destroy(teachers $teachers)
    {
        //
    }

    public function delete($id)
    {
        try {
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            try {
                if (function_exists('tenant') && tenant()) {
                    $tenantId = $tenantId ?: (string) tenant('id');
                }
            } catch (\Throwable $e) {
            }
            $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
            DB::table('teachers')
                ->where(function ($q) use ($tenantId, $schoolId) {
                    if ($tenantId) {
                        $q->where('tenant_id', $tenantId);
                    } elseif ($schoolId) {
                        $q->where('school_id', $schoolId);
                    } else {
                        $q->whereRaw('1=0');
                    }
                })
                ->where('id', (int)$id)
                ->delete();
            return redirect()->back()->with('message', 'Teacher deleted successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('errorMessage', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }
}
