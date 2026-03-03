<?php

namespace App\Livewire\Admin\IssueTypes;

use App\Models\IssueType;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function render()
    {
        $issueTypes = IssueType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('issues')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.admin.issue-types.index', [
            'issueTypes' => $issueTypes,
        ])->layout('layouts.app')->title('Issue Types');
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteIssueType(int $id): void
    {
        $this->authorize('delete', IssueType::class);

        $issueType = IssueType::findOrFail($id);

        if ($issueType->issues()->count() > 0) {
            session()->flash('error', 'Cannot delete issue type with associated issues.');
            return;
        }

        $issueType->delete();
        session()->flash('success', 'Issue type deleted successfully.');
    }

    public function getSortIcon(string $field): string
    {
        if ($this->sortField !== $field) {
            return 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4 4';
        }

        return $this->sortDirection === 'asc'
            ? 'M4.5 15.75l7.5-7.5 7.5 7.5'
            : 'M19.5 8.25l-7.5 7.5-7.5-7.5';
    }

    public function getPriorityBadge(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'medium' => 'badge-muted',
            'low' => 'badge-muted',
            default => 'badge-muted',
        };
    }
}
