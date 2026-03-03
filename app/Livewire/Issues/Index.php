<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Department;
use App\Models\IssueType;
use App\Models\User;
use App\Services\IssueService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $tab = 'open'; // open or closed
    public string $search = '';
    public ?int $department_id = null;
    public ?int $issue_type_id = null;
    public ?string $priority = null;
    public ?int $assigned_to = null;
    public ?string $date_from = null;
    public ?string $date_to = null;
    public string $order_by = 'created_at';
    public string $order_dir = 'desc';

    public array $selectedIssues = [];
    public bool $selectAll = false;

    protected $queryString = [
        'tab' => ['except' => 'open'],
        'search' => ['except' => ''],
        'department_id' => ['except' => ''],
        'issue_type_id' => ['except' => ''],
        'priority' => ['except' => ''],
        'assigned_to' => ['except' => ''],
    ];

    protected IssueService $issueService;

    public function boot(IssueService $issueService): void
    {
        $this->issueService = $issueService;
    }

    public function render()
    {
        $issues = $this->getIssues();

        return view('livewire.issues.index', [
            'issues' => $issues,
        ])
            ->layout('layouts.app')
            ->title('Issues');
    }

    protected function getIssues()
    {
        $filters = [
            'status' => $this->tab === 'open' ? 'open' : 'closed',
            'search' => $this->search,
            'department_id' => $this->department_id,
            'issue_type_id' => $this->issue_type_id,
            'priority' => $this->priority,
            'assigned_to' => $this->assigned_to,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
        ];

        return $this->issueService->getFilteredIssues($filters);
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedIssues = $this->getIssues()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedIssues = [];
        }
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'department_id',
            'issue_type_id',
            'priority',
            'assigned_to',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function closeSelected(): void
    {
        $this->authorize('close', Issue::class);

        $issues = Issue::whereIn('id', $this->selectedIssues)->open()->get();

        foreach ($issues as $issue) {
            $this->issueService->close($issue);
        }

        $this->selectedIssues = [];
        $this->selectAll = false;

        session()->flash('success', "{$issues->count()} issue(s) closed successfully.");
    }

    public function reopenSelected(): void
    {
        $this->authorize('reopen', Issue::class);

        $issues = Issue::whereIn('id', $this->selectedIssues)->closed()->get();

        foreach ($issues as $issue) {
            $this->issueService->reopen($issue);
        }

        $this->selectedIssues = [];
        $this->selectAll = false;

        session()->flash('success', "{$issues->count()} issue(s) reopened successfully.");
    }

    public function deleteSelected(): void
    {
        $this->authorize('delete', Issue::class);

        $count = Issue::whereIn('id', $this->selectedIssues)->delete();

        $this->selectedIssues = [];
        $this->selectAll = false;

        session()->flash('success', "{$count} issue(s) deleted successfully.");
    }

    public function closeIssue(int $issueId): void
    {
        $issue = Issue::findOrFail($issueId);
        $this->authorize('close', $issue);

        $this->issueService->close($issue);

        session()->flash('success', 'Issue closed successfully.');
    }

    public function reopenIssue(int $issueId): void
    {
        $issue = Issue::findOrFail($issueId);
        $this->authorize('reopen', $issue);

        $this->issueService->reopen($issue);

        session()->flash('success', 'Issue reopened successfully.');
    }

    public function deleteIssue(int $issueId): void
    {
        $issue = Issue::findOrFail($issueId);
        $this->authorize('delete', $issue);

        $this->issueService->delete($issue);

        session()->flash('success', 'Issue deleted successfully.');
    }

    #[Computed]
    public function departments(): array
    {
        return Department::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    #[Computed]
    public function issueTypes(): array
    {
        return IssueType::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    #[Computed]
    public function users(): array
    {
        return User::orderBy('name')
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    #[Computed]
    public function priorities(): array
    {
        return [
            'urgent' => 'Urgent',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    }

    #[On('issue-created')]
    #[On('issue-updated')]
    #[On('issue-closed')]
    #[On('issue-reopened')]
    public function refresh(): void
    {
        $this->resetPage();
    }
}
