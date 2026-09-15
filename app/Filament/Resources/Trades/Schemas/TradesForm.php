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
            ->columns([
                'default' => 3,
                'xl' => 3])
            ->components([
                TextInput::make('pair')
                    ->hiddenOn('create'),
                Select::make('position')
                    ->options([
                        'LONG' => 'Long',
                        'SHORT' => 'Short'])
                    ->hiddenOn('create'),
                Select::make('result')
                    ->options([
                        'Win' => 'Win',
                        'Lose' => 'Lose',
                        'BreakEven' => 'Breakeven'
                        ])
                    ->hiddenOn('create'),
                FileUpload::make('chart_images')
                    ->image()
                    ->disk('public')
                    ->directory('images')
                    ->multiple()
                    ->columnSpanFull(),
                FileUpload::make('result_image')
                    ->image()
                    ->disk('public')
                    ->directory('images')
                    ->columnSpanFull()
                    ->hiddenOn('create'),
                Textarea::make('note_transcript')
                    ->columnSpanFull(),
                Select::make('am_method_aligned')
                    ->options([
                        1 => "Aligned",
                        0 => "Not Aligned",
                    ])
                    ->hiddenOn('create'),
                TextInput::make('discipline_score')
                    ->numeric()
                    ->hiddenOn('create'),
                Textarea::make('rule_violations')
                    ->columnSpanFull()
                    ->hiddenOn('create'),
                Textarea::make('consistency_eval')
                    ->columnSpanFull()
                    ->hiddenOn('create'),
                Textarea::make('ai_analysis')
                    ->columnSpanFull()
                    ->hiddenOn('create'),
            ]);
    }
}
