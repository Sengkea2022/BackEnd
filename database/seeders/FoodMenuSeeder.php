<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\Price;
use Illuminate\Support\Str;

class FoodMenuSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();
        if ($stores->isEmpty()) {
            return;
        }

        // 1. Food Categories
        $categories = [
            ['code' => 'CAT-FOOD-01', 'name' => 'Burgers & Sandwiches', 'description' => 'Gourmet burgers and artisanal sandwiches.'],
            ['code' => 'CAT-FOOD-02', 'name' => 'Italian Pasta & Pizza', 'description' => 'Handmade pasta and wood-fired pizzas.'],
            ['code' => 'CAT-FOOD-03', 'name' => 'Japanese Sushi & Bowls', 'description' => 'Fresh sushi rolls, teriyaki and ramen.'],
            ['code' => 'CAT-FOOD-04', 'name' => 'Starters & Appetizers', 'description' => 'Crispy snacks, sides, and starters.'],
            ['code' => 'CAT-FOOD-05', 'name' => 'Cold Drinks & Juices', 'description' => 'Refreshing juices, boba teas, and cold beverages.'],
            ['code' => 'CAT-FOOD-06', 'name' => 'Hot Coffee & Tea', 'description' => 'Freshly brewed coffees, lattes, and warm tea.'],
            ['code' => 'CAT-FOOD-07', 'name' => 'Sweet Desserts', 'description' => 'Decadent cakes, ice cream, and pastries.'],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                ['code' => $catData['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                ]
            );
        }

        // 2. Food Products Catalog with Thumbnails
        $products = [
            // Burgers
            [
                'category_code' => 'CAT-FOOD-01',
                'product_name' => 'Classic Double Cheeseburger',
                'description' => 'Double beef patty, melted cheddar cheese, lettuce, tomato, and special burger sauce.',
                'image_path' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&auto=format&fit=crop&q=80',
                'price' => 7.50,
            ],
            [
                'category_code' => 'CAT-FOOD-01',
                'product_name' => 'Crispy BBQ Bacon Chicken Burger',
                'description' => 'Crispy fried chicken breast, smoked bacon, cheddar, BBQ sauce, and coleslaw.',
                'image_path' => 'https://images.unsplash.com/photo-1625813506062-0aeb1d7a094b?w=500&auto=format&fit=crop&q=80',
                'price' => 8.25,
            ],
            [
                'category_code' => 'CAT-FOOD-01',
                'product_name' => 'Mushroom Swiss Beef Burger',
                'description' => 'Juicy grilled beef patty topped with sauted mushrooms, swiss cheese, and garlic mayo.',
                'image_path' => 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=500&auto=format&fit=crop&q=80',
                'price' => 8.50,
            ],
            // Pasta & Pizza
            [
                'category_code' => 'CAT-FOOD-02',
                'product_name' => 'Creamy Truffle Carbonara Pasta',
                'description' => 'Fresh fettuccine in rich parmesan and white truffle cream sauce with bacon.',
                'image_path' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=500&auto=format&fit=crop&q=80',
                'price' => 11.50,
            ],
            [
                'category_code' => 'CAT-FOOD-02',
                'product_name' => 'Neapolitan Pepperoni Pizza',
                'description' => 'Wood-fired sourdough crust, San Marzano tomato sauce, mozzarella, and spicy pepperoni.',
                'image_path' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=500&auto=format&fit=crop&q=80',
                'price' => 12.90,
            ],
            [
                'category_code' => 'CAT-FOOD-02',
                'product_name' => 'Margherita Fresh Basil Pizza',
                'description' => 'Classic Italian pizza with fresh basil leaves, buffalo mozzarella, and tomato sauce.',
                'image_path' => 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=500&auto=format&fit=crop&q=80',
                'price' => 10.50,
            ],
            // Japanese
            [
                'category_code' => 'CAT-FOOD-03',
                'product_name' => 'Salmon Avocado Roll (8 pcs)',
                'description' => 'Fresh Atlantic salmon, creamy avocado, cucumber, wrapped in sushi rice and nori.',
                'image_path' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=500&auto=format&fit=crop&q=80',
                'price' => 9.80,
            ],
            [
                'category_code' => 'CAT-FOOD-03',
                'product_name' => 'Tonkotsu Pork Ramen Bowl',
                'description' => 'Rich 12-hour pork bone broth, tender chashu pork, soft-boiled egg, and ramen noodles.',
                'image_path' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=80',
                'price' => 10.50,
            ],
            [
                'category_code' => 'CAT-FOOD-03',
                'product_name' => 'Chicken Teriyaki Rice Bowl',
                'description' => 'Grilled chicken glazed in sweet teriyaki sauce served over steamed jasmine rice.',
                'image_path' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80',
                'price' => 8.90,
            ],
            // Starters
            [
                'category_code' => 'CAT-FOOD-04',
                'product_name' => 'Golden Crispy French Fries',
                'description' => 'Seasoned golden fries served with garlic aioli and ketchup.',
                'image_path' => 'https://images.unsplash.com/photo-1576107232684-1279f390859f?w=500&auto=format&fit=crop&q=80',
                'price' => 3.50,
            ],
            [
                'category_code' => 'CAT-FOOD-04',
                'product_name' => 'Spicy Buffalo Wings (6 pcs)',
                'description' => 'Crispy fried chicken wings tossed in tangy spicy buffalo sauce.',
                'image_path' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500&auto=format&fit=crop&q=80',
                'price' => 5.80,
            ],
            [
                'category_code' => 'CAT-FOOD-04',
                'product_name' => 'Crispy Mozzarella Cheese Sticks',
                'description' => 'Melted mozzarella cheese coated in herb breadcrumbs served with marinara sauce.',
                'image_path' => 'https://images.unsplash.com/photo-1531749668029-2db88e4276c7?w=500&auto=format&fit=crop&q=80',
                'price' => 4.50,
            ],
            // Cold Drinks
            [
                'category_code' => 'CAT-FOOD-05',
                'product_name' => 'Fresh Mango Passionfruit Smoothie',
                'description' => 'Blend of tropical fresh mangoes, passionfruit, coconut water, and ice.',
                'image_path' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=500&auto=format&fit=crop&q=80',
                'price' => 4.20,
            ],
            [
                'category_code' => 'CAT-FOOD-05',
                'product_name' => 'Iced Brown Sugar Milk Tea',
                'description' => 'Fresh milk, caramelized brown sugar pearls, and black tea over ice.',
                'image_path' => 'https://images.unsplash.com/photo-1558857563-b371033873b8?w=500&auto=format&fit=crop&q=80',
                'price' => 3.80,
            ],
            [
                'category_code' => 'CAT-FOOD-05',
                'product_name' => 'Fresh Iced Mint Lemonade',
                'description' => 'Freshly squeezed lemon juice, crushed mint leaves, and pure cane sugar over ice.',
                'image_path' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=80',
                'price' => 3.20,
            ],
            // Hot Coffee
            [
                'category_code' => 'CAT-FOOD-06',
                'product_name' => 'Hot Cappuccino Espresso',
                'description' => 'Rich double shot espresso with velvety steamed milk foam.',
                'image_path' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop&q=80',
                'price' => 3.50,
            ],
            [
                'category_code' => 'CAT-FOOD-06',
                'product_name' => 'Hot Matcha Green Tea Latte',
                'description' => 'Premium Uji green tea matcha whisked with warm oat milk.',
                'image_path' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80',
                'price' => 3.80,
            ],
            // Desserts
            [
                'category_code' => 'CAT-FOOD-07',
                'product_name' => 'Molten Chocolate Lava Cake',
                'description' => 'Warm Belgian chocolate cake with a gooey molten center, served with vanilla ice cream.',
                'image_path' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=500&auto=format&fit=crop&q=80',
                'price' => 5.50,
            ],
            [
                'category_code' => 'CAT-FOOD-07',
                'product_name' => 'Vanilla Bean New York Cheesecake',
                'description' => 'Classic creamy cheesecake on a graham cracker crust with strawberry coulis.',
                'image_path' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=500&auto=format&fit=crop&q=80',
                'price' => 4.80,
            ],
        ];

        foreach ($stores as $store) {
            // Delete dummy test items for this store
            Product::where('store_code', $store->code)
                ->where(function($q) {
                    $q->where('product_name', 'like', 'test%')
                      ->orWhere('category_code', 'Booking')
                      ->orWhere('category_code', 'like', '%booking%');
                })->delete();

            foreach ($products as $index => $pData) {
                $price = $pData['price'];
                unset($pData['price']);
                $pData['code'] = 'PRD-' . $store->code . '-' . sprintf('%02d', $index + 1);
                $pData['store_code'] = $store->code;
                $pData['is_active'] = true;
                $pData['uuid'] = (string) Str::uuid();

                $prd = Product::updateOrCreate(
                    ['store_code' => $store->code, 'product_name' => $pData['product_name']],
                    $pData
                );

                Price::updateOrCreate(
                    ['product_code' => $prd->code],
                    [
                        'uuid' => (string) Str::uuid(),
                        'code' => 'PRC-' . $prd->code,
                        'product_code' => $prd->code,
                        'currency_code' => 'USD',
                        'cost_price' => round($price * 0.6, 2),
                        'retail_unit_price' => $price,
                        'wholesale_unit_price' => round($price * 0.9, 2),
                        'min_wholesale_qty' => 10,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
