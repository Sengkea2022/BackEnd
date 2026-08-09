<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CategoryController extends ApiResourceController
{
    protected string $modelClass = Category::class;

    public static function slugify(string $name): string
    {
        $normalized = mb_strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $normalized);
        return trim($slug, '_');
    }

    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $rawName = trim($request->input('name', ''));
        if ($rawName !== '') {
            $slug = static::slugify($rawName);

            // 1. Search existing by slug/value (code), exact case-insensitive name, or normalized name
            $existing = Category::where('code', $slug)
                ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($rawName)])
                ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(name, ' ', '_'), '-', '_'), '&', '_')) = ?", [$slug])
                ->first();

            if ($existing) {
                return response()->json([
                    'data' => $existing,
                    'message' => 'Category already exists, assigned existing category.'
                ]);
            }

            // 2. Assign normalized slug value to code and user label to name
            $request->merge([
                'name' => $rawName,
                'code' => $slug ?: \Illuminate\Support\Str::slug($rawName, '_'),
            ]);
        }

        return parent::store($request);
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('categories', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('categories', 'code')->ignore($record?->id),
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
