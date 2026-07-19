<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
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

}
