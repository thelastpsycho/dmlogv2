<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Form extends Component
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public array $roles = [];
    public bool $is_active = true;

    public bool $isEditing = false;

    public function mount(?User $user = null): void
    {
        if ($user) {
            $this->user = $user;
            $this->isEditing = true;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->roles = $user->roles->pluck('id')->toArray();
            $this->is_active = $user->is_active;

            $this->authorize('update', $user);
        } else {
            $this->authorize('create', User::class);
        }
    }

    public function render()
    {
        $allRoles = Role::orderBy('name')->get();

        return view('livewire.admin.users.form', [
            'allRoles' => $allRoles,
        ])->layout('layouts.app')->title($this->isEditing ? 'Edit User' : 'Add User');
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'is_active' => $this->is_active,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $this->user->update($data);
            $this->user->roles()->sync($this->roles);

            session()->flash('success', 'User updated successfully.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
            ]);

            $user->roles()->attach($this->roles);

            session()->flash('success', 'User created successfully.');
        }

        return $this->redirectRoute('admin.users.index');
    }

    public function cancel()
    {
        return $this->redirectRoute('admin.users.index');
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $this->isEditing ? 'unique:users,email,' . $this->user->id : 'unique:users,email'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'is_active' => ['boolean'],
        ];

        if (!$this->isEditing || $this->password) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function getRoleOptionsProperty(): \Illuminate\Support\Collection
    {
        return Role::orderBy('name')->get();
    }
}
