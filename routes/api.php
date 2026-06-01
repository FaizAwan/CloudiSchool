<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ManualExamApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/send-otp', [AuthController::class, 'sendOTP']);
Route::post('/register/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes (Auth disabled temporarily due to missing server dependencies)
// Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);
Route::get('/dashboard/stats', [AuthController::class, 'dashboardStats']);

// CRUD Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [AuthController::class, 'getStudents']);
    Route::post('/students', [AuthController::class, 'addStudent']);
    Route::put('/students/{id}', [AuthController::class, 'updateStudent']);
    Route::delete('/students/{id}', [AuthController::class, 'deleteStudent']);

    Route::get('/teachers', [AuthController::class, 'getTeachers']);
    Route::post('/teachers', [AuthController::class, 'addTeacher']);
    Route::put('/teachers/{id}', [AuthController::class, 'updateTeacher']);
    Route::delete('/teachers/{id}', [AuthController::class, 'deleteTeacher']);

    Route::get('/parents', [AuthController::class, 'getParents']);
    Route::post('/parents', [AuthController::class, 'addParent']);
    Route::put('/parents/{id}', [AuthController::class, 'updateParent']);
    Route::delete('/parents/{id}', [AuthController::class, 'deleteParent']);
});

// Manual Exams
Route::get('/manual-exams/dependencies', [ManualExamApiController::class, 'getDependencies']);
Route::post('/manual-exams/students', [ManualExamApiController::class, 'getStudents']);
Route::post('/manual-exams/save-marks', [ManualExamApiController::class, 'saveMarks']);

// Fee Management
Route::get('/fees', [AuthController::class, 'getFees']);
Route::get('/fee-types', [AuthController::class, 'getFeeTypes']);

// Attendance
Route::get('/attendance', [AuthController::class, 'getAttendance']);
Route::post('/attendance', [AuthController::class, 'saveAttendance']);
// });
