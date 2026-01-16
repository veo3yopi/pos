<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')
                    ->label('Transaction')
                    ->sortable(),
                TextColumn::make('method')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('note')
                    ->wrap(),
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
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
