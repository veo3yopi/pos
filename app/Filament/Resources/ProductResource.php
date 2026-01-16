<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('name')
                ->required()
                ->maxLength(150),
            Textarea::make('description')
                ->rows(3)
                ->maxLength(1000),
            SpatieMediaLibraryFileUpload::make('images')
                ->collection('images')
                ->image()
                ->multiple()
                ->maxFiles(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->circular()
                    ->label('Image'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('created_at')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
