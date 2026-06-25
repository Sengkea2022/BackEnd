<?php

namespace App\Http\Controllers\Api;

use App\Models\Profit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ProfitController extends ApiResourceController
{
    protected string $modelClass = Profit::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('profits', 'uuid')->ignore($record?->id),
            ],
            'profit_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('profits', 'profit_no')->ignore($record?->id),
            ],
            'store_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:stores,uuid',
            ],
            'order_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                Rule::exists('orders', 'uuid'),
                Rule::unique('profits', 'order_uuid')->ignore($record?->id),
            ],
            'total_cost' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'total_revenue' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'gross_profit' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'profit_date' => [
                $record ? 'sometimes' : 'required',
                'date',
            ],
        ];
    }
}
