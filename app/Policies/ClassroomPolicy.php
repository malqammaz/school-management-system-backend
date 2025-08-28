<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassroomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all classrooms
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can view their assigned classrooms
        if ($user->role === 'teacher') {
            return true; // Will be filtered in controller
        }
        
        // Students cannot view classrooms
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Classroom $classroom): bool
    {
        // Admin can view any classroom
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can view their assigned classrooms
        if ($user->role === 'teacher') {
            return $classroom->teacher_id === $user->id;
        }
        
        // Students cannot view classrooms
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create classrooms
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Classroom $classroom): bool
    {
        // Only admin can update classrooms
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Classroom $classroom): bool
    {
        // Only admin can delete classrooms
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Classroom $classroom): bool
    {
        // Only admin can restore classrooms
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Classroom $classroom): bool
    {
        // Only admin can permanently delete classrooms
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view students in the classroom.
     */
    public function viewStudents(User $user, Classroom $classroom): bool
    {
        // Admin can view students in any classroom
        if ($user->role === 'admin') {
            return true;
        }
        
        // Teacher can view students in their assigned classrooms
        if ($user->role === 'teacher') {
            return $classroom->teacher_id === $user->id;
        }
        
        // Students cannot view other students
        return false;
    }
}
