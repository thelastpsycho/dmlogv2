<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\Department;
use App\Models\IssueType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get report data by date range.
     */
    public function dateRangeReport(?string $dateFrom = null, ?string $dateTo = null, ?int $categoryId = null): array
    {
        $dateFrom = $dateFrom ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $dateTo ?? now()->format('Y-m-d');

        $query = Issue::whereRaw('COALESCE(DATE(issue_date), DATE(created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issue_date), DATE(created_at)) <= ?', [$dateTo]);

        if ($categoryId) {
            $query->whereHas('issueTypes', function ($q) use ($categoryId) {
                $q->where('issue_category_id', $categoryId);
            });
        }

        $totalIssues = $query->count();

        $byStatusQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at');

        if ($categoryId) {
            $byStatusQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $byStatus = $byStatusQuery
            ->selectRaw('
                CASE
                    WHEN issues.status IN ("closed", "resolved") THEN "closed"
                    ELSE "open"
                END as status,
                COUNT(*) as count
            ')
            ->groupByRaw('CASE WHEN issues.status IN ("closed", "resolved") THEN "closed" ELSE "open" END')
            ->pluck('count', 'status');

        $byPriorityQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at');

        if ($categoryId) {
            $byPriorityQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $byPriority = $byPriorityQuery
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->orderByDesc('count')
            ->pluck('count', 'priority');

        $byDepartmentQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at')
            ->selectRaw('d.id, d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id');

        if ($categoryId) {
            $byDepartmentQuery
                ->join('issue_issue_type as iit2', 'issues.id', '=', 'iit2.issue_id')
                ->join('issue_types as it2', 'iit2.issue_type_id', '=', 'it2.id')
                ->where('it2.issue_category_id', $categoryId);
        }

        $byDepartment = $byDepartmentQuery
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => ['count' => $item->count, 'id' => $item->id]];
            });

        $byIssueTypeQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at')
            ->selectRaw('it.id, it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id');

        if ($categoryId) {
            $byIssueTypeQuery->where('it.issue_category_id', $categoryId);
        }

        $byIssueType = $byIssueTypeQuery
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => ['count' => $item->count, 'id' => $item->id]];
            });

        $byCategoryQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at')
            ->selectRaw('ic.id, ic.label, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
            ->join('issue_categories as ic', 'it.issue_category_id', '=', 'ic.id')
            ->groupBy('ic.id', 'ic.label')
            ->orderByDesc('count');

        $byCategory = $byCategoryQuery
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->label => ['count' => $item->count, 'id' => $item->id]];
            });

        $avgCloseTimeQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at')
            ->whereIn('issues.status', ['closed', 'resolved']);

        if ($categoryId) {
            $avgCloseTimeQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $avgCloseTime = $avgCloseTimeQuery
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, issues.created_at, issues.closed_at)) as avg_hours')
            ->value('avg_hours');

        // Daily trend
        $dailyTrendQuery = DB::table('issues')
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) >= ?', [$dateFrom])
            ->whereRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) <= ?', [$dateTo])
            ->whereNull('issues.deleted_at')
            ->selectRaw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at)) as date, COUNT(*) as count')
            ->groupBy(DB::raw('COALESCE(DATE(issues.issue_date), DATE(issues.created_at))'))
            ->orderBy('date');

        if ($categoryId) {
            $dailyTrendQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $dailyTrend = $dailyTrendQuery->pluck('count', 'date');

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_issues' => $totalIssues,
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'by_department' => $byDepartment,
            'by_issue_type' => $byIssueType,
            'by_category' => $byCategory,
            'daily_trend' => $dailyTrend,
            'avg_close_time_hours' => round($avgCloseTime ?? 0, 2),
            'category_id' => $categoryId,
        ];
    }

    /**
     * Get monthly report data.
     */
    public function monthlyReport(?int $year = null, ?int $month = null, ?int $categoryId = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $query = Issue::whereYear('issues.created_at', $year)
            ->whereMonth('issues.created_at', $month);

        if ($categoryId) {
            $query->whereHas('issueTypes', function ($q) use ($categoryId) {
                $q->where('issue_category_id', $categoryId);
            });
        }

        $totalIssues = $query->count();

        $byStatusQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereMonth('issues.created_at', $month)
            ->whereNull('issues.deleted_at');

        if ($categoryId) {
            $byStatusQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $byStatus = $byStatusQuery
            ->selectRaw('
                CASE
                    WHEN issues.status IN ("closed", "resolved") THEN "closed"
                    ELSE "open"
                END as status,
                COUNT(*) as count
            ')
            ->groupByRaw('CASE WHEN issues.status IN ("closed", "resolved") THEN "closed" ELSE "open" END')
            ->pluck('count', 'status');

        $byDepartmentQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereMonth('issues.created_at', $month)
            ->whereNull('issues.deleted_at')
            ->selectRaw('d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id');

        if ($categoryId) {
            $byDepartmentQuery
                ->join('issue_issue_type as iit2', 'issues.id', '=', 'iit2.issue_id')
                ->join('issue_types as it2', 'iit2.issue_type_id', '=', 'it2.id')
                ->where('it2.issue_category_id', $categoryId);
        }

        $byDepartment = $byDepartmentQuery
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->pluck('count', 'd.name');

        $byIssueTypeQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereMonth('issues.created_at', $month)
            ->whereNull('issues.deleted_at')
            ->selectRaw('it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id');

        if ($categoryId) {
            $byIssueTypeQuery->where('it.issue_category_id', $categoryId);
        }

        $byIssueType = $byIssueTypeQuery
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->pluck('count', 'it.name');

        $avgCloseTimeQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereMonth('issues.created_at', $month)
            ->whereNull('issues.deleted_at')
            ->whereIn('status', ['closed', 'resolved']);

        if ($categoryId) {
            $avgCloseTimeQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $avgCloseTime = $avgCloseTimeQuery
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, issues.created_at, issues.closed_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => now()->setDateTime($year, $month, 1, 0, 0)->format('F'),
            'total_issues' => $totalIssues,
            'by_status' => $byStatus,
            'by_department' => $byDepartment,
            'by_issue_type' => $byIssueType,
            'avg_close_time_hours' => round($avgCloseTime ?? 0, 2),
            'category_id' => $categoryId,
        ];
    }

    /**
     * Get yearly report data.
     */
    public function yearlyReport(?int $year = null, ?int $categoryId = null): array
    {
        $year = $year ?? now()->year;

        $issuesQuery = Issue::whereYear('created_at', $year);

        if ($categoryId) {
            $issuesQuery->whereHas('issueTypes', function ($q) use ($categoryId) {
                $q->where('issue_category_id', $categoryId);
            });
        }

        $totalIssues = $issuesQuery->count();

        $byMonthQuery = Issue::whereYear('created_at', $year);

        if ($categoryId) {
            $byMonthQuery->whereHas('issueTypes', function ($q) use ($categoryId) {
                $q->where('issue_category_id', $categoryId);
            });
        }

        $byMonth = $byMonthQuery
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $byStatusQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereNull('issues.deleted_at');

        if ($categoryId) {
            $byStatusQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $byStatus = $byStatusQuery
            ->selectRaw('
                CASE
                    WHEN issues.status IN ("closed", "resolved") THEN "closed"
                    ELSE "open"
                END as status,
                COUNT(*) as count
            ')
            ->groupByRaw('CASE WHEN issues.status IN ("closed", "resolved") THEN "closed" ELSE "open" END')
            ->pluck('count', 'status');

        $byDepartmentQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereNull('issues.deleted_at')
            ->selectRaw('d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id');

        if ($categoryId) {
            $byDepartmentQuery
                ->join('issue_issue_type as iit2', 'issues.id', '=', 'iit2.issue_id')
                ->join('issue_types as it2', 'iit2.issue_type_id', '=', 'it2.id')
                ->where('it2.issue_category_id', $categoryId);
        }

        $byDepartment = $byDepartmentQuery
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->pluck('count', 'd.name');

        $byIssueTypeQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereNull('issues.deleted_at')
            ->selectRaw('it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id');

        if ($categoryId) {
            $byIssueTypeQuery->where('it.issue_category_id', $categoryId);
        }

        $byIssueType = $byIssueTypeQuery
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->pluck('count', 'it.name');

        $avgCloseTimeQuery = DB::table('issues')
            ->whereYear('issues.created_at', $year)
            ->whereNull('issues.deleted_at')
            ->whereIn('status', ['closed', 'resolved']);

        if ($categoryId) {
            $avgCloseTimeQuery
                ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
                ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
                ->where('it.issue_category_id', $categoryId);
        }

        $avgCloseTime = $avgCloseTimeQuery
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, issues.created_at, issues.closed_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'year' => $year,
            'total_issues' => $totalIssues,
            'by_month' => $byMonth,
            'by_status' => $byStatus,
            'by_department' => $byDepartment,
            'by_issue_type' => $byIssueType,
            'avg_close_time_hours' => round($avgCloseTime ?? 0, 2),
            'category_id' => $categoryId,
        ];
    }

    /**
     * Get logbook report data (printable list).
     */
    public function logbookReport(array $filters = []): Collection
    {
        $query = Issue::with(['departments', 'issueTypes', 'createdBy', 'closedBy', 'comments.user', 'issueTypes.issueCategory']);

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Department filter
        if (isset($filters['department_id'])) {
            $departmentIds = is_array($filters['department_id'])
                ? $filters['department_id']
                : explode(',', $filters['department_id']);

            $query->whereHas('departments', function ($q) use ($departmentIds) {
                $q->whereIn('departments.id', $departmentIds);
            });
        }

        // Issue type filter
        if (isset($filters['issue_type_id'])) {
            $issueTypeIds = is_array($filters['issue_type_id'])
                ? $filters['issue_type_id']
                : explode(',', $filters['issue_type_id']);

            $query->whereHas('issueTypes', function ($q) use ($issueTypeIds) {
                $q->whereIn('issue_types.id', $issueTypeIds);
            });
        }

        // Issue category filter
        if (isset($filters['issue_category_id'])) {
            $query->whereHas('issueTypes', function ($q) use ($filters) {
                $q->where('issue_types.issue_category_id', $filters['issue_category_id']);
            });
        }

        // Status filter
        if (isset($filters['status'])) {
            if ($filters['status'] === 'open') {
                $query->whereNotIn('status', ['closed', 'resolved']);
            } elseif ($filters['status'] === 'closed') {
                $query->whereIn('status', ['closed', 'resolved']);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get KPI data for dashboard.
     */
    public function kpiData(): array
    {
        $openIssues = Issue::whereNotIn('status', ['closed', 'resolved'])->count();

        $closedToday = Issue::whereIn('status', ['closed', 'resolved'])
            ->whereDate('updated_at', today())
            ->count();

        $closedThisWeek = Issue::whereIn('status', ['closed', 'resolved'])
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $closedThisMonth = Issue::whereIn('status', ['closed', 'resolved'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $avgCloseTime = Issue::whereIn('status', ['closed', 'resolved'])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->value('avg_hours');

        $topDepartments = Issue::selectRaw('d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id')
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $topIssueTypes = Issue::selectRaw('it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $issuesTrend = Issue::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'open_issues' => $openIssues,
            'closed_today' => $closedToday,
            'closed_this_week' => $closedThisWeek,
            'closed_this_month' => $closedThisMonth,
            'avg_close_time_hours' => round($avgCloseTime ?? 0, 2),
            'top_departments' => $topDepartments,
            'top_issue_types' => $topIssueTypes,
            'issues_trend' => $issuesTrend,
        ];
    }
}
