<?php

namespace App\Filament\Resources\Trades\Pages;

use App\Filament\Actions\AnalyzeTrade;
use App\Filament\Resources\Trades\TradesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrades extends ViewRecord
{
    protected static string $resource = TradesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AnalyzeTrade::make(),
            EditAction::make(),
        ];
    }
}
