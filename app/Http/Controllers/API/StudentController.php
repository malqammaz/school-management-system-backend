<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StudentRequest;
use App\Http\Requests\GradeRequest;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // Check if user can view any students
        if (!Gate::allows('viewAny', Student::class)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to view students.'], 403);
        }

        $query = Student::with('user', 'classroom');

        // Role-based filtering
        if ($request->user()->role === 'teacher') {
            // Teachers can only see students in their assigned classrooms
            $query->whereHas('classroom', function($q) use ($request) {
                $q->where('teacher_id', $request->user()->id);
            });
        } elseif ($request->user()->role === 'student') {
            // Students can only see their own profile
            $query->where('user_id', $request->user()->id);
        }
        // Admin can see all students (no additional filtering needed)

        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->has('not_in_classroom') && !empty($request->not_in_classroom)) {
            $query->where(function($q) use ($request) {
                $q->where('assigned_class_id', '!=', $request->not_in_classroom)
                ->orWhereNull('assigned_class_id');
            });
        }

        $perPage = $request->get('per_page', 10);
        $students = $query->paginate($perPage);

        return response()->json($students);
    }

    public function show(Request $request, Student $student)
    {
        // Check if user can view this student
        if (!Gate::allows('view', $student)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to view this student.'], 403);
        }
        
        return response()->json($student->load('user', 'classroom'));
    }

    public function store(StudentRequest $request)
    {
        // Check if user can create students
        if (!Gate::allows('create', Student::class)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to create students.'], 403);
        }
        
        // Validation is handled by StudentRequest
        
        $student = Student::create($request->only(['user_id', 'assigned_class_id', 'grade']));

        return response()->json($student->load('user', 'classroom'), 201);
    }

    public function update(StudentRequest $request, Student $student)
    {
        // Check if user can update this student
        if (!Gate::allows('update', $student)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to update this student.'], 403);
        }
        
        // Validation is handled by StudentRequest
        
        // Update student fields based on role
        if ($request->user()->role === 'admin' || $request->user()->role === 'teacher') {
            $student->update($request->only(['assigned_class_id', 'grade']));
        }

        // Update user fields based on role
        if ($request->user()->role === 'admin' || $request->user()->role === 'student') {
            $student->user->update($request->only(['name', 'email', 'date_of_birth']));
        }

        return response()->json($student->load('user', 'classroom'));
    }

    public function destroy(Request $request, Student $student)
    {
        if ($request->user()->role === 'teacher'||$request->user()->role === 'admin' ) {
            $student->assigned_class_id = null;
            $student->grade = null;
            $student->save();
            return response()->json(['message' => 'Student removed from class']);
        }
        
        return response()->json(['message' => 'Forbidden. You do not have permission to delete students.'], 403);
    }

    public function updateGrade(GradeRequest $request, Student $student)
    {
        // Check specific permission for updating grades
        if (!Gate::allows('updateGrade', $student)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to update this student\'s grade.'], 403);
        }

        // Validation is handled by GradeRequest
        
        $student->grade = $request->grade;
        $student->save();

        return response()->json([
            'message' => 'Grade updated successfully',
            'student' => $student->load('user', 'classroom')
        ]);
    }
}
