<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class VisitByBrowser extends ChartWidget
{
    protected static ?string $heading = 'Browsers';

    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 4;

    protected static ?string $pollingInterval = '30s';

    public ?string $filter = 'week';

    protected static ?array $options = [
        'scales' => [
            'x' => [
                'display' => false,
            ],
            'y' => [
                'display' => false,
            ],
        ]
    ];

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = Visit::query()
            ->select("browser", DB::raw("count(*) as total"))
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
            ->groupBy("browser")
            ->orderByDesc("total")
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Browsers',
                    'data' => $data->map(fn($visit) => $visit->total)->toArray(),
                    'backgroundColor' => getColors()
                ],
            ],
            'labels' => $data->map(fn($visit) => $visit->browser)->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 days',
            'today' => 'Today',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
}
