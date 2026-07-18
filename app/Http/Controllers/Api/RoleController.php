<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Role\StoreRoleRequest;
use App\Http\Requests\Api\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Role::query()->with('permissions');

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $query->orderBy('name');
        $roles = $query->paginate($request->input('per_page', 15));

        return RoleResource::collection($roles);
    }

    public function show(Role $role): RoleResource
    {
        $role->load(['permissions', 'users']);
        return new RoleResource($role);
    }

    public function store(StoreRoleRequest $request): RoleResource
    {
        $role = Role::create($request->validated());

        // Sync permissions
        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        $role->load('permissions');
        return new RoleResource($role);
    }

    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $role->update($request->validated());

        // Sync permissions
        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        $role->load('permissions');
        return new RoleResource($role);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
