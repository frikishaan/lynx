<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $withPivot = ['role'];

    public function casts(): array
    {
        return [
            'default_team' => 'boolean'
        ];
    }

    public function members()
    {
        return $this->belongsToMany(User::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }
}
