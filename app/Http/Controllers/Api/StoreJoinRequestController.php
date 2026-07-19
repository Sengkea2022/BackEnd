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
        $storeUuid = $request->input('store_uuid');

        if ($storeUuid) {
            // Manager viewing store requests/invites
            $store = \App\Models\Store::where('uuid', $storeUuid)->firstOrFail();
            
            $isOwner = $store->user_code === $user->code;
            
            // Check if user has permission to manage users (if not owner)
            $hasPermission = $user->role && $user->role->permissions()->where('slug', 'edit-users')->exists();

            if (!$isOwner && (!$hasPermission || $user->store_code !== $store->code)) {
                return response()->json(['message' => 'Unauthorized. You do not have permission to view requests.'], 403);
            }

            $requests = StoreJoinRequest::with(['user.role', 'store'])->where('store_id', $store->id)->where('status', 'pending')->get();
            return response()->json(['data' => $requests]);
        } else {
            // User viewing their own requests/invites
            $requests = StoreJoinRequest::with(['store', 'user.role'])->where('user_id', $user->id)->where('status', 'pending')->get();
            return response()->json(['data' => $requests]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:request,invite',
            'store_uuid' => 'required_if:type,invite|uuid|exists:stores,uuid',
            'store_code' => 'required_if:type,request|string', 
            'email' => 'required_if:type,invite|email',
            'role_id' => 'required_if:type,invite|exists:roles,id',
        ]);
        
        $user = $request->user();
        
        if ($validated['type'] === 'request') {
            $store = \App\Models\Store::where('code', $validated['store_code'])
                        ->orWhere('uuid', $validated['store_code'])->firstOrFail();
            
            if ($user->store_code === $store->code) {
                return response()->json(['message' => 'You are already in this store.'], 400);
            }
            
            if (StoreJoinRequest::where('user_id', $user->id)->where('store_id', $store->id)->where('status', 'pending')->exists()) {
                return response()->json(['message' => 'You already have a pending request.'], 400);
            }
            
            $req = StoreJoinRequest::create([
                'user_id' => $user->id, 
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
            
            if (StoreJoinRequest::where('user_id', $targetUser->id)->where('store_id', $store->id)->where('status', 'pending')->exists()) {
                return response()->json(['message' => 'Invite already pending for this user.'], 400);
            }
            
            $req = StoreJoinRequest::create([
                'user_id' => $targetUser->id, 
                'store_id' => $store->id, 
                'type' => 'invite', 
                'role_id' => $targetRole->id, 
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
                $val = $request->validate(['role_id' => 'required|exists:roles,id']);
                $role = \App\Models\Role::find($val['role_id']);
                
                $userLevel = $user->role ? $user->role->level : 99;
                if (!$isOwner && $role->level <= $userLevel) {
                    return response()->json(['message' => 'You cannot assign a role equal or higher than your own'], 403);
                }
                
                $req->user->update(['store_code' => $store->code, 'role_id' => $role->id]);
            }
        } else {
            // User approves an invite
            if ($req->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            
            if ($validated['status'] === 'approved') {
                $req->user->update(['store_code' => $req->store->code, 'role_id' => $req->role_id]);
            }
        }
        
        $req->update(['status' => $validated['status']]);
        return response()->json(['message' => 'Request updated successfully.']);
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
