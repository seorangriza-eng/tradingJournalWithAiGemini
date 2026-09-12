<?php

namespace App\Filament\Resources\Trades\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TradesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Date')
                    ->date('d F Y')
                    ->columnSpanFull(),
                TextEntry::make('pair')
                    ->placeholder('-'),
                TextEntry::make('position')
                    ->badge()
                    ->placeholder('-')
                    ->color(fn (string $state): string => match ($state) {
                        'LONG' => 'success',
                        'SHORT' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state){
                        'LONG' => 'heroicon-m-fire',
                        'SHORT' => 'heroicon-m-fire',
                    }),
                TextEntry::make('result')
                    ->placeholder('-')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Win' => 'success',
                        'Lose' => 'danger',
                        'BreakEven' => 'info'
                    })
                    ->icon(fn (string $state): string => match ($state){
                        'Win' => 'heroicon-m-fire',
                        'Lose' => 'heroicon-m-x-mark',
                        'BreakEven' => 'heroicon-m-exclamation-triangle'
                    }),
                ImageEntry::make('chart_images')
                    ->state(fn ($record) => $record->chart_images)
                    ->disk('public')
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->placeholder('-')
                    ->imageGallery()
                    ->columnSpanFull(),
                TextEntry::make('note_transcript')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('am_method_aligned')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-badge'),
                TextEntry::make('discipline_score')
                    ->placeholder('-')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'danger',
                        '2' => 'danger',
                        '3' => 'danger',
                        '4' => 'danger',
                        '5' => 'warning',
                        '6' => 'warning',
                        '7' => 'warning',
                        '8' => 'success',
                        '9' => 'success',
                        '10' => 'success',
                    }),
                TextEntry::make('rule_violations')
                    ->placeholder('-')
                    ->color('warning')
                    ->columnSpanFull(),
                TextEntry::make('consistency_eval')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ai_analysis')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ])
            ->columns([
                'default' => 3,
                'xl' => 3
                ]);
    }
}
