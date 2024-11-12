<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VisitByDateChart extends ChartWidget
{
    protected static ?string $heading = 'Visits by date';

    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    public ?string $filter = 'week';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $currentTeam = Filament::getTenant();
        
        $data = Cache::remember(
            'visits_by_date_' . $this->filter . '_' . $currentTeam->id, 
            3600,
            fn () => Visit::query()
                ->select(
                    DB::raw("DATE(visited_at) as date"),
                    DB::raw("count(*) as total")
                )
                ->when(
                    $this->filter == 'today', 
                    fn(Builder $query) => $query->where('visited_at', '>=', now()->startOfDay())
                )
                ->when(
                    $this->filter == 'week', 
                    fn(Builder $query) => $query->where('visited_at', '>=', now()->subWeek())
                )
                ->when(
                    $this->filter == 'month', 
                    fn(Builder $query) => $query->where('visited_at', '>=', now()->subDays(30))
                )
                ->when(
                    $this->filter == 'year', 
                    fn(Builder $query) => $query->where('visited_at', '>=', now()->startOfYear())
                )
                ->join("links", "visits.link_id", "=", "links.id")
                ->where("links.team_id", $currentTeam->id)
                ->groupBy("date")
                ->orderBy("date", "asc")
                ->get()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Link visits by date',
                    'data' => $data->map(fn($visit) => $visit->total)->toArray(),
                ],
            ],
            'labels' => $data->map(fn($visit) => $visit->date)->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 days',
            'today' => 'Today',
            'month' => 'Last 30 days',
            'year' => 'This year',
        ];
    }
}
