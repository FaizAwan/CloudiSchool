<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use App\Models\teachers;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any exams.
     */
    public function viewAny(User $user)
    {
        return in_array($user->role, ['superadmin', 'admin', 'teacher', 'student']);
    }

    /**
     * Determine whether the user can view the exam.
     */
    public function view(User $user, Exam $exam)
    {
        // Superadmin and admin can view all exams
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can view their own exams
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            return $teacher && $exam->teacher_id === $teacher->id;
        }
        
        // Students can view published exams for their class
        if ($user->role === 'student') {
            $student = \App\Models\students::where('user_id', $user->id)->first();
            return $student && 
                   $exam->class_id == $student->class && 
                   $exam->status === 'published';
        }
        
        return false;
    }

    /**
     * Determine whether the user can create exams.
     */
    public function create(User $user)
    {
        // Superadmin, admin, and teachers can create exams
        return in_array($user->role, ['superadmin', 'admin', 'teacher']);
    }

    /**
     * Determine whether the user can update the exam.
     */
    public function update(User $user, Exam $exam)
    {
        // Superadmin and admin can update any exam
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can update their own exams
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            return $teacher && $exam->teacher_id === $teacher->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the exam.
     */
    public function delete(User $user, Exam $exam)
    {
        // Superadmin and admin can delete any exam
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can delete their own exams if no attempts exist
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            return $teacher && 
                   $exam->teacher_id === $teacher->id &&
                   $exam->attempts()->count() === 0;
        }
        
        return false;
    }

    /**
     * Determine whether the user can view exam results.
     */
    public function viewResults(User $user, Exam $exam)
    {
        // Superadmin and admin can view all results
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can view results for their exams
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            return $teacher && $exam->teacher_id === $teacher->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can grade exam answers.
     */
    public function grade(User $user, Exam $exam)
    {
        // Superadmin, admin, and the exam's teacher can grade
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            return $teacher && $exam->teacher_id === $teacher->id;
        }
        
        return false;
    }
}
