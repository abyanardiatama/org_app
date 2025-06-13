<?php

namespace App\Filament\Resources\PerlombaanResource\Pages;

use App\Filament\Resources\PerlombaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerlombaan extends EditRecord
{
    protected static string $resource = PerlombaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
