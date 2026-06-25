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
            'exchange_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('exchange_rates', 'exchange_no')->ignore($record?->id),
            ],
            'from_currency_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:currencies,uuid',
                'different:to_currency_uuid',
            ],
            'to_currency_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:currencies,uuid',
                'different:from_currency_uuid',
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
