<?php

namespace App\Filament\Widgets;

use App\Models\Trades;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

class WidgetDashboard extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected static bool $isLazy = true;
    protected ?string $heading = 'Trades Analytics';
    
    protected function getStats(): array
    {
        $totalTrades = Trades::count('created_at') ?? 0;
        $totalWin = Trades::where('result', 'win')
            ->count('created_at') ?? 0;
        // $totalLose = Trades::where('result', 'lose')
        //     ->count('created_at') ?? 0;
        $rawWinRate = $totalTrades > 0 ? (($totalWin / $totalTrades) * 100) : 0;
        $winRate = number_format($rawWinRate, 2);

        $averageScore = Trades::average('discipline_score' ?? 0);

        $discipline_chart = Trades::latest()
            ->take(10)
            ->pluck('discipline_score')
            ->reverse() // Balikkan agar urutan dari data lama ke data terbaru
            ->toArray();

        return [
            Stat::make('Win Rate', $winRate . ' %')
                ->description($winRate > 50 ? 'Pertahankan!' : 'Tetap Semangat!')
                ->descriptionColor($winRate > 50 ? 'success' : 'warning'),
            Stat::make('Average Score', number_format($averageScore, 2))
                ->description($averageScore > 8 ? 'Pertahankan!' : 'Wajib Lebih Sabar & Teliti Lagi ya!!!')
                ->descriptionColor($averageScore > 8 ? 'success' : 'danger')
                ->chart(empty($discipline_chart) ? [0] : $discipline_chart)
                ->color($averageScore > 8 ? 'success' : 'danger'),

            // Stat::make('Total Trades', $totalTrades),
            // Stat::make('Total Win', $totalWin),
            // Stat::make('Total Lose', $totalLose),
        ];
    }

    #[Override]
    protected function getColumns(): int|array|null
    {
        return([
            'md' => 4,
            'default' => 2,
        ]);
    }

}
