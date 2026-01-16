<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\SizeResource\Pages;
use App\Models\Size;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SizeResource extends Resource
{
    protected static ?string $model = Size::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-pointing-out';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(50),
            TextInput::make('code')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable()
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
            'index' => Pages\ListSizes::route('/'),
            'create' => Pages\CreateSize::route('/create'),
            'edit' => Pages\EditSize::route('/{record}/edit'),
        ];
    }
}
