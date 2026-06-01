<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;
use App\Models\teachers;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionBankPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any question bank items.
     */
    public function viewAny(User $user)
    {
        return in_array($user->role, ['superadmin', 'admin', 'teacher']);
    }

    /**
     * Determine whether the user can view the question bank item.
     */
    public function view(User $user, QuestionBank $questionBank)
    {
        // Superadmin and admin can view all questions
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can view their own questions or questions they can use
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            return $teacher && ($questionBank->created_by === $user->id || $questionBank->is_public);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create question bank items.
     */
    public function create(User $user)
    {
        // Superadmin, admin, and teachers can create questions
        return in_array($user->role, ['superadmin', 'admin', 'teacher']);
    }

    /**
     * Determine whether the user can update the question bank item.
     */
    public function update(User $user, QuestionBank $questionBank)
    {
        // Superadmin and admin can update any question
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can update their own questions
        if ($user->role === 'teacher') {
            return $questionBank->created_by === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the question bank item.
     */
    public function delete(User $user, QuestionBank $questionBank)
    {
        // Superadmin and admin can delete any question
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Teachers can delete their own questions if not used in exams
        if ($user->role === 'teacher') {
            return $questionBank->created_by === $user->id && 
                   $questionBank->usage_count == 0;
        }
        
        return false;
    }

    /**
     * Determine whether the user can add questions to exams.
     */
    public function addToExam(User $user)
    {
        return in_array($user->role, ['superadmin', 'admin', 'teacher']);
    }

    /**
     * Determine whether the user can bulk delete questions.
     */
    public function bulkDelete(User $user)
    {
        return in_array($user->role, ['superadmin', 'admin', 'teacher']);
    }
}
