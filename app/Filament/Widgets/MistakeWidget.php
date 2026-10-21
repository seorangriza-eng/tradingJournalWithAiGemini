<?php

namespace App\Filament\Widgets;

use App\Models\Mistake as ModelsMistake;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MistakeWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ModelsMistake::query())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d F'),
                TextColumn::make('mistake')
                    ->label('Kesalahan')
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('tambah')
                    ->schema([
                        TextInput::make('kesalahan')
                    ])
                    ->action(function ($data){
                        ModelsMistake::create([
                            'mistake' => $data['kesalahan'],
                        ]);
                    })
            ])
            ->recordActions([
                DeleteAction::make()
                    ->hiddenLabel()
                    ->icon('heroicon-m-trash')
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                //
            ]);
    }
}
