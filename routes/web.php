<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::get('/admin/register', function () {
    return view('admin.register');
})->name('admin.register');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/users', function () {
    return view('admin.users');
});




// User Management

Route::get('/admin/users', function () {
    return view('admin.users.index');
})->name('admin.users.index');

Route::get('/admin/users/create', function () {
    return view('admin.users.create');
})->name('admin.users.create');

Route::get('/admin/users/{id}', function ($id) {
    return view('admin.users.show', ['id' => $id]);
})->name('admin.users.show');

Route::get('/admin/users/{id}/edit', function ($id) {
    return view('admin.users.edit', ['id' => $id]);
})->name('admin.users.edit');

Route::get('/admin/users/{id}/password', function ($id) {
    return view('admin.users.password', ['id' => $id]);
})->name('admin.users.password');



// profile
Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile');
    

// Student management

Route::get('/admin/students', function () {
    return view('admin.students.index');
})->name('admin.students.index');

Route::get('/admin/students/create', function () {
    return view('admin.students.create');
})->name('admin.students.create');

Route::get('/admin/students/{id}', function ($id) {
    return view('admin.students.show', ['id' => $id]);
})->name('admin.students.show');

Route::get('/admin/students/{id}/edit', function ($id) {
    return view('admin.students.edit', ['id' => $id]);
})->name('admin.students.edit');



// Teacher management
Route::get('/admin/teachers', function () {
    return view('admin.teachers.index');
})->name('admin.teachers.index');

Route::get('/admin/teachers/create', function () {
    return view('admin.teachers.create');
})->name('admin.teachers.create');

Route::get('/admin/teachers/{id}', function ($id) {
    return view('admin.teachers.show', ['id' => $id]);
})->name('admin.teachers.show');

Route::get('/admin/teachers/{id}/edit', function ($id) {
    return view('admin.teachers.edit', ['id' => $id]);
})->name('admin.teachers.edit');