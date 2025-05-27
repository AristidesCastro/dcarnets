<?php

namespace App\Filament\Resources\CardsdesignResource\Pages;

use App\Filament\Resources\CardsdesignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCardsdesigns extends ListRecords
{
    protected static string $resource = CardsdesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
