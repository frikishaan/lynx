<?php

namespace App\Filament\Widgets;

use App\Models\Link;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverwiew extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total engagements', Number::abbreviate(Visit::count(), 1, 1)),
            Stat::make('Engagements in last 7 days', Number::abbreviate(
                Visit::where('visited_at', '>=', now()->subDays(7))->count(), 
                1, 1
            )),
            Stat::make('Total links', Number::abbreviate(
                Link::where('expires_at', null)
                    ->orWhere('expires_at', '>', now())
                    ->count(),
                2, 2
            )),
        ];
    }
}
