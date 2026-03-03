<?php

namespace App\Livewire\Admin\Departments;

use App\Models\Department;
use Livewire\Component;

class Form extends Component
{
    public ?Department $department = null;

    public string $name = '';
    public ?string $description = null;

    public bool $isEditing = false;

    public function mount(?Department $department = null): void
    {
        if ($department) {
            $this->department = $department;
            $this->isEditing = true;
            $this->name = $department->name;
            $this->description = $department->description;

            $this->authorize('update', $department);
        } else {
            $this->authorize('create', Department::class);
        }
    }

    public function render()
    {
        return view('livewire.admin.departments.form')
            ->layout('layouts.app')
            ->title($this->isEditing ? 'Edit Department' : 'Add Department');
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->department->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            session()->flash('success', 'Department updated successfully.');
        } else {
            Department::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            session()->flash('success', 'Department created successfully.');
        }

        return $this->redirectRoute('admin.departments.index');
    }

    public function cancel()
    {
        if ($this->isEditing) {
            return $this->redirectRoute('admin.departments.index');
        }

        return $this->redirectRoute('admin.departments.index');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', $this->isEditing ? 'unique:departments,name,' . $this->department->id : 'unique:departments,name'],
            'description' => ['nullable', 'string'],
        ];
    }
}
