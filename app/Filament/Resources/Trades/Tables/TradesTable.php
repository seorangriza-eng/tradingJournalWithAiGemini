<?php

namespace App\Filament\Resources\Trades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d M y'),
                TextColumn::make('pair')
                    ->searchable(),
                TextColumn::make('position')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'LONG' => 'success',
                        'SHORT' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state){
                        'LONG' => 'heroicon-m-fire',
                        'SHORT' => 'heroicon-m-fire',
                    }),
                TextColumn::make('result')
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
