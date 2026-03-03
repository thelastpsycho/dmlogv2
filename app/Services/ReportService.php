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
     * Get monthly report data.
     */
    public function monthlyReport(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $query = Issue::whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $totalIssues = $query->count();

        $byStatus = Issue::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('
                CASE
                    WHEN closed_at IS NOT NULL THEN "closed"
                    ELSE "open"
                END as status,
                COUNT(*) as count
            ')
            ->groupBy('status')
            ->pluck('count', 'status');

        $byDepartment = Issue::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id')
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->pluck('count', 'd.name');

        $byIssueType = Issue::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->pluck('count', 'it.name');

        $avgCloseTime = Issue::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
            ->value('avg_hours');

        $bySeverity = Issue::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('severity, COUNT(*) as count')
            ->whereNotNull('severity')
            ->groupBy('severity')
            ->orderByDesc('count')
            ->pluck('count', 'severity');

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => now()->setDateTime($year, $month, 1, 0, 0)->format('F'),
            'total_issues' => $totalIssues,
            'by_status' => $byStatus,
            'by_department' => $byDepartment,
            'by_issue_type' => $byIssueType,
            'avg_close_time_hours' => round($avgCloseTime ?? 0, 2),
            'by_severity' => $bySeverity,
        ];
    }

    /**
     * Get yearly report data.
     */
    public function yearlyReport(?int $year = null): array
    {
        $year = $year ?? now()->year;

        $issues = Issue::whereYear('created_at', $year);

        $totalIssues = $issues->count();

        $byMonth = Issue::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $byStatus = Issue::whereYear('created_at', $year)
            ->selectRaw('
                CASE
                    WHEN closed_at IS NOT NULL THEN "closed"
                    ELSE "open"
                END as status,
                COUNT(*) as count
            ')
            ->groupBy('status')
            ->pluck('count', 'status');

        $byDepartment = Issue::whereYear('created_at', $year)
            ->selectRaw('d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id')
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->pluck('count', 'd.name');

        $byIssueType = Issue::whereYear('created_at', $year)
            ->selectRaw('it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->pluck('count', 'it.name');

        $avgCloseTime = Issue::whereYear('created_at', $year)
            ->whereNotNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
            ->value('avg_hours');

        $bySeverity = Issue::whereYear('created_at', $year)
            ->selectRaw('severity, COUNT(*) as count')
            ->whereNotNull('severity')
            ->groupBy('severity')
            ->orderByDesc('count')
            ->pluck('count', 'severity');

        return [
            'year' => $year,
            'total_issues' => $totalIssues,
            'by_month' => $byMonth,
            'by_status' => $byStatus,
            'by_department' => $byDepartment,
            'by_issue_type' => $byIssueType,
            'avg_close_time_hours' => round($avgCloseTime ?? 0, 2),
            'by_severity' => $bySeverity,
        ];
    }

    /**
     * Get logbook report data (printable list).
     */
    public function logbookReport(array $filters = []): Collection
    {
        $query = Issue::with(['departments', 'issueTypes', 'createdBy', 'closedBy']);

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Department filter
        if (isset($filters['department_id'])) {
            $query->whereHas('departments', function ($q) use ($filters) {
                $q->where('departments.id', $filters['department_id']);
            });
        }

        // Issue type filter
        if (isset($filters['issue_type_id'])) {
            $query->whereHas('issueTypes', function ($q) use ($filters) {
                $q->where('issue_types.id', $filters['issue_type_id']);
            });
        }

        // Status filter
        if (isset($filters['status'])) {
            if ($filters['status'] === 'open') {
                $query->whereNull('closed_at');
            } elseif ($filters['status'] === 'closed') {
                $query->whereNotNull('closed_at');
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get KPI data for dashboard.
     */
    public function kpiData(): array
    {
        $openIssues = Issue::whereNull('closed_at')->count();

        $closedToday = Issue::whereNotNull('closed_at')
            ->whereDate('closed_at', today())
            ->count();

        $closedThisWeek = Issue::whereNotNull('closed_at')
            ->whereBetween('closed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $closedThisMonth = Issue::whereNotNull('closed_at')
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->count();

        $avgCloseTime = Issue::whereNotNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
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
