<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Issue\StoreIssueRequest;
use App\Http\Requests\Api\Issue\UpdateIssueRequest;
use App\Http\Resources\IssueResource;
use App\Models\Issue;
use App\Services\IssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IssueController extends Controller
{
    protected IssueService $issueService;

    public function __construct(IssueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Issue::query()
            ->with(['departments', 'issueTypes', 'issueTypes.issueCategory', 'createdBy', 'assignedTo']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->whereHas('departments', function ($q) use ($request) {
                $q->where('departments.id', $request->department_id);
            });
        }

        // Filter by issue type
        if ($request->has('issue_type_id')) {
            $query->whereHas('issueTypes', function ($q) use ($request) {
                $q->where('issue_types.id', $request->issue_type_id);
            });
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->input('per_page', 15);
        $issues = $query->paginate($perPage);

        return IssueResource::collection($issues);
    }

    public function show(Issue $issue): IssueResource
    {
        $issue->load(['departments', 'issueTypes', 'issueTypes.issueCategory', 'createdBy', 'updatedBy', 'assignedTo', 'closedBy', 'verifiedBy', 'comments.user', 'activityLogs.actor']);
        return new IssueResource($issue);
    }

    public function store(StoreIssueRequest $request): IssueResource
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $issue = Issue::create($data);

        // Sync departments
        if ($request->has('department_ids')) {
            $issue->departments()->sync($request->department_ids);
        }

        // Sync issue types
        if ($request->has('issue_type_ids')) {
            $issue->issueTypes()->sync($request->issue_type_ids);
        }

        $issue->load(['departments', 'issueTypes', 'issueTypes.issueCategory', 'createdBy']);

        return new IssueResource($issue);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): IssueResource
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // Include department_ids and issue_type_ids if present
        if ($request->has('department_ids')) {
            $data['department_ids'] = $request->department_ids;
        }
        if ($request->has('issue_type_ids')) {
            $data['issue_type_ids'] = $request->issue_type_ids;
        }

        $issue = $this->issueService->update($issue, $data);
        $issue->load(['departments', 'issueTypes', 'issueTypes.issueCategory', 'createdBy', 'updatedBy', 'activityLogs.actor']);

        return new IssueResource($issue);
    }

    public function destroy(Issue $issue): JsonResponse
    {
        $issue->delete();
        return response()->json(['message' => 'Issue deleted successfully']);
    }

    public function close(Issue $issue): IssueResource
    {
        $issue = $this->issueService->close($issue);
        $issue->load(['departments', 'issueTypes', 'createdBy', 'comments.user', 'closedBy', 'activityLogs.actor']);
        return new IssueResource($issue);
    }

    public function verify(Issue $issue): IssueResource
    {
        $this->authorize('verify', $issue);
        $issue = $this->issueService->verify($issue);
        $issue->load(['departments', 'issueTypes', 'createdBy', 'comments.user', 'verifiedBy', 'activityLogs.actor']);
        return new IssueResource($issue);
    }

    public function reopen(Issue $issue): IssueResource
    {
        $this->authorize('reopen', $issue);
        $issue = $this->issueService->reopen($issue);
        $issue->load(['departments', 'issueTypes', 'createdBy', 'comments.user', 'activityLogs.actor']);
        return new IssueResource($issue);
    }
}
