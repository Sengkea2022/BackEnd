<?php

namespace App\Models;

use App\Traits\HasQueryScopes;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasQueryScopes;

    protected array $searchable = [];

    use HasFactory, HasCode, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'code',
        'user_code',
        'name',
        'description',
        'logo_path',
        'is_active',
        'country',
        'state',
        'city',
        'commune',
        'village',
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
        return 'ST-';
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_code', 'code');
    }
}
