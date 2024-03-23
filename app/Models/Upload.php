<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upload extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function device(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
