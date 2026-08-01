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
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('order_items', 'code')->ignore($record?->id),
            ],
            'order_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:orders,code',
            ],
            'product_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:products,code',
            ],
            'price_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:prices,code',
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
