<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Multi Role
|--------------------------------------------------------------------------
*/

// Khusus ADMIN
Route::get('/role-admin', function () {
    return 'Berhasil masuk halaman ADMIN';
})->middleware(['auth', 'role:admin']);

// ADMIN dan EDITOR
Route::get('/editor', function () {
    return 'Berhasil masuk halaman EDITOR';
})->middleware(['auth', 'role:admin,editor']);

// ADMIN, EDITOR, dan USER
Route::get('/user', function () {
    return 'Berhasil masuk halaman USER';
})->middleware(['auth', 'role:admin,editor,user']);

require __DIR__.'/auth.php';