<?php

namespace App\Filament\Resources\Trades\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TradesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pair'),
                Select::make('position')
                    ->options(['LONG' => 'Long', 'SHORT' => 'Short']),
                Select::make('result')
                    ->options([
                        'Win' => 'Win',
                        'Lose' => 'Lose',
                        'BreakEven' => 'Breakeven'
                        ]),
                FileUpload::make('chart_images')
                    ->image()
                    ->disk('public')
                    ->directory('images')
                    ->multiple(),
                Textarea::make('note_transcript')
                    ->columnSpanFull(),
            ]);
    }
}
