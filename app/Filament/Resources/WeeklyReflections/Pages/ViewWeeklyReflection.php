<?php

namespace App\Filament\Resources\WeeklyReflections\Pages;

use App\Filament\Resources\WeeklyReflections\WeeklyReflectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWeeklyReflection extends ViewRecord
{
    protected static string $resource = WeeklyReflectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
