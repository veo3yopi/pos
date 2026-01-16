<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\StockResource\Pages;
use App\Models\Stock;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('variant_id')
                ->relationship('variant', 'sku')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('qty')
                ->numeric()
                ->minValue(0)
                ->required(),
        ]);
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
                TextColumn::make('variant.size.name')
                    ->label('Size'),
                TextColumn::make('variant.color.name')
                    ->label('Color'),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
