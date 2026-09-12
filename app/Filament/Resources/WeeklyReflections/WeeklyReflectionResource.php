<?php

namespace App\Filament\Resources\WeeklyReflections;

use App\Filament\Resources\WeeklyReflections\Pages\CreateWeeklyReflection;
use App\Filament\Resources\WeeklyReflections\Pages\EditWeeklyReflection;
use App\Filament\Resources\WeeklyReflections\Pages\ListWeeklyReflections;
use App\Filament\Resources\WeeklyReflections\Pages\ViewWeeklyReflection;
use App\Filament\Resources\WeeklyReflections\Schemas\WeeklyReflectionForm;
use App\Filament\Resources\WeeklyReflections\Schemas\WeeklyReflectionInfolist;
use App\Filament\Resources\WeeklyReflections\Tables\WeeklyReflectionsTable;
use App\Models\WeeklyReflection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WeeklyReflectionResource extends Resource
{
    protected static ?string $model = WeeklyReflection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Film;

    protected static ?string $recordTitleAttribute = 'periode';

    public static function form(Schema $schema): Schema
    {
        return WeeklyReflectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WeeklyReflectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeeklyReflectionsTable::configure($table);
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
            'index' => ListWeeklyReflections::route('/'),
            'create' => CreateWeeklyReflection::route('/create'),
            'view' => ViewWeeklyReflection::route('/{record}'),
            'edit' => EditWeeklyReflection::route('/{record}/edit'),
        ];
    }
}
