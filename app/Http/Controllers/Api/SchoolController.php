<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\students;
use App\Models\parents;
use App\Models\teachers;
use App\Models\fees;
use App\Models\Classes;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    // Students
    public function getStudents()
    {
        return response()->json(students::with('class')->get());
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'studentName' => 'required',
            'class_id' => 'required',
            'section' => 'required',
            'parent_id' => 'nullable',
            'grno' => 'required|unique:students',
            'gender' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable',
            'phone' => 'nullable',
        ]);
        $data['tenant_id'] = 2;
        $data['school_id'] = 2;
        $data['status'] = 'active';
        $data['session'] = 'April 2024 to March 2025';

        $student = students::create($data);
        return response()->json($student, 201);
    }

    public function updateStudent(Request $request, $id)
    {
        $student = students::findOrFail($id);
        $student->update($request->all());
        return response()->json($student);
    }

    public function deleteStudent($id)
    {
        students::findOrFail($id)->delete();
        return response()->json(['message' => 'Student deleted']);
    }

    // Parents
    public function getParents()
    {
        return response()->json(parents::all());
    }

    public function storeParent(Request $request)
    {
        $data = $request->validate([
            'parentName' => 'required',
            'fatherName' => 'nullable',
            'motherName' => 'nullable',
            'phone' => 'required',
            'email' => 'nullable|email',
            'address' => 'nullable',
        ]);
        $data['tenant_id'] = 2;
        $data['school_id'] = 2;
        $data['status'] = 'active';

        $parent = parents::create($data);
        return response()->json($parent, 201);
    }

    // Teachers
    public function getTeachers()
    {
        return response()->json(teachers::all());
    }

    public function storeTeacher(Request $request)
    {
        $data = $request->validate([
            'teacherName' => 'required',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required',
        ]);
        $data['tenant_id'] = 2;
        $data['school_id'] = 2;
        $data['status'] = 'active';
        $data['teacher_name'] = $data['teacherName'];

        $teacher = teachers::create($data);
        return response()->json($teacher, 201);
    }

    // Fees
    public function getFees()
    {
        return response()->json(fees::with('student')->get());
    }

    public function storeFee(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required',
            'fee_value' => 'required|numeric',
            'month_name' => 'required',
            'year' => 'required',
            'status' => 'required',
        ]);
        $data['tenant_id'] = 2;
        $data['school_id'] = 2;
        $data['fee_type_id'] = 1;
        $data['session'] = 'April 2024 to March 2025';
        $data['fee_name'] = 'Monthly Tuition Fee';

        $fee = fees::create($data);
        return response()->json($fee, 201);
    }

    public function getDashboardStats()
    {
        return response()->json([
            'students_count' => students::count(),
            'teachers_count' => teachers::count(),
            'parents_count' => parents::count(),
            'recent_fees' => fees::with('student')->latest()->take(5)->get(),
        ]);
    }

    public function getClasses()
    {
        return response()->json(Classes::all());
    }
}
