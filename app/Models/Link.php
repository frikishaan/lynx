<?php

namespace App\Models;

use App\Actions\GenerateShortId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function casts(): array
    {
        return [
            'is_expired' => 'bool'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function(Model $model){
            if(is_null($model->short_id))
            {
                $model->short_id = (new GenerateShortId)->execute();
            }
        });
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
