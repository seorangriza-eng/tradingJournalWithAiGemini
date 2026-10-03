<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RizaTradeRules extends Widget
{
    protected string $view = 'filament.widgets.riza-trade-rules';
    protected int | string | array $columnSpan = 'full'; 

    protected static ?int $sort = 2;
}
