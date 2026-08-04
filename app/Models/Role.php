<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasQueryScopes;

    protected array $searchable = [];

    protected $fillable = [
        'name',
        'slug',
        'shop_code',
        'level',
        'department',
    ];

    /**
     * The shop this role belongs to.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_code', 'code');
    }

    /**
     * The store this role belongs to.
     */
    public function store()
    {
        return $this->belongsTo(Shop::class, 'shop_code', 'code');
    }

    /**
     * The users that belong to the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
