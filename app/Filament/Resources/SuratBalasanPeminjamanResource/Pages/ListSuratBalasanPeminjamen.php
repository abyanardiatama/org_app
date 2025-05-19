<?php

namespace App\Filament\Resources\SuratBalasanPeminjamanResource\Pages;

use App\Filament\Resources\SuratBalasanPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratBalasanPeminjamen extends ListRecords
{
    protected static string $resource = SuratBalasanPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
