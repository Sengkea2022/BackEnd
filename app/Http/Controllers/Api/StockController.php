<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class StockController extends ApiResourceController
{
    protected string $modelClass = Stock::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('stocks', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('stocks', 'code')->ignore($record?->id),
            ],
            'shop_code' => [
                'sometimes',
                'string',
            ],
            'product_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:products,code',
            ],
            'qty' => [
                'sometimes',
                'integer',
            ],
            'low_stock_alert_qty' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}
