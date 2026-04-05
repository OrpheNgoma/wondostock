<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Évolution des Revenus (MRR)';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // Données des 12 derniers mois
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            // Calculer le MRR pour ce mois
            $mrr = Subscription::where('subscriptions.status', 'active')
                ->whereDate('subscriptions.created_at', '<=', $month->endOfMonth())
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.price') / 100;

            $data[] = $mrr;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenus Mensuels (FCFA)',
                    'data' => $data,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA'; }",
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) { return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA'; }",
                    ],
                ],
            ],
        ];
    }
}
