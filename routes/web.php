<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Admin\CompanyList;
use App\Livewire\Tasks\TaskDetail;
use App\Http\Controllers\CompanySwitchController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/tasks/{task}', TaskDetail::class)->name('tasks.show');
});

Route::middleware(['auth:sanctum', 'is_super_admin'])->prefix('admin')->group(function () {
    Route::get('/companies', CompanyList::class)->name('admin.companies');
});

// Company switcher for super admin
Route::post('/switch-company', [CompanySwitchController::class, 'switch'])->name('switch-company')->middleware('auth');

require __DIR__.'/jetstream.php';
