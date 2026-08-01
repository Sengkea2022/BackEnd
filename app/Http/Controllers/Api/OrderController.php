<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class OrderController extends ApiResourceController
{
    protected string $modelClass = Order::class;

    protected array $with = ['store', 'currency', 'items'];

    protected ?string $currentStoreIdentifier = null;

    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $this->currentStoreIdentifier = $request->input('filter.store_code') 
            ?? $request->input('filter.store_uuid')
            ?? $request->input('store_code') 
            ?? $request->input('store_uuid');

        if ($this->currentStoreIdentifier) {
            $filter = $request->input('filter', []);
            unset($filter['store_code'], $filter['store_uuid']);
            $request->merge(['filter' => $filter]);
        }

        return parent::index($request);
    }

    protected function resolveRecord(string $uuid): Model
    {
        return Order::where('uuid', $uuid)->orWhere('code', $uuid)->firstOrFail();
    }

    protected function query(): Builder
    {
        $query = parent::query();
        $user = request()?->user();

        $storeIdentifier = $this->currentStoreIdentifier
            ?? request()?->input('filter.store_code') 
            ?? request()?->input('filter.store_uuid')
            ?? request()?->input('store_code') 
            ?? request()?->input('store_uuid');

        if ($storeIdentifier) {
            $store = \App\Models\Store::where('code', $storeIdentifier)
                ->orWhere('uuid', $storeIdentifier)
                ->first();

            $targetStoreCode = $store ? $store->code : $storeIdentifier;

            $query->where(function ($q) use ($targetStoreCode, $storeIdentifier) {
                $q->where('store_code', $targetStoreCode)
                  ->orWhere('store_code', $storeIdentifier)
                  ->orWhereHas('store', function ($sq) use ($targetStoreCode, $storeIdentifier) {
                      $sq->where('code', $targetStoreCode)
                        ->orWhere('uuid', $storeIdentifier);
                  })
                  ->orWhereHas('items.product', function ($pq) use ($targetStoreCode, $storeIdentifier) {
                      $pq->where('store_code', $targetStoreCode)
                        ->orWhere('store_code', $storeIdentifier);
                  });
            });
        } elseif ($user && !in_array($user->role?->slug, ['superadmin', 'admin'])) {
            if (!empty($user->store_code) && $user->store_code !== 'N/A') {
                $userStoreCode = $user->store_code;
                $query->where(function ($q) use ($userStoreCode) {
                    $q->where('store_code', $userStoreCode)
                      ->orWhereHas('items.product', function ($pq) use ($userStoreCode) {
                          $pq->where('store_code', $userStoreCode);
                      });
                });
            }
        }

        return $query;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('orders', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('orders', 'code')->ignore($record?->id),
            ],
            'store_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:stores,code',
            ],
            'customer_code' => [
                'nullable',
                'string',
            ],
            'guest_link_code' => [
                'nullable',
                'string',
            ],
            'currency_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:currencies,code',
            ],
            'status' => [
                'sometimes',
                Rule::in(['pending', 'confirmed', 'processing', 'completed', 'cancelled', 'returned']),
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'reason' => [
                'nullable',
                'string',
            ],
        ];
    }
}

