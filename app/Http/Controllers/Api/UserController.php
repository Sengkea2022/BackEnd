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
            $user->load('role.permissions');
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
            $user->load('role.permissions');
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
            ->get(['id', 'name', 'email', 'store_no']);

        $staff = \App\Models\User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'staff');
            })
            ->get(['id', 'name', 'email', 'store_no']);

        return response()->json([
            'managers' => $managers,
            'staff' => $staff,
        ]);
    }
}
