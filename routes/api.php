<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\ClassroomController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GradeController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    
    // Profile routes - accessible by all authenticated users
    Route::get('profile', [ProfileController::class, 'index']);
    Route::put('profile', [ProfileController::class, 'update']);
    
    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        // Admin can create, update, delete classrooms
        Route::post('/classrooms', [ClassroomController::class, 'store']);
        Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update']);
        Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy']);
        Route::get('/teachers', [TeacherController::class, 'index']);
    });
    
    // Teacher and Admin routes - can view classrooms
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/classrooms', [ClassroomController::class, 'index']);
        Route::get('/classrooms/{classroom}', [ClassroomController::class, 'show']);
        Route::get('/classrooms/{classroom}/students', [ClassroomController::class, 'students']);
        Route::apiResource('students', StudentController::class);
        Route::put('/students/{student}/grades', [StudentController::class, 'updateGrade']);
    });
});
