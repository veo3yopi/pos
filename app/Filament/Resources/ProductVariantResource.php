<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
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

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('product_id')
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('size_id')
                ->relationship('size', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('color_id')
                ->relationship('color', 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('sku')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true),
            TextInput::make('price')
                ->numeric()
                ->minValue(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->sortable(),
                TextColumn::make('size.name')
                    ->sortable(),
                TextColumn::make('color.name')
                    ->sortable(),
                TextColumn::make('price')
                    ->money('IDR', true)
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
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
