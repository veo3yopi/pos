<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListProductVariants extends ListRecords
{
    protected static string $resource = ProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
