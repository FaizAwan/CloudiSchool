<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    /**
     * Show restricted access message for admin users
     */
    public function restricted()
    {
        if (Auth::user()->role === 'superadmin') {
            return redirect()->route('home');
        }

        return view('admin.restricted');
    }

    /**
     * Generate student credentials (Superadmin only)
     */
    public function generateStudentCredentials()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized. Only superadmin can access this feature.');
        }

        $credentials = StudentController::generateStudentCredentials();
        
        return view('admin.student-credentials', compact('credentials'));
    }

    /**
     * Dashboard for admin (limited access)
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return view('admin.restricted-dashboard');
        }

        return redirect()->route('home');
    }
}