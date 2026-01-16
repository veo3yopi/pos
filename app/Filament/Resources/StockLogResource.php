<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\StockLogResource\Pages;
use App\Models\StockLog;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;

class StockLogResource extends Resource
{
    protected static ?string $model = StockLog::class;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                TextColumn::make('note')
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockLogs::route('/'),
            'view' => Pages\ViewStockLog::route('/{record}'),
        ];
    }
}
