<?php

namespace App\Filament\Resources\WeeklyReflections\Pages;

use App\Filament\Actions\AiReflection;
use App\Filament\Resources\WeeklyReflections\WeeklyReflectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeeklyReflections extends ListRecords
{
    protected static string $resource = WeeklyReflectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Reflection'),
            AiReflection::make()
        ];
    }
}
