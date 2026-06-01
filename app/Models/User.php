<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
        'school_id',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'profile_image',
        'settings',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\School::class, 'tenant_id');
    }

    public function teacherProfile()
    {
        return $this->hasOne(teachers::class, 'user_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(students::class, 'user_id');
    }

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isAccountant()
    {
        return $this->role === 'accountant';
    }

    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    public function canManageExams()
    {
        return $this->role === 'superadmin';
    }

    public function canCreateExams()
    {
        return $this->role === 'superadmin';
    }

    public function canAccessModule($module)
    {
        // Only superadmin can access all modules
        if ($this->role === 'superadmin') {
            return true;
        }

        // Students can only access specific modules
        if ($this->role === 'student') {
            $studentAllowedModules = ['profile', 'exams.take', 'student-exams'];
            return in_array($module, $studentAllowedModules);
        }

        // Admin and other roles are restricted
        return false;
    }

    public function generateStudentCredentials()
    {
        if ($this->role !== 'student' || !$this->studentProfile) {
            return null;
        }

        $student = $this->studentProfile;
        $rollNumber = $student->grno;

        return [
            'username' => 'student_' . $rollNumber,
            'password' => 'pass_' . $rollNumber,
            'display_name' => $student->studentName,
            'roll_number' => $rollNumber
        ];
    }
}
