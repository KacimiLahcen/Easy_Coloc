<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExpenseController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// Colocation Routes
Route::post('/colocations/create', [ColocationController::class, 'store'])->name('colocations.store');
Route::post('/colocations/join', [ColocationController::class, 'join'])->name('colocations.join');


// Kick Member Route (Delete relationship)
Route::delete('/colocations/{colocation}/members/{user}', [ColocationController::class, 'kick'])
    ->name('colocations.members.kick');

// Admin Specific Routes (Protected by role check)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{user}/ban', [AdminController::class, 'toggleBan'])->name('admin.users.ban');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

Route::post('/payments/{payment}/mark-as-paid', [ExpenseController::class, 'markAsPaid'])
     ->name('payments.markAsPaid');