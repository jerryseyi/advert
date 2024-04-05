<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'uploads_id' => 'array',
        'expiration_date' => 'date',
        'disabled' => 'boolean'
    ];

    protected $appends = ['uploadsCount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }

    public function getUploadsCountAttribute(): int
    {
        return $this->uploads()->count();
    }

    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    public function owners(): HasMany
    {
        return $this->hasMany(View::class, 'owner_device_id');
    }

//    public function getUploadIdsAttribute($value)
//    {
//        return json_decode($value, true);
//    }
}
