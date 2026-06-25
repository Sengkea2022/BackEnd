<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CategoryController extends ApiResourceController
{
    protected string $modelClass = Category::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('categories', 'uuid')->ignore($record?->id),
            ],
            'category_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('categories', 'category_no')->ignore($record?->id),
            ],
            'name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($record?->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}
