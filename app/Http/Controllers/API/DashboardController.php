<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStudents = Student::whereNotNull('assigned_class_id')->count();
        $totalClassrooms = Classroom::count();

        return response()->json([
            'teachers' => $totalTeachers,
            'students' => $totalStudents,
            'classrooms' => $totalClassrooms,
        ]);
    }
}
