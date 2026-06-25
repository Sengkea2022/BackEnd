<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'profit_no',
        'store_uuid',
        'order_uuid',
        'total_cost',
        'total_revenue',
        'gross_profit',
        'profit_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_cost' => 'decimal:4',
            'total_revenue' => 'decimal:4',
            'gross_profit' => 'decimal:4',
            'profit_date' => 'date',
        ];
    }

    protected function numberColumn(): string
    {
        return 'profit_no';
    }
}
