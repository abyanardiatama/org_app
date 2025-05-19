<?php

namespace App\Filament\Resources\SuratPeminjamanResource\Pages;

use App\Filament\Resources\SuratPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratPeminjaman extends EditRecord
{
    protected static string $resource = SuratPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
