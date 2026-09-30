<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/register', [AdminController::class, 'register']);

Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

    Route::post('/admin/logout', [AdminController::class, 'logout']);



    // User Management

    Route::get('/admin/users', [
        UserManagementController::class,
        'index'
    ]);

    Route::get('/admin/users/{id}', [
        UserManagementController::class,
        'show'
    ]);

    Route::post('/admin/users', [
        UserManagementController::class,
        'store'
    ]);

    Route::put('/admin/users/{id}', [
        UserManagementController::class,
        'update'
    ]);

    Route::delete('/admin/users/{id}', [
        UserManagementController::class,
        'destroy'
    ]);

    Route::patch('/admin/users/{id}/status', [
        UserManagementController::class,
        'toggleStatus'
    ]);

    Route::patch('/admin/users/{id}/password', [
        UserManagementController::class,
        'changePassword'
    ]);




    // Student Management


    Route::get('/admin/students', [StudentManagementController::class, 'index']);
    Route::get('/admin/students/{id}', [StudentManagementController::class, 'show']);
    Route::post('/admin/students', [StudentManagementController::class, 'store']);
});




// manage profile

Route::middleware('auth:sanctum')->group(function() {

    Route::get('/profile', [
        ProfileController::class,
        'show'
    ]);

    Route::put('/profile', [
        ProfileController::class,
        'update'
    ]);

    Route::patch('/profile/password', [
        ProfileController::class,
        'changePassword'
    ]);
    
});
