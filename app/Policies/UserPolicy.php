<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('admin.users.view') || $user->can('admin.users.*');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('admin.users.view') || $user->can('admin.users.*');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.users.create') || $user->can('admin.users.*');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('admin.users.update') || $user->can('admin.users.*');
    }

    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->can('admin.users.delete') || $user->can('admin.users.*');
    }
}
