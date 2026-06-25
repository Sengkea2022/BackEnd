<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class OrderItemController extends ApiResourceController
{
    protected string $modelClass = OrderItem::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('order_items', 'uuid')->ignore($record?->id),
            ],
            'order_item_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('order_items', 'order_item_no')->ignore($record?->id),
            ],
            'order_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:orders,uuid',
            ],
            'product_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:products,uuid',
            ],
            'price_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:prices,uuid',
            ],
            'qty' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'is_wholesale' => [
                'sometimes',
                'boolean',
            ],
            'unit_price' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'line_price' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
        ];
    }
}
