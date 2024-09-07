<?php

namespace App\Models;

use App\Actions\GenerateShortId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;

class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'is_expired' => 'boolean',
            'password' => 'hashed',
            'expires_at' => 'datetime',
            'delete_after_expired' => 'boolean'
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

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(LinkChoice::class);
    }
    
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at ? $this->expires_at < now() : false;
    }

    public function isPasswordProtected(): bool
    {
        return (bool) $this->password;
    }

    public function hasChoices(): bool
    {
        if (! isset($this->choices_count)) {
            $this->loadCount("choices");
        }

        return (bool) $this->choices_count > 0;
    }

    public function getRedirectUrl(): string
    {
        return URL::query($this->long_url, [
            "utm_source" => $this->utm_source,
            "utm_medium" => $this->utm_medium,
            "utm_content" => $this->utm_content,
            "utm_term" => $this->utm_term,
            "utm_campaign" => $this->utm_campaign
        ]);
    }
}
