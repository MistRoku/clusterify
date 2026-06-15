<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanySwitchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes that are loaded by the RouteServiceProvider and assigned to the
| "web" middleware group.
|
*/

// Home redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Dashboard - Livewire component wrapped in closure
    Route::get('/dashboard', function () {
        return app(\App\Livewire\Dashboard::class);
    })->name('dashboard');

    // Task Detail - Livewire component with route parameter
    Route::get('/tasks/{task}', function ($task) {
        return app(\App\Livewire\Tasks\TaskDetail::class, ['task' => $task]);
    })->name('tasks.show');
});

// Super Admin routes
Route::middleware(['auth:sanctum', 'is_super_admin'])->prefix('admin')->group(function () {
    // Company List - Livewire component wrapped in closure
    Route::get('/companies', function () {
        return app(\App\Livewire\Admin\CompanyList::class);
    })->name('admin.companies');
});

// Company switcher (normal controller, no change needed)
Route::post('/switch-company', [CompanySwitchController::class, 'switch'])->name('switch-company')->middleware('auth');
Route::post('/reset-company', [CompanySwitchController::class, 'reset'])->name('reset-company')->middleware('auth');

// Jetstream authentication routes (already included via require)
require __DIR__.'/jetstream.php';
