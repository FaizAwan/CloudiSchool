<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }
        return view('superadmin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ]);

        $loginId = $request->input('login_id');
        $password = $request->input('password');

        // Check for specific hardcoded "superadmin" / "superadmin" first? 
        // Better to check DB user.
        // We will assume a user with email 'superadmin' or name 'superadmin' exists.

        $user = User::where(function($query) use ($loginId) {
            $query->where('email', $loginId)
                  ->orWhere('name', $loginId);
        })->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->role !== 'superadmin') {
                 return back()->withErrors(['login_id' => 'You are not authorized as Superadmin.']);
            }

            Auth::login($user);
            return redirect()->route('superadmin.dashboard');
        }

        return back()->withErrors(['login_id' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('superadmin.login');
    }
}
