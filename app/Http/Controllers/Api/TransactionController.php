<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class TransactionController extends ApiResourceController
{
    protected string $modelClass = Transaction::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('transactions', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('transactions', 'code')->ignore($record?->id),
            ],
            'order_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:orders,code',
            ],
            'type' => [
                $record ? 'sometimes' : 'required',
                Rule::in(['payment', 'refund', 'partial_refund']),
            ],
            'total_amount' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'paid_amount' => [
                $record ? 'sometimes' : 'required',
                'numeric',
            ],
            'change_amount' => [
                'sometimes',
                'numeric',
            ],
            'payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],
            'note' => [
                'nullable',
                'string',
            ],
        ];
    }
}
