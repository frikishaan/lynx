<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use App\Models\Link;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;

class EditLink extends EditRecord
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_link')
                ->label('Open link')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->outlined()
                ->size(ActionSize::Small)
                ->url(fn (Link $link): string => 'http://' . $link->getShortURL())
                ->extraAttributes([
                    'target' => '_blank',
                ]),
            Actions\DeleteAction::make()
        ];
    }
}
