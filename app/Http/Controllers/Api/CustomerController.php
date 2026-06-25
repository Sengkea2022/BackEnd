<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CustomerController extends ApiResourceController
{
    protected string $modelClass = Customer::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('customers', 'uuid')->ignore($record?->id),
            ],
            'customer_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('customers', 'customer_no')->ignore($record?->id),
            ],
            'name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'note' => [
                'nullable',
                'string',
            ],
        ];
    }
}
