<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreJoinRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreJoinRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['data' => []]);
        }

        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $storeUuid = $request->input('store_uuid');

        $roleSlug = $user->role?->slug;
        $isSuper = in_array($roleSlug, ['superadmin', 'admin']);
        $isOwner = $roleSlug === 'store-owner';
        $hasEditUsersPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        $isOwnerOrManager = $isSuper || $isOwner || $hasEditUsersPermission;

        if ($storeUuid) {
            $store = \App\Models\Store::where('uuid', $storeUuid)
                ->orWhere('code', $storeUuid)
                ->firstOrFail();

            $isStoreOwner = $store->user_code === $user->code;

            if (!$isSuper && !$isStoreOwner && (!$hasEditUsersPermission || $user->store_code !== $store->code)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            $requests = StoreJoinRequest::with(['user.role', 'store', 'role'])
                ->where('store_id', $store->id)
                ->where('type', 'request')
                ->where('status', 'pending')
                ->get();
            return response()->json(['data' => $requests]);
        }

        if ($isOwnerOrManager) {
            $query = StoreJoinRequest::with(['user.role', 'store', 'role'])
                ->where('type', 'request')
                ->where('status', 'pending');

            if (!$isSuper) {
                $storeIds = \App\Models\Store::where('user_code', $user->code);
                if (!empty($user->store_code) && $user->store_code !== 'N/A') {
                    $storeIds->orWhere('code', $user->store_code);
                }
                $query->whereIn('store_id', $storeIds->pluck('id'));
            }

            $requests = $query->get();
            return response()->json(['data' => $requests]);
        }

        // Regular staff member viewing their own requests & invitations
        $requests = StoreJoinRequest::with(['store', 'user.role', 'role'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->code) {
                    $q->orWhere('user_code', $user->code);
                }
            })
            ->where('status', 'pending')
            ->get();
        return response()->json(['data' => $requests]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:request,invite',
            'store_uuid' => 'required_if:type,invite|uuid|exists:stores,uuid',
            'store_code' => 'required_if:type,request|string', 
            'email' => 'required_if:type,invite|email',
            'role_id' => 'required_if:type,invite|exists:roles,id',
            'department' => 'sometimes|nullable|string|max:255',
        ]);
        
        $user = $request->user();
        
        if ($validated['type'] === 'request') {
            $store = \App\Models\Store::where('code', $validated['store_code'])
                        ->orWhere('uuid', $validated['store_code'])->firstOrFail();
            
            if ($user->store_code === $store->code) {
                return response()->json(['message' => 'You are already in this store.'], 400);
            }
            
            if (StoreJoinRequest::where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->code) $q->orWhere('user_code', $user->code);
            })->where('store_id', $store->id)->where('status', 'pending')->exists()) {
                return response()->json(['message' => 'You already have a pending request.'], 400);
            }
            
            $req = StoreJoinRequest::create([
                'user_id' => $user->id,
                'user_code' => $user->code,
                'store_id' => $store->id, 
                'type' => 'request', 
                'status' => 'pending'
            ]);
            return response()->json(['message' => 'Request to join sent successfully.', 'data' => $req]);
        } else {
            // Invite
            $store = \App\Models\Store::where('uuid', $validated['store_uuid'])->firstOrFail();
            $isOwner = $store->user_code === $user->code;
            
            $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
            if (!$isOwner && (!$hasPermission || $user->store_code !== $store->code)) {
                return response()->json(['message' => 'Unauthorized to invite staff'], 403);
            }

            // Department check for manager
            $department = $validated['department'] ?? null;
            $managerDept = $user->role?->department ?? $user->department;
            if (!$isOwner && $user->role?->slug !== 'superadmin' && !empty($managerDept)) {
                $department = $managerDept; // enforce manager's department
            }
            
            $targetUser = \App\Models\User::where('email', $validated['email'])->first();
            if (!$targetUser) return response()->json(['message' => 'User not found with this email'], 404);
            
            if ($targetUser->store_code === $store->code) {
                return response()->json(['message' => 'User is already in this store'], 400);
            }
            
            // Hierarchy check
            $targetRole = \App\Models\Role::find($validated['role_id']);
            $userLevel = $user->role ? $user->role->level : 99;
            if (!$isOwner && $targetRole->level <= $userLevel) {
                return response()->json(['message' => 'You cannot invite someone to a role equal or higher than your own'], 403);
            }
            
            if (StoreJoinRequest::where(function ($q) use ($targetUser) {
                $q->where('user_id', $targetUser->id);
                if ($targetUser->code) $q->orWhere('user_code', $targetUser->code);
            })->where('store_id', $store->id)->where('status', 'pending')->exists()) {
                return response()->json(['message' => 'Invite already pending for this user.'], 400);
            }
            
            $req = StoreJoinRequest::create([
                'user_id' => $targetUser->id, 
                'user_code' => $targetUser->code,
                'store_id' => $store->id, 
                'type' => 'invite', 
                'role_id' => $targetRole->id, 
                'department' => $department,
                'status' => 'pending'
            ]);
            return response()->json(['message' => 'Invitation sent successfully.', 'data' => $req]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);
        
        $req = StoreJoinRequest::with(['store', 'user'])->findOrFail($id);
        
        if ($req->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed'], 400);
        }
        
        $user = $request->user();
        
        if ($req->type === 'request') {
            // Manager approves a request
            $store = $req->store;
            $isOwner = $store->user_code === $user->code;
            $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
            
            if (!$isOwner && (!$hasPermission || $user->store_code !== $store->code)) {
                return response()->json(['message' => 'Unauthorized. You cannot approve requests for this store.'], 403);
            }
            
            if ($validated['status'] === 'approved') {
                $val = $request->validate([
                    'role_id' => 'sometimes|nullable|exists:roles,id',
                    'department' => 'sometimes|nullable|string|max:255',
                ]);

                $roleId = $val['role_id'] ?? null;
                if (!$roleId) {
                    $staffRole = \App\Models\Role::where('store_code', $store->code)->where('slug', 'staff')->first()
                        ?? \App\Models\Role::where('slug', 'staff')->first();
                    $roleId = $staffRole?->id;
                }

                $role = \App\Models\Role::findOrFail($roleId);
                
                $userLevel = $user->role ? $user->role->level : 99;
                if (!$isOwner && $role->level <= $userLevel) {
                    return response()->json(['message' => 'You cannot assign a role equal or higher than your own'], 403);
                }

                $department = $val['department'] ?? $req->department;
                $managerDept = $user->role?->department ?? $user->department;
                if (!$isOwner && $user->role?->slug !== 'superadmin' && !empty($managerDept)) {
                    $department = $managerDept;
                }
                
                $updateData = ['store_code' => $store->code, 'role_id' => $role->id];
                if ($department) {
                    $updateData['department'] = $department;
                }
                $req->user->update($updateData);
            }
        } else {
            // User approves an invite
            if ($req->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            
            if ($validated['status'] === 'approved') {
                $updateData = ['store_code' => $req->store->code, 'role_id' => $req->role_id];
                if ($req->department) {
                    $updateData['department'] = $req->department;
                }
                $req->user->update($updateData);
            }
        }
        
        $req->update(['status' => $validated['status']]);
        return response()->json([
            'message' => 'Request updated successfully.',
            'user' => $req->user->fresh('role')
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $req = StoreJoinRequest::findOrFail($id);
        $user = $request->user();
        
        // Can be deleted by the user who owns it, or by store management
        $isOwner = $req->store->user_code === $user->code;
        $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();
        $isStoreMgmt = $isOwner || ($hasPermission && $user->store_code === $req->store->code);
        
        if ($req->user_id !== $user->id && !$isStoreMgmt) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $req->delete();
        return response()->json(['message' => 'Request deleted.']);
    }
}
