<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\IssueResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::with('roles');

        // Filter by role
        if ($request->has('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('name');
        $users = $query->paginate($request->input('per_page', 15));

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        $user->load('roles');
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $data = $request->validated();

        // Handle password
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = User::create($data);

        // Sync roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $roleIds = collect($data['roles'])->pluck('id')->filter()->toArray();
            if (!empty($roleIds)) {
                $user->roles()->sync($roleIds);
            }
        } elseif (isset($data['role_ids']) && is_array($data['role_ids'])) {
            $user->roles()->sync($data['role_ids']);
        }

        $user->load('roles');
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();

        // Handle password update
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        // Sync roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $roleIds = collect($data['roles'])->pluck('id')->filter()->toArray();
            if (!empty($roleIds)) {
                $user->roles()->sync($roleIds);
            }
        } elseif (isset($data['role_ids']) && is_array($data['role_ids'])) {
            $user->roles()->sync($data['role_ids']);
        }

        $user->load('roles');
        return new UserResource($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function assignRole(Request $request, User $user): UserResource
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->roles()->syncWithoutDetaching([$request->role_id]);
        $user->load(['roles', 'permissions']);
        return new UserResource($user);
    }

    public function removeRole(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->roles()->detach([$request->role_id]);
        return response()->json(['message' => 'Role removed successfully']);
    }

    public function changePassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function activate(User $user): UserResource
    {
        $user->update(['is_active' => true]);
        $user->load(['roles', 'permissions']);
        return new UserResource($user);
    }

    public function deactivate(User $user): UserResource
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot deactivate yourself'], 403);
        }

        $user->update(['is_active' => false]);
        $user->load(['roles', 'permissions']);
        return new UserResource($user);
    }

    public function permissions(User $user): JsonResponse
    {
        $permissions = $user->getAllPermissions();
        return response()->json([
            'permissions' => $permissions->pluck('name'),
            'roles' => $user->roles->pluck('name')
        ]);
    }

    public function issues(Request $request, User $user): AnonymousResourceCollection
    {
        $query = $user->assignedIssues()
            ->with(['departments', 'issueTypes', 'createdBy', 'assignedTo']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $issues = $query->paginate($request->input('per_page', 15));

        return IssueResource::collection($issues);
    }
}
