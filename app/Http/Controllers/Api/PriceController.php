<?php

namespace App\Http\Controllers\Api;

use App\Models\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PriceController extends ApiResourceController
{
    protected string $modelClass = Price::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('prices', 'uuid')->ignore($record?->id),
            ],
            'price_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('prices', 'price_no')->ignore($record?->id),
            ],
            'product_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:products,uuid',
            ],
            'currency_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:currencies,uuid',
            ],
            'cost_price' => [
                'nullable',
                'numeric',
            ],
            'retail_unit_price' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'wholesale_unit_price' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'min_wholesale_qty' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
