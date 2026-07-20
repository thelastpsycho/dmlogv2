<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IssueService
{
    /**
     * Create a new issue with activity logging.
     */
    public function create(array $data): Issue
    {
        return DB::transaction(function () use ($data) {
            $issue = Issue::create($data);

            // Attach departments if provided
            if (!empty($data['department_ids'] ?? [])) {
                $issue->departments()->attach($data['department_ids']);
            }

            // Attach issue types if provided
            if (!empty($data['issue_type_ids'] ?? [])) {
                $issue->issueTypes()->attach($data['issue_type_ids']);
            }

            // Log activity
            $this->logActivity($issue, 'created', 'Issue was created');

            return $issue->load(['departments', 'issueTypes', 'createdBy', 'assignedTo']);
        });
    }

    /**
     * Update an existing issue with activity logging.
     */
    public function update(Issue $issue, array $data): Issue
    {
        return DB::transaction(function () use ($issue, $data) {
            $changes = $this->trackChanges($issue, $data);

            $issue->update($data);

            // Sync departments if provided
            if (array_key_exists('department_ids', $data)) {
                $issue->departments()->sync($data['department_ids']);
            }

            // Sync issue types if provided
            if (array_key_exists('issue_type_ids', $data)) {
                $issue->issueTypes()->sync($data['issue_type_ids']);
            }

            // Log activity for each change
            foreach ($changes as $change) {
                $this->logActivity($issue, 'updated', $change);
            }

            return $issue->load(['departments', 'issueTypes', 'createdBy', 'assignedTo']);
        });
    }

    /**
     * Close an issue.
     */
    public function close(Issue $issue, ?string $note = null): Issue
    {
        return DB::transaction(function () use ($issue, $note) {
            $issue->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by_user_id' => Auth::id(),
            ]);

            $this->logActivity($issue, 'closed', "Issue was closed" . ($note ? ": {$note}" : ''));

            return $issue->fresh();
        });
    }

    /**
     * Verify a closed issue.
     */
    public function verify(Issue $issue, ?string $note = null): Issue
    {
        return DB::transaction(function () use ($issue, $note) {
            $issue->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by_user_id' => Auth::id(),
            ]);

            $this->logActivity($issue, 'verified', "Issue was verified" . ($note ? ": {$note}" : ''));

            return $issue->fresh();
        });
    }

    /**
     * Reopen a closed issue.
     */
    public function reopen(Issue $issue, ?string $note = null): Issue
    {
        return DB::transaction(function () use ($issue, $note) {
            $issue->update([
                'status' => 'open',
                'closed_at' => null,
                'closed_by_user_id' => null,
                'verified_at' => null,
                'verified_by_user_id' => null,
            ]);

            $this->logActivity($issue, 'reopened', "Issue was reopened" . ($note ? ": {$note}" : ''));

            return $issue->fresh();
        });
    }

    /**
     * Delete an issue (soft delete).
     */
    public function delete(Issue $issue): bool
    {
        $this->logActivity($issue, 'deleted', 'Issue was deleted');

        return $issue->delete();
    }

    /**
     * Permanently delete an issue (force delete).
     */
    public function forceDelete(Issue $issue): bool
    {
        return $issue->forceDelete();
    }

    /**
     * Restore a soft deleted issue.
     */
    public function restore(Issue $issue): Issue
    {
        $issue->restore();

        $this->logActivity($issue, 'restored', 'Issue was restored');

        return $issue->fresh();
    }

    /**
     * Assign issue to a user.
     */
    public function assign(Issue $issue, int $userId): Issue
    {
        $previousAssignee = $issue->assigned_to_user_id;

        $issue->update(['assigned_to_user_id' => $userId]);

        $newAssignee = $issue->assignedTo?->name ?? 'Unassigned';
        $previousAssigneeName = $issue->assignedTo?->name ?? 'Unassigned';

        $this->logActivity($issue, 'assigned', "Assigned to {$newAssignee}" .
            ($previousAssignee ? " (from: {$previousAssigneeName})" : ''));

        return $issue->fresh();
    }

    /**
     * Get issues with filters applied.
     */
    public function getFilteredIssues(array $filters, int $perPage = 15, bool $usePagination = true)
    {
        $query = Issue::with(['departments', 'issueTypes', 'createdBy', 'assignedTo']);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by department
        if (!empty($filters['department_id'])) {
            $departmentIds = is_array($filters['department_id'])
                ? $filters['department_id']
                : explode(',', $filters['department_id']);

            $query->whereHas('departments', function ($q) use ($departmentIds) {
                $q->whereIn('departments.id', $departmentIds);
            });
        }

        // Filter by issue type
        if (!empty($filters['issue_type_id'])) {
            $issueTypeIds = is_array($filters['issue_type_id'])
                ? $filters['issue_type_id']
                : explode(',', $filters['issue_type_id']);

            $query->whereHas('issueTypes', function ($q) use ($issueTypeIds) {
                $q->whereIn('issue_types.id', $issueTypeIds);
            });
        }

        // Filter by category (through issue types)
        if (!empty($filters['category_id'])) {
            $query->whereHas('issueTypes', function ($q) use ($filters) {
                $q->where('issue_types.issue_category_id', $filters['category_id']);
            });
        }

        // Filter by priority
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Filter by assigned user
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to_user_id', $filters['assigned_to']);
        }

        // Filter by created by
        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // Filter by date range (use issue_date if available, otherwise created_at)
        if (!empty($filters['date_from'])) {
            $query->whereRaw('COALESCE(DATE(issue_date), DATE(created_at)) >= ?', [$filters['date_from']]);
        }

        if (!empty($filters['date_to'])) {
            $query->whereRaw('COALESCE(DATE(issue_date), DATE(created_at)) <= ?', [$filters['date_to']]);
        }

        // Search by title, description, location, or room number
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'desc';

        $query->orderBy($orderBy, $orderDir);

        return $usePagination ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Track changes for activity logging.
     */
    protected function trackChanges(Issue $issue, array $data): array
    {
        $changes = [];
        $fieldLabels = [
            'title' => 'Title',
            'description' => 'Description',
            'priority' => 'Priority',
            'status' => 'Status',
            'location' => 'Location',
            'assigned_to' => 'Assigned To',
        ];

        foreach ($data as $field => $newValue) {
            if ($issue->$field != $newValue) {
                $oldValue = $issue->$field;

                if ($field === 'assigned_to') {
                    $oldUser = $issue->assignedTo?->name ?? 'Unassigned';
                    $newUser = User::find($newValue)?->name ?? 'Unassigned';
                    $changes[] = "{$fieldLabels[$field]} changed from {$oldUser} to {$newUser}";
                } elseif (isset($fieldLabels[$field])) {
                    $changes[] = "{$fieldLabels[$field]} changed from {$oldValue} to {$newValue}";
                }
            }
        }

        return $changes;
    }

    /**
     * Log activity for an issue.
     */
    protected function logActivity(Issue $issue, string $action, string $description): void
    {
        ActivityLog::create([
            'subject_type' => Issue::class,
            'subject_id' => $issue->id,
            'actor_user_id' => Auth::id(),
            'action' => $action,
            'meta' => [
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }

    /**
     * Get activity log for an issue.
     */
    public function getActivityLog(Issue $issue)
    {
        return ActivityLog::where('subject_type', Issue::class)
            ->where('subject_id', $issue->id)
            ->with('actor')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get statistics for issues.
     */
    public function getStatistics(): array
    {
        return [
            'open' => Issue::open()->count(),
            'closed_today' => Issue::closed()->whereDate('closed_at', today())->count(),
            'closed_this_week' => Issue::closed()->whereBetween('closed_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
            'urgent' => Issue::open()->where('priority', 'urgent')->count(),
            'high' => Issue::open()->where('priority', 'high')->count(),
            'avg_close_time' => $this->calculateAverageCloseTime(),
        ];
    }

    /**
     * Calculate average close time in hours.
     */
    protected function calculateAverageCloseTime(): float
    {
        return Issue::closed()
            ->whereNotNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
            ->value('avg_hours') ?? 0;
    }
}
