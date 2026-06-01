<?php

namespace App\Http\Controllers;

use App\Models\parents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use Illuminate\Support\Facades\Schema;

class ParentsController extends Controller
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
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';

        $schoolList = DB::table('schools')
            ->select('id', 'schoolName')
            ->when(!$isSuperadmin, function ($q) use ($tenantId, $schoolId) {
                if ($tenantId) {
                    $q->where('id', $tenantId);
                } elseif ($schoolId) {
                    $q->where('id', $schoolId);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->get();

        // parentList removed - will be loaded via AJAX for superior performance
        return view('parents', Compact('schoolList'));
    }
    public function addParent(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';

        $allowedSchoolId = $tenantId ? ($tenantId == $request->school_id ? $request->school_id : $tenantId) : $schoolId;
        // Basic permission check
        if (!$isSuperadmin && $schoolId && $schoolId != $request->school_id) {
            return redirect('parents')->with('errorMessage', 'Invalid school selection');
        }

        $parentExists = DB::table('parents')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when(!$tenantId && $schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('parentName', $request->parentName)
            ->exists();

        if ($parentExists) {
            return redirect('parents')->with('errorMessage', 'Parent Name Already Exists');
        }

        $data = [
            'tenant_id' => $tenantId,
            'school_id' => $request->school_id ?: ($schoolId ?: $tenantId),
            'parentName' => $request->parentName,
            'is_commandercityschool_employee' => $request->is_commandercityschool_employee,
            'phone' => $request->phoneNumber,
            'address' => $request->address,
            'status' => $request->input('status'),
            'status_other' => $request->input('status_other'),
            'status_business_name' => $request->input('status_business_name'),
            'status_private_job_detail' => $request->input('status_private_job_detail'),
            'status_government_job_detail' => $request->input('status_government_job_detail'),
            'status_unemployed_reason' => $request->input('status_unemployed_reason'),
            'status_staff_detail' => $request->input('status_staff_detail'),
            'resident_country' => $request->input('resident_country'),
            'resident_city' => $request->input('resident_city'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Handle fallback logic for staff detail -> status_other if needed, but keeping it simple is better.
        // If columns don't exist, this might throw SQL error. We should ideally wrap in try-catch or be sure.
        // Assuming columns exist based on previous 'addParent' code structure which implied they are optional but likely present.
        // To be safe, we will just use the data array. If fields are missing in DB, we'll catch the error.

        try {
            DB::table('parents')->insert($data);
            return redirect('parents')->with('message', 'Parent Added Successfully');
        } catch (\Exception $e) {
            Log::error('Add Parent Error: ' . $e->getMessage());
            return redirect('parents')->with('errorMessage', 'Failed to add parent. ensure all fields are valid.');
        }
    }

    public function getParents(Request $request)
    {
        Log::info('getParents called', [
            'user_id' => auth()->id(),
            'params' => $request->all()
        ]);

        $columns = ['parents.id', 'parents.parentName', 'parents.is_commandercityschool_employee', 'parents.phone', 'parents.address', 'schools.schoolName']; // Define columns to be selected
        $searchValue = $request->input('search.value');


        // Get total count of records
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $totalData = DB::table('parents')
            ->when($tenantId, fn($q) => $q->where('parents.tenant_id', $tenantId))
            ->when(!$tenantId && (auth()->user()->school_id ?? null), function ($q) {
                $q->where('parents.school_id', auth()->user()->school_id);
            })
            ->count();


        // Sorting
        $sortColumnIndex = $request->input('order.0.column', 0);
        $sortDirection = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sortBy = isset($columns[$sortColumnIndex]) ? $columns[$sortColumnIndex] : 'parents.id';

        // Searching

        // $parents = DB::table('parents')
        //     ->select('parents.id', 'parents.parentName', 'parents.is_commandercityschool_employee', 'parents.phoneNumber', 'parents.address', 'schools.schoolName')
        //     ->leftJoin('schools', 'parents.school_id', '=', 'schools.id')
        //     ->where(function($query) use ($searchValue) {
        //         $query->where('parents.parentName', 'like', '%' . $searchValue . '%')
        //               ->orWhere('parents.phoneNumber', 'like', '%' . $searchValue . '%')
        //               ->orWhere('parents.address', 'like', '%' . $searchValue . '%')
        //               ->orWhere('parents.is_commandercityschool_employee', 'like', '%' . $searchValue . '%')
        //               ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%');
        //     })
        //      ->orderBy($sortBy, $sortDirection)
        //     ->offset($request->input('start')) // Offset based on start index
        //     ->limit($request->input('length')) // Limit based on length
        //     ->get(); // Use get() to retrieve the data

        try {
            $parents = DB::table('parents')
                ->select(
                    'parents.id',
                    'parents.parentName',
                    'parents.is_commandercityschool_employee',
                    'parents.phone as phoneNumber',
                    'parents.address',
                    'schools.schoolName',
                    DB::raw('(SELECT COUNT(*) FROM students WHERE students.parent_id = parents.id) AS student_count')
                )
                ->leftJoin('schools', 'parents.school_id', '=', 'schools.id')
                ->when($tenantId, fn($q) => $q->where('parents.tenant_id', $tenantId))
                ->when(!$tenantId && (auth()->user()->school_id ?? null), function ($q) {
                    $q->where('parents.school_id', auth()->user()->school_id);
                })
                ->when($searchValue, function ($query) use ($searchValue) {
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('parents.parentName', 'like', '%' . $searchValue . '%')
                            ->orWhere('parents.phone', 'like', '%' . $searchValue . '%')
                            ->orWhere('parents.address', 'like', '%' . $searchValue . '%')
                            ->orWhere('parents.is_commandercityschool_employee', 'like', '%' . $searchValue . '%')
                            ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%');
                    });
                })
                ->orderBy($sortBy, $sortDirection)
                ->offset($request->input('start')) // Offset based on start index
                ->limit($request->input('length')) // Limit based on length
                ->get(); // Use get() to retrieve the data
        } catch (\Throwable $e) {
            Log::error('getParents error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }


        // Data format for DataTables
        $data = [];
        foreach ($parents as $parent) {
            $viewUrl = route('viewParent', $parent->id);
            $baseUrl = request()->getBaseUrl();
            
            $actionButton = '<div class="d-flex justify-content-center gap-2">';
            $actionButton .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-soft-info px-3" title="View Details"> <i class="bi bi-eye"></i> </a>';
            $actionButton .= '<button type="button" class="btn btn-sm btn-soft-primary px-3" data-bs-toggle="modal" data-bs-target="#editModal"  data-bs-id="' . $parent->id . '" data-bs-is_commandercityschool_employee="' . $parent->is_commandercityschool_employee . '" data-bs-name="' . $parent->parentName . '" data-bs-phone="' . $parent->phoneNumber . '" data-bs-address="' . $parent->address . '" title="Edit Profile"> <i class="bi bi-pencil"></i> </button>';
            $actionButton .= '<a href="' . $baseUrl . '/deleteParent/' . $parent->id . '" class="btn btn-sm btn-soft-danger px-3 delete-confirm" title="Delete Record"> <i class="bi bi-trash"></i> </a>';
            $actionButton .= '</div>';

            $rowData = [
                'id' => $parent->id,
                'parentName' => $parent->parentName,
                'totalChildren' => $parent->student_count,
                'is_commandercityschool_employee' => $parent->is_commandercityschool_employee,
                'phoneNumber' => $parent->phoneNumber,
                'address' => $parent->address,
                'schoolName' => $parent->schoolName,
                'action' => $actionButton
            ];
            $data[] = $rowData;
        }

        // JSON response
        $response = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalData),
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function viewParent($id)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';

        $q = DB::table('parents')->where('parents.id', $id);
        if (!$isSuperadmin && $tenantId) {
            $q->where('parents.tenant_id', $tenantId);
        }
        $parent = $q->first();

        if (!$parent) {
            return redirect()->route('parents')->with('errorMessage', 'Parent not found.');
        }

        $students = DB::table('students')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->select('students.*', 'classes.className')
            ->where('students.parent_id', $id)
            ->get();

        return view('viewParent', compact('parent', 'students'));
    }

    public function deleteParent($parentID)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';
        $q = DB::table('parents')->where('id', (int)$parentID);
        if (!$isSuperadmin) {
            if ($tenantId) {
                $q->where('tenant_id', $tenantId);
            } elseif ($schoolId) {
                $q->where('school_id', $schoolId);
            } else {
                return redirect()->back()->with('errorMessage', 'Unauthorized');
            }
        }
        $q->delete();
        return redirect()->back()->with('message', 'Parent Deleted Successfully');
    }

    public function updateParent(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'parentName' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:50',
            'is_commandercityschool_employee' => 'required|in:Yes,No',
            'address' => 'nullable|string|max:500',
        ]);
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';

        $q = DB::table('parents')->where('id', (int)$request->id);
        if (!$isSuperadmin) {
            if ($tenantId) {
                $q->where('tenant_id', $tenantId);
            } elseif ($schoolId) {
                $q->where('school_id', $schoolId);
            } else {
                return redirect()->back()->with('errorMessage', 'Unauthorized');
            }
        }

        $update = [
            'parentName' => trim($request->parentName),
            'phone' => trim((string)$request->phoneNumber),
            'is_commandercityschool_employee' => $request->is_commandercityschool_employee,
            'address' => trim((string)$request->address),
            'status' => $request->input('status'),
            'status_other' => $request->input('status_other'),
            'status_business_name' => $request->input('status_business_name'),
            'status_private_job_detail' => $request->input('status_private_job_detail'),
            'status_government_job_detail' => $request->input('status_government_job_detail'),
            'status_unemployed_reason' => $request->input('status_unemployed_reason'),
            'status_staff_detail' => $request->input('status_staff_detail'),
            'resident_country' => $request->input('resident_country'),
            'resident_city' => $request->input('resident_city'),
            'updated_at' => now(),
        ];

        try {
            $affected = $q->update($update);
            return redirect()->back()->with('message', 'Parent Updated Successfully');
        } catch (\Exception $e) {
            Log::error('Update Parent Error: ' . $e->getMessage());
            return redirect()->back()->with('errorMessage', 'Failed to update parent.');
        }
    }

    public function addParentFromStudent(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';

        $allowedSchoolId = $tenantId ?: $schoolId;
        if (!$isSuperadmin) {
            if (!$allowedSchoolId || (int)$request->school_id !== (int)$allowedSchoolId) {
                return redirect('students')->with('message', 'Invalid school selection');
            }
        }

        $parentExists = DB::table('parents')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when(!$tenantId && $schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('parentName', $request->parentName)
            ->exists();

        if ($parentExists) {
            return redirect('students')->with('message', 'Parent Name Already Exists');
        }

        DB::table('parents')->insert([
            'tenant_id' => $tenantId,
            'school_id' => $allowedSchoolId ?: $request->school_id,
            'parentName' => $request->parentName,
            'is_commandercityschool_employee' => $request->is_commandercityschool_employee,
            'phone' => $request->phoneNumber,
            'address' => $request->address,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('students')->with('message', 'Parent Added Successfully');
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
     * @param  \App\Models\parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function show(parents $parents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function edit(parents $parents)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, parents $parents)
    {
        //
    }

    public function editJson($id)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;
        $isSuperadmin = ($user->role ?? '') === 'superadmin';

        $q = DB::table('parents')->where('id', (int)$id);
        if (!$isSuperadmin) {
            if ($tenantId) {
                $q->where('tenant_id', $tenantId);
            } elseif ($schoolId) {
                $q->where('school_id', $schoolId);
            } else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        $parent = $q->first();
        if (!$parent) {
            return response()->json(['error' => 'Parent not found'], 404);
        }
        return response()->json($parent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function destroy(parents $parents)
    {
        //
    }

    public function seedPakistaniParents()
    {
        $pakistaniNames = [
            'Abdul Rehman',
            'Muhammad Yusuf',
            'Saeed Anwar',
            'Nusrat Fateh',
            'Javed Miandad',
            'Imran Khan',
            'Wasim Akram',
            'Shoaib Akhtar',
            'Shahid Afridi',
            'Inzamam-ul-Haq',
            'Misbah-ul-Haq',
            'Younis Khan',
            'Sarfaraz Ahmed',
            'Babar Azam',
            'Shaheen Afridi',
            'Fakhar Zaman',
            'Shadab Khan',
            'Hassan Ali',
            'Mohammad Rizwan',
            'Azhar Ali',
            'Asif Ali',
            'Haris Rauf',
            'Naseem Shah',
            'Iftikhar Ahmed',
            'Shan Masood',
            'Abid Ali',
            'Imam-ul-Haq',
            'Yasir Shah',
            'Mohammad Abbas',
            'Faheem Ashraf'
        ];

        $parents = DB::table('parents')->get();

        foreach ($parents as $index => $parent) {
            $newName = $pakistaniNames[$index % count($pakistaniNames)];
            if ($index >= count($pakistaniNames)) {
                $newName .= ' ' . ($index + 1);
            }

            DB::table('parents')->where('id', $parent->id)->update([
                'parentName' => $newName,
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('message', "Parents updated with Pakistani names.");
    }
}
