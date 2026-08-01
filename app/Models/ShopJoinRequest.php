<?php

namespace App\Models;

use App\Traits\HasQueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopJoinRequest extends Model
{
    use HasFactory, HasQueryScopes;

    protected array $searchable = [];

    protected $fillable = [
        'user_id',
        'user_code',
        'shop_id',
        'status',
        'type',
        'role_id',
        'department',
    ];

    /**
     * Get the user who requested to join.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shop requested to join.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the role requested/invited for.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
