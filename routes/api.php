<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DmLogBookController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\IssueBulkController;
use App\Http\Controllers\Api\IssueCategoryController;
use App\Http\Controllers\Api\IssueCommentController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\IssueTypeController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SavedFilterController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes for authentication
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected API Routes

#public route for issues
Route::get('/all-issues', [IssueController::class, 'index'])->name('api.issues.index');

Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Reports API (returns JSON for charts)
    Route::get('/reports/month', [ReportController::class, 'month'])->name('api.reports.month');
    Route::get('/reports/year', [ReportController::class, 'year'])->name('api.reports.year');

    // Statistics API
    Route::get('/statistics/dashboard', [StatisticsController::class, 'dashboard'])->name('api.statistics.dashboard');
    Route::get('/statistics/by-department', [StatisticsController::class, 'byDepartment'])->name('api.statistics.by-department');
    Route::get('/statistics/by-user', [StatisticsController::class, 'byUser'])->name('api.statistics.by-user');
    Route::get('/statistics/trends', [StatisticsController::class, 'trends'])->name('api.statistics.trends');

    // AI Chat tools
    Route::post('/ai/tools/{name}', [AiChatController::class, 'handle'])->name('api.ai.tools');

    // Issues API
    Route::name('api.')->group(function () {
        Route::apiResource('issues', IssueController::class);
        Route::post('/issues/{issue}/close', [IssueController::class, 'close'])->name('issues.close');
        Route::post('/issues/{issue}/reopen', [IssueController::class, 'reopen'])->name('issues.reopen');
        Route::post('/issues/{issue}/verify', [IssueController::class, 'verify'])->name('issues.verify');
        Route::get('/issues/{issue}/comments', [IssueCommentController::class, 'index'])->name('issues.comments');

        // Bulk operations
        Route::post('/issues/bulk/create', [IssueBulkController::class, 'bulkCreate'])->name('issues.bulk.create');
        Route::put('/issues/bulk/update', [IssueBulkController::class, 'bulkUpdate'])->name('issues.bulk.update');
        Route::delete('/issues/bulk/delete', [IssueBulkController::class, 'bulkDelete'])->name('issues.bulk.delete');
        Route::post('/issues/bulk/close', [IssueBulkController::class, 'bulkClose'])->name('issues.bulk.close');
        Route::post('/issues/bulk/reopen', [IssueBulkController::class, 'bulkReopen'])->name('issues.bulk.reopen');
    });

    // Issue Comments API
    Route::apiResource('issue-comments', IssueCommentController::class);

    // Departments API
    Route::apiResource('departments', DepartmentController::class);
    Route::get('/departments/{department}/issues', [DepartmentController::class, 'issues'])->name('departments.issues');

    // Issue Types API
    Route::apiResource('issue-types', IssueTypeController::class);

    // Issue Categories API
    Route::apiResource('issue-categories', IssueCategoryController::class);
    Route::get('/issue-categories/{issue_category}/types', [IssueCategoryController::class, 'types'])->name('issue-categories.types');

    // Users API
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles.assign');
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/password', [UserController::class, 'changePassword'])->name('users.password.change');
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::get('/users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
    Route::get('/users/{user}/issues', [UserController::class, 'issues'])->name('users.issues');

    // Roles API
    Route::apiResource('roles', RoleController::class);

    // Permissions API (read-only)
    Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);

    // Activity Logs API (read-only)
    Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
    Route::get('/activity-logs/subject/{subject_type}/{subject_id}', [ActivityLogController::class, 'bySubject'])->name('activity-logs.subject');

    // Saved Filters API
    Route::apiResource('saved-filters', SavedFilterController::class);

    // DM Log Book API
    Route::apiResource('logbook', DmLogBookController::class);

    // Export API
    Route::post('/exports/issues/csv', [ExportController::class, 'exportIssuesCsv'])->name('api.exports.issues.csv');
    Route::post('/exports/issues/excel', [ExportController::class, 'exportIssuesExcel'])->name('api.exports.issues.excel');
    Route::post('/exports/issues/pdf', [ExportController::class, 'exportIssuesPdf'])->name('api.exports.issues.pdf');
    Route::post('/exports/reports', [ExportController::class, 'exportReports'])->name('api.exports.reports');
});
