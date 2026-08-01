<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasCode, HasUuid, HasQueryScopes;

    protected array $searchable = ['code'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'code',
        'store_code',
        'customer_code',
        'guest_link_code',
        'currency_code',
        'status',
        'note',
        'reason',
    ];

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }


    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function guestLink(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GuestLink::class, 'guest_link_code', 'code');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_code', 'code');
    }
}
