<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('No')
                    ->sortable(),
                TextColumn::make('cashier.name')
                    ->label('Cashier')
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('discount_total')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
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
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
