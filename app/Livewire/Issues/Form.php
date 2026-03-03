<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Department;
use App\Models\IssueType;
use App\Models\User;
use App\Services\IssueService;
use Livewire\Component;
use Livewire\Attributes\Rule;

class Form extends Component
{
    public ?Issue $issue = null;

    #[Rule(['required', 'string', 'max:255'])]
    public string $title = '';

    #[Rule(['nullable', 'string'])]
    public ?string $description = null;

    #[Rule(['required', 'in:urgent,high,medium,low'])]
    public string $priority = 'medium';

    #[Rule(['nullable', 'string', 'max:255'])]
    public ?string $location = null;

    public array $department_ids = [];
    public array $issue_type_ids = [];

    #[Rule(['nullable', 'exists:users,id'])]
    public ?int $assigned_to = null;

    public bool $isEditing = false;

    protected IssueService $issueService;

    public function boot(IssueService $issueService): void
    {
        $this->issueService = $issueService;
    }

    public function mount(?Issue $issue = null): void
    {
        if ($issue) {
            $this->issue = $issue;
            $this->isEditing = true;
            $this->title = $issue->title;
            $this->description = $issue->description;
            $this->priority = $issue->priority;
            $this->location = $issue->location;
            $this->assigned_to = $issue->assigned_to;
            $this->department_ids = $issue->departments->pluck('id')->toArray();
            $this->issue_type_ids = $issue->issueTypes->pluck('id')->toArray();

            $this->authorize('update', $issue);
        } else {
            $this->authorize('create', Issue::class);
        }
    }

    public function save()
    {
        $this->validate([
            'department_ids' => ['required', 'array', 'min:1'],
            'department_ids.*' => ['exists:departments,id'],
            'issue_type_ids' => ['required', 'array', 'min:1'],
            'issue_type_ids.*' => ['exists:issue_types,id'],
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'location' => $this->location,
            'assigned_to' => $this->assigned_to ?: null,
            'department_ids' => $this->department_ids,
            'issue_type_ids' => $this->issue_type_ids,
        ];

        if ($this->isEditing) {
            $issue = $this->issueService->update($this->issue, $data);
            session()->flash('success', 'Issue updated successfully.');
            $this->dispatch('issue-updated');
        } else {
            $data['created_by'] = auth()->id();
            $issue = $this->issueService->create($data);
            session()->flash('success', 'Issue created successfully.');
            $this->dispatch('issue-created');
        }

        return $this->redirectRoute('issues.show', $issue);
    }

    public function render()
    {
        return view('livewire.issues.form')
            ->layout('layouts.app')
            ->title($this->isEditing ? 'Edit Issue' : 'Create Issue');
    }

    public function cancel()
    {
        if ($this->isEditing) {
            return $this->redirectRoute('issues.show', $this->issue);
        }

        return $this->redirectRoute('issues.index');
    }

    public function getDepartmentsProperty(): array
    {
        return Department::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getIssueTypesProperty(): array
    {
        return IssueType::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getUsersProperty(): array
    {
        return User::orderBy('name')
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(fn ($user) => [$user->id => $user->name])
            ->toArray();
    }

    public function getPrioritiesProperty(): array
    {
        return [
            'urgent' => 'Urgent',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    }

    public function getPriorityColorsProperty(): array
    {
        return [
            'urgent' => 'text-danger bg-danger/20',
            'high' => 'text-warning bg-warning/20',
            'medium' => 'text-muted bg-muted/20',
            'low' => 'text-muted bg-muted/20',
        ];
    }
}
