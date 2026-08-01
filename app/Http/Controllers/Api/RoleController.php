<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected array $with = ['permissions'];

    protected function rules($role = null): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'level' => 'sometimes|integer|min:1|max:99',
            'department' => 'sometimes|nullable|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $shopUuid = $request->input('shop_uuid') ?? $request->input('store_uuid');
        $shopCode = null;
        if ($shopUuid) {
            $shop = Shop::where('uuid', $shopUuid)->first();
            $shopCode = $shop ? $shop->code : null;
        }
        
        $query = Role::query()->with('permissions');
        
        if ($shopCode) {
            $query->where(function ($q) use ($shopCode) {
                $q->where('shop_code', $shopCode)
                  ->orWhere('store_code', $shopCode);
            });
        } else {
            // If no shop code provided, only show global roles
            if ($request->user() && $request->user()->role?->slug !== 'superadmin') {
                $query->whereNull('shop_code')->whereNull('store_code');
            }
        }
        
        $roles = $query->get();
        
        return response()->json([
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $shopUuid = $request->input('shop_uuid') ?? $request->input('store_uuid');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'sometimes|integer|min:1|max:99',
            'department' => 'sometimes|nullable|string|max:255',
        ]);

        if (!$shopUuid) {
            return response()->json(['message' => 'shop_uuid is required'], 422);
        }

        $shop = Shop::where('uuid', $shopUuid)->firstOrFail();

        $userShopCode = $request->user() ? ($request->user()->shop_code ?? $request->user()->store_code) : null;
        $isShopOwner = $shop->user_code === $request->user()->code;

        if ($request->user()->role?->slug !== 'superadmin' && !$isShopOwner && $shop->code !== $userShopCode) {
            return response()->json(['message' => 'You cannot create roles for other shops'], 403);
        }

        $slug = \Illuminate\Support\Str::slug($validated['name']);
        
        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'level' => $validated['level'] ?? 3,
            'department' => $validated['department'] ?? null,
            'shop_code' => $shop->code,
            'store_code' => $shop->code,
        ]);

        return response()->json([
            'data' => $role->fresh($this->with),
        ], 201);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $record = $this->resolveRecord($uuid);
        
        if ($record->slug === 'superadmin' || $record->slug === 'store-owner' || $record->slug === 'shop-owner') {
            return response()->json(['message' => 'Cannot modify global roles'], 403);
        }

        if ($request->user() && $request->user()->role_id === $record->id && $request->user()->role?->slug !== 'superadmin') {
            return response()->json(['message' => 'You cannot modify your own role'], 403);
        }

        $roleShopCode = $record->shop_code ?? $record->store_code;

        if (is_null($roleShopCode) && $request->user() && $request->user()->role?->slug !== 'superadmin') {
            return response()->json(['message' => 'You cannot modify global roles'], 403);
        }

        $shop = Shop::where('code', $roleShopCode)->first();
        $isShopOwner = $shop && $shop->user_code === $request->user()->code;
        $userShopCode = $request->user() ? ($request->user()->shop_code ?? $request->user()->store_code) : null;
        
        if ($roleShopCode !== null && $request->user()->role?->slug !== 'superadmin' && !$isShopOwner && $roleShopCode !== $userShopCode) {
            return response()->json(['message' => 'You cannot modify roles for other shops'], 403);
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
        
        $roleShopCode = $record->shop_code ?? $record->store_code;

        if (is_null($roleShopCode)) {
            return response()->json(['message' => 'Cannot delete global roles'], 403);
        }

        $shop = Shop::where('code', $roleShopCode)->first();
        $isShopOwner = $shop && $shop->user_code === $request->user()->code;
        $userShopCode = $request->user() ? ($request->user()->shop_code ?? $request->user()->store_code) : null;
        
        if ($request->user()->role?->slug !== 'superadmin' && !$isShopOwner && $roleShopCode !== $userShopCode) {
            return response()->json(['message' => 'You cannot delete roles for other shops'], 403);
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
