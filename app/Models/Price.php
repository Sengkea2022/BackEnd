<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'price_no',
        'product_uuid',
        'currency_uuid',
        'cost_price',
        'retail_unit_price',
        'wholesale_unit_price',
        'min_wholesale_qty',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:4',
            'retail_unit_price' => 'decimal:4',
            'wholesale_unit_price' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    protected function numberColumn(): string
    {
        return 'price_no';
    }
}
