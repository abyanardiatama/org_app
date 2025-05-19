<?php

namespace App\Filament\Resources\SuratBalasanPeminjamanResource\Pages;

use App\Filament\Resources\SuratBalasanPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratBalasanPeminjaman extends EditRecord
{
    protected static string $resource = SuratBalasanPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
