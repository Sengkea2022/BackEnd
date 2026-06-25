<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class StoreController extends ApiResourceController
{
    protected string $modelClass = Store::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('stores', 'uuid')->ignore($record?->id),
            ],
            'store_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('stores', 'store_no')->ignore($record?->id),
            ],
            'user_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:users,uuid',
            ],
            'name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'logo_path' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
