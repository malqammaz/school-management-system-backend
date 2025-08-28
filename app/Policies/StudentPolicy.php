<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all students
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can view students in their assigned classrooms
        if ($user->role === 'teacher') {
            return true; // Will be filtered in controller
        }
        
        // Students cannot view other students
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        // Admin can view any student
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can view students in their assigned classrooms
        if ($user->role === 'teacher') {
            return $student->classroom && $student->classroom->teacher_id === $user->id;
        }
        
        // Student can only view their own profile
        if ($user->role === 'student') {
            return $user->id === $student->user_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create students
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        // Admin can update any student
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can update students in their assigned classrooms
        if ($user->role === 'teacher') {
            return $student->classroom && $student->classroom->teacher_id === $user->id;
        }
        
        // Student can only update their own profile (limited fields)
        if ($user->role === 'student') {
            return $user->id === $student->user_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        // Only admin can delete students
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        // Only admin can restore students
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        // Only admin can permanently delete students
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update grades.
     */
    public function updateGrade(User $user, Student $student): bool
    {
        // Admin can update any student's grade
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can update grades for students in their assigned classrooms
        if ($user->role === 'teacher') {
            return $student->classroom && $student->classroom->teacher_id === $user->id;
        }
        
        // Students cannot update their own grades
        return false;
    }
}
