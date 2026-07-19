<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuid, HasQueryScopes;

    protected array $searchable = [];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'user_code',
        'order_code',
        'type',
        'title',
        'body',
        'read_at',
    ];

    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
