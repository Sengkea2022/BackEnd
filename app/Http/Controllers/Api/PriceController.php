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
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('prices', 'code')->ignore($record?->id),
            ],
            'product_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:products,code',
            ],
            'currency_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:currencies,code',
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
