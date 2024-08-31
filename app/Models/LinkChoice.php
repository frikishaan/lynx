<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkChoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
