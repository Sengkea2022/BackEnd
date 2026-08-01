<?php

namespace App\Http\Controllers\Api;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ExchangeRateController extends ApiResourceController
{
    protected string $modelClass = ExchangeRate::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('exchange_rates', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('exchange_rates', 'code')->ignore($record?->id),
            ],
            'from_currency_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:currencies,code',
                'different:to_currency_code',
            ],
            'to_currency_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:currencies,code',
                'different:from_currency_code',
            ],
            'rate' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'exchange_date' => [
                $record ? 'sometimes' : 'required',
                'date',
            ],
        ];
    }
}
