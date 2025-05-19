<?php

namespace App\Filament\Resources\SuratPeminjamanResource\Pages;

use App\Filament\Resources\SuratPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPeminjamen extends ListRecords
{
    protected static string $resource = SuratPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
