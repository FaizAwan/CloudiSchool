<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user profile page
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure user is properly loaded with all fields
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access your profile.');
        }

        // Refresh user data from database to ensure all fields are loaded
        $user = User::find($user->id);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User account not found.');
        }

        $profileData = $this->getUserProfileData($user);
        $settings = $this->getUserSettings($user);

        return view('profile.index', compact('user', 'profileData', 'settings'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $profileData = $this->getUserProfileData($user);

        return view('profile.edit', compact('user', 'profileData'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update user basic info
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->date_of_birth = $request->date_of_birth;
            $user->gender = $request->gender;

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                // Store new image
                $imagePath = $request->file('profile_image')->store('profile-images', 'public');
                $user->profile_image = $imagePath;
            }

            $user->save();

            // Update role-specific data
            $this->updateRoleSpecificData($user, $request);

            DB::commit();
            return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');

        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update profile. Please try again.')->withInput();
        }
    }

    /**
     * Show password change form
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Update user password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Password changed successfully!');
    }

    /**
     * Show account settings
     */
    public function settings()
    {
        $user = Auth::user();
        $settings = $this->getUserSettings($user);

        return view('profile.settings', compact('user', 'settings'));
    }

    /**
     * Update account settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'exam_reminders' => 'boolean',
            'result_notifications' => 'boolean',
            'fee_reminders' => 'boolean',
            'language' => 'in:en,ur',
            'timezone' => 'string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update user settings (store as JSON)
        $settings = [
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'exam_reminders' => $request->has('exam_reminders'),
            'result_notifications' => $request->has('result_notifications'),
            'fee_reminders' => $request->has('fee_reminders'),
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'Asia/Karachi'
        ];

        $user->settings = json_encode($settings);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Settings updated successfully!');
    }

    /**
     * Update role-specific data
     */
    public function updateRoleData(Request $request)
    {
        $user = Auth::user();
        $role = $request->input('role');

        // Validate that the role matches the user's role
        if ($role !== $user->role) {
            return redirect()->back()->with('error', 'Invalid role specified.');
        }

        DB::beginTransaction();
        try {
            switch ($role) {
                case 'teacher':
                    $validator = Validator::make($request->all(), [
                        'employee_id' => 'nullable|string|max:50',
                        'className' => 'nullable|string|max:100',
                        'subject' => 'nullable|string|max:100',
                        'qualification' => 'nullable|string|max:255',
                        'experience' => 'nullable|integer|min:0',
                        'salary' => 'nullable|numeric|min:0',
                        'joining_date' => 'nullable|date'
                    ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    // Check if teacher record exists
                    $teacher = DB::table('teachers')->where('user_id', $user->id)->first();

                    if ($teacher) {
                        // Update existing record
                        DB::table('teachers')
                            ->where('user_id', $user->id)
                            ->update([
                            'employee_id' => $request->employee_id,
                            'className' => $request->className,
                            'subject' => $request->subject,
                            'qualification' => $request->qualification,
                            'experience' => $request->experience,
                            'salary' => $request->salary,
                            'joining_date' => $request->joining_date,
                            'updated_at' => now()
                        ]);
                    }
                    else {
                        // Create new record
                        DB::table('teachers')->insert([
                            'user_id' => $user->id,
                            'employee_id' => $request->employee_id,
                            'className' => $request->className,
                            'subject' => $request->subject,
                            'qualification' => $request->qualification,
                            'experience' => $request->experience,
                            'salary' => $request->salary,
                            'joining_date' => $request->joining_date,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    break;

                case 'student':
                    $validator = Validator::make($request->all(), [
                        'studentName' => 'nullable|string|max:255',
                        'fatherName' => 'nullable|string|max:255',
                        'emergency_contact' => 'nullable|string|max:20'
                    ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    // Check if student record exists
                    $student = DB::table('students')->where('user_id', $user->id)->first();

                    if ($student) {
                        // Update existing record
                        DB::table('students')
                            ->where('user_id', $user->id)
                            ->update([
                            'studentName' => $request->studentName,
                            'fatherName' => $request->fatherName,
                            'emergency_contact' => $request->emergency_contact,
                            'updated_at' => now()
                        ]);
                    }
                    else {
                        // Create new record
                        DB::table('students')->insert([
                            'user_id' => $user->id,
                            'studentName' => $request->studentName,
                            'fatherName' => $request->fatherName,
                            'emergency_contact' => $request->emergency_contact,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    break;

                case 'parent':
                    $validator = Validator::make($request->all(), [
                        'father_name' => 'nullable|string|max:255',
                        'mother_name' => 'nullable|string|max:255',
                        'occupation' => 'nullable|string|max:255',
                        'monthly_income' => 'nullable|numeric|min:0'
                    ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    // Check if parent record exists
                    $parent = DB::table('parents')
                        ->where('user_id', $user->id)
                        ->when($user->tenant_id, function ($q) use ($user) {
                        $q->where('tenant_id', $user->tenant_id); })
                        ->first();

                    if ($parent) {
                        // Update existing record
                        DB::table('parents')
                            ->where('user_id', $user->id)
                            ->when($user->tenant_id, function ($q) use ($user) {
                            $q->where('tenant_id', $user->tenant_id); })
                            ->update([
                            'father_name' => $request->father_name,
                            'mother_name' => $request->mother_name,
                            'occupation' => $request->occupation,
                            'monthly_income' => $request->monthly_income,
                            'updated_at' => now()
                        ]);
                    }
                    else {
                        // Create new record
                        DB::table('parents')->insert([
                            'user_id' => $user->id,
                            'tenant_id' => $user->tenant_id,
                            'father_name' => $request->father_name,
                            'mother_name' => $request->mother_name,
                            'occupation' => $request->occupation,
                            'monthly_income' => $request->monthly_income,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid role specified.');
            }

            DB::commit();
            return redirect()->route('profile.index')->with('success', ucfirst($role) . ' information updated successfully!');

        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update information. Please try again.')->withInput();
        }
    }

    /**
     * Get user profile data based on role
     */
    private function getUserProfileData($user)
    {
        $profileData = [];

        switch ($user->role) {
            case 'teacher':
                $teacher = DB::table('teachers')->where('user_id', $user->id)->first();
                if ($teacher) {
                    $profileData = [
                        'employee_id' => $teacher->employee_id ?? 'N/A',
                        'className' => $teacher->className ?? 'N/A',
                        'subject' => $teacher->subject ?? 'N/A',
                        'qualification' => $teacher->qualification ?? 'N/A',
                        'experience' => $teacher->experience ?? 'N/A',
                        'salary' => $teacher->salary ?? 'N/A',
                        'joining_date' => $teacher->joining_date ?? 'N/A'
                    ];
                }
                break;

            case 'student':
                $student = DB::table('students')->where('user_id', $user->id)->first();
                if ($student) {
                    $profileData = [
                        'grno' => $student->grno ?? 'N/A',
                        'studentName' => $student->studentName ?? 'N/A',
                        'fatherName' => $student->fatherName ?? 'N/A',
                        'className' => $student->className ?? 'N/A',
                        'session' => $student->session ?? 'N/A',
                        'status' => $student->status ?? 'N/A',
                        'admission_date' => $student->admission_date ?? 'N/A'
                    ];
                }
                break;

            case 'parent':
                $parent = DB::table('parents')
                    ->where('user_id', $user->id)
                    ->when($user->tenant_id, function ($q) use ($user) {
                    $q->where('tenant_id', $user->tenant_id); })
                    ->first();
                if ($parent) {
                    $children = DB::table('students')->where('parent_id', $parent->id)->get();
                    $profileData = [
                        'father_name' => $parent->father_name ?? 'N/A',
                        'mother_name' => $parent->mother_name ?? 'N/A',
                        'occupation' => $parent->occupation ?? 'N/A',
                        'monthly_income' => $parent->monthly_income ?? 'N/A',
                        'children' => $children
                    ];
                }
                break;

            case 'admin':
            case 'superadmin':
                $profileData = [
                    'role' => ucfirst($user->role),
                    'school_id' => $user->school_id ?? 'N/A',
                    'permissions' => $user->role === 'superadmin' ? 'All Permissions' : 'School Administration'
                ];
                break;
        }

        return $profileData;
    }

    /**
     * Update role-specific data
     */
    private function updateRoleSpecificData($user, $request)
    {
        switch ($user->role) {
            case 'teacher':
                if ($request->filled(['qualification', 'experience'])) {
                    DB::table('teachers')
                        ->where('user_id', $user->id)
                        ->update([
                        'qualification' => $request->qualification,
                        'experience' => $request->experience,
                        'updated_at' => now()
                    ]);
                }
                break;

            case 'student':
                if ($request->filled('emergency_contact')) {
                    DB::table('students')
                        ->where('user_id', $user->id)
                        ->update([
                        'emergency_contact' => $request->emergency_contact,
                        'updated_at' => now()
                    ]);
                }
                break;

            case 'parent':
                if ($request->filled(['occupation', 'monthly_income'])) {
                    DB::table('parents')
                        ->where('user_id', $user->id)
                        ->when($user->tenant_id, function ($q) use ($user) {
                        $q->where('tenant_id', $user->tenant_id); })
                        ->update([
                        'occupation' => $request->occupation,
                        'monthly_income' => $request->monthly_income,
                        'updated_at' => now()
                    ]);
                }
                break;
        }
    }

    /**
     * Get user settings
     */
    private function getUserSettings($user)
    {
        $defaultSettings = [
            'email_notifications' => true,
            'sms_notifications' => false,
            'exam_reminders' => true,
            'result_notifications' => true,
            'fee_reminders' => true,
            'language' => 'en',
            'timezone' => 'Asia/Karachi'
        ];

        if ($user->settings) {
            $decoded = json_decode($user->settings, true);
            if (is_array($decoded)) {
                return array_merge($defaultSettings, $decoded);
            }
        }

        return $defaultSettings;
    }
}
