<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function showCurrent(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->load(['role.permissions', 'store']);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateCurrent(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $user?->update($validated);

        if ($user) {
            $user->load(['role.permissions', 'store']);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    public function getAssignablePersonnel(): JsonResponse
    {
        $managers = \App\Models\User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'manager');
            })
            ->get(['id', 'name', 'email', 'store_code']);

        $staff = \App\Models\User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'staff');
            })
            ->get(['id', 'name', 'email', 'store_code']);

        return response()->json([
            'managers' => $managers,
            'staff' => $staff,
        ]);
    }
    public function getStoreOwners(): JsonResponse
    {
        $owners = \App\Models\User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'store-owner');
            })
            ->get(['id', 'code', 'name', 'email']);

        return response()->json([
            'owners' => $owners,
        ]);
    }

    public function getStoreDepartments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_uuid' => 'required|uuid|exists:stores,uuid'
        ]);

        $store = \App\Models\Store::where('uuid', $validated['store_uuid'])->firstOrFail();

        $userDepts = \App\Models\User::where('store_code', $store->code)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->pluck('department');

        $roleDepts = \App\Models\Role::where('store_code', $store->code)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->pluck('department');

        $departments = $userDepts->merge($roleDepts)->unique()->values();

        return response()->json([
            'data' => $departments
        ]);
    }

    public function indexStoreStaff(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_uuid' => 'required|uuid|exists:stores,uuid',
            'department' => 'sometimes|nullable|string',
        ]);

        $store = \App\Models\Store::where('uuid', $validated['store_uuid'])->firstOrFail();
        
        $user = $request->user();
        $isOwner = $store->user_code === $user->code;
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        
        if (!$isOwner && (!$hasPermission || $user->store_code !== $store->code)) {
            if ($user->store_code !== $store->code && $user->role?->slug !== 'superadmin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $query = \App\Models\User::with('role')
            ->where('store_code', $store->code);

        // Manager Department Scope check (role department takes precedence over user department)
        $managerDept = $user->role?->department ?? $user->department;

        if (!$isOwner && $user->role?->slug !== 'superadmin' && !empty($managerDept)) {
            $query->where('department', $managerDept);
        } elseif (!empty($validated['department'])) {
            $query->where('department', $validated['department']);
        }

        $staff = $query->get();

        return response()->json([
            'data' => $staff
        ]);
    }

    public function updateStaff(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'sometimes|nullable|exists:roles,id',
            'department' => 'sometimes|nullable|string|max:255',
            'active_status' => 'sometimes|string|in:active,inactive',
        ]);

        $targetUser = \App\Models\User::with('role')->where('uuid', $uuid)->firstOrFail();
        $user = $request->user();

        $storeCode = $targetUser->store_code;
        if (!$storeCode) {
            return response()->json(['message' => 'User is not assigned to any store.'], 400);
        }

        $store = \App\Models\Store::where('code', $storeCode)->firstOrFail();
        $isOwner = $store->user_code === $user->code;
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();

        if (!$isOwner && (!$hasPermission || $user->store_code !== $storeCode)) {
            return response()->json(['message' => 'Unauthorized to update staff.'], 403);
        }

        // Hierarchy check
        $targetLevel = $targetUser->role ? $targetUser->role->level : 99;
        $userLevel = $user->role ? $user->role->level : 99;

        if (!$isOwner && $user->role?->slug !== 'superadmin' && $targetLevel <= $userLevel) {
            return response()->json(['message' => 'You cannot modify a user with a rank equal to or higher than your own.'], 403);
        }

        // Manager Department Scope check
        $managerDept = $user->role?->department ?? $user->department;
        if (!$isOwner && $user->role?->slug !== 'superadmin' && !empty($managerDept)) {
            if ($targetUser->department !== $managerDept) {
                return response()->json(['message' => 'You can only manage staff in your role\'s assigned department (' . $managerDept . ').'], 403);
            }
            // If changing department, restrict manager to their own department
            if (array_key_exists('department', $validated) && $validated['department'] !== $managerDept) {
                return response()->json(['message' => 'You can only assign staff to your role\'s assigned department (' . $managerDept . ').'], 403);
            }
        }

        if (array_key_exists('role_id', $validated) && $validated['role_id']) {
            $newRole = \App\Models\Role::find($validated['role_id']);
            if ($newRole && !$isOwner && $user->role?->slug !== 'superadmin' && $newRole->level <= $userLevel) {
                return response()->json(['message' => 'You cannot assign a role rank equal to or higher than your own.'], 403);
            }
        }

        $targetUser->update($validated);

        return response()->json([
            'message' => 'Staff updated successfully.',
            'data' => $targetUser->fresh('role'),
        ]);
    }

    public function removeStore(Request $request, $uuid): JsonResponse
    {
        $targetUser = \App\Models\User::with('role')->where('uuid', $uuid)->firstOrFail();
        $user = $request->user();

        // If the user is removing themselves (Leave Store)
        if ($targetUser->id === $user->id) {
            $targetUser->update(['store_code' => null, 'role_id' => null]);
            return response()->json(['message' => 'You have left the store.']);
        }

        // Otherwise, it's a "Kick Out" attempt
        $storeCode = $targetUser->store_code;
        if (!$storeCode) {
            return response()->json(['message' => 'User is not in any store.'], 400);
        }

        $store = \App\Models\Store::where('code', $storeCode)->firstOrFail();
        $isOwner = $store->user_code === $user->code;
        
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        
        if (!$isOwner && (!$hasPermission || $user->store_code !== $storeCode)) {
            return response()->json(['message' => 'Unauthorized to kick out staff.'], 403);
        }

        // Hierarchy check
        $targetLevel = $targetUser->role ? $targetUser->role->level : 99;
        $userLevel = $user->role ? $user->role->level : 99;
        
        if (!$isOwner && $targetLevel <= $userLevel) {
            return response()->json(['message' => 'You cannot kick out a user with a rank equal to or higher than your own.'], 403);
        }

        // Manager Department Scope check
        $managerDept = $user->role?->department ?? $user->department;
        if (!$isOwner && $user->role?->slug !== 'superadmin' && !empty($managerDept)) {
            if ($targetUser->department !== $managerDept) {
                return response()->json(['message' => 'You can only remove staff in your assigned department (' . $managerDept . ').'], 403);
            }
        }

        $targetUser->update(['store_code' => null, 'role_id' => null]);
        return response()->json(['message' => 'User has been removed from the store.']);
    }
}
