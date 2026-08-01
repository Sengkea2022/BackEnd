<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasCode, HasUuid, HasQueryScopes;

    protected array $searchable = ['code', 'name', 'phone', 'email'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'code',
        'name',
        'phone',
        'email',
        'note',
    ];

    public function codePrefix(): string
    {
        return 'CUST-';
    }

    public function codePadding(): int
    {
        return 4;
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'customer_code', 'code');
    }
}
