<?php

namespace App\Filament\Resources\Trades\Pages;

use App\Filament\Resources\Trades\TradesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrades extends EditRecord
{
    protected static string $resource = TradesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
