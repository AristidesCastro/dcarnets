<?php

namespace App\Filament\Resources\PeopletypeResource\Pages;

use App\Filament\Resources\PeopletypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeopletype extends EditRecord
{
    protected static string $resource = PeopletypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
