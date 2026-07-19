<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory, HasCode, HasUuid, HasQueryScopes;

    protected array $searchable = ['code'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'code',
        'product_code',
        'currency_code',
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

}
