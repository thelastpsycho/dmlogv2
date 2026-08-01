<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiToolsService
{
    /**
     * Resolve a named period (or explicit start/end dates) into a date range.
     */
    private function resolveRange(?string $period, ?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {
            return [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
        }

        $start = match ($period) {
            'today' => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            'this_week' => now()->startOfWeek(),
            'last_month' => now()->subMonth(),
            'this_month' => now()->startOfMonth(),
            'last_week', null => now()->subWeek(),
            default => now()->subWeek(),
        };

        return [$start, now()];
    }

    public function summary(?string $period = 'last_week', ?string $startDate = null, ?string $endDate = null, int $limit = 25): array
    {
        [$start, $end] = $this->resolveRange($period, $startDate, $endDate);
        $limit = min($limit, 50);

        $cacheKey = 'ai:summary:'.$start->format('Y-m-d').':'.$end->format('Y-m-d');

        $stats = Cache::remember($cacheKey, 300, function () use ($start, $end) {
            return Issue::whereBetween('created_at', [$start, $end])
                ->selectRaw("
                    COUNT(*) as total_issues,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_issues,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_issues,
                    SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_issues,
                    SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_issues,
                    AVG(TIMESTAMPDIFF(HOUR, created_at, COALESCE(closed_at, NOW()))) as avg_resolution_hours
                ")
                ->first();
        });

        $totalIssues = (int) $stats->total_issues;
        $closedIssues = (int) $stats->closed_issues;

        $issues = Issue::whereBetween('created_at', [$start, $end])
            ->with('departments')
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'type' => $period,
            ],
            'summary' => [
                'total_issues' => $totalIssues,
                'open_issues' => (int) $stats->open_issues,
                'closed_issues' => $closedIssues,
                'urgent_issues' => (int) $stats->urgent_issues,
                'high_issues' => (int) $stats->high_issues,
                'avg_resolution_hours' => round($stats->avg_resolution_hours ?? 0, 1),
                'closure_rate' => $totalIssues > 0 ? round(($closedIssues / $totalIssues) * 100, 1) : 0,
            ],
            'issues_returned' => $issues->count(),
            'issues' => $issues->map(fn (Issue $issue) => $this->compactIssue($issue))->all(),
        ];
    }

    public function roomSearch(string $roomNumber, int $limit = 10): array
    {
        $limit = min($limit, 50);

        $issues = Issue::where('room_number', 'like', "%{$roomNumber}%")
            ->with('departments')
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'room_number' => $roomNumber,
            'total_found' => $issues->count(),
            'issues' => $issues->map(fn (Issue $issue) => $this->compactIssue($issue))->all(),
        ];
    }

    public function guestSearch(string $guestName, int $limit = 10): array
    {
        $limit = min($limit, 50);

        $issues = Issue::where('name', 'like', "%{$guestName}%")
            ->with('departments')
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'guest_name' => $guestName,
            'total_found' => $issues->count(),
            'issues' => $issues->map(fn (Issue $issue) => $this->compactIssue($issue))->all(),
        ];
    }

    public function departmentStats(?string $period = null): array
    {
        [$start, $end] = $period
            ? $this->resolveRange($period, null, null)
            : [null, null];

        $query = Department::withCount(['issues as open_issues' => function ($q) use ($start) {
            $q->where('status', '!=', 'closed');
            if ($start) {
                $q->where('issues.created_at', '>=', $start);
            }
        }])->withCount(['issues as closed_issues' => function ($q) use ($start) {
            $q->where('status', 'closed');
            if ($start) {
                $q->where('issues.created_at', '>=', $start);
            }
        }]);

        $departments = $query->get();

        $departmentStats = $departments->map(function (Department $department) {
            $total = $department->open_issues + $department->closed_issues;

            return [
                'id' => $department->id,
                'name' => $department->name,
                'open_issues' => $department->open_issues,
                'closed_issues' => $department->closed_issues,
                'total_issues' => $total,
                'closure_rate' => $total > 0 ? round(($department->closed_issues / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('total_issues')->values();

        $topDepartment = $departmentStats->first();

        return [
            'period' => $period ? ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d'), 'type' => $period] : null,
            'summary' => [
                'total_departments' => $departmentStats->count(),
                'total_issues' => $departmentStats->sum('total_issues'),
                'top_department' => $topDepartment ? [
                    'name' => $topDepartment['name'],
                    'issues' => $topDepartment['total_issues'],
                ] : null,
            ],
            'departments' => $departmentStats->all(),
        ];
    }

    public function urgentIssues(int $limit = 20): array
    {
        $limit = min($limit, 50);

        $issues = Issue::where('priority', 'urgent')
            ->where('status', '!=', 'closed')
            ->with('departments')
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'total_found' => $issues->count(),
            'issues' => $issues->map(fn (Issue $issue) => $this->compactIssue($issue))->all(),
        ];
    }

    private function compactIssue(Issue $issue): array
    {
        return [
            'id' => $issue->id,
            'title' => $issue->title,
            'description' => Str::limit($issue->description ?? '', 300),
            'recovery' => Str::limit($issue->recovery ?? '', 300),
            'status' => $issue->status,
            'priority' => $issue->priority,
            'location' => $issue->location,
            'room_number' => $issue->room_number,
            'guest_name' => $issue->name,
            'department' => $issue->departments->pluck('name')->implode(', ') ?: null,
            'created_at' => optional($issue->created_at)->toDateTimeString(),
            'closed_at' => optional($issue->closed_at)->toDateTimeString(),
        ];
    }
}
