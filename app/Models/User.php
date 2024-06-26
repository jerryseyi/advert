<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['deviceCount'];

    public function makeAdmin(): void
    {
        $this->role = 'admin';

        $this->save();
    }

    public function removeAdmin(): void
    {
        $this->role = 'customer';
        $this->save();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }

    public function device(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function getDeviceCountAttribute()
    {
        return $this->device()->count();
    }
}
