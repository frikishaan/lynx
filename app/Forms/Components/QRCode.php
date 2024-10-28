<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class QRCode extends Field
{
    protected string $view = 'forms.components.qr-code';

    protected string | Closure | null  $url;

    public function url(string | Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url);
    }

    public function isLabelHidden(): bool
    {
        return true;
    }
}
