<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('student.classroom');
        return response()->json($user);
    }
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->only(['name', 'email', 'date_of_birth']);

        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|unique:users,email,' . $user->id,
            'date_of_birth' => 'sometimes|date',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->makeHidden(['password', 'remember_token']),
        ]);
    }
}
