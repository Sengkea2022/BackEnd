<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreJoinRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreJoinRequestController extends Controller
{
    /**
     * Display a listing of the pending requests (Admin only).
     */
    public function index(Request $request): JsonResponse
    {
        // Simple role check: ensure user is Admin or SuperAdmin
        $user = $request->user();
        if (! $user->hasRole('admin') && ! $user->hasRole('superadmin')) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can view requests.',
            ], 403);
        }

        $requests = StoreJoinRequest::query()
            ->with(['user.role', 'store'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'requests' => $requests,
        ]);
    }

    /**
     * Store a newly created request in storage (Staff/Manager only).
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Validate the request
        $validated = $request->validate([
            'store_uuid' => ['required', 'string', 'uuid', 'exists:stores,uuid'],
        ]);

        $store = \App\Models\Store::query()->where('uuid', $validated['store_uuid'])->firstOrFail();

        // Check if there is already a pending request
        $exists = StoreJoinRequest::query()
            ->where('user_id', $user->id)
            ->where('store_id', $store->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You already have a pending request for this store.',
            ], 422);
        }

        // Create the join request
        $joinRequest = StoreJoinRequest::query()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Request to join store submitted successfully.',
            'request' => $joinRequest,
        ], 201);
    }

    /**
     * Update the specified request in storage (Approve or Reject - Admin only).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $admin = $request->user();
        if (! $admin->hasRole('admin') && ! $admin->hasRole('superadmin')) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can approve or reject requests.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['approved', 'rejected'])],
        ]);

        $joinRequest = StoreJoinRequest::query()->with(['user', 'store'])->find($id);

        if (! $joinRequest) {
            return response()->json([
                'message' => 'Request not found.',
            ], 404);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json([
                'message' => 'This request has already been processed.',
            ], 422);
        }

        if ($validated['status'] === 'approved') {
            $joinRequest->update(['status' => 'approved']);
            // Assign user to store_no
            $joinRequest->user->update([
                'store_no' => $joinRequest->store->store_no,
            ]);
        } else {
            $joinRequest->update(['status' => 'rejected']);
        }

        return response()->json([
            'message' => 'Request has been ' . $validated['status'] . ' successfully.',
            'request' => $joinRequest,
        ]);
    }
}
