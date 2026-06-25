<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'store_no',
        'user_uuid',
        'name',
        'description',
        'logo_path',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function numberColumn(): string
    {
        return 'store_no';
    }
}
