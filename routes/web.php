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