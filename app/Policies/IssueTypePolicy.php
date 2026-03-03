<?php

namespace App\Policies;

use App\Models\IssueType;
use App\Models\User;

class IssueTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.issue-types.view');
    }

    public function view(User $user, IssueType $issueType): bool
    {
        return $user->can('admin.issue-types.view');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.issue-types.create');
    }

    public function update(User $user, IssueType $issueType): bool
    {
        return $user->can('admin.issue-types.update');
    }

    public function delete(User $user, IssueType $issueType): bool
    {
        return $user->can('admin.issue-types.delete') && $issueType->issues()->count() === 0;
    }
}
