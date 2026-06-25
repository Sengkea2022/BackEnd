<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class OrderController extends ApiResourceController
{
    protected string $modelClass = Order::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('orders', 'uuid')->ignore($record?->id),
            ],
            'order_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('orders', 'order_no')->ignore($record?->id),
            ],
            'store_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:stores,uuid',
            ],
            'customer_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:customers,uuid',
            ],
            'guest_link_uuid' => [
                'nullable',
                'uuid',
                'exists:guest_links,uuid',
            ],
            'currency_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:currencies,uuid',
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
