<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'order_no',
        'store_uuid',
        'customer_uuid',
        'guest_link_uuid',
        'currency_uuid',
        'status',
        'note',
        'reason',
    ];

    protected function numberColumn(): string
    {
        return 'order_no';
    }
}
