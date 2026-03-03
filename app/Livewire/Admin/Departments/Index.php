<?php

namespace App\Livewire\Admin\Departments;

use App\Models\Department;
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
        $departments = Department::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('issues')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.admin.departments.index', [
            'departments' => $departments,
        ])->layout('layouts.app')->title('Departments');
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

    public function deleteDepartment(int $id): void
    {
        $this->authorize('delete', Department::class);

        $department = Department::findOrFail($id);

        if ($department->issues()->count() > 0) {
            session()->flash('error', 'Cannot delete department with associated issues.');
            return;
        }

        $department->delete();
        session()->flash('success', 'Department deleted successfully.');
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
}
