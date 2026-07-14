<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\Positions;
use App\Enums\UserActiveStatus;
use App\Enums\UserPaidStatus;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'user_no',
        'phone',
        'date_of_birth',
        'store_no',
        'position',
        'active_status',
        'paid_status',
        'google_id',
        'avatar',
        'department',
        'role_id',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'two_factor_confirmed_at' => 'datetime',
            'active_status' => UserActiveStatus::class,
            'paid_status' => UserPaidStatus::class,
            'position' => Positions::class,
            'otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Generate a UUID for the uuid column before inserting a new user.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }

            if (empty($user->user_no)) {
                $latestUserNo = User::query()->latest('id')->value('user_no');
                $user->user_no = (string) (((int) ($latestUserNo ?? 0)) + 1);
            }
        });
    }

    /**
     * The role that belongs to the user.
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role?->slug === $roleSlug;
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->role?->permissions->contains('slug', $permissionSlug) ?? false;
    }
}
