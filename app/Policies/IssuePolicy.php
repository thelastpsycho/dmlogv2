<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    /**
     * Determine if the user can view any issues.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('issues.view') || $user->can('issues.view.own');
    }

    /**
     * Determine if the user can view a specific issue.
     */
    public function view(User $user, Issue $issue): bool
    {
        if ($user->can('issues.view')) {
            return true;
        }

        return $user->can('issues.view.own') && $issue->created_by === $user->id;
    }

    /**
     * Determine if the user can create issues.
     */
    public function create(User $user): bool
    {
        return $user->can('issues.create');
    }

    /**
     * Determine if the user can update a specific issue.
     */
    public function update(User $user, Issue $issue): bool
    {
        if ($user->can('issues.update')) {
            return true;
        }

        return $user->can('issues.update.own') && $issue->created_by === $user->id;
    }

    /**
     * Determine if the user can delete a specific issue.
     */
    public function delete(User $user, Issue $issue): bool
    {
        if ($user->can('issues.delete')) {
            return true;
        }

        return $user->can('issues.delete.own') && $issue->created_by === $user->id;
    }

    /**
     * Determine if the user can close a specific issue.
     */
    public function close(User $user, Issue $issue): bool
    {
        return $user->can('issues.close') && $issue->status === 'open';
    }

    /**
     * Determine if the user can reopen a specific issue.
     */
    public function reopen(User $user, Issue $issue): bool
    {
        return $user->can('issues.reopen') && $issue->status === 'closed';
    }

    /**
     * Determine if the user can export issues.
     */
    public function export(User $user): bool
    {
        return $user->can('issues.export');
    }
}
