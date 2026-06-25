<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ProductController extends ApiResourceController
{
    protected string $modelClass = Product::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('products', 'uuid')->ignore($record?->id),
            ],
            'product_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('products', 'product_no')->ignore($record?->id),
            ],
            'store_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:stores,uuid',
            ],
            'category_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:categories,uuid',
            ],
            'product_name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'image_path' => [
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
