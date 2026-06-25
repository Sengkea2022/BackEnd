<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'stock_no',
        'store_uuid',
        'product_uuid',
        'qty',
        'low_stock_alert_qty',
    ];

    protected function numberColumn(): string
    {
        return 'stock_no';
    }
}
