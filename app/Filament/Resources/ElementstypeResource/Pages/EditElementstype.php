<?php

namespace App\Filament\Resources\ElementstypeResource\Pages;

use App\Filament\Resources\ElementstypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElementstype extends EditRecord
{
    protected static string $resource = ElementstypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
