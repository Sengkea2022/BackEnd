<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CurrencyController extends ApiResourceController
{
    protected string $modelClass = Currency::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('currencies', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('currencies', 'code')->ignore($record?->id),
            ],
            'country_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:10',
            ],
            'currency_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:10',
                Rule::unique('currencies', 'currency_code')->ignore($record?->id),
            ],
            'currency_name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:100',
            ],
            'symbol' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:10',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
