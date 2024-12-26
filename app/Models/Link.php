<?php

namespace App\Models;

use App\Actions\GenerateShortId;
use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;

#[ScopedBy([TeamScope::class])]
class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'password'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['domain'];

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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
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

    public function scopeWithExpired(Builder $query): void
    {
        $query->orWhere('expires_at', '<', now());
    }

    public function scopeOnlyExpired(Builder $query): void
    {
        $query->where('expires_at', '<', now());
    }

    public function getDomainName(): string
    {
        if($this->domain_id == null) {
            return config('app.url');
        }

        if(! isset($this->domain)) {
            $this->load('domain:name');
        }

        return 'http://' . $this->domain->name;
    }

    public function getShortURL(): string
    {
        return implode('/', [$this->getDomainName(), $this->short_id]);
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
