<?php

namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;

class PosTerminal extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'POS Kasir';

    protected static string|UnitEnum|null $navigationGroup = 'POS';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.pos-terminal';
}
