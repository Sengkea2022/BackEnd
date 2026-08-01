<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showCurrent(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role.permissions', 'shop']);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateCurrent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'bio' => 'sometimes|nullable|string',
            'locale' => 'sometimes|string|max:10',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'user' => $user->fresh(['role.permissions', 'shop']),
        ]);
    }

    public function getAssignablePersonnel(): JsonResponse
    {
        $managers = User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'manager');
            })
            ->get(['id', 'name', 'email', 'shop_code', 'store_code']);

        $staff = User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'staff');
            })
            ->get(['id', 'name', 'email', 'shop_code', 'store_code']);

        return response()->json([
            'managers' => $managers,
            'staff' => $staff,
        ]);
    }

    public function getStoreOwners(): JsonResponse
    {
        $owners = User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'store-owner')
                      ->orWhere('slug', 'shop-owner');
            })
            ->get(['id', 'code', 'name', 'email']);

        return response()->json([
            'owners' => $owners,
        ]);
    }

    public function getStoreDepartments(Request $request): JsonResponse
    {
        $shopUuid = $request->input('shop_uuid') ?? $request->input('store_uuid');
        
        if (!$shopUuid) {
            return response()->json(['message' => 'shop_uuid is required'], 422);
        }

        $shop = Shop::where('uuid', $shopUuid)->firstOrFail();

        $userDepts = User::where(function ($q) use ($shop) {
                $q->where('shop_code', $shop->code)
                  ->orWhere('store_code', $shop->code);
            })
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->pluck('department');

        $roleDepts = Role::where(function ($q) use ($shop) {
                $q->where('shop_code', $shop->code)
                  ->orWhere('store_code', $shop->code);
            })
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
        $shopUuid = $request->input('shop_uuid') ?? $request->input('store_uuid');
        
        if (!$shopUuid) {
            return response()->json(['message' => 'shop_uuid is required'], 422);
        }

        $department = $request->input('department');

        $shop = Shop::where('uuid', $shopUuid)->firstOrFail();
        
        $user = $request->user();
        $isOwner = $shop->user_code === $user->code;
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        $userShopCode = $user->shop_code ?? $user->store_code;
        
        if (!$isOwner && (!$hasPermission || $userShopCode !== $shop->code)) {
            if ($userShopCode !== $shop->code && $user->role?->slug !== 'superadmin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $query = User::with('role')
            ->where(function ($q) use ($shop) {
                $q->where('shop_code', $shop->code)
                  ->orWhere('store_code', $shop->code);
            });

        // Manager Department Scope check (role department takes precedence over user department)
        $managerDept = $user->role?->department ?? $user->department;

        if (!$isOwner && $user->role?->slug !== 'superadmin' && !empty($managerDept)) {
            $query->where('department', $managerDept);
        } elseif (!empty($department)) {
            $query->where('department', $department);
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

        $targetUser = User::with('role')->where('uuid', $uuid)->firstOrFail();
        $user = $request->user();

        $shopCode = $targetUser->shop_code ?? $targetUser->store_code;
        if (!$shopCode) {
            return response()->json(['message' => 'User is not assigned to any shop.'], 400);
        }

        $shop = Shop::where('code', $shopCode)->firstOrFail();
        $isOwner = $shop->user_code === $user->code;
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        $userShopCode = $user->shop_code ?? $user->store_code;

        if (!$isOwner && (!$hasPermission || $userShopCode !== $shopCode)) {
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
            $newRole = Role::find($validated['role_id']);
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
        $targetUser = User::with('role')->where('uuid', $uuid)->firstOrFail();
        $user = $request->user();

        // If the user is removing themselves (Leave Shop)
        if ($targetUser->id === $user->id) {
            $targetUser->update(['shop_code' => null, 'store_code' => null, 'role_id' => null]);
            return response()->json(['message' => 'You have left the shop.']);
        }

        // Otherwise, it's a "Kick Out" attempt
        $shopCode = $targetUser->shop_code ?? $targetUser->store_code;
        if (!$shopCode) {
            return response()->json(['message' => 'User is not in any shop.'], 400);
        }

        $shop = Shop::where('code', $shopCode)->firstOrFail();
        $isOwner = $shop->user_code === $user->code;
        
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        $userShopCode = $user->shop_code ?? $user->store_code;
        
        if (!$isOwner && (!$hasPermission || $userShopCode !== $shopCode)) {
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

        $targetUser->update(['shop_code' => null, 'store_code' => null, 'role_id' => null]);
        return response()->json(['message' => 'User has been removed from the shop.']);
    }
}
