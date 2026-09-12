<?php

namespace App\Filament\Resources\Trades;

use App\Filament\Resources\Trades\Pages\CreateTrades;
use App\Filament\Resources\Trades\Pages\EditTrades;
use App\Filament\Resources\Trades\Pages\ListTrades;
use App\Filament\Resources\Trades\Pages\ViewTrades;
use App\Filament\Resources\Trades\Schemas\TradesForm;
use App\Filament\Resources\Trades\Schemas\TradesInfolist;
use App\Filament\Resources\Trades\Tables\TradesTable;
use App\Models\Trades;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TradesResource extends Resource
{
    protected static ?string $model = Trades::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Sparkles;

    protected static ?string $recordTitleAttribute = 'pair';

    public static function form(Schema $schema): Schema
    {
        return TradesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TradesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrades::route('/'),
            'create' => CreateTrades::route('/create'),
            'view' => ViewTrades::route('/{record}'),
            'edit' => EditTrades::route('/{record}/edit'),
        ];
    }
}
