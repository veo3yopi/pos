<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ReceiptTransaction extends Page
{
    use InteractsWithRecord;

    protected static string $resource = TransactionResource::class;

    protected string $view = 'filament.resources.transactions.receipt';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load([
            'cashier',
            'items.variant.product',
            'items.variant.size',
            'items.variant.color',
            'payments',
        ]);
    }

    public function getTitle(): string
    {
        return 'Receipt #' . $this->record->id;
    }
}
