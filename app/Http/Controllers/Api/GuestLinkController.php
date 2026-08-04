<?php

namespace App\Http\Controllers\Api;

use App\Models\GuestLink;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Category;
use App\Models\Price;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class GuestLinkController extends ApiResourceController
{
    protected string $modelClass = GuestLink::class;

    protected array $with = ['store'];

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! $request->has('token') || empty($request->input('token'))) {
            $request->merge(['token' => 'gl_' . Str::random(24)]);
        }

        return parent::store($request);
    }

    protected function query(): Builder
    {
        $query = parent::query();
        $user = request()?->user();

        $shopCode = request()?->input('filter.shop_code') ?? request()?->input('filter.store_code');
        if ($shopCode) {
            $query->where('shop_code', $shopCode);
        } elseif ($user && $user->role?->slug !== 'superadmin') {
            $userShopCode = $user->shop_code ?? $user->store_code;
            if (!empty($userShopCode) && $userShopCode !== 'N/A') {
                $query->where('shop_code', $userShopCode);
            }
        }

        return $query;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('guest_links', 'uuid')->ignore($record?->id),
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('guest_links', 'code')->ignore($record?->id),
            ],
            'shop_code' => [
                $record ? 'sometimes' : 'required',
                'string',
                'exists:shops,code',
            ],
            'token' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::unique('guest_links', 'token')->ignore($record?->id),
            ],
            'label' => [
                'nullable',
                'string',
                'max:255',
            ],
            'qr_path' => [
                'nullable',
                'string',
                'max:500',
            ],
            'expires_at' => [
                'nullable',
                'date',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function resolveToken(Request $request, string $identifier): \Illuminate\Http\JsonResponse
    {
        // 1. Prioritize looking up Shop directly by UUID or Code
        $shop = Shop::where('uuid', $identifier)
            ->orWhere('code', $identifier)
            ->first();

        $guestLink = null;

        // 2. If shop wasn't found directly, try looking up guest_link token
        if (! $shop) {
            $guestLink = GuestLink::where('token', $identifier)
                ->where('is_active', true)
                ->with(['shop', 'store'])
                ->first();

            if ($guestLink) {
                $shop = $guestLink->shop ?? $guestLink->store;
            }
        }

        if (! $shop) {
            return response()->json([
                'message' => 'Shop menu not found for specified identifier.',
            ], 404);
        }

        if ($guestLink && $guestLink->expires_at && $guestLink->expires_at->isPast()) {
            return response()->json([
                'message' => 'Guest link has expired.',
            ], 410);
        }

        $products = Product::where('shop_code', $shop->code)
            ->with(['prices', 'category', 'stocks'])
            ->get();

        $categoryCodes = $products->pluck('category_code')->unique()->filter();
        $categories = Category::whereIn('code', $categoryCodes)->get();

        return response()->json([
            'data' => [
                'guest_link' => $guestLink,
                'shop' => $shop,
                'store' => $shop,
                'categories' => $categories,
                'products' => $products,
            ],
        ]);
    }

    public function placeGuestOrder(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'store_uuid' => 'nullable|string',
            'store_code' => 'nullable|string',
            'guest_link_code' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'currency_code' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $shopIdentifier = $request->input('shop_uuid') ?? $request->input('shop_code') ?? $request->input('store_uuid') ?? $request->input('store_code') ?? $request->input('store');

        if (! $shopIdentifier) {
            return response()->json(['message' => 'Shop identifier (shop_uuid or shop_code) is required.'], 422);
        }

        $shop = Shop::where('uuid', $shopIdentifier)->orWhere('code', $shopIdentifier)->first();

        if (! $shop) {
            return response()->json(['message' => 'Shop not found.'], 440);
        }

        $shopCode = $shop->code;
        $customerName = $validated['customer_name'] ?? 'Guest Customer';
        $customerPhone = $validated['customer_phone'] ?? null;
        $currencyCode = $validated['currency_code'] ?? 'USD';

        // 1. Ensure Currency exists or fallback
        if (! Currency::where('code', $currencyCode)->exists()) {
            $currencyCode = Currency::first()?->code ?? 'USD';
        }

        $deviceToken = $request->input('device_token') ?? $validated['guest_link_code'] ?? null;

        // 2. Rate Limiting / Anti-DDoS: Prevent rapid repeated order submissions within 10 seconds
        if ($deviceToken) {
            $recentOrder = Order::where('guest_link_code', $deviceToken)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->first();

            if ($recentOrder) {
                return response()->json([
                    'message' => 'Please wait 10 seconds before placing another order.'
                ], 429);
            }
        }

        // 3. Create Order
        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $note = $validated['note'] ?? 'Placed via Customer Menu';
        if ($customerName && $customerName !== 'Guest Customer') {
            $note .= " [Customer: {$customerName}]";
        }
        if ($customerPhone) {
            $note .= " [Phone: {$customerPhone}]";
        }
        $deviceInfo = $request->input('device_info');
        if ($deviceToken) {
            $note .= " [DeviceToken: {$deviceToken}]";
        }
        if ($deviceInfo) {
            $note .= " [Device: {$deviceInfo}]";
        }

        $order = Order::create([
            'uuid' => (string) Str::uuid(),
            'code' => $orderCode,
            'shop_code' => $shopCode,
            'customer_code' => 'GUEST',
            'guest_link_code' => $deviceToken,
            'currency_code' => $currencyCode,
            'status' => 'pending',
            'note' => $note,
        ]);

        // 4. Create OrderItems for each item in the cart
        $items = $request->input('items', []);
        foreach ($items as $itemData) {
            if (empty($itemData['product_code'])) continue;

            $qty = (int) ($itemData['qty'] ?? 1);
            $unitPrice = (float) ($itemData['unit_price'] ?? 0);
            $linePrice = (float) ($itemData['line_price'] ?? ($qty * $unitPrice));

            // Resolve price_code
            $priceCode = $itemData['price_code'] ?? null;
            if (empty($priceCode)) {
                $priceRecord = Price::where('product_code', $itemData['product_code'])->first();
                if ($priceRecord) {
                    $priceCode = $priceRecord->code;
                } else {
                    $priceRecord = Price::create([
                        'uuid' => (string) Str::uuid(),
                        'code' => 'PRC-' . $itemData['product_code'],
                        'product_code' => $itemData['product_code'],
                        'currency_code' => $currencyCode,
                        'retail_unit_price' => $unitPrice,
                        'wholesale_unit_price' => $unitPrice,
                        'is_active' => true,
                    ]);
                    $priceCode = $priceRecord->code;
                }
            }

            OrderItem::create([
                'uuid' => (string) Str::uuid(),
                'code' => 'ORI-' . strtoupper(Str::random(8)),
                'order_code' => $order->code,
                'product_code' => $itemData['product_code'],
                'price_code' => $priceCode,
                'qty' => $qty,
                'is_wholesale' => false,
                'unit_price' => $unitPrice,
                'line_price' => $linePrice,
            ]);
        }

        return response()->json([
            'message' => 'Order placed successfully.',
            'data' => $order->load(['store', 'currency', 'items']),
        ], 201);
    }

    public function getGuestOrderHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        $deviceToken = $request->input('device_token');
        $shopIdentifier = $request->input('shop_uuid') ?? $request->input('shop_code') ?? $request->input('store_uuid') ?? $request->input('store_code') ?? $request->input('store');

        if (! $deviceToken) {
            return response()->json(['message' => 'device_token parameter is required.'], 422);
        }

        if (! $shopIdentifier) {
            return response()->json(['message' => 'shop_uuid or shop_code is required.'], 422);
        }

        $shop = Shop::where('uuid', $shopIdentifier)->orWhere('code', $shopIdentifier)->first();
        $targetShopCode = $shop ? $shop->code : $shopIdentifier;

        $orders = Order::query()
            ->where(function ($q) use ($targetShopCode, $shopIdentifier) {
                $q->where('shop_code', $targetShopCode)
                  ->orWhereHas('shop', function ($sq) use ($targetShopCode, $shopIdentifier) {
                      $sq->where('code', $targetShopCode)
                        ->orWhere('uuid', $shopIdentifier);
                  })
                  ->orWhereHas('items.product', function ($pq) use ($targetShopCode) {
                      $pq->where('shop_code', $targetShopCode);
                  });
            })
            ->with(['shop', 'currency', 'items', 'items.product'])
            ->where(function ($q) use ($deviceToken) {
                $q->where('guest_link_code', $deviceToken)
                  ->orWhere('note', 'LIKE', "%{$deviceToken}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }
}
