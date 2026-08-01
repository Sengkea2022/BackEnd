<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasCode, HasUuid, HasQueryScopes;

    protected array $searchable = ['code', 'product_name'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'code',
        'store_code',
        'category_code',
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

    public function codePrefix(): string
    {
        return 'PR-';
    }

    public function codePadding(): int
    {
        return 4;
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_code', 'code');
    }

    public function prices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Price::class, 'product_code', 'code');
    }

    public function stocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Stock::class, 'product_code', 'code');
    }
}
