<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\TransactionItemResource\Pages;
use App\Models\TransactionItem;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;

class TransactionItemResource extends Resource
{
    protected static ?string $model = TransactionItem::class;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('variant.product.name')
                    ->label('Product'),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('discount')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListTransactionItems::route('/'),
            'view' => Pages\ViewTransactionItem::route('/{record}'),
        ];
    }
}
