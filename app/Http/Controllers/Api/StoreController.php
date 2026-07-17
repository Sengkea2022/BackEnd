<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class StoreController extends ApiResourceController
{
    protected string $modelClass = Store::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('stores', 'uuid')->ignore($record?->id),
            ],
            'store_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('stores', 'store_no')->ignore($record?->id),
            ],
            'user_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:users,uuid',
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
        $validated = $request->validate($this->rules());

        $managerId = $validated['manager_id'] ?? null;
        $staffIds = $validated['staff_ids'] ?? [];

        unset($validated['manager_id'], $validated['staff_ids']);

        /** @var Store $store */
        $store = Store::query()->create($validated);

        if ($managerId) {
            \App\Models\User::query()->where('id', $managerId)->update([
                'store_no' => $store->store_no,
            ]);
        }

        if (!empty($staffIds)) {
            \App\Models\User::query()->whereIn('id', $staffIds)->update([
                'store_no' => $store->store_no,
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
                'store_no' => $store->store_no,
            ]);
        }

        if (is_array($staffIds)) {
            \App\Models\User::query()
                ->where('store_no', $store->store_no)
                ->whereHas('role', function ($q) {
                    $q->where('slug', 'staff');
                })
                ->whereNotIn('id', $staffIds)
                ->update(['store_no' => 'N/A']);

            if (!empty($staffIds)) {
                \App\Models\User::query()->whereIn('id', $staffIds)->update([
                    'store_no' => $store->store_no,
                ]);
            }
        }

        return response()->json([
            'data' => $store->fresh($this->with),
        ]);
    }
}
