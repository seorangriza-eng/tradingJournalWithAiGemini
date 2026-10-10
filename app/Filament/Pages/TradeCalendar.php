<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TradeCalendarWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TradeCalendar extends Page
{
    protected string $view = 'filament.pages.trade-calendar';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected function getHeaderWidgets(): array
    {
        return [
            TradeCalendarWidget::class,
        ];
    }
}
