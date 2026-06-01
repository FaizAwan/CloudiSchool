<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\School;
use Illuminate\Http\Request;
use DB;

class ClassesController extends Controller
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
       $tenantId = auth()->user()->tenant_id ?? null;
       $schoolId = auth()->user()->school_id ?? null;
       $schoolsByTenant = $tenantId ? School::query()->where('id', $tenantId)->orderBy('schoolName')->get() : collect();
       $schoolList = $schoolsByTenant->count() ? $schoolsByTenant : ($schoolId ? School::query()->where('id', $schoolId)->get() : collect());
        return view('classes',compact('schoolList'));
    }

    public function getClasses(Request $request)
    {
         $tenantId = auth()->user()->tenant_id ?? null;
         $perPage = $request->input('length'); // Records per page
    $page = $request->input('start') / $perPage + 1; // Current page number

    $columns = ['classes.id', 'classes.className', 'schools.schoolName']; // Define columns to be selected
    
    // Sorting
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');
    $sortBy = $columns[$sortColumnIndex];
    
    // Searching
    $searchValue = $request->input('search.value');

    // Get total count of records
    $totalData = DB::table('classes')
                    ->leftJoin('schools', 'classes.school_id', '=', 'schools.id')
                    ->when($tenantId, function($q) use ($tenantId) { $q->where('classes.tenant_id', $tenantId); })
                    ->where(function($query) use ($searchValue) {
                        $query->where('classes.className', 'like', '%' . $searchValue . '%')
                              ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%');
                    })
                    ->count();

    // Perform raw SQL query to retrieve paginated data
    $classes = DB::table('classes')
                    ->select('classes.id', 'classes.className', 'schools.schoolName', 
                        DB::raw("(SELECT GROUP_CONCAT(sectionName ORDER BY sectionName SEPARATOR ', ') FROM sections WHERE sections.class_id = classes.id) as sectionNames")
                    )
                    ->leftJoin('schools', 'classes.school_id', '=', 'schools.id')
                    ->when($tenantId, function($q) use ($tenantId) { $q->where('classes.tenant_id', $tenantId); })
                    ->where(function($query) use ($searchValue) {
                        $query->where('classes.className', 'like', '%' . $searchValue . '%')
                              ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%');
                    })
                    ->orderBy($sortBy, $sortDirection)
                    ->offset(($page - 1) * $perPage) // Calculate offset based on page and perPage
                    ->limit($perPage) // Limit based on perPage
                    ->get(); // Use get() to retrieve the data

    // Data format for DataTables
    $data = [];
    $serialNumber = ($page - 1) * $perPage + 1; // Initialize serial number for the current page
    foreach ($classes as $class) {
        $actionButton = '<button type="button" class="btn btn-sm btn-primary edit-button" data-toggle="modal" data-target="#editModal" data-id="'.$class->id.'" data-name="'.$class->className.'" title="Edit"> <i class="bi bi-pencil"></i> Edit </button> '
            .'<a href="deleteClass/'.$class->id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this class?\')" title="Delete"> <i class="bi bi-trash"></i> Delete </a> '
            .'<a href="classStudents/'.$class->id.'" class="btn btn-sm btn-success" title="View Students"> <i class="bi bi-eye"></i> View </a>';

        $displayClassName = $class->className;

        $rowData = [
            'id' => $serialNumber, // Use serial number instead of database ID
            'className' => $displayClassName,
            'schoolName' => $class->schoolName, // Added schoolName field
            'action' => $actionButton
        ];
        $data[] = $rowData;
        $serialNumber++; // Increment serial number for the next row
    }

    // JSON response
    $response = [
        'draw' => intval($request->input('draw')),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalData), // Count is the same for filtered data
        'data' => $data,
    ];
    
    return response()->json($response);

    }

    public function addClass(Request $request){

       if (empty($request->school_id) || empty($request->className)) {
           return redirect()->back()->with('message', 'Add school and class');
       }

       // Check if the combination of school_id and className already exists
        $tenantId = auth()->user()->tenant_id ?? null;
        $schoolAllowed = $tenantId ? School::query()->where('id', $request->school_id)->where('id', $tenantId)->exists() : false;
        if (!$schoolAllowed) {
            return redirect()->back()->with('message', 'Invalid school selection');
        }
        $existingClass = DB::table('classes')
                            ->where('school_id', $request->school_id)
                            ->where('className', $request->className)
                            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
                            ->first();
        
        if ($existingClass) {
            $classId = $existingClass->id;
            $msgPart = "Sections added to existing class";
        } else {
            // insert the new class
            $classId = DB::table('classes')->insertGetId([
                'school_id' => $request->school_id,
                'className' => $request->className,
                'tenant_id' => $tenantId
            ]);
            $msgPart = "Class and Sections Added Successfully";
        }

        // Add optional sections
        if ($request->has('sections') && !empty(trim($request->sections))) {
            $sections = explode(',', $request->sections);
            foreach ($sections as $sectionName) {
                $sectionName = trim($sectionName);
                if (!empty($sectionName)) {
                    DB::table('sections')->insert([
                        'class_id' => $classId,
                        'sectionName' => $sectionName,
                        'tenant_id' => $tenantId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        // Redirect back with a success message
        return redirect()->back()->with('message', $msgPart);

    }

    public function updateClass(Request $request){

        $tenantId = auth()->user()->tenant_id ?? null;
        $updateParent = DB::table('classes')
        ->where('id', $request->id)
        ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
        ->update(['className'=> $request->className]);
        return redirect()->back()->with('message', 'Class Updated Successfully');
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
     * @param  \App\Models\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function show(Classes $classes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function edit(Classes $classes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classes $classes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classes $classes)
    {
        //
    }

    public function deleteClass($id)
    {
        $tenantId = auth()->user()->tenant_id ?? null;
        
        DB::table('classes')
            ->where('id', $id)
            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
            ->delete();
        
        DB::table('sections')
            ->where('class_id', $id)
            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
            ->delete();

        return redirect()->back()->with('message', 'Class Deleted Successfully');
    }
}
