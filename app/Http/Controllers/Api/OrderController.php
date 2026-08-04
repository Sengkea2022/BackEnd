<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class OrderController extends ApiResourceController
{
    protected string $modelClass = Order::class;

    protected array $with = ['shop', 'currency', 'items'];

    protected ?string $currentShopIdentifier = null;

    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $this->currentShopIdentifier = $request->input('filter.shop_code') 
            ?? $request->input('filter.shop_uuid')
            ?? $request->input('filter.store_code') 
            ?? $request->input('filter.store_uuid')
            ?? $request->input('shop_code') 
            ?? $request->input('shop_uuid');

        if ($this->currentShopIdentifier) {
            $filter = $request->input('filter', []);
            unset($filter['shop_code'], $filter['shop_uuid'], $filter['store_code'], $filter['store_uuid']);
            $request->merge(['filter' => $filter]);
        }

        $query = $this->query();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $completedCount = (clone $query)->where('status', 'completed')->count();

        $response = parent::index($request);
        $data = $response->getData(true);

        if (isset($data['meta']) && is_array($data['meta'])) {
            $data['meta']['pending_count'] = $pendingCount;
            $data['meta']['completed_count'] = $completedCount;
            return response()->json($data);
        }

        return response()->json([
            'data' => $data['data'] ?? $data,
            'meta' => [
                'total' => is_array($data) ? count($data) : 0,
                'pending_count' => $pendingCount,
                'completed_count' => $completedCount,
            ]
        ]);
    }

    protected function resolveRecord(string $uuid): Model
    {
        return Order::where('uuid', $uuid)->orWhere('code', $uuid)->firstOrFail();
    }

    protected function query(): Builder
    {
        $query = parent::query();
        $user = request()?->user();

        $shopIdentifier = $this->currentShopIdentifier
            ?? request()?->input('filter.shop_code') 
            ?? request()?->input('filter.shop_uuid')
            ?? request()?->input('filter.store_code') 
            ?? request()?->input('filter.store_uuid')
            ?? request()?->input('shop_code') 
            ?? request()?->input('shop_uuid');

        if ($shopIdentifier) {
            $shop = \App\Models\Shop::where('code', $shopIdentifier)
                ->orWhere('uuid', $shopIdentifier)
                ->first();

            $targetShopCode = $shop ? $shop->code : $shopIdentifier;

            $query->where(function ($q) use ($targetShopCode, $shopIdentifier) {
                $q->where('shop_code', $targetShopCode)
                  ->orWhere('shop_code', $shopIdentifier)
                  ->orWhereHas('shop', function ($sq) use ($targetShopCode, $shopIdentifier) {
                      $sq->where('code', $targetShopCode)
                        ->orWhere('uuid', $shopIdentifier);
                  })
                  ->orWhereHas('items.product', function ($pq) use ($targetShopCode, $shopIdentifier) {
                      $pq->where('shop_code', $targetShopCode)
                        ->orWhere('shop_code', $shopIdentifier);
                  });
            });
        } elseif ($user && !in_array($user->role?->slug, ['superadmin', 'admin'])) {
            if (!empty($user->shop_code) && $user->shop_code !== 'N/A') {
                $userShopCode = $user->shop_code;
                $query->where(function ($q) use ($userShopCode) {
                    $q->where('shop_code', $userShopCode)
                      ->orWhereHas('items.product', function ($pq) use ($userShopCode) {
                          $pq->where('shop_code', $userShopCode);
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
            'shop_code' => [
                'sometimes',
                'nullable',
                'string',
                'exists:shops,code',
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

