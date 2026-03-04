<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Models\Category;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// Colocation Routes
Route::post('/colocations/create', [ColocationController::class, 'store'])->name('colocations.store');
Route::post('/colocations/join', [ColocationController::class, 'join'])->name('colocations.join');


// Kick Member Route (Delete relationship)
Route::delete('/colocations/{colocation}/members/{user}', [ColocationController::class, 'kick'])
    ->name('colocations.members.kick');



Route::middleware('auth')->group(function () {

    // Admin Specific Routes (Protected by role check)
    
    // Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    // Route::post('/admin/users/{user}/ban', [AdminController::class, 'toggleBan'])->name('admin.users.ban');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::post('/payments/{payment}/mark-as-paid', [PaymentController::class, 'markAsPaid'])
        ->name('payments.markAsPaid');


    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // owner cancel it for all
    Route::patch('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');

    // member quits alone
    Route::delete('/colocations/{colocation}/quit', [ColocationController::class, 'quit'])->name('colocations.quit');
});

require __DIR__ . '/auth.php';


Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');


Route::delete('/colocations/{colocation}/members/{user}', [ColocationController::class, 'kick'])->name('colocations.members.kick');

