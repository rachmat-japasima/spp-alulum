<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')
    ->middleware(['auth', 'active', 'verified'])->group(function () {
        // Edit Profile User
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // User
        Route::get('/users', [UserController::class, 'index'])->name('user.table');
        Route::get('/users/add', [UserController::class, 'create'])->name('user.add');
        Route::post('/users/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::patch('/users/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::patch('/users/reset/{id}', [UserController::class, 'changePassword'])->name('user.changePassword');
        Route::get('/users/activate/{id}', [UserController::class, 'active'])->name('user.active');
        Route::get('/users/inActive/{id}', [UserController::class, 'hold'])->name('user.inActive');
        Route::get('/users/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');

        // Resource routing
        Route::resources([
            'teachers' => TeacherController::class,
            'students' => StudentController::class,
        ]);


    });

require __DIR__.'/auth.php';
