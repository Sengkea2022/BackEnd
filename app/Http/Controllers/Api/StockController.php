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
            'stock_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('stocks', 'stock_no')->ignore($record?->id),
            ],
            'store_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:stores,uuid',
            ],
            'product_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:products,uuid',
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
