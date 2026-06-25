<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'order_item_no',
        'order_uuid',
        'product_uuid',
        'price_uuid',
        'qty',
        'is_wholesale',
        'unit_price',
        'line_price',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_wholesale' => 'boolean',
            'unit_price' => 'decimal:4',
            'line_price' => 'decimal:4',
        ];
    }

    protected function numberColumn(): string
    {
        return 'order_item_no';
    }
}
