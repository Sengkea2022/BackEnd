<?php

namespace App\Models;

use App\Traits\HasQueryScopes;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSequentialNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasQueryScopes;

    protected array $searchable = [];

    use HasFactory, HasSequentialNumber, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'product_no',
        'store_uuid',
        'category_uuid',
        'product_name',
        'description',
        'image_path',
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
        return 'product_no';
    }
}
