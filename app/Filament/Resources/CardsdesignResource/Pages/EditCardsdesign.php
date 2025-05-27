<?php

namespace App\Filament\Resources\CardsdesignResource\Pages;

use App\Filament\Resources\CardsdesignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCardsdesign extends EditRecord
{
    protected static string $resource = CardsdesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
