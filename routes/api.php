<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\LessonController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/enrollments/check/{courseId}', [EnrollmentController::class, 'check']);

    Route::get('/courses/{courseId}/lessons/{lessonId}', [LessonController::class, 'show']);
    Route::post('/lessons/{lessonId}/complete', [LessonController::class, 'complete']);
});