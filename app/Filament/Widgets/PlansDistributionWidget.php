<?php

namespace App\Filament\Widgets;

use App\Models\Plan;
use Filament\Widgets\ChartWidget;

class PlansDistributionWidget extends ChartWidget
{
    protected ?string $heading = 'Répartition des Plans';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $plans = Plan::withCount(['subscriptions as active_subscriptions_count' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        $colors = [
            '#3B82F6', // Blue
            '#F59E0B', // Amber
            '#10B981', // Green
            '#8B5CF6', // Purple
            '#EF4444', // Red
        ];

        return [
            'datasets' => [
                [
                    'data' => $plans->pluck('active_subscriptions_count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $plans->count()),
                    'borderColor' => array_slice($colors, 0, $plans->count()),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $plans->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) { 
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }",
                    ],
                ],
            ],
        ];
    }
}
