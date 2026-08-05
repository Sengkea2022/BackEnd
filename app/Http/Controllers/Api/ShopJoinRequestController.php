<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShopJoinRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = ShopJoinRequest::with(['user', 'shop', 'role']);

        if ($user->role?->slug !== 'developer') {
            if ($user->role?->slug === 'shop-owner') {
                $query->whereHas('shop', function ($q) use ($user) {
                    $q->where('user_code', $user->code);
                });
            } else if (!empty($user->shop_code) && $user->shop_code !== 'N/A') {
                $query->whereHas('shop', function ($q) use ($user) {
                    $q->where('code', $user->shop_code);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json([
            'data' => $query->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'department' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        // Check if there is already a pending request
        $existing = ShopJoinRequest::where('user_id', $user->id)
            ->where('shop_id', $request->shop_id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already have a pending join request for this shop.'], 422);
        }

        $joinRequest = ShopJoinRequest::create([
            'user_id' => $user->id,
            'user_code' => $user->code,
            'shop_id' => $request->shop_id,
            'status' => 'pending',
            'type' => 'request',
            'department' => $request->department,
        ]);

        return response()->json([
            'message' => 'Join request submitted successfully.',
            'data' => $joinRequest->load(['user', 'shop']),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $user = $request->user();
        $joinRequest = ShopJoinRequest::with('shop')->findOrFail($id);

        // Check permission: superadmin, shop owner, or manager of the target shop
        if ($user->role?->slug !== 'developer' && $joinRequest->shop->user_code !== $user->code && $user->shop_code !== $joinRequest->shop->code) {
            return response()->json(['message' => 'Unauthorized to update this request.'], 403);
        }

        $joinRequest->update(['status' => $request->status]);

        // If approved, assign user to shop
        if ($request->status === 'approved') {
            $targetUser = User::find($joinRequest->user_id);
            if ($targetUser) {
                $targetUser->shop_code = $joinRequest->shop->code;
                
                // If a role was assigned, update role_id
                if ($joinRequest->role_id) {
                    $targetUser->role_id = $joinRequest->role_id;
                } else {
                    // Default to staff role if not set
                    $staffRole = \App\Models\Role::where('slug', 'staff')->first();
                    if ($staffRole) {
                        $targetUser->role_id = $staffRole->id;
                    }
                }

                if ($joinRequest->department) {
                    $targetUser->department = $joinRequest->department;
                }

                $targetUser->save();
            }
        }

        return response()->json([
            'message' => 'Join request updated successfully.',
            'data' => $joinRequest->load(['user', 'shop', 'role']),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $joinRequest = ShopJoinRequest::findOrFail($id);
        $user = $request->user();

        if ($user->role?->slug !== 'developer' && $joinRequest->user_id !== $user->id && $joinRequest->shop->user_code !== $user->code) {
            return response()->json(['message' => 'Unauthorized to delete this request.'], 403);
        }

        $joinRequest->delete();

        return response()->json(['message' => 'Request deleted successfully.']);
    }
}
