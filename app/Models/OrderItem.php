<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasCode, HasUuid, HasQueryScopes;

    protected array $searchable = ['code'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'code',
        'order_code',
        'product_code',
        'price_code',
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

}
