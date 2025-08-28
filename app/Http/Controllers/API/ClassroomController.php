<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ClassroomRequest;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        // Check if user can view any classrooms
        if (!Gate::allows('viewAny', Classroom::class)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to view classrooms.'], 403);
        }

        $query = Classroom::with('teacher')->withCount('students');

        // Role-based filtering
        if ($request->user()->role === 'teacher') {
            // Teachers can only see their assigned classrooms
            $query->where('teacher_id', $request->user()->id);
        }
        // Admin can see all classrooms (no additional filtering needed)

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);
        return $query->paginate($perPage);
    }

    public function store(ClassroomRequest $request)
    {
        // Check if user can create classrooms
        if (!Gate::allows('create', Classroom::class)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to create classrooms.'], 403);
        }
        
        // Validation is handled by ClassroomRequest
        
        $classroom = Classroom::create($request->validated());
        return response()->json($classroom->load('teacher'), 201);
    }

    public function show(Request $request, Classroom $classroom)
    {
        // Check if user can view this classroom
        if (!Gate::allows('view', $classroom)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to view this classroom.'], 403);
        }
        
        return response()->json($classroom->load('teacher', 'students'));
    }

    public function update(ClassroomRequest $request, Classroom $classroom)
    {
        // Check if user can update this classroom
        if (!Gate::allows('update', $classroom)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to update this classroom.'], 403);
        }
        
        // Validation is handled by ClassroomRequest
        
        $classroom->update($request->validated());
        return response()->json($classroom->load('teacher'));
    }

    public function destroy(Request $request, Classroom $classroom)
    {
        // Check if user can delete this classroom
        if (!Gate::allows('delete', $classroom)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to delete this classroom.'], 403);
        }
        
        $classroom->delete();
        return response()->json(['message' => 'Classroom deleted successfully']);
    }

    public function students(Request $request, Classroom $classroom)
    {
        // Check specific permission for viewing students in this classroom
        if (!Gate::allows('viewStudents', $classroom)) {
            return response()->json(['message' => 'Forbidden. You do not have permission to view students in this classroom.'], 403);
        }

        $perPage = $request->get('per_page', 10);

        $query = Student::with('user')
                ->where('assigned_class_id', $classroom->id);

        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $students = $query->paginate($perPage);

        return response()->json($students);
    }
}
