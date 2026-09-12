<?php

namespace App\Filament\Resources\WeeklyReflections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WeeklyReflectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('periode')
                    ->columnSpanFull(),
                Textarea::make('perlu_dipertahankan')
                    ->columnSpanFull(),
                Textarea::make('saran_perbaikan')
                    ->columnSpanFull(),
            ]);
    }
}
