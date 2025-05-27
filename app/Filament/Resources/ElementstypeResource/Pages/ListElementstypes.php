<?php

namespace App\Filament\Resources\ElementstypeResource\Pages;

use App\Filament\Resources\ElementstypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElementstypes extends ListRecords
{
    protected static string $resource = ElementstypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
