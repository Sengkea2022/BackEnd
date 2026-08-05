<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ShopController extends ApiResourceController
{
    protected string $modelClass = Shop::class;
    protected array $with = ['owner'];

    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        $user = request()?->user();
        $query = parent::query();

        if ($user) {
            if ($user->role?->slug === 'developer') {
                return $query;
            }
            if ($user->role?->slug === 'shop-owner') {
                return $query->where(function ($q) use ($user) {
                    $q->where('user_code', $user->code);
                    if (!empty($user->shop_code) && $user->shop_code !== 'N/A') {
                        $q->orWhere('code', $user->shop_code);
                    }
                });
            }
            if (!empty($user->shop_code) && $user->shop_code !== 'N/A') {
                return $query->where('code', $user->shop_code);
            }
            // Allow unassigned users to view active shops for join request dropdown
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
        $shop = $this->resolveRecord($uuid);

        if ($user && $user->role?->slug !== 'developer' && $shop->user_code !== $user->code) {
            if (empty($user->shop_code) || $user->shop_code === 'N/A' || $user->shop_code !== $shop->code) {
                return response()->json(['message' => 'Unauthorized. You are not assigned to this shop.'], 403);
            }
        }

        return response()->json([
            'data' => $shop,
        ]);
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('shops', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('shops', 'code')->ignore($record?->id),
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
            'theme_color' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
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
        if (!in_array($request->user()->role?->slug, ['developer', 'shop-owner'])) {
            return response()->json(['message' => 'Only admins can create shops.'], 403);
        }

        $validated = $request->validate($this->rules());

        if (empty($validated['user_code'])) {
            $validated['user_code'] = $request->user()->code;
        }

        $managerId = $validated['manager_id'] ?? null;
        $staffIds = $validated['staff_ids'] ?? [];

        unset($validated['manager_id'], $validated['staff_ids']);

        /** @var Shop $shop */
        $shop = Shop::query()->create($validated);

        // Generate default shop-specific roles
        \App\Models\Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'shop_code' => $shop->code,
        ]);
        
        \App\Models\Role::create([
            'name' => 'Staff',
            'slug' => 'staff',
            'shop_code' => $shop->code,
        ]);

        if ($managerId) {
            \App\Models\User::query()->where('id', $managerId)->update([
                'shop_code' => $shop->code,
            ]);
        }

        if (!empty($staffIds)) {
            \App\Models\User::query()->whereIn('id', $staffIds)->update([
                'shop_code' => $shop->code,
            ]);
        }

        return response()->json([
            'data' => $shop->fresh($this->with),
        ], 201);
    }

    public function update(\Illuminate\Http\Request $request, string $uuid): \Illuminate\Http\JsonResponse
    {
        $shop = $this->resolveRecord($uuid);
        $validated = $request->validate($this->rules($shop));

        $managerId = $validated['manager_id'] ?? null;
        $staffIds = $validated['staff_ids'] ?? null;

        unset($validated['manager_id'], $validated['staff_ids']);

        $shop->update($validated);

        if ($managerId) {
            \App\Models\User::query()->where('id', $managerId)->update([
                'shop_code' => $shop->code,
            ]);
        }

        if (is_array($staffIds)) {
            \App\Models\User::query()
                ->where('shop_code', $shop->code)
                ->whereHas('role', function ($q) {
                    $q->where('slug', 'staff');
                })
                ->whereNotIn('id', $staffIds)
                ->update(['shop_code' => 'N/A']);

            if (!empty($staffIds)) {
                \App\Models\User::query()->whereIn('id', $staffIds)->update([
                    'shop_code' => $shop->code,
                ]);
            }
        }

        return response()->json([
            'data' => $shop->fresh($this->with),
        ]);
    }
}
