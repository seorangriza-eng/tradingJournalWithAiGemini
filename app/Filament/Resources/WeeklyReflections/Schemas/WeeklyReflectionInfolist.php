<?php

namespace App\Filament\Resources\WeeklyReflections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WeeklyReflectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('periode')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('perlu_dipertahankan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('saran_perbaikan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
