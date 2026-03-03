<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Issues\Form;
use App\Livewire\Issues\Index;
use App\Livewire\Issues\Show;
use App\Livewire\Admin\Departments\Form as DepartmentForm;
use App\Livewire\Admin\Departments\Index as DepartmentIndex;
use App\Livewire\Admin\IssueTypes\Form as IssueTypeForm;
use App\Livewire\Admin\IssueTypes\Index as IssueTypeIndex;
use App\Livewire\Admin\Users\Form as UserForm;
use App\Livewire\Admin\Users\Index as UserIndex;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Reports\Monthly;
use App\Livewire\Reports\Yearly;
use App\Livewire\Reports\Logbook;
use App\Livewire\Statistics\Index as StatisticsIndex;
use App\Livewire\Graphs\Issues as GraphsIssues;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('issues.index');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Issues routes
    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', Index::class)->name('index');
        Route::get('/create', Form::class)->name('create');
        Route::get('/{issue}', Show::class)->name('show');
        Route::get('/{issue}/edit', Form::class)->name('edit');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', ReportsIndex::class)->name('index');
        Route::get('/monthly', Monthly::class)->name('monthly');
        Route::get('/yearly', Yearly::class)->name('yearly');
        Route::get('/logbook', Logbook::class)->name('logbook');
    });

    // Graphs
    Route::prefix('graphs')->name('graphs.')->group(function () {
        Route::get('/issues', GraphsIssues::class)->name('issues');
        Route::get('/', function () {
            return redirect()->route('graphs.issues');
        })->name('index');
    });

    // Statistics
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', StatisticsIndex::class)->name('index');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', UserIndex::class)->name('index');
            Route::get('/create', UserForm::class)->name('create');
            Route::get('/{user}/edit', UserForm::class)->name('edit');
        });

        // Departments
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', DepartmentIndex::class)->name('index');
            Route::get('/create', DepartmentForm::class)->name('create');
            Route::get('/{department}/edit', DepartmentForm::class)->name('edit');
        });

        // Issue Types
        Route::prefix('issue-types')->name('issue-types.')->group(function () {
            Route::get('/', IssueTypeIndex::class)->name('index');
            Route::get('/create', IssueTypeForm::class)->name('create');
            Route::get('/{issueType}/edit', IssueTypeForm::class)->name('edit');
        });
    });
});

require __DIR__.'/auth.php';
