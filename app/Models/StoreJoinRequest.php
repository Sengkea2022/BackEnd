<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreJoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'status',
    ];

    /**
     * Get the user who requested to join.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store requested to join.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
