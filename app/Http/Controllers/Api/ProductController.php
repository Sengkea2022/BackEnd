<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ProductController extends ApiResourceController
{
    protected string $modelClass = Product::class;

    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if ($user && $user->role?->slug !== 'superadmin') {
            $storeCode = $request->input('filter.store_code');
            if ($storeCode) {
                $store = \App\Models\Store::where('code', $storeCode)->first();
                if ($store && $store->user_code !== $user->code) {
                    if (empty($user->store_code) || $user->store_code === 'N/A' || $user->store_code !== $store->code) {
                        return response()->json(['message' => 'Unauthorized access to store products'], 403);
                    }
                }
            } elseif (empty($user->store_code) || $user->store_code === 'N/A') {
                return response()->json(['data' => [], 'meta' => ['total' => 0]]);
            }
        }

        return parent::index($request);
    }

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
