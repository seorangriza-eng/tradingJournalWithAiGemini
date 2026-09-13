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
        $totalLose = Trades::where('result', 'lose')
            ->count('created_at') ?? 0;
        $rawWinRate = $totalTrades > 0 ? (($totalWin / $totalTrades) * 100) : 0;
        $winRate = number_format($rawWinRate, 2);

        return [
            Stat::make('Win Rate', $winRate . ' %')
                ->description($winRate > 60 ? 'Pertahankan!' : 'Tetap Semangat!')
                ->descriptionColor($winRate > 60 ? 'success' : 'warning'),
            Stat::make('Total Trades', $totalTrades),
            Stat::make('Total Win', $totalWin),
            Stat::make('Total Lose', $totalLose),
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
