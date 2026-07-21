<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends ApiResourceController
{
    protected string $modelClass = Role::class;

    protected array $with = ['permissions'];

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'level' => 'sometimes|integer|min:3|max:99',
            'department' => 'sometimes|nullable|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $storeUuid = $request->input('store_uuid');
        $storeCode = null;
        if ($storeUuid) {
            $store = \App\Models\Store::where('uuid', $storeUuid)->first();
            $storeCode = $store ? $store->code : null;
        }
        
        $query = Role::query()->with('permissions');
        
        if ($storeCode) {
            $query->where('store_code', $storeCode);
        } else {
            // If no store code provided, only show global roles
            if ($request->user() && $request->user()->role?->slug !== 'superadmin') {
                $query->whereNull('store_code');
            }
        }
        
        $roles = $query->get();
        
        return response()->json([
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'store_uuid' => 'required|uuid|exists:stores,uuid',
            'level' => 'sometimes|integer|min:1|max:99',
            'department' => 'sometimes|nullable|string|max:255',
        ]);

        $store = \App\Models\Store::where('uuid', $validated['store_uuid'])->firstOrFail();

        $userStoreCode = $request->user() ? $request->user()->store_code : null;
        $isStoreOwner = $store->user_code === $request->user()->code;

        if ($request->user()->role?->slug !== 'superadmin' && !$isStoreOwner && $store->code !== $userStoreCode) {
            return response()->json(['message' => 'You cannot create roles for other stores'], 403);
        }

        $slug = \Illuminate\Support\Str::slug($validated['name']);
        
        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'level' => $validated['level'] ?? 3,
            'department' => $validated['department'] ?? null,
            'store_code' => $store->code,
        ]);

        return response()->json([
            'data' => $role->fresh($this->with),
        ], 201);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $record = $this->resolveRecord($uuid);
        
        if ($record->slug === 'superadmin' || $record->slug === 'store-owner') {
            return response()->json(['message' => 'Cannot modify global roles'], 403);
        }

        if ($request->user() && $request->user()->role_id === $record->id && $request->user()->role?->slug !== 'superadmin') {
            return response()->json(['message' => 'You cannot modify your own role'], 403);
        }

        if (is_null($record->store_code) && $request->user() && $request->user()->role?->slug !== 'superadmin') {
            return response()->json(['message' => 'You cannot modify global roles'], 403);
        }

        $store = \App\Models\Store::where('code', $record->store_code)->first();
        $isStoreOwner = $store && $store->user_code === $request->user()->code;
        $userStoreCode = $request->user() ? $request->user()->store_code : null;
        
        if ($record->store_code !== null && $request->user()->role?->slug !== 'superadmin' && !$isStoreOwner && $record->store_code !== $userStoreCode) {
            return response()->json(['message' => 'You cannot modify roles for other stores'], 403);
        }

        $userLevel = $request->user() ? $request->user()->role?->level : 99;
        if ($request->user()->role?->slug !== 'superadmin' && $record->level <= $userLevel) {
            return response()->json(['message' => 'You cannot modify a role with a rank equal to or higher than your own.'], 403);
        }

        $validated = $request->validate($this->rules($record));

        if (isset($validated['level']) && $request->user()->role?->slug !== 'superadmin' && $validated['level'] <= $userLevel) {
            return response()->json(['message' => 'You cannot promote a role to a rank equal to or higher than your own.'], 403);
        }

        $updateData = [];
        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }
        if (isset($validated['level'])) {
            $updateData['level'] = $validated['level'];
        }
        if (array_key_exists('department', $validated)) {
            $updateData['department'] = $validated['department'];
        }

        if (!empty($updateData)) {
            $record->update($updateData);
        }

        if (isset($validated['permissions'])) {
            $record->permissions()->sync($validated['permissions']);
        }

        return response()->json([
            'data' => $record->fresh($this->with),
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $request = request();
        $record = $this->resolveRecord($uuid);
        
        if (is_null($record->store_code)) {
            return response()->json(['message' => 'Cannot delete global roles'], 403);
        }

        $store = \App\Models\Store::where('code', $record->store_code)->first();
        $isStoreOwner = $store && $store->user_code === $request->user()->code;
        $userStoreCode = $request->user() ? $request->user()->store_code : null;
        
        if ($request->user()->role?->slug !== 'superadmin' && !$isStoreOwner && $record->store_code !== $userStoreCode) {
            return response()->json(['message' => 'You cannot delete roles for other stores'], 403);
        }

        $userLevel = $request->user() ? $request->user()->role?->level : 99;
        if ($request->user()->role?->slug !== 'superadmin' && $record->level <= $userLevel) {
            return response()->json(['message' => 'You cannot delete a role with a rank equal to or higher than your own.'], 403);
        }

        // Prevent deleting a role if users are currently assigned to it
        if (\App\Models\User::where('role_id', $record->id)->exists()) {
            return response()->json(['message' => 'Cannot delete role because users are assigned to it'], 400);
        }

        $record->delete();

        return response()->json(null, 204);
    }

    protected function resolveRecord(string $identifier): Model
    {
        return Role::where('id', $identifier)->orWhere('slug', $identifier)->firstOrFail();
    }
}
