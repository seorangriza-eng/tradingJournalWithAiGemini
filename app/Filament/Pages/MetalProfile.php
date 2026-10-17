<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MetalProfile extends Page
{
    protected string $view = 'filament.pages.metal-profile';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowPathRoundedSquare;

}
