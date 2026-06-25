<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'transaction_no',
        'order_uuid',
        'type',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:4',
            'paid_amount' => 'decimal:4',
            'change_amount' => 'decimal:4',
        ];
    }

    protected function numberColumn(): string
    {
        return 'transaction_no';
    }
}
