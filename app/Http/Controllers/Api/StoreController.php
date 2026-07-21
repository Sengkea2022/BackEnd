<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class StoreController extends ApiResourceController
{
    protected string $modelClass = Store::class;
    protected array $with = ['owner'];

    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        $user = request()?->user();
        $query = parent::query();

        if ($user) {
            if ($user->role?->slug === 'superadmin') {
                return $query;
            }
            if ($user->role?->slug === 'store-owner') {
                return $query->where(function ($q) use ($user) {
                    $q->where('user_code', $user->code);
                    if (!empty($user->store_code) && $user->store_code !== 'N/A') {
                        $q->orWhere('code', $user->store_code);
                    }
                });
            }
            if (!empty($user->store_code) && $user->store_code !== 'N/A') {
                return $query->where('code', $user->store_code);
            }
            // Allow unassigned users to view active stores for join request dropdown
            if (request()?->isMethod('get')) {
                return $query->where('is_active', true);
            }
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function show(string $uuid): \Illuminate\Http\JsonResponse
    {
        $user = request()?->user();
        $store = $this->resolveRecord($uuid);

        if ($user && $user->role?->slug !== 'superadmin' && $store->user_code !== $user->code) {
            if (empty($user->store_code) || $user->store_code === 'N/A' || $user->store_code !== $store->code) {
                return response()->json(['message' => 'Unauthorized. You are not assigned to this store.'], 403);
            }
        }

        return response()->json([
            'data' => $store,
        ]);
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('stores', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('stores', 'code')->ignore($record?->id),
            ],
            'user_code' => [
                'sometimes',
                'nullable',
                'string',
                'exists:users,code',
            ],
            'name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'logo_path' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'manager_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'staff_ids' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'staff_ids.*' => [
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        if (!in_array($request->user()->role?->slug, ['superadmin', 'store-owner'])) {
            return response()->json(['message' => 'Only admins can create stores.'], 403);
        }

        $validated = $request->validate($this->rules());

        if (empty($validated['user_code'])) {
            $validated['user_code'] = $request->user()->code;
        }

        $managerId = $validated['manager_id'] ?? null;
        $staffIds = $validated['staff_ids'] ?? [];

        unset($validated['manager_id'], $validated['staff_ids']);

        /** @var Store $store */
        $store = Store::query()->create($validated);

        // Generate default store-specific roles
        \App\Models\Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'store_code' => $store->code,
        ]);
        
        \App\Models\Role::create([
            'name' => 'Staff',
            'slug' => 'staff',
            'store_code' => $store->code,
        ]);

        if ($managerId) {
            \App\Models\User::query()->where('id', $managerId)->update([
                'store_code' => $store->code,
            ]);
        }

        if (!empty($staffIds)) {
            \App\Models\User::query()->whereIn('id', $staffIds)->update([
                'store_code' => $store->code,
            ]);
        }

        return response()->json([
            'data' => $store->fresh($this->with),
        ], 201);
    }

    public function update(\Illuminate\Http\Request $request, string $uuid): \Illuminate\Http\JsonResponse
    {
        $store = $this->resolveRecord($uuid);
        $validated = $request->validate($this->rules($store));

        $managerId = $validated['manager_id'] ?? null;
        $staffIds = $validated['staff_ids'] ?? null;

        unset($validated['manager_id'], $validated['staff_ids']);

        $store->update($validated);

        if ($managerId) {
            \App\Models\User::query()->where('id', $managerId)->update([
                'store_code' => $store->code,
            ]);
        }

        if (is_array($staffIds)) {
            \App\Models\User::query()
                ->where('store_code', $store->code)
                ->whereHas('role', function ($q) {
                    $q->where('slug', 'staff');
                })
                ->whereNotIn('id', $staffIds)
                ->update(['store_code' => 'N/A']);

            if (!empty($staffIds)) {
                \App\Models\User::query()->whereIn('id', $staffIds)->update([
                    'store_code' => $store->code,
                ]);
            }
        }

        return response()->json([
            'data' => $store->fresh($this->with),
        ]);
    }
}
