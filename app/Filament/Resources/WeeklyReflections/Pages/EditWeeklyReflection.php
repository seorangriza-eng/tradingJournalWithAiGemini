<?php

namespace App\Filament\Resources\WeeklyReflections\Pages;

use App\Filament\Resources\WeeklyReflections\WeeklyReflectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWeeklyReflection extends EditRecord
{
    protected static string $resource = WeeklyReflectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
