<?php

namespace App\Filament\Resources\Trades\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('discipline_score')
                    ->label('Score')
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
                SelectColumn::make('result')
                    ->options([
                        'Win' => 'Win',
                        'Lose' => 'Lose',
                        'BreakEven' => 'BreakEven'
                    ])
                    ->alignCenter(),
                // TextColumn::make('result')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                    //         'Win' => 'success',
                    //         'Lose' => 'danger',
                    //         'BreakEven' => 'info'
                    //     })
                    //     ->icon(fn (string $state): string => match ($state){
                        //         'Win' => 'heroicon-m-fire',
                        //         'Lose' => 'heroicon-m-x-mark',
                //         'BreakEven' => 'heroicon-m-exclamation-triangle'
                //     }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d M y'),
                ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('result')
                    ->options([
                        'Win' => 'Win',
                        'Lose' => 'Lose',
                        'BreakEven' => 'BreakEven'
                    ])
            ])
            ->deferFilters()
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->close()
            )
            ->recordActions([
                ActionGroup::make([

                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
                ->icon('heroicon-m-bars-3')
            ], position: RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                //
            ]);
    }
}
