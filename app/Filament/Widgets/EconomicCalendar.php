<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class EconomicCalendar extends Widget
{
    protected string $view = 'filament.widgets.economic-calendar';
    protected int | string | array $columnSpan = 'full'; 

    protected static ?int $sort = 3;
}
