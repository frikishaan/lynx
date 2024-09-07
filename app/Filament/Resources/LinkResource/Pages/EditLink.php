<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
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
            Action::make('visit_link')
            ->outlined()
            ->label('Visit link')
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->size(ActionSize::Small)
            ->url(fn (): string => config('app.url') . '/' . $this->record->short_id)
            ->extraAttributes([
                'target' => '_blank',
            ]),
            Actions\DeleteAction::make()
        ];
    }
}
