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
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('profits', 'code')->ignore($record?->id),
            ],
            'shop_code' => [
                'sometimes',
                'string',
            ],
            'store_code' => [
                'sometimes',
                'string',
            ],
            'order_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                Rule::exists('orders', 'code'),
                Rule::unique('profits', 'order_code')->ignore($record?->id),
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
