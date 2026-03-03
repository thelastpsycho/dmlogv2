<?php

namespace App\Livewire\Admin\IssueTypes;

use App\Models\IssueType;
use Livewire\Component;

class Form extends Component
{
    public ?IssueType $issueType = null;

    public string $name = '';
    public ?string $description = null;
    public string $default_severity = 'medium';

    public bool $isEditing = false;

    public function mount(?IssueType $issueType = null): void
    {
        if ($issueType) {
            $this->issueType = $issueType;
            $this->isEditing = true;
            $this->name = $issueType->name;
            $this->description = $issueType->description;
            $this->default_severity = $issueType->default_severity;

            $this->authorize('update', $issueType);
        } else {
            $this->authorize('create', IssueType::class);
        }
    }

    public function render()
    {
        return view('livewire.admin.issue-types.form')
            ->layout('layouts.app')
            ->title($this->isEditing ? 'Edit Issue Type' : 'Add Issue Type');
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->issueType->update([
                'name' => $this->name,
                'description' => $this->description,
                'default_severity' => $this->default_severity,
            ]);

            session()->flash('success', 'Issue type updated successfully.');
        } else {
            IssueType::create([
                'name' => $this->name,
                'description' => $this->description,
                'default_severity' => $this->default_severity,
            ]);

            session()->flash('success', 'Issue type created successfully.');
        }

        return $this->redirectRoute('admin.issue-types.index');
    }

    public function cancel()
    {
        return $this->redirectRoute('admin.issue-types.index');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', $this->isEditing ? 'unique:issue_types,name,' . $this->issueType->id : 'unique:issue_types,name'],
            'description' => ['nullable', 'string'],
            'default_severity' => ['required', 'in:urgent,high,medium,low'],
        ];
    }

    public function getSeveritiesProperty(): array
    {
        return [
            'urgent' => 'Urgent',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    }

    public function getSeverityBadgesProperty(): array
    {
        return [
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'medium' => 'badge-muted',
            'low' => 'badge-muted',
        ];
    }
}
