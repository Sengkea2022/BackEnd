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

        // Convert legacy filter[store_code] to filter[shop_code]
        if ($request->has('filter.store_code')) {
            $filter = $request->input('filter', []);
            if (!isset($filter['shop_code']) && isset($filter['store_code'])) {
                $filter['shop_code'] = $filter['store_code'];
            }
            unset($filter['store_code']);
            $request->merge(['filter' => $filter]);
        }

        if ($user && $user->role?->slug !== 'superadmin') {
            $shopCode = $request->input('filter.shop_code');
            if ($shopCode) {
                $shop = \App\Models\Shop::where('code', $shopCode)->first();
                if ($shop && $shop->user_code !== $user->code) {
                    $userShopCode = $user->shop_code ?? $user->store_code;
                    if (empty($userShopCode) || $userShopCode === 'N/A' || $userShopCode !== $shop->code) {
                        return response()->json(['message' => 'Unauthorized access to shop products'], 403);
                    }
                }
            } else {
                $userShopCode = $user->shop_code ?? $user->store_code;
                if (empty($userShopCode) || $userShopCode === 'N/A') {
                    return response()->json(['data' => [], 'meta' => ['total' => 0]]);
                }
            }
        }

        return parent::index($request);
    }

    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = array_merge($this->rules(), [
            'price' => 'sometimes|nullable|numeric',
            'stock' => 'sometimes|nullable|integer',
        ]);
        $validated = $request->validate($rules);

        $product = Product::create($validated);

        $priceVal = (float) ($request->input('price') ?? 0);
        \App\Models\Price::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'code' => 'PRC-' . $product->code,
            'product_code' => $product->code,
            'currency_code' => 'USD',
            'retail_unit_price' => $priceVal,
            'wholesale_unit_price' => $priceVal,
            'is_active' => true,
        ]);

        if ($request->has('stock')) {
            \App\Models\Stock::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'code' => 'STK-' . $product->code,
                'shop_code' => $product->shop_code ?? $product->store_code,
                'product_code' => $product->code,
                'qty' => (int) $request->input('stock', 0),
            ]);
        }

        return response()->json([
            'data' => $product->fresh($this->with),
        ], 201);
    }

    public function update(\Illuminate\Http\Request $request, string $uuid): \Illuminate\Http\JsonResponse
    {
        $product = $this->resolveRecord($uuid);
        $rules = array_merge($this->rules($product), [
            'price' => 'sometimes|nullable|numeric',
            'stock' => 'sometimes|nullable|integer',
        ]);
        $validated = $request->validate($rules);

        $product->update($validated);

        if ($request->has('price')) {
            $priceVal = (float) $request->input('price');
            $price = \App\Models\Price::where('product_code', $product->code)->first();
            if ($price) {
                $price->update(['retail_unit_price' => $priceVal, 'wholesale_unit_price' => $priceVal]);
            } else {
                \App\Models\Price::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'code' => 'PRC-' . $product->code,
                    'product_code' => $product->code,
                    'currency_code' => 'USD',
                    'retail_unit_price' => $priceVal,
                    'wholesale_unit_price' => $priceVal,
                    'is_active' => true,
                ]);
            }
        }

        if ($request->has('stock')) {
            $stockVal = (int) $request->input('stock');
            $stock = \App\Models\Stock::where('product_code', $product->code)->first();
            if ($stock) {
                $stock->update(['qty' => $stockVal]);
            } else {
                \App\Models\Stock::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'code' => 'STK-' . $product->code,
                    'shop_code' => $product->shop_code ?? $product->store_code,
                    'product_code' => $product->code,
                    'qty' => $stockVal,
                ]);
            }
        }

        return response()->json([
            'data' => $product->fresh($this->with),
        ]);
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('products', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('products', 'code')->ignore($record?->id),
            ],
            'shop_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:shops,code',
            ],
            'category_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:categories,code',
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
