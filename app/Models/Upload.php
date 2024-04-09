<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upload extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['imagePath', 'excluded'];

    protected $casts = ['disabled' => 'boolean'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImagePathAttribute(): string
    {
        return asset('/uploads') . '/' . $this->image;
    }

    public function getExcludedAttribute(): bool
    {
        return Device::whereJsonContains('upload_ids', $this->id)->exists();
    }
}
