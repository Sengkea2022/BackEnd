<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasCode, HasUuid, HasQueryScopes;

    protected array $searchable = ['code', 'name'];

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
    ];

    protected $appends = ['value', 'label'];

    public function getValueAttribute(): string
    {
        return $this->code ?? \Illuminate\Support\Str::slug($this->name, '_');
    }

    public function getLabelAttribute(): string
    {
        return $this->name;
    }
}
